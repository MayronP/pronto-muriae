<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Executa os seeders na ordem das dependências.
        $this->call('PerfilSeeder');
        $this->call('UsuarioSeeder');
        $this->call('ClassificacaoRiscoSeeder');
        $this->call('ProtocoloSeeder');
        $this->call('ProtocoloEtapaSeeder');
        $this->call('Cid10Seeder');
    }
}
