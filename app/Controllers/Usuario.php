<?php

namespace App\Controllers;

use App\Models\PerfilModel;
use App\Models\UsuarioModel;

class Usuario extends BaseController
{
    private $UsuarioModel;
    private $PerfilModel;

    public function __construct()
    {
        $this->UsuarioModel = new UsuarioModel();
        $this->PerfilModel  = new PerfilModel();
    }

    /**
     * Lista os usuários com busca por nome, login ou e-mail e filtro de perfil.
     *
     * @return string
     */
    public function index()
    {
        $titulo      = "Usuários";
        $subtitulo   = "Consulte os profissionais e seus perfis de acesso.";
        $paginaAtiva = "admin";
        $busca       = trim((string) $this->request->getGet("busca"));
        $idPerfil    = (int) $this->request->getGet("id_perfil");
        $usuarios    = $this->UsuarioModel->listaUsuarios($busca, $idPerfil);
        $perfis      = $this->PerfilModel->lista("id_perfil");

        $resumoUsuarios = [
            "ativos"      => $this->UsuarioModel->totalPorSituacao(1),
            "nomesPerfis" => implode(", ", array_column($perfis, "nome")),
            "inativos"    => $this->UsuarioModel->totalPorSituacao(0),
        ];

        return view("admin/listaUsuario", compact("titulo", "subtitulo", "paginaAtiva", "busca", "idPerfil", "usuarios", "perfis", "resumoUsuarios"));
    }

    /**
     * Exibe o formulário de inclusão/alteração de usuário.
     *
     * @param string $action
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function form($action = "insert", $id = 0)
    {
        $dados = ($id > 0) ? $this->UsuarioModel->getById($id) : ["ativo" => 1];
        $erros = [];

        if (!in_array($action, ["insert", "update"]) || ($action == "update" && empty($dados["id_usuario"]))) {
            session()->setFlashdata("msgError", "Usuário não encontrado.");

            return redirect()->to("admin/usuarios");
        }

        unset($dados["senha_hash"]);

        return $this->exibeForm($action, $dados, $erros);
    }

    /**
     * Grava (insere ou atualiza) um usuário.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function store()
    {
        $action = $this->request->getPost("action");
        $id     = (int) $this->request->getPost("id_usuario");

        if ($id > 0 && empty($this->UsuarioModel->getById($id))) {
            session()->setFlashdata("msgError", "Usuário não encontrado.");

            return redirect()->to("admin/usuarios");
        }

        $dados = [
            "id_usuario"            => $id > 0 ? $id : null,
            "nome"                  => trim((string) $this->request->getPost("nome")),
            "login"                 => trim((string) $this->request->getPost("login")),
            "email"                 => trim((string) $this->request->getPost("email")),
            "registro_profissional" => trim((string) $this->request->getPost("registro_profissional")),
            "id_perfil"             => $this->request->getPost("id_perfil"),
            "ativo"                 => $this->request->getPost("ativo"),
            "senha"                 => (string) $this->request->getPost("senha"),
            "confirma_senha"        => (string) $this->request->getPost("confirma_senha"),
        ];

        if ($dados["registro_profissional"] === "") {
            $dados["registro_profissional"] = null;
        }

        if ($dados["senha"] != "") {
            $dados["senha_hash"] = password_hash($dados["senha"], PASSWORD_DEFAULT);
        }

        $erros = [];

        if ($id > 0 && $id == session()->get("usuarioId") && ($dados["ativo"] != 1 || $dados["id_perfil"] != PERFIL_ADMINISTRADOR)) {
            $erros["ativo"] = "Você não pode desativar o seu próprio usuário nem retirar o seu perfil de administrador.";
        }

        if (empty($erros) && !$this->UsuarioModel->save($dados)) {
            $erros = $this->UsuarioModel->errors();
        }

        if (!empty($erros)) {
            unset($dados["senha"], $dados["confirma_senha"], $dados["senha_hash"]);

            return $this->exibeForm($action, $dados, $erros);
        }

        $descricao = ($id > 0) ? "Usuário alterado" : "Usuário cadastrado";

        if ($id == 0) {
            $id = $this->UsuarioModel->getInsertID();
        }

        $this->gravaLog($descricao, "usuario", $id, $dados["nome"]);
        session()->setFlashdata("msgSucesso", "Usuário salvo com sucesso.");

        return redirect()->to("admin/usuarios");
    }

    /**
     * Ativa ou desativa o acesso de um usuário.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function situacao()
    {
        $id      = (int) $this->request->getPost("id_usuario");
        $usuario = $this->UsuarioModel->getById($id);

        if (empty($usuario)) {
            session()->setFlashdata("msgError", "Usuário não encontrado.");
        } elseif ($id == session()->get("usuarioId")) {
            session()->setFlashdata("msgError", "Você não pode alterar a situação do seu próprio usuário.");
        } elseif (!$this->UsuarioModel->update($id, ["ativo" => ($usuario["ativo"] == 1) ? 0 : 1])) {
            session()->setFlashdata("msgError", "Não foi possível alterar a situação do usuário. Tente novamente.");
        } else {
            $ativo = ($usuario["ativo"] == 1) ? 0 : 1;

            $this->gravaLog(($ativo == 1) ? "Usuário ativado" : "Usuário desativado", "usuario", $id, $usuario["nome"]);
            session()->setFlashdata("msgSucesso", "Situação do usuário alterada para " . getSituacaoUsuario($ativo) . ".");
        }

        return redirect()->to("admin/usuarios");
    }

    /**
     * Monta o formulário de usuário.
     *
     * @param string $action
     * @param array $dados
     * @param array $erros
     * @return string
     */
    private function exibeForm($action, $dados, $erros)
    {
        $titulo      = "Usuário" . subTitulo($action);
        $subtitulo   = "Defina os dados de acesso e o perfil do profissional.";
        $paginaAtiva = "admin";
        $perfis      = $this->PerfilModel->lista("id_perfil");

        return view("admin/formUsuario", compact("titulo", "subtitulo", "paginaAtiva", "action", "dados", "erros", "perfis"));
    }
}
