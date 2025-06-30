<?php

ob_start();
require_once ('fpdf/fpdf.php');
require_once ('geral.php');

$pdo = conectaBd();

if (!isset($_GET['id_mem'])) {
    enviarSweetAlert('template/gestao/membro-gestao.php', 'erroAlerta', 'Membro não informado!');
}

$id_mem = intval($_GET['id_mem']);

//SQL PARA PUXAR DADOS DE EMPRÉSTIMO
$stmt = $pdo->prepare("SELECT mem_nome FROM membro WHERE pk_mem = ?");
$stmt->execute([$id_mem]);
$membro = $stmt->fetch();

if (!$membro) {
    enviarSweetAlert('template/gestao/membro-gestao.php', 'erroAlerta', 'Membro não encontrado!');
}

$pdf = new FPDF("P", "pt", "A4");
$pdf-> AddPage();

//VERIFICANDO SE O USUÁRIO É ATIVO
if(!isset($_SESSION['statusUser']) || $_SESSION['statusUser'] !== 'Ativo') {
    enviarSweetAlert('template/index.php', 'erroAlerta', 'Acesso a página negado!');
}

class PDF extends FPDF {
    function Header() {
        $this-> Image('static/img/LOGO-DARK-MODE.png', 5, 1, 50);
        $this-> Ln(30);
        $this-> SetFont('Arial', 'B', 15);
        $this-> Cell(80); 
        $this-> Cell(90, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Histórico de Empréstimos'), 1, 0, 'C');
        $this-> Ln(20);
    }

    function Footer() {
        $this-> SetY(-15);
        $this-> SetFont('Arial', 'I', 8);
        $this-> Cell(0, 10,  iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Página'). ''. $this-> PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF();
//Ativa o uso do {nb} para total de páginas no rodapé
$pdf-> AliasNbPages();
$pdf-> AddPage();
$pdf-> SetFont('Times', '', 12);

$pdf->Cell(0,10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Histórico de empréstimos de ') . iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $membro['mem_nome']), 0,1);
//EMPRÉSTIMOS -------------------------------------
$pdf->Ln(5);

$pdf-> SetFillColor(200, 220, 255);
$pdf-> Cell(30, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Empréstimo'), 1, 0, 'C', true);
$pdf-> Cell(30, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Devolução'), 1, 0, 'C', true);
$pdf-> Cell(35, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Devolução Real'), 1, 0, 'C', true);
$pdf-> Cell(60, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Livro'), 1, 0, 'L', true);
$pdf-> Cell(40, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Status'), 1, 1, 'C', true);

$stmt = $pdo-> query("SELECT * FROM emprestimo WHERE fk_mem = :id_mem ");
$stmt-> bindParam(':id_mem', $id_mem, PDO::PARAM_INT);

$fill = false;
while($emprestimo = $stmt-> fetch(PDO::FETCH_ASSOC)) {
    //PUXANDO DADOS FK
    $livro = selecionarPorId('livro', $emprestimo['fk_liv'], 'pk_liv');

    $pdf-> SetFillColor(240, 240, 240);
    $pdf-> Cell(30, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $emprestimo['pk_emp']), 1, 0, 'C', $fill);
    $pdf-> Cell(30, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $emprestimo['emp_dataDev']), 1, 0, 'C', $fill);
    $pdf-> Cell(35, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $emprestimo['emp_dataDevReal']), 1, 0, 'C', $fill);
    $pdf-> Cell(60, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $livro['liv_titulo']), 1, 0, 'L', $fill);
    $pdf-> Cell(40, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $emprestimo['emp_status']), 1, 1, 'C', $fill);
    $fill = !$fill; //ALTERNA A COR
}

//RESERVAS -------------------------------------
$pdf->Ln(5);

$pdf-> SetFillColor(200, 220, 255);
$pdf-> Cell(30, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Reserva'), 1, 0, 'C', true);
$pdf-> Cell(30, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Vencimento'), 1, 0, 'C', true);
$pdf-> Cell(35, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Finalizada'), 1, 0, 'C', true);
$pdf-> Cell(60, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Livro'), 1, 0, 'L', true);
$pdf-> Cell(40, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Status'), 1, 1, 'C', true);

$stmt = $pdo-> query("SELECT * FROM reserva WHERE fk_mem = :id_mem ");
$stmt-> bindParam(':id_mem', $id_mem, PDO::PARAM_INT);

$fill = false;
while($reserva = $stmt-> fetch(PDO::FETCH_ASSOC)) {
    //PUXANDO DADOS FK
    $livro = selecionarPorId('livro', $reserva['fk_liv'], 'pk_liv');

    $pdf-> SetFillColor(240, 240, 240);
    $pdf-> Cell(30, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $reserva['pk_res']), 1, 0, 'C', $fill);
    $pdf-> Cell(30, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $reserva['res_dataVencimento']), 1, 0, 'C', $fill);
    $pdf-> Cell(35, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $reserva['res_dataFinalizada']), 1, 0, 'C', $fill);
    $pdf-> Cell(60, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $livro['liv_titulo']), 1, 0, 'L', $fill);
    $pdf-> Cell(40, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $reserva['res_status']), 1, 1, 'C', $fill);
    $fill = !$fill; //ALTERNA A COR
}

//MULTAS -------------------------------------
$pdf->Ln(5);

$pdf-> SetFillColor(200, 220, 255);
$pdf-> Cell(30, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Multa'), 1, 0, 'C', true);
$pdf-> Cell(30, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Valor'), 1, 0, 'C', true);
$pdf-> Cell(35, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Dias Atraso'), 1, 0, 'C', true);
$pdf-> Cell(60, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Livro'), 1, 0, 'L', true);
$pdf-> Cell(40, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Status'), 1, 1, 'C', true);

$stmt = $pdo-> query("SELECT * FROM multa WHERE fk_mem = :id_mem ");
$stmt-> bindParam(':id_mem', $id_mem, PDO::PARAM_INT);

$fill = false;
while($multa = $stmt-> fetch(PDO::FETCH_ASSOC)) {
    //PUXANDO DADOS FK
    $emprestimo = selecionarPorId('emprestimo', $multa['fk_emp'], 'pk_emp');
    $livro = selecionarPorId('livro', $emprestimo['fk_liv'], 'pk_liv');

    $pdf-> SetFillColor(240, 240, 240);
    $pdf-> Cell(30, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $multa['pk_mul']), 1, 0, 'C', $fill);
    $pdf-> Cell(30, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $multa['mul_valor']), 1, 0, 'C', $fill);
    $pdf-> Cell(35, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $multa['mul_qtdDias']), 1, 0, 'C', $fill);
    $pdf-> Cell(60, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $livro['liv_titulo']), 1, 0, 'L', $fill);
    $pdf-> Cell(40, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $multa['mul_status']), 1, 1, 'C', $fill);
    $fill = !$fill; //ALTERNA A COR
}

$pdf-> Output("historico_emprestimo.pdf", "I");

?>