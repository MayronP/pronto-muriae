<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MetaHistoricaAtendimento extends Migration
{
    public function up()
    {
        $this->forge->addColumn('atendimento', [
            'tempo_alvo_inicio' => ['type' => 'INT', 'null' => true],
        ]);
        // Mantém a meta vazia nos atendimentos antigos.
    }

    public function down()
    {
        $this->forge->dropColumn('atendimento', 'tempo_alvo_inicio');
    }
}
