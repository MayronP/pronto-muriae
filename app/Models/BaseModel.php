<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model
{
    /**
     * Retorna todos os registros da tabela ordenados.
     *
     * @param string $orderby
     * @return array
     */
    public function lista(string $orderby = "nome"): array
    {
        return $this->orderBy($orderby)->findAll();
    }

    /**
     * Retorna um registro pela chave primária (ou array vazio).
     *
     * @param int $id
     * @return array
     */
    public function getById(int $id): array
    {
        return $this->where([$this->primaryKey => $id])->first() ?? [];
    }
}
