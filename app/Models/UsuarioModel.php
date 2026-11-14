<?php

namespace App\Models;

class UsuarioModel extends BaseModel
{
    protected $table = "usuario";
    protected $primaryKey = "id_usuario";
    protected $allowedFields = [
        "id_perfil",
        "nome",
        "login",
        "email",
        "registro_profissional",
        "senha_hash",
        "ativo"
    ];

    protected $useTimestamps = true;
    protected $createdField = "data_cadastro";
    protected $updatedField = "";

    protected $validationRules = [
        "id_usuario" => [
            "label" => "Id",
            "rules" => "permit_empty|is_natural_no_zero"
        ],
        "nome" => [
            "label" => "Nome",
            "rules" => "required|min_length[3]|max_length[150]"
        ],
        "login" => [
            "label" => "Login",
            "rules" => "required|min_length[3]|max_length[80]|alpha_numeric_punct|is_unique[usuario.login,id_usuario,{id_usuario}]"
        ],
        "email" => [
            "label" => "E-mail",
            "rules" => "required|valid_email|max_length[150]|is_unique[usuario.email,id_usuario,{id_usuario}]"
        ],
        "registro_profissional" => [
            "label" => "Registro profissional",
            "rules" => "permit_empty|max_length[30]"
        ],
        "id_perfil" => [
            "label" => "Perfil",
            "rules" => "required|is_not_unique[perfil.id_perfil]"
        ],
        "ativo" => [
            "label" => "Situação",
            "rules" => "required|in_list[0,1]"
        ],
        "senha" => [
            "label" => "Senha",
            "rules" => "required_without[id_usuario]|permit_empty|min_length[6]|max_length[72]"
        ],
        "confirma_senha" => [
            "label" => "Confirmação da senha",
            "rules" => "matches[senha]"
        ]
    ];

    /**
     * Localiza um usuário ativo pelo login ou pelo e-mail, com o nome do perfil.
     *
     * @param string $login
     * @return array
     */
    public function getByLogin(string $login): array
    {
        return $this->select("usuario.*, perfil.nome AS perfil")
            ->join("perfil", "perfil.id_perfil = usuario.id_perfil")
            ->groupStart()
                ->where("usuario.login", $login)
                ->orWhere("usuario.email", $login)
            ->groupEnd()
            ->where("usuario.ativo", 1)
            ->first() ?? [];
    }

    /**
     * Lista os usuários com o nome do perfil, aplicando busca e filtro de perfil.
     *
     * @param string $busca
     * @param int $idPerfil
     * @return array
     */
    public function listaUsuarios(string $busca = "", int $idPerfil = 0): array
    {
        $this->select("usuario.id_usuario, usuario.nome, usuario.login, usuario.email, usuario.id_perfil, usuario.ativo, perfil.nome AS perfil")
            ->join("perfil", "perfil.id_perfil = usuario.id_perfil");

        if ($busca != "") {
            $this->groupStart()
                ->like("usuario.nome", $busca)
                ->orLike("usuario.email", $busca)
                ->orLike("usuario.login", $busca)
            ->groupEnd();
        }

        if ($idPerfil > 0) {
            $this->where("usuario.id_perfil", $idPerfil);
        }

        return $this->orderBy("usuario.nome")->findAll();
    }

    /**
     * Lista os usuários ativos de um perfil (ex.: médicos para o relatório).
     *
     * @param int $idPerfil
     * @return array
     */
    public function listaPorPerfil(int $idPerfil): array
    {
        return $this->select("id_usuario, nome")
            ->where("id_perfil", $idPerfil)
            ->where("ativo", 1)
            ->orderBy("nome")
            ->findAll();
    }

    /**
     * Total de usuários ativos (1) ou inativos (0).
     *
     * @param int $ativo
     * @return int
     */
    public function totalPorSituacao(int $ativo): int
    {
        return $this->where("ativo", $ativo)->countAllResults();
    }
}
