<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Perfil extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_perfil' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'descricao' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'ativo' => [
                'type'    => 'TINYINT',
                'default' => 1,
            ],
        ]);

        $this->forge->addKey('id_perfil', true);
        $this->forge->addUniqueKey('nome', 'uk_perfil_nome');
        $this->forge->createTable('perfil', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('perfil');
    }
}
