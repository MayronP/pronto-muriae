<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Reclassificacao extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_reclassificacao' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'id_atendimento' => [
                'type' => 'INT',
            ],
            'id_usuario' => [
                'type' => 'INT',
            ],
            'id_classificacao_anterior' => [
                'type' => 'INT',
            ],
            'id_classificacao_nova' => [
                'type' => 'INT',
            ],
            'motivo' => [
                'type' => 'TEXT',
            ],
            'data_hora' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('id_reclassificacao', true);
        $this->forge->addKey('id_classificacao_anterior', false, false, 'fk_reclassificacao_anterior');
        $this->forge->addKey('id_classificacao_nova', false, false, 'fk_reclassificacao_nova');
        $this->forge->addKey('id_atendimento', false, false, 'idx_reclassificacao_atendimento');
        $this->forge->addKey('id_usuario', false, false, 'idx_reclassificacao_usuario');
        $this->forge->addForeignKey('id_classificacao_anterior', 'classificacao_risco', 'id_classificacao', 'CASCADE', 'RESTRICT', 'fk_reclassificacao_anterior');
        $this->forge->addForeignKey('id_atendimento', 'atendimento', 'id_atendimento', 'CASCADE', 'CASCADE', 'fk_reclassificacao_atendimento');
        $this->forge->addForeignKey('id_classificacao_nova', 'classificacao_risco', 'id_classificacao', 'CASCADE', 'RESTRICT', 'fk_reclassificacao_nova');
        $this->forge->addForeignKey('id_usuario', 'usuario', 'id_usuario', 'CASCADE', 'RESTRICT', 'fk_reclassificacao_usuario');
        $this->forge->createTable('reclassificacao', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('reclassificacao');
    }
}
