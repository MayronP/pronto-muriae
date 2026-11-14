<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Controla as escritas simultâneas no banco.
 */
class FluxoEscritaFilter implements FilterInterface
{
    private $db;
    private ?string $bloqueio = null;

    public function before(RequestInterface $request, $arguments = null)
    {
        if (!in_array(strtoupper($request->getMethod()), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return;
        }

        // A autenticação e o perfil já foram conferidos.
        $this->db = db_connect();
        $nome = 'muriae.escrita.' . sha1($this->db->database . ':' . $this->db->DBPrefix);
        $resultado = $this->db->query('SELECT GET_LOCK(?, 5) AS adquirido', [$nome])->getRowArray();
        if ((int) ($resultado['adquirido'] ?? 0) !== 1) {
            return service('response')->setStatusCode(503)->setHeader('Retry-After', '2')
                ->setContentType('text/plain', 'UTF-8')
                ->setBody('O sistema está ocupado. Nenhuma alteração foi realizada. Aguarde e tente novamente.');
        }

        $this->bloqueio = $nome;
        // Libera o bloqueio também em caso de erro.
        register_shutdown_function(function () { $this->libera(); });
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $this->libera();
    }

    private function libera(): void
    {
        if ($this->bloqueio === null) {
            return;
        }
        $nome = $this->bloqueio;
        $this->bloqueio = null;
        try {
            $this->db->query('SELECT RELEASE_LOCK(?)', [$nome]);
        } catch (\Throwable $exception) {
            log_message('critical', 'Não foi possível liberar o bloqueio de escrita; a conexão deve ser encerrada.');
        }
    }
}
