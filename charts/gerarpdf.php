<?php
// Verificar se FPDF existe
if (!file_exists('fpdf/fpdf.php')) {
    die('<h2>Erro: Biblioteca FPDF não encontrada!</h2>
         <p>Baixe o FPDF em: <a href="http://www.fpdf.org/en/download.php" target="_blank">http://www.fpdf.org/en/download.php</a></p>
         <p>Extraia na pasta "fpdf/" do seu projeto</p>');
}

require('fpdf/fpdf.php');


// Configuração do banco de dados ALEXANDRIA
$host = 'localhost:3307';
$dbname = 'ALEXANDRIA';
$username = 'root';
$password = '';

try {
    // Conexão com o banco
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query para buscar dados das categorias
    $sql = "SELECT 
                c.cat_nome as categoria,
                COUNT(cl.fk_liv) as total_livros,
                SUM(l.liv_estoque) as total_estoque
            FROM CATEGORIA c
            LEFT JOIN CAT_LIV cl ON c.pk_cat = cl.fk_cat
            LEFT JOIN LIVRO l ON cl.fk_liv = l.pk_liv
            GROUP BY c.pk_cat, c.cat_nome
            ORDER BY total_livros DESC";

    $stmt = $pdo->query($sql);
    $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Buscar estatísticas gerais
    $stats_sql = "SELECT 
                    (SELECT COUNT(*) FROM LIVRO) as total_livros,
                    (SELECT SUM(liv_estoque) FROM LIVRO) as total_estoque,
                    (SELECT COUNT(*) FROM EMPRESTIMO WHERE emp_status IN ('Empréstimo Ativo', 'Renovação Ativa')) as emprestimos_ativos,
                    (SELECT COUNT(*) FROM MEMBRO WHERE fk_status = 1) as membros_ativos";

    $stats_stmt = $pdo->query($stats_sql);
    $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

    // Calcular totais para percentuais corretos
    $total_titulos_geral = array_sum(array_column($dados, 'total_livros'));

} catch (PDOException $e) {
    die('<h2>Erro na conexão com banco:</h2><p>' . $e->getMessage() . '</p>');
}

// Classe PDF personalizada
class AlexandriaPDF extends FPDF
{
    // Cabeçalho
    function Header()
    {
        // Cor de fundo do cabeçalho
        $this->SetFillColor(52, 73, 94);
        $this->Rect(0, 0, 210, 30, 'F');
        
        // Texto do cabeçalho
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial','B',18);
        $this->SetY(8);
        $this->Cell(0,8,'SISTEMA ALEXANDRIA',0,1,'C');
        
        $this->SetFont('Arial','B',12);
        $this->Cell(0,6,utf8_decode('Relatório do Acervo por Categoria'),0,1,'C');
        
        $this->SetFont('Arial','',9);
        $this->Cell(0,5,'Gerado em: ' . date('d/m/Y H:i:s'),0,1,'C');
        
        // Linha separadora
        $this->SetY(32);
        $this->SetDrawColor(52, 73, 94);
        $this->Line(10, 32, 200, 32);
        
        $this->SetTextColor(0);
        $this->Ln(8);
    }

    // Rodapé
    function Footer()
    {
        $this->SetY(-20);
        
        // Linha separadora
        $this->SetDrawColor(52, 73, 94);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->SetTextColor(100);
        
        // Informações do rodapé
        $this->Cell(0,10,'Sistema Alexandria - Biblioteca Digital',0,0,'L');
        $this->Cell(0,10,utf8_decode('Página ').$this->PageNo().' de {nb}',0,0,'R');
    }

    // Caixas de estatísticas
    function AdicionarEstatisticas($stats)
    {
        $this->SetFont('Arial','B',14);
        $this->SetTextColor(52, 73, 94);
        $this->Cell(0,10,utf8_decode('RESUMO EXECUTIVO'),0,1,'L');
        $this->Ln(5);
        
        // Configurações das caixas
        $largura_caixa = 45;
        $altura_caixa = 25;
        $espacamento = 2;
        
        $estatisticas = [
            ['label' => 'Total de Títulos', 'valor' => number_format($stats['total_livros']), 'cor' => [52, 152, 219]],
            ['label' => 'Exemplares', 'valor' => number_format($stats['total_estoque']), 'cor' => [46, 204, 113]],
            ['label' => 'Empréstimos Ativos', 'valor' => number_format($stats['emprestimos_ativos']), 'cor' => [230, 126, 34]],
            ['label' => 'Membros Ativos', 'valor' => number_format($stats['membros_ativos']), 'cor' => [155, 89, 182]]
        ];
        
        $x_inicial = 10;
        $y_inicial = $this->GetY();
        
        foreach ($estatisticas as $i => $stat) {
            $x = $x_inicial + ($i * ($largura_caixa + $espacamento));
            $y = $y_inicial;
            
            // Sombra da caixa
            $this->SetFillColor(220, 220, 220);
            $this->Rect($x + 1, $y + 1, $largura_caixa, $altura_caixa, 'F');
            
            // Caixa principal
            $this->SetFillColor($stat['cor'][0], $stat['cor'][1], $stat['cor'][2]);
            $this->Rect($x, $y, $largura_caixa, $altura_caixa, 'F');
            
            // Borda
            $this->SetDrawColor(200, 200, 200);
            $this->Rect($x, $y, $largura_caixa, $altura_caixa);
            
            // Valor (número grande)
            $this->SetXY($x + 2, $y + 4);
            $this->SetFont('Arial','B',16);
            $this->SetTextColor(255, 255, 255);
            $this->Cell($largura_caixa - 4, 10, $stat['valor'], 0, 1, 'C');
            
            // Label
            $this->SetXY($x + 2, $y + 16);
            $this->SetFont('Arial','B',8);
            $this->Cell($largura_caixa - 4, 6, utf8_decode($stat['label']), 0, 1, 'C');
        }
        
        $this->SetY($y_inicial + $altura_caixa + 15);
        $this->SetTextColor(0);
    }

    // Tabela de categorias
    function TabelaCategorias($dados, $total_geral)
    {
        $this->SetFont('Arial','B',14);
        $this->SetTextColor(52, 73, 94);
        $this->Cell(0,10,utf8_decode('DETALHAMENTO POR CATEGORIA'),0,1,'L');
        $this->Ln(5);
        
        // Cabeçalho da tabela
        $this->SetFillColor(52, 73, 94);
        $this->SetTextColor(255);
        $this->SetDrawColor(255, 255, 255);
        $this->SetLineWidth(0.3);
        $this->SetFont('Arial','B',9);
        
        // Larguras das colunas
        $larguras = [60, 25, 30, 25, 30];
        $cabecalhos = ['Categoria', 'Títulos', 'Estoque', 'Percentual', 'Média/Título'];
        
        foreach ($cabecalhos as $i => $cabecalho) {
            $this->Cell($larguras[$i], 10, utf8_decode($cabecalho), 1, 0, 'C', true);
        }
        $this->Ln();
        
        // Dados da tabela
        $this->SetFillColor(245, 245, 245);
        $this->SetTextColor(0);
        $this->SetFont('Arial','',8);
        
        $fill = false;
        $total_estoque = 0;
        
        foreach ($dados as $row) {
            $percentual = ($row['total_livros'] / $total_geral) * 100;
            $media = $row['total_livros'] > 0 ? $row['total_estoque'] / $row['total_livros'] : 0;
            $total_estoque += $row['total_estoque'];
            
            $this->Cell($larguras[0], 8, utf8_decode($row['categoria']), 1, 0, 'L', $fill);
            $this->Cell($larguras[1], 8, number_format($row['total_livros']), 1, 0, 'C', $fill);
            $this->Cell($larguras[2], 8, number_format($row['total_estoque']), 1, 0, 'C', $fill);
            $this->Cell($larguras[3], 8, number_format($percentual, 1) . '%', 1, 0, 'C', $fill);
            $this->Cell($larguras[4], 8, number_format($media, 1), 1, 0, 'C', $fill);
            $this->Ln();
            
            $fill = !$fill;
        }
        
        // Linha de total
        $this->SetFont('Arial','B',9);
        $this->SetFillColor(52, 73, 94);
        $this->SetTextColor(255);
        
        $media_geral = $total_geral > 0 ? $total_estoque / $total_geral : 0;
        
        $this->Cell($larguras[0], 10, 'TOTAL GERAL', 1, 0, 'C', true);
        $this->Cell($larguras[1], 10, number_format($total_geral), 1, 0, 'C', true);
        $this->Cell($larguras[2], 10, number_format($total_estoque), 1, 0, 'C', true);
        $this->Cell($larguras[3], 10, '100%', 1, 0, 'C', true);
        $this->Cell($larguras[4], 10, number_format($media_geral, 1), 1, 0, 'C', true);
        
        $this->Ln(15);
        $this->SetTextColor(0);
    }

    // Observações finais
    function AdicionarObservacoes()
    {
        $this->SetFont('Arial','B',10);
        $this->SetTextColor(52, 73, 94);
        $this->Cell(0,6,utf8_decode('OBSERVAÇÕES:'),0,1,'L');
        
        $this->SetFont('Arial','',9);
        $this->SetTextColor(100);
        
        $observacoes = [
            'Este relatório apresenta a distribuição atual do acervo por categoria.',
            'Os percentuais são calculados com base no total de títulos cadastrados.',
            'A média por título indica quantos exemplares existem de cada obra.',
            'Dados atualizados automaticamente na data/hora de geração do relatório.'
        ];
        
        foreach ($observacoes as $obs) {
            $this->Cell(5, 5, chr(149), 0, 0, 'L'); // Bullet point
            $this->Cell(0, 5, utf8_decode($obs), 0, 1, 'L');
            $this->Ln(1);
        }
    }
}

// Gerar o PDF
$pdf = new AlexandriaPDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// Adicionar conteúdo
$pdf->AdicionarEstatisticas($stats);
$pdf->TabelaCategorias($dados, $total_titulos_geral);
$pdf->AdicionarObservacoes();

// Definir nome do arquivo
$nome_arquivo = 'Relatorio_Acervo_Alexandria_' . date('Y-m-d_H-i-s') . '.pdf';

// Saída do PDF
// Use 'I' para visualizar no navegador ou 'D' para download forçado
$pdf->Output('D', $nome_arquivo);
?>