<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Protocolo extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_protocolo' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
            ],
            'marco_inicial' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'janela_minutos' => [
                'type' => 'SMALLINT',
                'null' => true,
            ],
            'fonte' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'ativo' => [
                'type'    => 'TINYINT',
                'default' => 1,
            ],
        ]);

        $this->forge->addKey('id_protocolo', true);
        $this->forge->addUniqueKey('nome', 'uk_protocolo_nome');
        $this->forge->createTable('protocolo', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('protocolo');
    }
}
