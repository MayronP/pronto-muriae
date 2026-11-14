<?php

/**
 * formatValor
 *
 * @param float $valor
 * @param int $decimais
 * @return string
 */
function formatValor($valor, $decimais = 2)
{
    return number_format((float) $valor, $decimais, ",", ".");
}

/**
 * formatData - Converte uma data do banco (Y-m-d) para d/m/Y
 *
 * @param string|null $data
 * @return string
 */
function formatData($data)
{
    if (empty($data)) {
        return "—";
    }

    return date("d/m/Y", strtotime($data));
}

/**
 * formatDataHora - Converte data/hora do banco (Y-m-d H:i:s) para d/m/Y H:i
 *
 * @param string|null $dataHora
 * @return string
 */
function formatDataHora($dataHora)
{
    if (empty($dataHora)) {
        return "—";
    }

    return date("d/m/Y H:i", strtotime($dataHora));
}

/**
 * formatHora - Retorna somente o horário (H:i) de uma data/hora do banco
 *
 * @param string|null $dataHora
 * @return string
 */
function formatHora($dataHora)
{
    if (empty($dataHora)) {
        return "—";
    }

    return date("H:i", strtotime($dataHora));
}

/**
 * calculaIdade - Idade em anos a partir da data de nascimento
 *
 * @param string|null $dataNascimento
 * @return string
 */
function calculaIdade($dataNascimento)
{
    if (empty($dataNascimento)) {
        return "—";
    }

    $idade = idadeEmAnos($dataNascimento);

    return $idade . ($idade == 1 ? " ano" : " anos");
}

/**
 * tempoDecorrido - Tempo entre duas datas (ou até agora), ex.: "08 min" ou "1h 16min"
 *
 * @param string $inicio
 * @param string|null $fim
 * @return string
 */
function tempoDecorrido($inicio, $fim = null)
{
    if (empty($inicio)) {
        return "—";
    }

    return formataMinutos(minutosEntre($inicio, $fim));
}

/**
 * minutosEntre - Minutos inteiros entre duas datas do banco (ou até agora)
 *
 * @param string|null $inicio
 * @param string|null $fim
 * @return int
 */
function minutosEntre($inicio, $fim = null)
{
    if (empty($inicio)) {
        return 0;
    }

    $fim = empty($fim) ? time() : strtotime($fim);

    return max(0, intdiv($fim - strtotime($inicio), 60));
}

/**
 * formataMinutos - Minutos no formato "08 min" ou "1h 16min"
 *
 * @param int|float|string|null $minutos
 * @return string
 */
function formataMinutos($minutos)
{
    if ($minutos === null || $minutos === "") {
        return "—";
    }

    $minutos = (int) round((float) $minutos);
    $horas   = intdiv($minutos, 60);
    $resto   = $minutos % 60;

    if ($horas > 0) {
        return $horas . "h " . str_pad($resto, 2, "0", STR_PAD_LEFT) . "min";
    }

    return str_pad($resto, 2, "0", STR_PAD_LEFT) . " min";
}

/**
 * formataPercentual - Parte sobre o total em porcentagem inteira, ex.: "85%"
 *
 * @param int|string|null $parte
 * @param int|string|null $total
 * @return string
 */
function formataPercentual($parte, $total)
{
    if (empty($total)) {
        return "—";
    }

    return round((float) $parte * 100 / (float) $total) . "%";
}

/**
 * situacaoMeta - Espera pelo médico comparada ao tempo-alvo da cor
 *
 * @param string $referencia
 * @param int $tempoAlvo
 * @return array
 */
function situacaoMeta($referencia, $tempoAlvo)
{
    $espera = minutosEntre($referencia);

    return [
        "espera"   => $espera,
        "restante" => (int) $tempoAlvo - $espera,
        "atrasado" => $espera > (int) $tempoAlvo,
    ];
}

/**
 * situacaoPermanencia - Tempo desde a chegada comparado ao limite de permanência
 *
 * @param string $chegada
 * @return array
 */
function situacaoPermanencia($chegada)
{
    $minutos = minutosEntre($chegada);
    $limite  = PERMANENCIA_MAXIMA_HORAS * 60;
    $nivel   = "normal";

    if ($minutos >= $limite) {
        $nivel = "excedido";
    } elseif ($minutos >= $limite - PERMANENCIA_AVISO_HORAS * 60) {
        $nivel = "proximo";
    }

    return [
        "minutos"  => $minutos,
        "restante" => $limite - $minutos,
        "nivel"    => $nivel,
    ];
}

/**
 * calculaNews2 - Calcula o escore NEWS2 pelos sinais vitais
 *
 * @param array $sinais
 * @param string|null $dataNascimento
 * @param string|null $dataReferencia
 * @return array
 */
function calculaNews2(array $sinais, $dataNascimento = null, $dataReferencia = null)
{
    if (!isset($sinais["nivel_consciencia"], $sinais["oxigenio_suplementar"], $sinais["gestante"])) {
        return ["escore" => null, "risco" => "", "classe" => "", "motivo" => "triagem sem os dados do escore"];
    }

    if ($sinais["gestante"] == 1) {
        return ["escore" => null, "risco" => "", "classe" => "", "motivo" => "não se aplica a gestantes"];
    }

    if (!empty($dataNascimento) && idadeEmAnos($dataNascimento, $dataReferencia) < 16) {
        return ["escore" => null, "risco" => "", "classe" => "", "motivo" => "não se aplica a menores de 16 anos"];
    }

    $pontos = [
        pontuaFaixaNews2((float) $sinais["frequencia_respiratoria"], [[8, 3], [11, 1], [20, 0], [24, 2], [INF, 3]]),
        pontuaFaixaNews2((float) $sinais["saturacao"], [[91, 3], [93, 2], [95, 1], [INF, 0]]),
        $sinais["oxigenio_suplementar"] == 1 ? 2 : 0,
        pontuaFaixaNews2((float) strtok((string) $sinais["pressao_arterial"], "/"), [[90, 3], [100, 2], [110, 1], [219, 0], [INF, 3]]),
        pontuaFaixaNews2((float) $sinais["frequencia_cardiaca"], [[40, 3], [50, 1], [90, 0], [110, 1], [130, 2], [INF, 3]]),
        $sinais["nivel_consciencia"] == "A" ? 0 : 3,
        pontuaFaixaNews2((float) $sinais["temperatura"], [[35.0, 3], [36.0, 1], [38.0, 0], [39.0, 1], [INF, 2]]),
    ];

    $escore = array_sum($pontos);

    if ($escore >= 7) {
        return ["escore" => $escore, "risco" => "Alto", "classe" => "news2-alto", "motivo" => ""];
    }

    if ($escore >= 5) {
        return ["escore" => $escore, "risco" => "Médio", "classe" => "news2-medio", "motivo" => ""];
    }

    if (in_array(3, $pontos, true)) {
        return ["escore" => $escore, "risco" => "Baixo-médio", "classe" => "news2-baixo-medio", "motivo" => ""];
    }

    return ["escore" => $escore, "risco" => "Baixo", "classe" => "news2-baixo", "motivo" => ""];
}

/**
 * mensagemNews2 - Escore NEWS2 para a mensagem de sucesso (ou vazio sem escore)
 *
 * @param array $news2
 * @return string
 */
function mensagemNews2(array $news2)
{
    if ($news2["escore"] === null) {
        return "";
    }

    $mensagem = " NEWS2 " . $news2["escore"] . " (risco " . mb_strtolower($news2["risco"]) . ").";

    if ($news2["escore"] >= 5) {
        $mensagem .= " Avise o médico.";
    }

    return $mensagem;
}

/**
 * pontuaFaixaNews2 - Pontos da primeira faixa cujo limite superior alcança o valor
 *
 * @param float $valor
 * @param array $faixas
 * @return int
 */
function pontuaFaixaNews2($valor, array $faixas)
{
    foreach ($faixas as $faixa) {
        if ($valor <= $faixa[0]) {
            return $faixa[1];
        }
    }

    return end($faixas)[1];
}

/**
 * formataCpf - Deixa o CPF no formato 000.000.000-00
 *
 * @param string|null $cpf
 * @return string
 */
function formataCpf($cpf)
{
    $numeros = preg_replace("/\D/", "", (string) $cpf);

    if (strlen($numeros) != 11) {
        return (string) $cpf;
    }

    return substr($numeros, 0, 3) . "." . substr($numeros, 3, 3) . "." . substr($numeros, 6, 3) . "-" . substr($numeros, 9, 2);
}

/**
 * getIniciais - Iniciais do nome, ex.: "Mariana Souza" => "MS"
 *
 * @param string|null $nome
 * @return string
 */
function getIniciais($nome)
{
    $partes = [];

    foreach (explode(" ", trim((string) $nome)) as $parte) {
        if (preg_match('/^\p{L}/u', $parte)) {
            $partes[] = $parte;
        }
    }

    if (empty($partes)) {
        return "";
    }

    $texto = mb_substr($partes[0], 0, 1);

    if (count($partes) > 1) {
        $texto .= mb_substr(end($partes), 0, 1);
    }

    return mb_strtoupper($texto);
}

/**
 * dataAtualPorExtenso - Data de hoje com o dia da semana, ex.: "quinta-feira, 01/10/2026"
 *
 * @return string
 */
function dataAtualPorExtenso()
{
    $dias = ["domingo", "segunda-feira", "terça-feira", "quarta-feira", "quinta-feira", "sexta-feira", "sábado"];

    return $dias[(int) date("w")] . ", " . date("d/m/Y");
}

/**
 * getDescricaoSexo - Descrição do sexo gravado no cadastro
 *
 * @param string|null $sexo
 * @return string
 */
function getDescricaoSexo($sexo)
{
    $descricoes = [
        "F" => "Feminino",
        "M" => "Masculino",
        "O" => "Outro",
    ];

    return $descricoes[$sexo] ?? "";
}

/**
 * idadeEmAnos - Idade em anos completos (hoje ou na data de referência)
 *
 * @param string|null $dataNascimento
 * @param string|null $dataReferencia
 * @return int|null
 */
function idadeEmAnos($dataNascimento, $dataReferencia = null)
{
    if (empty($dataNascimento)) {
        return null;
    }

    return date_diff(date_create($dataNascimento), date_create($dataReferencia ?: "today"))->y;
}

/**
 * normalizaCid - Código da CID-10 como gravado no banco, ex.: "i21.9" => "I219"
 *
 * @param string|null $codigo
 * @return string
 */
function normalizaCid($codigo)
{
    return strtoupper(preg_replace("/[^A-Za-z0-9]/", "", (string) $codigo));
}

/**
 * formataCid - Código da CID-10 com o ponto, ex.: "I219" => "I21.9"
 *
 * @param string|null $codigo
 * @return string
 */
function formataCid($codigo)
{
    $codigo = normalizaCid($codigo);

    if (strlen($codigo) == 4) {
        return substr($codigo, 0, 3) . "." . substr($codigo, 3);
    }

    return $codigo;
}

/**
 * normalizaTexto - Texto em minúsculas e sem acentos, para comparações
 *
 * @param string|null $texto
 * @return string
 */
function normalizaTexto($texto)
{
    $acentos = [
        "á" => "a", "à" => "a", "â" => "a", "ã" => "a", "ä" => "a",
        "é" => "e", "è" => "e", "ê" => "e", "ë" => "e",
        "í" => "i", "ì" => "i", "î" => "i", "ï" => "i",
        "ó" => "o", "ò" => "o", "ô" => "o", "õ" => "o", "ö" => "o",
        "ú" => "u", "ù" => "u", "û" => "u", "ü" => "u",
        "ç" => "c", "ñ" => "n",
    ];

    return trim(preg_replace("/\s+/", " ", strtr(mb_strtolower((string) $texto), $acentos)));
}

/**
 * alergiaDoMedicamento - Alergia que coincide com o nome do medicamento (não reconhece classes)
 *
 * @param string $medicamento
 * @param string|null $alergias
 * @return string
 */
function alergiaDoMedicamento($medicamento, $alergias)
{
    $medicamento = normalizaTexto($medicamento);

    foreach (preg_split("/[,;\/()]| e /", normalizaTexto(str_replace(["\r", "\n"], ",", (string) $alergias))) as $termo) {
        $termo = trim($termo, " .-");

        if (mb_strlen($termo) < 3) {
            continue;
        }

        if (preg_match("/\b" . preg_quote($termo, "/") . "\b/", $medicamento) || preg_match("/\b" . preg_quote($medicamento, "/") . "\b/", $termo)) {
            return $termo;
        }
    }

    return "";
}
