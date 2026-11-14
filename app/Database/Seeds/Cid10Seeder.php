<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Cid10Seeder extends Seeder
{
    public function run()
    {
        // Os códigos da CID-10 ficam em cid10.sql, nesta mesma pasta
        $arquivo = __DIR__ . '/cid10.sql';

        if (!is_file($arquivo)) {
            throw new \RuntimeException("Arquivo {$arquivo} não encontrado.");
        }

        preg_match_all("/\('((?:[^']|'')*)', '((?:[^']|'')*)'\)/u", file_get_contents($arquivo), $linhas, PREG_SET_ORDER);

        $aCodigos = [];

        foreach ($linhas as $linha) {
            $aCodigos[] = [
                'codigo'    => str_replace("''", "'", $linha[1]),
                'descricao' => str_replace("''", "'", $linha[2])
            ];
        }

        // Grava em lotes de 500; ignore() não duplica os códigos já gravados
        foreach (array_chunk($aCodigos, 500) as $lote) {
            $this->db->table('cid10')->ignore(true)->insertBatch($lote);
        }
    }
}
