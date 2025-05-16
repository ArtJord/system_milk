<?php



namespace App\Services;

use App\Models\Leite;
use App\Models\Lucro;
use App\Models\Despesa;
use App\DTO\EstoqueDTO;
use App\DTO\FinanceiroDTO;

class RelatorioService {
    // Método para gerar resumo do estoque de leite
    public function gerarResumoEstoque($leites, $lucros) {
        $totalProduzido = 0;
        $totalVendido = 0;

        foreach ($leites as $leite) {
            $totalProduzido += $leite['quantidade_litros'];
        }

        foreach ($lucros as $lucro) {
            if ($lucro['tipo'] == 'leite') {
                $totalVendido += $lucro['quantidade'];
            }
        }

        $estoqueAtual = $totalProduzido - $totalVendido;
        return new EstoqueDTO($totalProduzido, $totalVendido, $estoqueAtual);
    }

    // Método para gerar resumo financeiro (lucros - despesas)
    public function gerarResumoFinanceiro($lucros, $despesas) {
        $totalLucros = 0;
        $totalDespesas = 0;

        foreach ($lucros as $lucro) {
            $totalLucros += $lucro['valor'];
        }

        foreach ($despesas as $despesa) {
            $totalDespesas += $despesa['valor'];
        }

        $balanco = $totalLucros - $totalDespesas;
        return new FinanceiroDTO($totalLucros, $totalDespesas, $balanco);
    }
}
