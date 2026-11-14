<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProtocoloEtapaSeeder extends Seeder
{
    public function run()
    {
        $aEtapas = [
            ['protocolo' => 'Dor torácica', 'descricao' => 'ECG realizado e interpretado', 'meta_minutos' => 10, 'ordem' => 1],
            ['protocolo' => 'Dor torácica', 'descricao' => 'Início da fibrinólise (se indicada)', 'meta_minutos' => 30, 'ordem' => 2],
            ['protocolo' => 'Dor torácica', 'descricao' => 'Angioplastia primária: passagem do fio-guia (se indicada)', 'meta_minutos' => 90, 'ordem' => 3],
            ['protocolo' => 'AVC', 'descricao' => 'Tomografia de crânio realizada', 'meta_minutos' => 25, 'ordem' => 1],
            ['protocolo' => 'AVC', 'descricao' => 'Início do trombolítico (se indicado)', 'meta_minutos' => 60, 'ordem' => 2],
            ['protocolo' => 'Sepse', 'descricao' => 'Coleta de lactato', 'meta_minutos' => 60, 'ordem' => 1],
            ['protocolo' => 'Sepse', 'descricao' => 'Coleta de hemoculturas (antes do antimicrobiano)', 'meta_minutos' => 60, 'ordem' => 2],
            ['protocolo' => 'Sepse', 'descricao' => 'Antimicrobiano endovenoso iniciado', 'meta_minutos' => 60, 'ordem' => 3],
        ];

        foreach ($aEtapas as $etapa) {

            // Buscar id do protocolo
            $aProtocolo = $this->db
                    ->table('protocolo')
                    ->where('nome', $etapa['protocolo'])
                    ->get()
                    ->getRowArray();

            if (is_null($aProtocolo)) {
                throw new \RuntimeException("Protocolo {$etapa['protocolo']} não encontrado. Execute o ProtocoloSeeder antes.");
            }

            $tbEtapa = $this->db->table('protocolo_etapa');

            if ($tbEtapa->where([
                'id_protocolo' => $aProtocolo['id_protocolo'],
                'ordem'        => $etapa['ordem']
                ])->countAllResults() == 0) {

                $tbEtapa->insert([
                    'id_protocolo' => $aProtocolo['id_protocolo'],
                    'descricao'    => $etapa['descricao'],
                    'meta_minutos' => $etapa['meta_minutos'],
                    'ordem'        => $etapa['ordem']
                ]);
            }
        }
    }
}
