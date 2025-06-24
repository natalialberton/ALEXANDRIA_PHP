<?php
require_once __DIR__ . '/../../fpdf/fpdf.php';
require_once __DIR__ . '/../../conexao.php';


$pdo = conectarBanco();

if (!isset($_GET['id_mem'])) {
    die("Membro não informado.");
}

$id_mem = intval($_GET['id_mem']);

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',16);
        $this->Cell(0,10, utf8_decode('Relatório de Empréstimos'),0,1,'C');
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10, utf8_decode('Página ') . $this->PageNo(),0,0,'C');
    }

    function NbLines($w, $txt) {
        $cw = &$this->CurrentFont['cw'];
        if($w==0)
            $w = $this->w-$this->rMargin-$this->x;
        $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
        $s = str_replace("\r",'',$txt);
        $nb = strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while($i<$nb) {
            $c = $s[$i];
            if($c=="\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep = $i;
            $l += $cw[$c];
            if($l>$wmax) {
                if($sep==-1) {
                    if($i==$j)
                        $i++;
                } else
                    $i = $sep+1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);


$stmt = $pdo->prepare("SELECT mem_nome FROM membro WHERE pk_mem = ?");
$stmt->execute([$id_mem]);
$membro = $stmt->fetch();

if (!$membro) {
    die("Membro não encontrado.");
}

$pdf->Cell(0,10, utf8_decode("Histórico de empréstimos de: ") . utf8_decode($membro['mem_nome']), 0,1);
$pdf->Ln(5);


$stmt = $pdo->prepare("
    SELECT 
        e.pk_emp,
        e.emp_dataEmp,
        e.emp_dataDev,
        e.emp_dataDevReal,
        e.emp_status,
        l.liv_titulo,
        u.user_nome AS funcionario
    FROM emprestimo e
    JOIN livro l ON e.fk_liv = l.pk_liv
    JOIN usuario u ON e.fk_user = u.pk_user
    WHERE e.fk_mem = ?
    ORDER BY e.emp_dataEmp DESC
");
$stmt->execute([$id_mem]);

$emprestimos = $stmt->fetchAll();

if (!$emprestimos) {
    $pdf->Cell(0,10, utf8_decode("Nenhum empréstimo encontrado para este membro."), 0,1);
} else {
    // Cabeçalho da tabela
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(30,10, utf8_decode('Empréstimo'), 1, 0, 'C');
    $pdf->Cell(30,10, utf8_decode('Devolução'), 1, 0, 'C');
    $pdf->Cell(30,10, utf8_decode('Devolução Real'), 1, 0, 'C');
    $pdf->Cell(60,10, utf8_decode('Livro'), 1, 0, 'C');
    $pdf->Cell(40,10, utf8_decode('Status'), 1, 1, 'C');

    $pdf->SetFont('Arial','',11);

   foreach ($emprestimos as $e) {

    $dataEmp = date('d/m/Y', strtotime($e['emp_dataEmp']));
    $dataDev = $e['emp_dataDev'] ? date('d/m/Y', strtotime($e['emp_dataDev'])) : '-';
    $dataDevReal = $e['emp_dataDevReal'] ? date('d/m/Y', strtotime($e['emp_dataDevReal'])) : '-';
    $livro = utf8_decode($e['liv_titulo']);
    $status = utf8_decode($e['emp_status']);


    $x = $pdf->GetX();
    $y = $pdf->GetY();

    $nb_livro = $pdf->NbLines(60, $livro);
    $altura = max(10, 5 * $nb_livro);

  
    $pdf->Cell(30, $altura, $dataEmp, 1);
    $pdf->Cell(30, $altura, $dataDev, 1);
    $pdf->Cell(30, $altura, $dataDevReal, 1);


    $pdf->SetXY($x + 90, $y);
    $pdf->MultiCell(60,5, $livro,1);


    $pdf->SetXY($x + 150, $y);
    $pdf->Cell(40, $altura, $status, 1, 0, 'C');

  
    $pdf->SetY($y + $altura);
}
}

$pdf->Output();
?>
