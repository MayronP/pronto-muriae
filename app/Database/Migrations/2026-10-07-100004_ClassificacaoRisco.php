<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ClassificacaoRisco extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_classificacao' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'cor' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'ordem_prioridade' => [
                'type' => 'TINYINT',
            ],
            'tempo_alvo_minutos' => [
                'type'    => 'SMALLINT',
                'default' => 0,
            ],
            'ativo' => [
                'type'    => 'TINYINT',
                'default' => 1,
            ],
        ]);

        $this->forge->addKey('id_classificacao', true);
        $this->forge->addUniqueKey('nome', 'uk_classificacao_nome');
        $this->forge->addKey('ordem_prioridade', false, false, 'idx_classificacao_prioridade');
        $this->forge->createTable('classificacao_risco', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('classificacao_risco');
    }
}
