<?php

namespace App\Controllers;

use App\Models\Cid10Model;

class Cid10 extends BaseController
{
    private $Cid10Model;

    public function __construct()
    {
        $this->Cid10Model = new Cid10Model();
    }

    /**
     * Busca na CID-10 pelo código ou pela descrição.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function buscar()
    {
        $termo     = trim((string) $this->request->getGet("termo"));
        $resultado = [];

        if (mb_strlen($termo) >= 2) {
            foreach ($this->Cid10Model->busca($termo) as $cid) {
                $resultado[] = [
                    "codigo"    => formataCid($cid["codigo"]),
                    "descricao" => $cid["descricao"],
                ];
            }
        }

        return $this->response->setJSON($resultado);
    }
}
