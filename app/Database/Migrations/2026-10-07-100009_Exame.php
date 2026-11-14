<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Exame extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_exame' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'id_atendimento' => [
                'type' => 'INT',
            ],
            'id_solicitante' => [
                'type' => 'INT',
            ],
            'id_usuario_resultado' => [
                'type' => 'INT',
                'null' => true,
            ],
            'descricao' => [
                'type'       => 'VARCHAR',
                'constraint' => 180,
            ],
            'data_hora_solicitacao' => [
                'type' => 'DATETIME',
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'resultado' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'data_hora_resultado' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_exame', true);
        $this->forge->addKey('id_atendimento', false, false, 'idx_exame_atendimento');
        $this->forge->addKey('id_solicitante', false, false, 'idx_exame_solicitante');
        $this->forge->addKey('status', false, false, 'idx_exame_status');
        $this->forge->addKey('id_usuario_resultado', false, false, 'idx_exame_usuario_resultado');
        $this->forge->addForeignKey('id_atendimento', 'atendimento', 'id_atendimento', 'CASCADE', 'CASCADE', 'fk_exame_atendimento');
        $this->forge->addForeignKey('id_solicitante', 'usuario', 'id_usuario', 'CASCADE', 'RESTRICT', 'fk_exame_solicitante');
        $this->forge->addForeignKey('id_usuario_resultado', 'usuario', 'id_usuario', 'CASCADE', 'SET NULL', 'fk_exame_usuario_resultado');
        $this->forge->createTable('exame', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('exame');
    }
}
