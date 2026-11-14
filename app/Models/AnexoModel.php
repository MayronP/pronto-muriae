<?php

namespace App\Models;

class AnexoModel extends BaseModel
{
    protected $table = "anexo";
    protected $primaryKey = "id_anexo";
    protected $allowedFields = [
        "id_atendimento",
        "id_exame",
        "id_usuario",
        "nome_original",
        "caminho_arquivo",
        "tipo_arquivo",
        "tamanho"
    ];

    protected $useTimestamps = true;
    protected $createdField = "data_hora";
    protected $updatedField = "";

    protected $validationRules = [
        "id_atendimento" => [
            "label" => "Atendimento",
            "rules" => "required|is_natural_no_zero"
        ],
        "id_usuario" => [
            "label" => "Usuário",
            "rules" => "required|is_natural_no_zero"
        ],
        "nome_original" => [
            "label" => "Nome do arquivo",
            "rules" => "required|max_length[255]"
        ],
        "caminho_arquivo" => [
            "label" => "Arquivo",
            "rules" => "required|max_length[500]"
        ]
    ];

    /**
     * Move o arquivo para fora da pasta pública e grava o anexo.
     *
     * @param \CodeIgniter\HTTP\Files\UploadedFile $arquivo
     * @param int $idAtendimento
     * @param int|null $idExame
     * @param int $idUsuario
     * @return int
     */
    public function gravaArquivo($arquivo, int $idAtendimento, $idExame, int $idUsuario): int
    {
        $nomeArquivo = $arquivo->getRandomName();

        $dados = [
            "id_atendimento"  => $idAtendimento,
            "id_exame"        => $idExame,
            "id_usuario"      => $idUsuario,
            "nome_original"   => $arquivo->getClientName(),
            "caminho_arquivo" => "anexos/" . $nomeArquivo,
            "tipo_arquivo"    => $arquivo->getMimeType(),
            "tamanho"         => $arquivo->getSize(),
        ];

        $arquivo->move(WRITEPATH . "uploads/anexos", $nomeArquivo);

        return $this->insert($dados);
    }

    /**
     * Anexos do atendimento (inclusive os ligados a exames).
     *
     * @param int $idAtendimento
     * @return array
     */
    public function listaPorAtendimento(int $idAtendimento): array
    {
        return $this->select("anexo.*, usuario.nome AS usuario, exame.descricao AS exame")
            ->join("usuario", "usuario.id_usuario = anexo.id_usuario")
            ->join("exame", "exame.id_exame = anexo.id_exame", "left")
            ->where("anexo.id_atendimento", $idAtendimento)
            ->orderBy("anexo.data_hora")
            ->findAll();
    }
}
