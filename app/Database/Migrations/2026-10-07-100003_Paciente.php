<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Paciente extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_paciente' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'nome_completo' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'nome_social' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'cpf' => [
                'type'       => 'VARCHAR',
                'constraint' => 14,
                'null'       => true,
            ],
            'cns' => [
                'type'       => 'CHAR',
                'constraint' => 15,
                'null'       => true,
            ],
            'data_nascimento' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'nome_mae' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'sexo' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'telefone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'endereco' => [
                'type'       => 'VARCHAR',
                'constraint' => 180,
                'null'       => true,
            ],
            'numero' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'bairro' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'cidade' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'estado' => [
                'type'       => 'CHAR',
                'constraint' => 2,
                'null'       => true,
            ],
            'nome_contato' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'telefone_contato' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'alergias' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ],
            'nao_identificado' => [
                'type'    => 'TINYINT',
                'default' => 0,
            ],
            'caracteristicas' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id_paciente', true);
        $this->forge->addUniqueKey('cns', 'uk_paciente_cns');
        $this->forge->addUniqueKey('cpf', 'uk_paciente_cpf');
        $this->forge->addKey('nome_completo', false, false, 'idx_paciente_nome');
        $this->forge->createTable('paciente', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('paciente');
    }
}
