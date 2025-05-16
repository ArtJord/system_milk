<?php


namespace App\DTO;

class FinanceiroDTO {
    public $totalLucros;
    public $totalDespesas;
    public $balanco;

    public function __construct($totalLucros, $totalDespesas, $balanco) {
        $this->totalLucros = $totalLucros;
        $this->totalDespesas = $totalDespesas;
        $this->balanco = $balanco;
    }
}
