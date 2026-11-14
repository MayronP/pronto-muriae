<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PassagemPlantao extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_passagem' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'id_atendimento' => [
                'type' => 'INT',
            ],
            'id_usuario' => [
                'type' => 'INT',
            ],
            'situacao' => [
                'type' => 'TEXT',
            ],
            'historico' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'avaliacao' => [
                'type' => 'TEXT',
            ],
            'recomendacao' => [
                'type' => 'TEXT',
            ],
            'data_hora' => [
                'type' => 'DATETIME',
            ],
            'id_usuario_recebimento' => [
                'type' => 'INT',
                'null' => true,
            ],
            'data_hora_recebimento' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_passagem', true);
        $this->forge->addKey('id_atendimento', false, false, 'idx_passagem_atendimento');
        $this->forge->addKey('id_usuario_recebimento', false, false, 'idx_passagem_recebimento');
        $this->forge->addKey('id_usuario', false, false, 'idx_passagem_usuario');
        $this->forge->addForeignKey('id_atendimento', 'atendimento', 'id_atendimento', 'CASCADE', 'CASCADE', 'fk_passagem_atendimento');
        $this->forge->addForeignKey('id_usuario_recebimento', 'usuario', 'id_usuario', 'CASCADE', 'SET NULL', 'fk_passagem_recebimento');
        $this->forge->addForeignKey('id_usuario', 'usuario', 'id_usuario', 'CASCADE', 'RESTRICT', 'fk_passagem_usuario');
        $this->forge->createTable('passagem_plantao', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('passagem_plantao');
    }
}
