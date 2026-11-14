<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ProtocoloAtendimento extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_protocolo_atendimento' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'id_atendimento' => [
                'type' => 'INT',
            ],
            'id_protocolo' => [
                'type' => 'INT',
            ],
            'id_usuario' => [
                'type' => 'INT',
            ],
            'data_hora_abertura' => [
                'type' => 'DATETIME',
            ],
            'inicio_sintomas' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'situacao' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'data_hora_encerramento' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'observacao' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id_protocolo_atendimento', true);
        $this->forge->addUniqueKey(['id_atendimento', 'id_protocolo'], 'uk_protocolo_atendimento');
        $this->forge->addKey('id_protocolo', false, false, 'idx_protocolo_atendimento_protocolo');
        $this->forge->addKey('id_usuario', false, false, 'idx_protocolo_atendimento_usuario');
        $this->forge->addForeignKey('id_atendimento', 'atendimento', 'id_atendimento', 'CASCADE', 'CASCADE', 'fk_protocolo_atendimento_atendimento');
        $this->forge->addForeignKey('id_protocolo', 'protocolo', 'id_protocolo', 'CASCADE', 'RESTRICT', 'fk_protocolo_atendimento_protocolo');
        $this->forge->addForeignKey('id_usuario', 'usuario', 'id_usuario', 'CASCADE', 'RESTRICT', 'fk_protocolo_atendimento_usuario');
        $this->forge->createTable('protocolo_atendimento', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('protocolo_atendimento');
    }
}
