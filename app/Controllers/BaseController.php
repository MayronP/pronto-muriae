<?php

namespace App\Controllers;

use App\Models\LogAuditoriaModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        $this->helpers = ['form', 'url', 'Crud', 'Funcoes'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }

    /**
     * Registra uma ação importante na auditoria (log_auditoria).
     *
     * @param string $acao
     * @param string $entidade
     * @param int $registroId
     * @param string|null $detalhes
     * @return bool
     */
    protected function gravaLog($acao, $entidade, $registroId, $detalhes = null): bool
    {
        try {
            $LogAuditoriaModel = new LogAuditoriaModel();
            if ($LogAuditoriaModel->insert([
                "id_usuario"  => session()->get("usuarioId"),
                "acao"        => $acao,
                "entidade"    => $entidade,
                "registro_id" => $registroId,
                "detalhes"    => $detalhes,
            ]) !== false) {
                return true;
            }
        } catch (\Throwable $exception) {
            // A operação já pode ter sido salva.
        }

        // Evita dados clínicos e detalhes SQL no log.
        log_message('critical', 'Falha na trilha de auditoria. Usuário {usuario}; registro {registro}.', [
            'usuario' => (int) session()->get('usuarioId'),
            'registro' => (int) $registroId,
        ]);
        session()->setFlashdata('msgError', 'A operação foi processada, mas seu registro de auditoria falhou. Não repita a operação; informe o administrador.');
        return false;
    }

    /**
     * Volta para a página anterior só se ela for do próprio sistema.
     *
     * @param string $padrao
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    protected function voltaPaginaAnterior($padrao = "dashboard")
    {
        if (str_starts_with(previous_url(), base_url())) {
            return redirect()->back();
        }

        return redirect()->to($padrao);
    }
}
