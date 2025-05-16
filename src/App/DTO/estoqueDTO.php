<?php



namespace App\DTO;

class EstoqueDTO {
    public $totalProduzido;
    public $totalVendido;
    public $estoqueAtual;

    public function __construct($totalProduzido, $totalVendido, $estoqueAtual) {
        $this->totalProduzido = $totalProduzido;
        $this->totalVendido = $totalVendido;
        $this->estoqueAtual = $estoqueAtual;
    }
}
