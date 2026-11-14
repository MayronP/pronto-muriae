<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ClassificacaoRiscoSeeder extends Seeder
{
    public function run()
    {
        // Tempos-alvo do Sistema Manchester (ordem 1 = mais urgente)
        $aClassificacoes = [
            ['id_classificacao' => 1, 'nome' => 'Vermelho', 'cor' => 'vermelho', 'ordem_prioridade' => 1, 'tempo_alvo_minutos' => 0],
            ['id_classificacao' => 2, 'nome' => 'Laranja', 'cor' => 'laranja', 'ordem_prioridade' => 2, 'tempo_alvo_minutos' => 10],
            ['id_classificacao' => 3, 'nome' => 'Amarelo', 'cor' => 'amarelo', 'ordem_prioridade' => 3, 'tempo_alvo_minutos' => 60],
            ['id_classificacao' => 4, 'nome' => 'Verde', 'cor' => 'verde', 'ordem_prioridade' => 4, 'tempo_alvo_minutos' => 120],
            ['id_classificacao' => 5, 'nome' => 'Azul', 'cor' => 'azul', 'ordem_prioridade' => 5, 'tempo_alvo_minutos' => 240],
        ];

        $tbClassificacao = $this->db->table('classificacao_risco');

        foreach ($aClassificacoes as $classificacao) {

            if ($tbClassificacao->where('id_classificacao', $classificacao['id_classificacao'])->countAllResults() == 0) {
                $tbClassificacao->insert($classificacao);
            }
        }
    }
}
