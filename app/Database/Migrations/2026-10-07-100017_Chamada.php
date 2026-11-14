<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Chamada extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_chamada' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'id_atendimento' => [
                'type' => 'INT',
            ],
            'id_usuario' => [
                'type' => 'INT',
            ],
            'local' => [
                'type'       => 'VARCHAR',
                'constraint' => 60,
            ],
            'data_hora' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('id_chamada', true);
        $this->forge->addKey('id_usuario', false, false, 'fk_chamada_usuario');
        $this->forge->addKey('id_atendimento', false, false, 'idx_chamada_atendimento');
        $this->forge->addKey('data_hora', false, false, 'idx_chamada_data_hora');
        $this->forge->addForeignKey('id_atendimento', 'atendimento', 'id_atendimento', 'CASCADE', 'CASCADE', 'fk_chamada_atendimento');
        $this->forge->addForeignKey('id_usuario', 'usuario', 'id_usuario', 'CASCADE', 'RESTRICT', 'fk_chamada_usuario');
        $this->forge->createTable('chamada', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('chamada');
    }
}
