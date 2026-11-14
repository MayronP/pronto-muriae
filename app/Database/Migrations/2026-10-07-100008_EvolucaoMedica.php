<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EvolucaoMedica extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_evolucao' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'id_atendimento' => [
                'type' => 'INT',
            ],
            'id_medico' => [
                'type' => 'INT',
            ],
            'data_hora' => [
                'type' => 'DATETIME',
            ],
            'evolucao' => [
                'type' => 'TEXT',
            ],
            'observacoes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'hipotese_diagnostica' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'conclusao_clinica' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_evolucao', true);
        $this->forge->addKey('id_atendimento', false, false, 'idx_evolucao_atendimento');
        $this->forge->addKey('data_hora', false, false, 'idx_evolucao_data_hora');
        $this->forge->addKey('id_medico', false, false, 'idx_evolucao_medico');
        $this->forge->addForeignKey('id_atendimento', 'atendimento', 'id_atendimento', 'CASCADE', 'CASCADE', 'fk_evolucao_atendimento');
        $this->forge->addForeignKey('id_medico', 'usuario', 'id_usuario', 'CASCADE', 'RESTRICT', 'fk_evolucao_medico');
        $this->forge->createTable('evolucao_medica', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('evolucao_medica');
    }
}
