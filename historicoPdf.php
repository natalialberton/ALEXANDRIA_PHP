<?php

ob_start();
require_once ('fpdf/fpdf.php');
require_once ('geral.php');

$pdo = conectaBd();

$id_mem = $_GET['id_mem'];

//SQL PARA PUXAR DADOS DE EMPRÉSTIMO
$stmt = $pdo->prepare("SELECT * FROM membro WHERE pk_mem = :id_mem");
$stmt-> bindParam(":id_mem", $id_mem, PDO::PARAM_INT);
$stmt->execute();
$membro = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$membro) {
    enviarSweetAlert('template/gestao/membro-gestao.php', 'erroAlerta', 'Membro não encontrado!');
}

$pdf = new FPDF("P", "pt", "A4");
$pdf-> AddPage();

class PDF extends FPDF {
    function Header() {
        $this-> Image('static/img/LOGO-DARK-MODE.png', 5, 5, 30);
        $this-> Ln(10);
        $this-> SetFont('Times', 'B', 15);
        $this-> Cell(60); 
        $this-> Cell(70, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Histórico de Empréstimos'), 0, 0, 'C');
        $this-> Ln(20);
    }

    function Footer() {
        $this-> SetY(-15);
        $this-> SetFont('Times', 'I', 8);
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

$stmt = $pdo-> prepare("SELECT * FROM emprestimo WHERE fk_mem = :id_mem ");
$stmt-> bindParam(':id_mem', $id_mem, PDO::PARAM_INT);
$stmt-> execute();
$emprestimos = $stmt-> fetchAll();

if ($emprestimos) {
    $pdf-> SetFillColor(200, 220, 255);
    $pdf-> Cell(30, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Empréstimo'), 1, 0, 'C', true);
    $pdf-> Cell(30, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Devolução'), 1, 0, 'C', true);
    $pdf-> Cell(35, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Devolução Real'), 1, 0, 'C', true);
    $pdf-> Cell(60, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Livro'), 1, 0, 'L', true);
    $pdf-> Cell(40, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Status'), 1, 1, 'C', true);

    $fill = false;
    forEach($emprestimos as $emprestimo) {
        //PUXANDO DADOS FK
        $livroOrig = selecionarPorId('livro', $emprestimo['fk_liv'], 'pk_liv');
        $livro = mb_strimwidth($livroOrig['liv_titulo'], 0, 30, '...');

        $pdf-> SetFillColor(240, 240, 240);
        $pdf-> Cell(30, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $emprestimo['pk_emp']), 1, 0, 'C', $fill);
        $pdf-> Cell(30, 10, date('d/m/Y', strtotime($emprestimo["emp_dataDev"])) ?? null, 1, 0, 'C', $fill);
        $pdf-> Cell(35, 10, !empty($emprestimo["emp_dataDevReal"]) ? date('d/m/Y', strtotime($emprestimo["emp_dataDevReal"])) : null, 1, 0, 'C', $fill);
        $pdf-> Cell(60, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $livro), 1, 0, 'L', $fill);
        $pdf-> Cell(40, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $emprestimo['emp_status']), 1, 1, 'C', $fill);
        $fill = !$fill; //ALTERNA A COR
    }
} else {
    $pdf-> Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'O membro não tem empréstimos registrados.'), 0, 1, 'L');
}

//RESERVAS -------------------------------------
$pdf->Ln(10);

$stmt = $pdo-> prepare("SELECT * FROM reserva WHERE fk_mem = :id_mem ");
$stmt-> bindParam(':id_mem', $id_mem, PDO::PARAM_INT);
$stmt-> execute();
$reservas = $stmt-> fetchAll();

if ($reservas) {
    $pdf-> SetFillColor(200, 220, 255);
    $pdf-> Cell(30, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Reserva'), 1, 0, 'C', true);
    $pdf-> Cell(30, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Vencimento'), 1, 0, 'C', true);
    $pdf-> Cell(35, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Finalizada'), 1, 0, 'C', true);
    $pdf-> Cell(60, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Livro'), 1, 0, 'L', true);
    $pdf-> Cell(40, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Status'), 1, 1, 'C', true);

    $fill = false;
    forEach($reservas as $reserva) {
        //PUXANDO DADOS FK
        $livroOrig = selecionarPorId('livro', $reserva['fk_liv'], 'pk_liv');
        $livro = mb_strimwidth($livroOrig['liv_titulo'], 0, 30, '...');

        $pdf-> SetFillColor(240, 240, 240);
        $pdf-> Cell(30, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $reserva['pk_res']), 1, 0, 'C', $fill);
        $pdf-> Cell(30, 10, date('d/m/Y', strtotime($reserva["res_dataVencimento"])) ?? null, 1, 0, 'C', $fill);
        $pdf-> Cell(35, 10, !empty($reserva["res_dataFinalizada"]) ? date('d/m/Y', strtotime($reserva["res_dataFinalizada"])) : null, 1, 0, 'C', $fill);
        $pdf-> Cell(60, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $livro), 1, 0, 'L', $fill);
        $pdf-> Cell(40, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $reserva['res_status']), 1, 1, 'C', $fill);
        $fill = !$fill; //ALTERNA A COR
    }
} else {
    $pdf-> Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'O membro não tem reservas registradas.'), 0, 1, 'L');
}

//MULTAS -------------------------------------
$pdf->Ln(10);

$stmt = $pdo-> prepare("SELECT * FROM multa WHERE fk_mem = :id_mem ");
$stmt-> bindParam(':id_mem', $id_mem, PDO::PARAM_INT);
$stmt-> execute();
$multas = $stmt-> fetchAll();

if ($multas) {
    $pdf-> SetFillColor(200, 220, 255);
    $pdf-> Cell(30, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Multa'), 1, 0, 'C', true);
    $pdf-> Cell(30, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Valor'), 1, 0, 'C', true);
    $pdf-> Cell(35, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Dias Atraso'), 1, 0, 'C', true);
    $pdf-> Cell(60, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Livro'), 1, 0, 'L', true);
    $pdf-> Cell(40, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Status'), 1, 1, 'C', true);

    $fill = false;
    forEach($multas as $multa) {
        //PUXANDO DADOS FK
        $emprestimo = selecionarPorId('emprestimo', $multa['fk_emp'], 'pk_emp');
        $livroOrig = selecionarPorId('livro', $emprestimo['fk_liv'], 'pk_liv');
        $livro = mb_strimwidth($livroOrig['liv_titulo'], 0, 30, '...');

        $pdf-> SetFillColor(240, 240, 240);
        $pdf-> Cell(30, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $multa['pk_mul']), 1, 0, 'C', $fill);
        $pdf-> Cell(30, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $multa['mul_valor']), 1, 0, 'C', $fill);
        $pdf-> Cell(35, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $multa['mul_qtdDias']), 1, 0, 'C', $fill);
        $pdf-> Cell(60, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $livro), 1, 0, 'L', $fill);
        $pdf-> Cell(40, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $multa['mul_status']), 1, 1, 'C', $fill);
        $fill = !$fill; //ALTERNA A COR
    }
} else {
    $pdf-> Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'O membro não tem multas pendentes.'), 0, 1, 'L');
}

$pdf-> Output("historico_emprestimo.pdf", "I");

?>