<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class Anexo extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_anexo' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'id_atendimento' => [
                'type' => 'INT',
            ],
            'id_exame' => [
                'type' => 'INT',
                'null' => true,
            ],
            'id_usuario' => [
                'type' => 'INT',
            ],
            'nome_original' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'caminho_arquivo' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
            ],
            'tipo_arquivo' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
            ],
            'tamanho' => [
                'type' => 'BIGINT',
                'null' => true,
            ],
            'data_hora' => [
                'type'    => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('id_anexo', true);
        $this->forge->addKey('id_atendimento', false, false, 'idx_anexo_atendimento');
        $this->forge->addKey('id_exame', false, false, 'idx_anexo_exame');
        $this->forge->addKey('id_usuario', false, false, 'idx_anexo_usuario');
        $this->forge->addForeignKey('id_atendimento', 'atendimento', 'id_atendimento', 'CASCADE', 'CASCADE', 'fk_anexo_atendimento');
        $this->forge->addForeignKey('id_exame', 'exame', 'id_exame', 'CASCADE', 'CASCADE', 'fk_anexo_exame');
        $this->forge->addForeignKey('id_usuario', 'usuario', 'id_usuario', 'CASCADE', 'RESTRICT', 'fk_anexo_usuario');
        $this->forge->createTable('anexo', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('anexo');
    }
}
