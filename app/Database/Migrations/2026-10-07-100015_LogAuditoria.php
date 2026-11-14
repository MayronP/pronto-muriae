<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class LogAuditoria extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_log' => [
                'type'           => 'BIGINT',
                'auto_increment' => true,
            ],
            'id_usuario' => [
                'type' => 'INT',
            ],
            'acao' => [
                'type'       => 'VARCHAR',
                'constraint' => 180,
            ],
            'entidade' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'registro_id' => [
                'type' => 'BIGINT',
                'null' => true,
            ],
            'data_hora' => [
                'type'    => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'detalhes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_log', true);
        $this->forge->addKey('data_hora', false, false, 'idx_log_data_hora');
        $this->forge->addKey(['entidade', 'registro_id'], false, false, 'idx_log_entidade_registro');
        $this->forge->addKey('id_usuario', false, false, 'idx_log_usuario');
        $this->forge->addForeignKey('id_usuario', 'usuario', 'id_usuario', 'CASCADE', 'RESTRICT', 'fk_log_usuario');
        $this->forge->createTable('log_auditoria', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('log_auditoria');
    }
}
