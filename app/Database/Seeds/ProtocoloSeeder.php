<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProtocoloSeeder extends Seeder
{
    public function run()
    {
        // O marco_inicial define de onde contam as metas do protocolo
        $aProtocolos = [
            ['id_protocolo' => 1, 'nome' => 'Dor torácica', 'marco_inicial' => 'chegada', 'janela_minutos' => null, 'fonte' => 'HC-UFTM/Ebserh, Abordagem da dor torácica na unidade de urgência e emergência (PRT.HCUFTMUUE.001)'],
            ['id_protocolo' => 2, 'nome' => 'AVC', 'marco_inicial' => 'chegada', 'janela_minutos' => 270, 'fonte' => 'SESA-ES, Diretriz Estadual do AVC (2025); Ministério da Saúde, Linha de cuidado do AVC no adulto'],
            ['id_protocolo' => 3, 'nome' => 'Sepse', 'marco_inicial' => 'abertura', 'janela_minutos' => null, 'fonte' => 'Instituto Latino Americano de Sepse (ILAS), Protocolo de tratamento, revisão de 2018 (pacote de 1 hora)'],
        ];

        $tbProtocolo = $this->db->table('protocolo');

        foreach ($aProtocolos as $protocolo) {

            if ($tbProtocolo->where('id_protocolo', $protocolo['id_protocolo'])->countAllResults() == 0) {
                $tbProtocolo->insert($protocolo);
            }
        }
    }
}
