<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Atendimento extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_atendimento' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'id_paciente' => [
                'type' => 'INT',
            ],
            'id_usuario_admissao' => [
                'type' => 'INT',
            ],
            'id_medico' => [
                'type' => 'INT',
                'null' => true,
            ],
            'numero' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'data_hora_chegada' => [
                'type' => 'DATETIME',
            ],
            'data_hora_inicio_atendimento' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'id_classificacao_inicio' => [
                'type' => 'INT',
                'null' => true,
            ],
            'minutos_espera' => [
                'type' => 'INT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
            ],
            'observacao_inicial' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'data_hora_inicio_observacao' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'leito_observacao' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
                'null'       => true,
            ],
            'desfecho' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'cid_principal' => [
                'type'       => 'VARCHAR',
                'constraint' => 4,
                'null'       => true,
            ],
            'data_hora_obito' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'declaracao_obito' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'termo_alta_pedido' => [
                'type' => 'TINYINT',
                'null' => true,
            ],
            'resumo_final' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'observacoes_finais' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'data_hora_encerramento' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_atendimento', true);
        $this->forge->addUniqueKey('numero', 'uk_atendimento_numero');
        $this->forge->addKey('data_hora_chegada', false, false, 'idx_atendimento_chegada');
        $this->forge->addKey('cid_principal', false, false, 'idx_atendimento_cid');
        $this->forge->addKey('id_classificacao_inicio', false, false, 'idx_atendimento_classificacao_inicio');
        $this->forge->addKey('id_medico', false, false, 'idx_atendimento_medico');
        $this->forge->addKey('id_paciente', false, false, 'idx_atendimento_paciente');
        $this->forge->addKey('status', false, false, 'idx_atendimento_status');
        $this->forge->addKey('id_usuario_admissao', false, false, 'idx_atendimento_usuario_admissao');
        $this->forge->addForeignKey('cid_principal', 'cid10', 'codigo', 'CASCADE', 'RESTRICT', 'fk_atendimento_cid');
        $this->forge->addForeignKey('id_classificacao_inicio', 'classificacao_risco', 'id_classificacao', 'CASCADE', 'RESTRICT', 'fk_atendimento_classificacao_inicio');
        $this->forge->addForeignKey('id_medico', 'usuario', 'id_usuario', 'CASCADE', 'SET NULL', 'fk_atendimento_medico');
        $this->forge->addForeignKey('id_paciente', 'paciente', 'id_paciente', 'CASCADE', 'RESTRICT', 'fk_atendimento_paciente');
        $this->forge->addForeignKey('id_usuario_admissao', 'usuario', 'id_usuario', 'CASCADE', 'RESTRICT', 'fk_atendimento_usuario_admissao');
        $this->forge->createTable('atendimento', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('atendimento');
    }
}
