<?php

namespace App\Models;

class LogAuditoriaModel extends BaseModel
{
    protected $table = "log_auditoria";
    protected $primaryKey = "id_log";
    protected $allowedFields = [
        "id_usuario",
        "acao",
        "entidade",
        "registro_id",
        "detalhes"
    ];

    protected $useTimestamps = true;
    protected $createdField = "data_hora";
    protected $updatedField = "";

    protected $validationRules = [
        "id_usuario" => [
            "label" => "Usuário",
            "rules" => "required|is_natural_no_zero"
        ],
        "acao" => [
            "label" => "Ação",
            "rules" => "required|max_length[180]"
        ]
    ];

    /**
     * Registros de auditoria com o nome do usuário, aplicando os filtros da tela.
     *
     * @param array $filtros
     * @return array
     */
    public function pesquisar(array $filtros): array
    {
        $this->select("log_auditoria.*, usuario.nome AS usuario")
            ->join("usuario", "usuario.id_usuario = log_auditoria.id_usuario");

        if (!empty($filtros["usuario"])) {
            $this->like("usuario.nome", $filtros["usuario"]);
        }

        if (!empty($filtros["acao"])) {
            $this->like("log_auditoria.acao", $filtros["acao"]);
        }

        if (!empty($filtros["data_inicial"])) {
            $this->where("log_auditoria.data_hora >=", $filtros["data_inicial"] . " 00:00:00");
        }

        if (!empty($filtros["data_final"])) {
            $this->where("log_auditoria.data_hora <=", $filtros["data_final"] . " 23:59:59");
        }

        return $this->orderBy("log_auditoria.data_hora", "DESC")
            ->orderBy("log_auditoria.id_log", "DESC")
            ->findAll(200);
    }
}
