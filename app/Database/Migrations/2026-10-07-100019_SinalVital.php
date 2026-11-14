<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SinalVital extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_sinal_vital' => [
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
            'pressao_arterial' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'temperatura' => [
                'type'       => 'DECIMAL',
                'constraint' => '4,1',
            ],
            'frequencia_cardiaca' => [
                'type' => 'SMALLINT',
            ],
            'frequencia_respiratoria' => [
                'type' => 'SMALLINT',
            ],
            'saturacao' => [
                'type' => 'TINYINT',
            ],
            'oxigenio_suplementar' => [
                'type' => 'TINYINT',
            ],
            'nivel_consciencia' => [
                'type'       => 'CHAR',
                'constraint' => 1,
            ],
            'dor' => [
                'type' => 'TINYINT',
                'null' => true,
            ],
            'glicemia' => [
                'type' => 'SMALLINT',
                'null' => true,
            ],
            'observacoes' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id_sinal_vital', true);
        $this->forge->addKey('id_atendimento', false, false, 'idx_sinal_vital_atendimento');
        $this->forge->addKey('id_usuario', false, false, 'idx_sinal_vital_usuario');
        $this->forge->addForeignKey('id_atendimento', 'atendimento', 'id_atendimento', 'CASCADE', 'CASCADE', 'fk_sinal_vital_atendimento');
        $this->forge->addForeignKey('id_usuario', 'usuario', 'id_usuario', 'CASCADE', 'RESTRICT', 'fk_sinal_vital_usuario');
        $this->forge->createTable('sinal_vital', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('sinal_vital');
    }
}
