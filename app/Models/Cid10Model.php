<?php

namespace App\Models;

class Cid10Model extends BaseModel
{
    protected $table = "cid10";
    protected $primaryKey = "codigo";
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        "codigo",
        "descricao"
    ];

    /**
     * Busca na CID-10 pelo início do código ou por parte da descrição.
     *
     * @param string $termo
     * @param int $limite
     * @return array
     */
    public function busca(string $termo, int $limite = 20): array
    {
        return $this->groupStart()
                ->like("codigo", normalizaCid($termo), "after")
                ->orLike("descricao", $termo)
            ->groupEnd()
            ->orderBy("codigo")
            ->findAll($limite);
    }

    /**
     * Retorna o código da CID-10 (aceita "I21.9" ou "I219") ou array vazio.
     *
     * @param string|null $codigo
     * @return array
     */
    public function getByCodigo($codigo): array
    {
        if (normalizaCid($codigo) == "") {
            return [];
        }

        return $this->where("codigo", normalizaCid($codigo))->first() ?? [];
    }
}
