<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Prescricao extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_prescricao' => [
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
            'observacoes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
        ]);

        $this->forge->addKey('id_prescricao', true);
        $this->forge->addKey('id_atendimento', false, false, 'idx_prescricao_atendimento');
        $this->forge->addKey('data_hora', false, false, 'idx_prescricao_data_hora');
        $this->forge->addKey('id_medico', false, false, 'idx_prescricao_medico');
        $this->forge->addForeignKey('id_atendimento', 'atendimento', 'id_atendimento', 'CASCADE', 'CASCADE', 'fk_prescricao_atendimento');
        $this->forge->addForeignKey('id_medico', 'usuario', 'id_usuario', 'CASCADE', 'RESTRICT', 'fk_prescricao_medico');
        $this->forge->createTable('prescricao', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('prescricao');
    }
}
