<?php

namespace App\Models;

class PerfilModel extends BaseModel
{
    protected $table = "perfil";
    protected $primaryKey = "id_perfil";
    protected $allowedFields = [
        "nome",
        "descricao",
        "ativo"
    ];

    protected $validationRules = [
        "nome" => [
            "label" => "Nome",
            "rules" => "required|max_length[50]"
        ]
    ];
}
