<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Cid10 extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'codigo' => [
                'type'       => 'VARCHAR',
                'constraint' => 4,
            ],
            'descricao' => [
                'type'       => 'VARCHAR',
                'constraint' => 300,
            ],
        ]);

        $this->forge->addKey('codigo', true);
        $this->forge->createTable('cid10', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('cid10');
    }
}
