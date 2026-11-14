<?php

namespace App\Controllers;

use App\Models\AtendimentoModel;
use App\Models\Cid10Model;
use App\Models\DocumentoModel;
use App\Models\ExameModel;
use App\Models\PrescricaoMedicamentoModel;

class Documento extends BaseController
{
    private $AtendimentoModel;
    private $DocumentoModel;
    private $Cid10Model;
    private $ExameModel;
    private $PrescricaoMedicamentoModel;

    public function __construct()
    {
        $this->AtendimentoModel           = new AtendimentoModel();
        $this->DocumentoModel             = new DocumentoModel();
        $this->Cid10Model                 = new Cid10Model();
        $this->ExameModel                 = new ExameModel();
        $this->PrescricaoMedicamentoModel = new PrescricaoMedicamentoModel();
    }

    /**
     * Lista os documentos emitidos no atendimento.
     *
     * @param int $idAtendimento
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function index($idAtendimento)
    {
        $atendimento = $this->AtendimentoModel->getCompleto($idAtendimento);

        if (empty($atendimento)) {
            session()->setFlashdata("msgError", "Atendimento não encontrado.");

            return redirect()->to("fila");
        }

        $dados = [
            "tipo"             => (session()->get("usuarioPerfilId") == PERFIL_MEDICO) ? "receita" : "comparecimento",
            "dias_afastamento" => 1,
        ];

        return $this->exibePagina($atendimento, $dados, []);
    }

    /**
     * Emite o documento e abre a versão para impressão.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function store()
    {
        $idAtendimento = (int) $this->request->getPost("id_atendimento");
        $atendimento   = $this->AtendimentoModel->getCompleto($idAtendimento);

        if (empty($atendimento)) {
            session()->setFlashdata("msgError", "Atendimento não encontrado.");

            return redirect()->to("fila");
        }

        $dados = $this->request->getPost(["tipo", "receita", "dias_afastamento", "cid", "autoriza_cid", "observacao_atestado", "observacao_comparecimento", "destino", "motivo_encaminhamento"]);

        if ($dados["tipo"] != "comparecimento" && session()->get("usuarioPerfilId") != PERFIL_MEDICO) {
            return $this->exibePagina($atendimento, $dados, ["tipo" => "Receita, atestado e encaminhamento são emitidos pelo médico."]);
        }

        $regras = [
            "tipo" => [
                "label" => "Documento",
                "rules" => "required|in_list[" . implode(",", array_keys(getTiposDocumento())) . "]"
            ]
        ];

        if ($dados["tipo"] == "receita") {
            $regras["receita"] = [
                "label" => "Medicamentos e orientações",
                "rules" => "required|min_length[3]"
            ];
        }

        if ($dados["tipo"] == "atestado") {
            $regras["dias_afastamento"] = [
                "label" => "Dias de afastamento",
                "rules" => "required|is_natural_no_zero|less_than_equal_to[60]"
            ];

            if (trim((string) $dados["cid"]) != "") {
                $regras["autoriza_cid"] = [
                    "label"  => "Autorização do paciente",
                    "rules"  => "required|in_list[1]",
                    "errors" => [
                        "required" => "O CID só entra no atestado com a autorização do paciente."
                    ]
                ];
            }
        }

        if ($dados["tipo"] == "encaminhamento") {
            $regras["destino"] = [
                "label" => "Encaminhado para",
                "rules" => "required|max_length[150]"
            ];
            $regras["motivo_encaminhamento"] = [
                "label" => "Motivo e resumo clínico",
                "rules" => "required|min_length[3]"
            ];
        }

        if (!$this->validate($regras)) {
            return $this->exibePagina($atendimento, $dados, $this->validator->getErrors());
        }

        $conteudos = [
            "receita"        => $dados["receita"],
            "atestado"       => $dados["observacao_atestado"],
            "comparecimento" => $dados["observacao_comparecimento"],
            "encaminhamento" => $dados["motivo_encaminhamento"],
        ];
        $conteudo = trim((string) $conteudos[$dados["tipo"]]);

        $cid = [];

        if ($dados["tipo"] == "atestado" && trim((string) $dados["cid"]) != "") {
            $cid = $this->Cid10Model->getByCodigo($dados["cid"]);

            if (empty($cid)) {
                return $this->exibePagina($atendimento, $dados, ["cid" => "Código não encontrado na CID-10."]);
            }
        }

        $documento = [
            "id_atendimento"   => $idAtendimento,
            "id_usuario"       => session()->get("usuarioId"),
            "tipo"             => $dados["tipo"],
            "conteudo"         => $conteudo !== "" ? $conteudo : null,
            "dias_afastamento" => $dados["tipo"] == "atestado" ? (int) $dados["dias_afastamento"] : null,
            "cid"              => $cid["codigo"] ?? null,
            "destino"          => $dados["tipo"] == "encaminhamento" ? trim($dados["destino"]) : null,
            "data_hora"        => date("Y-m-d H:i:s"),
        ];

        if (!$this->DocumentoModel->save($documento)) {
            return $this->exibePagina($atendimento, $dados, $this->DocumentoModel->errors());
        }

        $id = $this->DocumentoModel->getInsertID();

        $this->gravaLog("Documento emitido", "documento", $id, getTiposDocumento()[$dados["tipo"]] . " - atendimento " . $atendimento["numero"]);
        session()->setFlashdata("msgSucesso", getTiposDocumento()[$dados["tipo"]] . " emitido(a). Confira e imprima.");

        return redirect()->to("documentos/imprimir/" . $id);
    }

    /**
     * Exibe a versão para impressão do documento.
     *
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function imprimir($id)
    {
        $documento = $this->DocumentoModel->getCompleto($id);

        if (empty($documento)) {
            session()->setFlashdata("msgError", "Documento não encontrado.");

            return redirect()->to("dashboard");
        }

        $titulo = getTiposDocumento()[$documento["tipo"]];
        $voltar = "documentos/index/" . $documento["id_atendimento"];

        return view("documentos/imprimir", compact("titulo", "voltar", "documento"));
    }

    /**
     * Exibe o sumário do atendimento encerrado para impressão.
     *
     * @param int $idAtendimento
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function sumario($idAtendimento)
    {
        $atendimento = $this->AtendimentoModel->getCompleto($idAtendimento);

        if (empty($atendimento) || $atendimento["status"] != "encerrado") {
            session()->setFlashdata("msgError", "O sumário fica disponível depois do encerramento do atendimento.");

            return redirect()->to(empty($atendimento) ? "fila" : "atendimentos/detalhes/" . $idAtendimento);
        }

        $titulo       = "Sumário do atendimento";
        $voltar       = "atendimentos/detalhes/" . $idAtendimento;
        $exames       = $this->ExameModel->listaPorAtendimento($idAtendimento);
        $medicamentos = $this->PrescricaoMedicamentoModel->listaPorAtendimento($idAtendimento);

        return view("documentos/sumario", compact("titulo", "voltar", "atendimento", "exames", "medicamentos"));
    }

    /**
     * Monta a tela de documentos do atendimento.
     *
     * @param array $atendimento
     * @param array $dados
     * @param array $erros
     * @return string
     */
    private function exibePagina($atendimento, $dados, $erros)
    {
        $titulo      = "Documentos";
        $subtitulo   = "Receita, atestado, declaração de comparecimento e encaminhamento.";
        $paginaAtiva = ($atendimento["status"] == "em_observacao") ? "observacao" : "atendimento";
        $documentos  = $this->DocumentoModel->listaPorAtendimento($atendimento["id_atendimento"]);
        $tipos       = (session()->get("usuarioPerfilId") == PERFIL_MEDICO) ? getTiposDocumento() : ["comparecimento" => getTiposDocumento()["comparecimento"]];
        $cid         = $this->Cid10Model->getByCodigo($dados["cid"] ?? "");

        return view("documentos/index", compact("titulo", "subtitulo", "paginaAtiva", "atendimento", "documentos", "tipos", "cid", "dados", "erros"));
    }
}
