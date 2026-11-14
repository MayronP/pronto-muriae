<?php

namespace App\Filters;

use App\Models\UsuarioModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AutenticacaoFilter implements FilterInterface
{
    /**
     * Bloqueia quem não está logado ou foi desativado e quem não tem
     * o perfil exigido pela rota.
     *
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return \CodeIgniter\HTTP\RedirectResponse|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->has("usuarioId")) {
            session()->setFlashdata("msgError", "Faça login para acessar o sistema.");

            return redirect()->to("login");
        }

        $UsuarioModel = new UsuarioModel();
        $usuario      = $UsuarioModel->getById((int) session()->get("usuarioId"));

        if (empty($usuario) || $usuario["ativo"] != 1 || $usuario["id_perfil"] != session()->get("usuarioPerfilId")) {
            session()->remove(["usuarioId", "usuarioNome", "usuarioPerfilId", "usuarioPerfil", "localAtendimento"]);
            session()->regenerate(true);
            session()->setFlashdata("msgError", "Sua sessão foi encerrada. Entre novamente.");

            return redirect()->to("login");
        }

        if (!empty($arguments) && !in_array(session()->get("usuarioPerfilId"), $arguments)) {
            session()->setFlashdata("msgError", "Acesso não autorizado para o seu perfil.");

            return redirect()->to("dashboard");
        }
    }

    /**
     * Nada a fazer depois da requisição.
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     * @return void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
