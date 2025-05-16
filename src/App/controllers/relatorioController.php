<?php

/*

namespace App\Controllers;

use App\Services\RelatorioService;
use App\Models\leite;
use App\Models\lucro;
use App\Models\despesa;

class RelatorioController {
    private $relatorioService;

    public function __construct() {
        $this->relatorioService = new RelatorioService();
    }

    public function gerarRelatorioEstoque() {
        // Buscando os dados de leite e lucro do banco
        $leites = Leite::all();  // Supondo que você tenha um método para buscar todos os leites
        $lucros = Lucro::all();  // Supondo que você tenha um método para buscar todos os lucros

        // Gerando o relatório de estoque
        $relatorioEstoque = $this->relatorioService->gerarResumoEstoque($leites, $lucros);

        // Exibindo ou retornando o relatório
        return response()->json($relatorioEstoque);
    }

    public function gerarRelatorioFinanceiro() {
        // Buscando os dados de lucro e despesa do banco
        $lucros = Lucro::all();  // Supondo que você tenha um método para buscar todos os lucros
        $despesas = Despesa::all();  // Supondo que você tenha um método para buscar todas as despesas

        // Gerando o relatório financeiro
        $relatorioFinanceiro = $this->relatorioService->gerarResumoFinanceiro($lucros, $despesas);

        // Exibindo ou retornando o relatório
        return response()->json($relatorioFinanceiro);
    }
}
