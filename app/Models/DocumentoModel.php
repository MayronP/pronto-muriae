<?php

namespace App\Models;

class DocumentoModel extends BaseModel
{
    protected $table = "documento";
    protected $primaryKey = "id_documento";
    protected $allowedFields = [
        "id_atendimento",
        "id_usuario",
        "tipo",
        "conteudo",
        "dias_afastamento",
        "cid",
        "destino",
        "data_hora"
    ];

    protected $validationRules = [
        "id_atendimento" => [
            "label" => "Atendimento",
            "rules" => "required|is_natural_no_zero"
        ],
        "id_usuario" => [
            "label" => "Responsável",
            "rules" => "required|is_natural_no_zero"
        ],
        "tipo" => [
            "label" => "Documento",
            "rules" => "required|in_list[receita,atestado,comparecimento,encaminhamento]"
        ],
        "dias_afastamento" => [
            "label" => "Dias de afastamento",
            "rules" => "permit_empty|is_natural_no_zero|less_than_equal_to[60]"
        ],
        "cid" => [
            "label" => "CID-10",
            "rules" => "permit_empty|is_not_unique[cid10.codigo]"
        ],
        "destino" => [
            "label" => "Encaminhado para",
            "rules" => "permit_empty|max_length[150]"
        ],
        "data_hora" => [
            "label" => "Data e horário",
            "rules" => "required|valid_date[Y-m-d H:i:s]"
        ]
    ];

    protected $validationMessages = [
        "cid" => [
            "is_not_unique" => "Código não encontrado na CID-10."
        ]
    ];

    /**
     * Documentos emitidos no atendimento (mais recente primeiro).
     *
     * @param int $idAtendimento
     * @return array
     */
    public function listaPorAtendimento(int $idAtendimento): array
    {
        return $this->select("documento.*, usuario.nome AS usuario")
            ->join("usuario", "usuario.id_usuario = documento.id_usuario")
            ->where("documento.id_atendimento", $idAtendimento)
            ->orderBy("documento.data_hora", "DESC")
            ->orderBy("documento.id_documento", "DESC")
            ->findAll();
    }

    /**
     * Documento com o paciente, o atendimento, a CID e quem emitiu.
     *
     * @param int $id
     * @return array
     */
    public function getCompleto(int $id): array
    {
        return $this->select("documento.*, cid10.descricao AS cid_descricao, usuario.nome AS usuario, usuario.registro_profissional, perfil.nome AS perfil")
            ->select("atendimento.numero, atendimento.data_hora_chegada, atendimento.data_hora_encerramento, atendimento.status")
            ->select("paciente.id_paciente, paciente.nome_completo, paciente.nome_social, paciente.cpf, paciente.cns, paciente.data_nascimento")
            ->join("usuario", "usuario.id_usuario = documento.id_usuario")
            ->join("perfil", "perfil.id_perfil = usuario.id_perfil")
            ->join("atendimento", "atendimento.id_atendimento = documento.id_atendimento")
            ->join("paciente", "paciente.id_paciente = atendimento.id_paciente")
            ->join("cid10", "cid10.codigo = documento.cid", "left")
            ->where("documento.id_documento", $id)
            ->first() ?? [];
    }
}
