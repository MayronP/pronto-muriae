<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Administracao extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_administracao' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'id_item_prescricao' => [
                'type' => 'INT',
            ],
            'data_hora_prevista' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'situacao' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'id_usuario' => [
                'type' => 'INT',
                'null' => true,
            ],
            'data_hora' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'motivo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id_administracao', true);
        $this->forge->addKey('id_item_prescricao', false, false, 'idx_administracao_item');
        $this->forge->addKey('data_hora_prevista', false, false, 'idx_administracao_prevista');
        $this->forge->addKey('id_usuario', false, false, 'idx_administracao_usuario');
        $this->forge->addForeignKey('id_item_prescricao', 'prescricao_medicamento', 'id_item_prescricao', 'CASCADE', 'CASCADE', 'fk_administracao_item');
        $this->forge->addForeignKey('id_usuario', 'usuario', 'id_usuario', 'CASCADE', 'SET NULL', 'fk_administracao_usuario');
        $this->forge->createTable('administracao', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('administracao');
    }
}
