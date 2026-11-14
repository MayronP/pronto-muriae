<?php

namespace App\Validation;

class DocumentoRules
{
    public function cpf_valido($valor, ?string &$error = null): bool
    {
        $cpf = preg_replace('/\D/', '', (string) $valor);
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            $error = 'Informe um CPF com dígitos verificadores válidos.';
            return false;
        }

        for ($posicao = 9; $posicao <= 10; $posicao++) {
            $soma = 0;
            for ($i = 0; $i < $posicao; $i++) {
                $soma += (int) $cpf[$i] * ($posicao + 1 - $i);
            }
            $digito = ($soma * 10) % 11;
            if (($digito === 10 ? 0 : $digito) !== (int) $cpf[$posicao]) {
                $error = 'Informe um CPF com dígitos verificadores válidos.';
                return false;
            }
        }

        return true;
    }
}
