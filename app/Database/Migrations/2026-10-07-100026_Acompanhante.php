<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Acompanhante extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_acompanhante' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'id_atendimento' => [
                'type' => 'INT',
            ],
            'id_usuario' => [
                'type' => 'INT',
            ],
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'parentesco' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'documento' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'telefone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'data_hora_entrada' => [
                'type' => 'DATETIME',
            ],
            'data_hora_saida' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_acompanhante', true);
        $this->forge->addKey('id_atendimento', false, false, 'idx_acompanhante_atendimento');
        $this->forge->addKey('id_usuario', false, false, 'idx_acompanhante_usuario');
        $this->forge->addForeignKey('id_atendimento', 'atendimento', 'id_atendimento', 'CASCADE', 'CASCADE', 'fk_acompanhante_atendimento');
        $this->forge->addForeignKey('id_usuario', 'usuario', 'id_usuario', 'CASCADE', 'RESTRICT', 'fk_acompanhante_usuario');
        $this->forge->createTable('acompanhante', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('acompanhante');
    }
}
