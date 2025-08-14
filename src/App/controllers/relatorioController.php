<?php

namespace App\Controllers;

use App\Services\RelatorioService;
use App\model\Leite;
use App\model\Lucro;
use App\model\Despesa;
use Exception;
use PDO;

class RelatorioController
{
    private $pdo;
    private $relatorioService;

    public function __construct(PDO $db)
    {
        $this->pdo = $db;
        $this->relatorioService = new RelatorioService();
    }

    public function getResumoEstoque()
    {
        try {
            $leiteModel = new Leite($this->pdo);
            $lucroModel = new Lucro($this->pdo);

            $leites = $leiteModel->getAllLeites();
            $lucros = $lucroModel->getAllLucros();

            $resumoEstoque = $this->relatorioService->gerarResumoEstoque($leites, $lucros);

            echo json_encode($resumoEstoque);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao gerar resumo do estoque: " . $e->getMessage()]);
        }
    }

    public function getResumoFinanceiro()
    {
        try {
            $lucroModel = new Lucro($this->pdo);
            $despesaModel = new Despesa($this->pdo);

            $lucros = $lucroModel->getAllLucros();
            $despesas = $despesaModel->getAllDespesas();

            $resumoFinanceiro = $this->relatorioService->gerarResumoFinanceiro($lucros, $despesas);

            echo json_encode($resumoFinanceiro);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao gerar resumo financeiro: " . $e->getMessage()]);
        }
    }
}
