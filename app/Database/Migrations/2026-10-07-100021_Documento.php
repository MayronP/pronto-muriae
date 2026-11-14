<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Documento extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_documento' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'id_atendimento' => [
                'type' => 'INT',
            ],
            'id_usuario' => [
                'type' => 'INT',
            ],
            'tipo' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'conteudo' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'dias_afastamento' => [
                'type' => 'TINYINT',
                'null' => true,
            ],
            'cid' => [
                'type'       => 'VARCHAR',
                'constraint' => 4,
                'null'       => true,
            ],
            'destino' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'data_hora' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('id_documento', true);
        $this->forge->addKey('id_atendimento', false, false, 'idx_documento_atendimento');
        $this->forge->addKey('cid', false, false, 'idx_documento_cid');
        $this->forge->addKey('id_usuario', false, false, 'idx_documento_usuario');
        $this->forge->addForeignKey('id_atendimento', 'atendimento', 'id_atendimento', 'CASCADE', 'CASCADE', 'fk_documento_atendimento');
        $this->forge->addForeignKey('cid', 'cid10', 'codigo', 'CASCADE', 'RESTRICT', 'fk_documento_cid');
        $this->forge->addForeignKey('id_usuario', 'usuario', 'id_usuario', 'CASCADE', 'RESTRICT', 'fk_documento_usuario');
        $this->forge->createTable('documento', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('documento');
    }
}
