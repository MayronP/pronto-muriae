<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsuarioSeeder extends Seeder
{
    public function run()
    {
        // Senha inicial dos três usuários: pronto@123
        $aUsuarios = [
            ['perfil' => 'Administrador', 'nome' => 'Administrador do Sistema', 'login' => 'admin', 'email' => 'admin@muriaepronto.local'],
            ['perfil' => 'Enfermagem', 'nome' => 'Enfermagem Plantão', 'login' => 'enfermagem', 'email' => 'enfermagem@muriaepronto.local'],
            ['perfil' => 'Médico', 'nome' => 'Médico Plantão', 'login' => 'medico', 'email' => 'medico@muriaepronto.local'],
        ];

        foreach ($aUsuarios as $usuario) {

            // Buscar id do perfil
            $aPerfil = $this->db
                    ->table('perfil')
                    ->where('nome', $usuario['perfil'])
                    ->get()
                    ->getRowArray();

            if (is_null($aPerfil)) {
                throw new \RuntimeException("Perfil {$usuario['perfil']} não encontrado. Execute o PerfilSeeder antes.");
            }

            $tbUsuario = $this->db->table('usuario');

            if ($tbUsuario->where('login', $usuario['login'])->countAllResults() == 0) {

                $tbUsuario->insert([
                    'id_perfil'  => $aPerfil['id_perfil'],
                    'nome'       => $usuario['nome'],
                    'login'      => $usuario['login'],
                    'email'      => $usuario['email'],
                    'senha_hash' => password_hash('pronto@123', PASSWORD_DEFAULT)
                ]);
            }
        }
    }
}
