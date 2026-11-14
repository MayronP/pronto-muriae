<?php

use App\Validation\DocumentoRules;
use CodeIgniter\Test\CIUnitTestCase;

final class DocumentoRulesTest extends CIUnitTestCase
{
    public function testCpfValidoFormatadoOuNumerico(): void
    {
        $regra = new DocumentoRules();
        $this->assertTrue($regra->cpf_valido('529.982.247-25'));
        $this->assertTrue($regra->cpf_valido('52998224725'));
    }

    public function testCpfInvalidoOuRepetido(): void
    {
        $regra = new DocumentoRules();
        foreach (['111.111.111-11', '000.000.000-00', '529.982.247-24', '123', ''] as $cpf) {
            $this->assertFalse($regra->cpf_valido($cpf));
        }
    }
}
