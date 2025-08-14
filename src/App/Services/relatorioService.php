<?php

namespace App\Services;

use App\Models\Leite;
use App\Models\Lucro;
use App\Models\Despesa;
use App\DTO\EstoqueDTO;
use App\DTO\FinanceiroDTO;

class RelatorioService
{
    // Gera um resumo do estoque de leite
    public function gerarResumoEstoque($leites, $lucros)
    {
        $totalProduzido = 0;
        $totalVendido = 0;

        foreach ($leites as $leite) {
            $totalProduzido += floatval($leite['quantidade_litros']);
        }

        foreach ($lucros as $lucro) {
            if (isset($lucro['categoria']) && $lucro['categoria'] === 'Venda de leite') {
                // Se você usa valor_total em litros, pegue o campo correto aqui
                $totalVendido += floatval($lucro['quantidade']);
            }
        }

        $estoqueAtual = $totalProduzido - $totalVendido;

        return new EstoqueDTO($totalProduzido, $totalVendido, $estoqueAtual);
    }

    // Gera um resumo financeiro com base nos lucros e despesas
    public function gerarResumoFinanceiro($lucros, $despesas)
    {
        $totalLucros = 0;
        $totalDespesas = 0;

        foreach ($lucros as $lucro) {
            $totalLucros += floatval($lucro['valor_total']);
        }

        foreach ($despesas as $despesa) {
            $totalDespesas += floatval($despesa['valor_total']);
        }

        $balanco = $totalLucros - $totalDespesas;

        return new FinanceiroDTO($totalLucros, $totalDespesas, $balanco);
    }
}
