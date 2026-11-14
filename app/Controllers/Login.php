<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class Login extends BaseController
{
    private $UsuarioModel;

    public function __construct()
    {
        $this->UsuarioModel = new UsuarioModel();
    }

    /**
     * Exibe a tela de login.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function index()
    {
        if (session()->has("usuarioId")) {
            return redirect()->to("dashboard");
        }

        $titulo = "Entrar";
        $dados  = [];
        $erros  = [];

        return view("login/index", compact("titulo", "dados", "erros"));
    }

    /**
     * Valida login e senha, inicia a sessão e direciona conforme o perfil.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function autenticar()
    {
        $regras = [
            "login" => [
                "label" => "Login ou e-mail",
                "rules" => "required|max_length[150]"
            ],
            "senha" => [
                "label" => "Senha",
                "rules" => "required"
            ]
        ];

        $dados = $this->request->getPost();

        if (!$this->validate($regras)) {
            $titulo = "Entrar";
            $erros  = $this->validator->getErrors();

            return view("login/index", compact("titulo", "dados", "erros"));
        }

        // Usuários iniciais criados pelo DatabaseSeeder.
        $usuario = $this->UsuarioModel->getByLogin(trim($dados["login"]));

        if (empty($usuario) || !password_verify($this->request->getPost("senha"), $usuario["senha_hash"])) {
            session()->setFlashdata("msgError", "Login ou senha inválidos.");

            return redirect()->to("login");
        }

        session()->regenerate();
        session()->set([
            "usuarioId"       => $usuario["id_usuario"],
            "usuarioNome"     => $usuario["nome"],
            "usuarioPerfilId" => $usuario["id_perfil"],
            "usuarioPerfil"   => $usuario["perfil"],
        ]);

        $this->gravaLog("Login realizado", "usuario", $usuario["id_usuario"], $usuario["nome"]);

        if ($usuario["id_perfil"] == PERFIL_ENFERMAGEM) {
            return redirect()->to("triagem");
        } elseif ($usuario["id_perfil"] == PERFIL_MEDICO) {
            return redirect()->to("fila");
        }

        return redirect()->to("dashboard");
    }

    /**
     * Encerra a sessão do usuário.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function logout()
    {
        if (session()->has("usuarioId")) {
            $this->gravaLog("Logout realizado", "usuario", session()->get("usuarioId"), session()->get("usuarioNome"));
        }

        session()->destroy();

        return redirect()->to("login");
    }
}
