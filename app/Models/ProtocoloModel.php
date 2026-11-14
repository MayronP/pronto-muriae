<?php

namespace App\Models;

class ProtocoloModel extends BaseModel
{
    protected $table = "protocolo";
    protected $primaryKey = "id_protocolo";
    protected $allowedFields = [
        "nome",
        "marco_inicial",
        "janela_minutos",
        "fonte",
        "ativo"
    ];

    /**
     * Protocolos gerenciados ativos, por nome.
     *
     * @return array
     */
    public function listaAtivos(): array
    {
        return $this->where("ativo", 1)
            ->orderBy("nome")
            ->findAll();
    }
}
