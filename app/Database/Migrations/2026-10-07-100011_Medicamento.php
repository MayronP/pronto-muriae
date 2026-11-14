<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Medicamento extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_medicamento' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => 180,
            ],
            'apresentacao' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'ativo' => [
                'type'    => 'TINYINT',
                'default' => 1,
            ],
        ]);

        $this->forge->addKey('id_medicamento', true);
        $this->forge->addKey('nome', false, false, 'idx_medicamento_nome');
        $this->forge->createTable('medicamento', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('medicamento');
    }
}
