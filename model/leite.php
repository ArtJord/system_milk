<?php

class Leite
{
    private $pdo;

    
    public function __construct($db)
    {
        $this->pdo = $db;
    }

    
    public function create($data_fabricacao, $quantidade, $unidade_quantidade = 'litros')
    {
        
        $stmt = $this->pdo->prepare("INSERT INTO leite (data_fabricacao, quantidade_litros, unidade_quantidade) 
                                     VALUES (?, ?, ?)");

        
        return $stmt->execute([$data_fabricacao, $quantidade, $unidade_quantidade]);
    }

    public function update($id, $data_fabricacao, $quantidade, $unidade_quantidade = 'litros')
    {
        
        $stmt = $this->pdo->prepare("UPDATE leite 
                                     SET data_fabricacao = ?, quantidade_litros = ?, unidade_quantidade = ? 
                                     WHERE id = ?");
        
      
        return $stmt->execute([$data_fabricacao, $quantidade, $unidade_quantidade, $id]);
    }

    public function getAllLeites()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM leite");
        $stmt->execute();

        $leites = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $leites[] = $row;
        }

        return $leites;
    }

    
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM leite WHERE id = ?");
        return $stmt->execute([$id]);
    }

    
    public function somarLeite()
    {
        $sql = "SELECT quantidade_litros, unidade_quantidade FROM leite";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $totalLitros = 0;

        // Processa as linhas retornadas
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Se for 'ml', converte para litros
            if ($row['unidade_quantidade'] == 'ml') {
                $quantidadeEmLitros = $row['quantidade_litros'] / 1000;
            } else {
                $quantidadeEmLitros = $row['quantidade_litros'];
            }

            // Soma a quantidade convertida ao total
            $totalLitros += $quantidadeEmLitros;
        }

        return $totalLitros;
    }
}
?>
