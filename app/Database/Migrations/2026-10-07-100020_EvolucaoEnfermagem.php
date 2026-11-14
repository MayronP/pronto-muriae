<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EvolucaoEnfermagem extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_evolucao_enfermagem' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'id_atendimento' => [
                'type' => 'INT',
            ],
            'id_usuario' => [
                'type' => 'INT',
            ],
            'data_hora' => [
                'type' => 'DATETIME',
            ],
            'avaliacao' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'diagnostico' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'planejamento' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'implementacao' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'evolucao' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_evolucao_enfermagem', true);
        $this->forge->addKey('id_atendimento', false, false, 'idx_evolucao_enfermagem_atendimento');
        $this->forge->addKey('id_usuario', false, false, 'idx_evolucao_enfermagem_usuario');
        $this->forge->addForeignKey('id_atendimento', 'atendimento', 'id_atendimento', 'CASCADE', 'CASCADE', 'fk_evolucao_enfermagem_atendimento');
        $this->forge->addForeignKey('id_usuario', 'usuario', 'id_usuario', 'CASCADE', 'RESTRICT', 'fk_evolucao_enfermagem_usuario');
        $this->forge->createTable('evolucao_enfermagem', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('evolucao_enfermagem');
    }
}
