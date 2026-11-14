<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PrescricaoMedicamento extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_item_prescricao' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'id_prescricao' => [
                'type' => 'INT',
            ],
            'id_medicamento' => [
                'type' => 'INT',
            ],
            'dose' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
            ],
            'via' => [
                'type'       => 'VARCHAR',
                'constraint' => 60,
            ],
            'frequencia' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'intervalo_horas' => [
                'type' => 'TINYINT',
                'null' => true,
            ],
            'se_necessario' => [
                'type'    => 'TINYINT',
                'default' => 0,
            ],
            'data_hora_inicio' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'observacoes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'situacao' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
        ]);

        $this->forge->addKey('id_item_prescricao', true);
        $this->forge->addKey('id_medicamento', false, false, 'idx_item_medicamento');
        $this->forge->addKey('id_prescricao', false, false, 'idx_item_prescricao');
        $this->forge->addKey('situacao', false, false, 'idx_item_situacao');
        $this->forge->addForeignKey('id_medicamento', 'medicamento', 'id_medicamento', 'CASCADE', 'RESTRICT', 'fk_item_medicamento');
        $this->forge->addForeignKey('id_prescricao', 'prescricao', 'id_prescricao', 'CASCADE', 'CASCADE', 'fk_item_prescricao');
        $this->forge->createTable('prescricao_medicamento', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('prescricao_medicamento');
    }
}
