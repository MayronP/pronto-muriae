<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ProtocoloEtapa extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_etapa' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'id_protocolo' => [
                'type' => 'INT',
            ],
            'descricao' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
            ],
            'meta_minutos' => [
                'type' => 'SMALLINT',
            ],
            'ordem' => [
                'type' => 'TINYINT',
            ],
        ]);

        $this->forge->addKey('id_etapa', true);
        $this->forge->addKey('id_protocolo', false, false, 'idx_etapa_protocolo');
        $this->forge->addForeignKey('id_protocolo', 'protocolo', 'id_protocolo', 'CASCADE', 'CASCADE', 'fk_etapa_protocolo');
        $this->forge->createTable('protocolo_etapa', false, ['engine' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('protocolo_etapa');
    }
}
