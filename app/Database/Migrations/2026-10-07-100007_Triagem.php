<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Triagem extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_triagem' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'id_atendimento' => [
                'type' => 'INT',
            ],
            'id_enfermeiro' => [
                'type' => 'INT',
            ],
            'id_classificacao' => [
                'type' => 'INT',
            ],
            'queixa_principal' => [
                'type' => 'TEXT',
            ],
            'observacoes_iniciais' => [
                'type' => 'TEXT',
                'null' => true,
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
                'null' => true,
            ],
            'nivel_consciencia' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'null'       => true,
            ],
            'gestante' => [
                'type' => 'TINYINT',
                'null' => true,
            ],
            'dor' => [
                'type' => 'TINYINT',
                'null' => true,
            ],
            'glicemia' => [
                'type' => 'SMALLINT',
                'null' => true,
            ],
            'peso' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
            ],
            'data_hora' => [
                'type' => 'DATETIME',
            ],
            'data_hora_reclassificacao' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_triagem', true);
        $this->forge->addUniqueKey('id_atendimento', 'uk_triagem_atendimento');
        $this->forge->addKey('id_classificacao', false, false, 'idx_triagem_classificacao');
        $this->forge->addKey('data_hora', false, false, 'idx_triagem_data_hora');
        $this->forge->addKey('id_enfermeiro', false, false, 'idx_triagem_enfermeiro');
        $this->forge->addForeignKey('id_atendimento', 'atendimento', 'id_atendimento', 'CASCADE', 'CASCADE', 'fk_triagem_atendimento');
        $this->forge->addForeignKey('id_classificacao', 'classificacao_risco', 'id_classificacao', 'CASCADE', 'RESTRICT', 'fk_triagem_classificacao');
        $this->forge->addForeignKey('id_enfermeiro', 'usuario', 'id_usuario', 'CASCADE', 'RESTRICT', 'fk_triagem_enfermeiro');
        $this->forge->createTable('triagem', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('triagem');
    }
}
