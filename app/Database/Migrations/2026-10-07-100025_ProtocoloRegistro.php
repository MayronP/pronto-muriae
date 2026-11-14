<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ProtocoloRegistro extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_registro' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'id_protocolo_atendimento' => [
                'type' => 'INT',
            ],
            'id_etapa' => [
                'type' => 'INT',
            ],
            'id_usuario' => [
                'type' => 'INT',
            ],
            'data_hora' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('id_registro', true);
        $this->forge->addUniqueKey(['id_protocolo_atendimento', 'id_etapa'], 'uk_protocolo_registro_etapa');
        $this->forge->addKey('id_etapa', false, false, 'idx_protocolo_registro_etapa');
        $this->forge->addKey('id_usuario', false, false, 'idx_protocolo_registro_usuario');
        $this->forge->addForeignKey('id_etapa', 'protocolo_etapa', 'id_etapa', 'CASCADE', 'RESTRICT', 'fk_protocolo_registro_etapa');
        $this->forge->addForeignKey('id_protocolo_atendimento', 'protocolo_atendimento', 'id_protocolo_atendimento', 'CASCADE', 'CASCADE', 'fk_protocolo_registro_protocolo');
        $this->forge->addForeignKey('id_usuario', 'usuario', 'id_usuario', 'CASCADE', 'RESTRICT', 'fk_protocolo_registro_usuario');
        $this->forge->createTable('protocolo_registro', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('protocolo_registro');
    }
}
