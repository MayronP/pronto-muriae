<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class Usuario extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_usuario' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'id_perfil' => [
                'type' => 'INT',
            ],
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'login' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'registro_profissional' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'senha_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'ativo' => [
                'type'    => 'TINYINT',
                'default' => 1,
            ],
            'data_cadastro' => [
                'type'    => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('id_usuario', true);
        $this->forge->addUniqueKey('email', 'uk_usuario_email');
        $this->forge->addUniqueKey('login', 'uk_usuario_login');
        $this->forge->addKey('id_perfil', false, false, 'idx_usuario_perfil');
        $this->forge->addForeignKey('id_perfil', 'perfil', 'id_perfil', 'CASCADE', 'RESTRICT', 'fk_usuario_perfil');
        $this->forge->createTable('usuario', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('usuario');
    }
}
