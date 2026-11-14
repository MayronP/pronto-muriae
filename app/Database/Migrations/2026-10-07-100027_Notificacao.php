<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Notificacao extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_notificacao' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'id_atendimento' => [
                'type' => 'INT',
            ],
            'id_usuario' => [
                'type' => 'INT',
            ],
            'agravo' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'tipo' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'data_hora' => [
                'type' => 'DATETIME',
            ],
            'situacao' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'id_usuario_notificacao' => [
                'type' => 'INT',
                'null' => true,
            ],
            'data_hora_notificacao' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'numero_sinan' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id_notificacao', true);
        $this->forge->addKey('id_atendimento', false, false, 'idx_notificacao_atendimento');
        $this->forge->addKey('situacao', false, false, 'idx_notificacao_situacao');
        $this->forge->addKey('id_usuario', false, false, 'idx_notificacao_usuario');
        $this->forge->addKey('id_usuario_notificacao', false, false, 'idx_notificacao_usuario_notificacao');
        $this->forge->addForeignKey('id_atendimento', 'atendimento', 'id_atendimento', 'CASCADE', 'CASCADE', 'fk_notificacao_atendimento');
        $this->forge->addForeignKey('id_usuario', 'usuario', 'id_usuario', 'CASCADE', 'RESTRICT', 'fk_notificacao_usuario');
        $this->forge->addForeignKey('id_usuario_notificacao', 'usuario', 'id_usuario', 'CASCADE', 'SET NULL', 'fk_notificacao_usuario_notificacao');
        $this->forge->createTable('notificacao', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('notificacao');
    }
}
