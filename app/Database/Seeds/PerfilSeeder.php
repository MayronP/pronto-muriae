<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PerfilSeeder extends Seeder
{
    public function run()
    {
        // Os ids são usados nas constantes PERFIL_* (app/Config/Constants.php)
        $aPerfis = [
            ['id_perfil' => 1, 'nome' => 'Administrador', 'descricao' => 'Usuários, perfis, indicadores, relatórios e auditoria'],
            ['id_perfil' => 2, 'nome' => 'Enfermagem', 'descricao' => 'Admissão, triagem, sinais vitais e classificação de risco'],
            ['id_perfil' => 3, 'nome' => 'Médico', 'descricao' => 'Atendimento clínico, evolução, exames, prescrição e desfecho'],
        ];

        $tbPerfil = $this->db->table('perfil');

        foreach ($aPerfis as $perfil) {

            if ($tbPerfil->where('id_perfil', $perfil['id_perfil'])->countAllResults() == 0) {
                $tbPerfil->insert($perfil);
            }
        }
    }
}
