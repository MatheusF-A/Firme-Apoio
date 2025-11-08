<?php
session_start();
// Inclui a biblioteca FPDF (Assumindo que está em bibliotecas/fpdf/fpdf.php)
require_once __DIR__ . "/../bibliotecas/fpdf/fpdf.php";
require_once __DIR__ . "/../config/conexao.php"; 

// --- 1. Segurança e Coleta de IDs ---
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'voluntario' || !isset($_SESSION['id_usuario'])) {
    die("Acesso negado.");
}
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID do usuário não fornecido.");
}

$usuarioID = $_GET['id'];
$voluntarioID = $_SESSION['id_usuario'];

// --- 2. Busca de Dados de Verificação e Usuário ---
try {
    // A. Verifica se o voluntário tem permissão (está acompanhando o usuário)
    $sqlPermissao = "SELECT * FROM acompanhamento 
                     WHERE usuarioID = :usuarioID AND voluntarioID = :voluntarioID";
    $stmtPermissao = $conn->prepare($sqlPermissao);
    $stmtPermissao->bindParam(':usuarioID', $usuarioID, PDO::PARAM_INT);
    $stmtPermissao->bindParam(':voluntarioID', $voluntarioID, PDO::PARAM_INT);
    $stmtPermissao->execute();
    
    if ($stmtPermissao->rowCount() === 0) {
        die("Permissão negada. Você não está acompanhando este usuário.");
    }

    // B. Pega os dados básicos do usuário
    $sqlUsuario = "SELECT nome FROM usuario WHERE usuarioID = :usuarioID";
    $stmtUsuario = $conn->prepare($sqlUsuario);
    $stmtUsuario->bindParam(':usuarioID', $usuarioID, PDO::PARAM_INT);
    $stmtUsuario->execute();
    $dadosUsuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);
    $nomeUsuario = $dadosUsuario['nome'] ?? 'Usuário Desconhecido';

    // --- 3. Busca de Dados da Autoavaliação ---

    // 3a. Média de Humor (Cálculo no SQL)
    $sqlHumor = "SELECT AVG(notaHumor) as mediaHumor, COUNT(*) as totalAvaliacoes
                 FROM autoavaliacao WHERE usuarioID = :usuarioID";
    $stmtHumor = $conn->prepare($sqlHumor);
    $stmtHumor->bindParam(':usuarioID', $usuarioID, PDO::PARAM_INT);
    $stmtHumor->execute();
    $dadosHumor = $stmtHumor->fetch(PDO::FETCH_ASSOC);

    // 3b. Últimas 3 Respostas Escritas (Ordenar por data decrescente)
    $sqlRespostas = "SELECT PerguntaUm, PerguntaDois, PerguntaTres, dataRealizacao
                     FROM autoavaliacao 
                     WHERE usuarioID = :usuarioID 
                     ORDER BY dataRealizacao DESC LIMIT 3";
    $stmtRespostas = $conn->prepare($sqlRespostas);
    $stmtRespostas->bindParam(':usuarioID', $usuarioID, PDO::PARAM_INT);
    $stmtRespostas->execute();
    $ultimasRespostas = $stmtRespostas->fetchAll(PDO::FETCH_ASSOC);

    // --- 4. Busca de Dados de Progresso (Hábitos e Exercícios) ---

    // 4a. Hábitos
    $sqlHabitos = "SELECT nome, concluido FROM habitos WHERE usuarioID = :usuarioID";
    $stmtHabitos = $conn->prepare($sqlHabitos);
    $stmtHabitos->bindParam(':usuarioID', $usuarioID, PDO::PARAM_INT);
    $stmtHabitos->execute();
    $habitos = $stmtHabitos->fetchAll(PDO::FETCH_ASSOC);

    // 4b. Exercícios
    $sqlExercicios = "SELECT nome, concluido FROM exercicios WHERE usuarioID = :usuarioID";
    $stmtExercicios = $conn->prepare($sqlExercicios);
    $stmtExercicios->bindParam(':usuarioID', $usuarioID, PDO::PARAM_INT);
    $stmtExercicios->execute();
    $exercicios = $stmtExercicios->fetchAll(PDO::FETCH_ASSOC);


} catch (Exception $e) {
    die("Erro no banco de dados: " . $e->getMessage());
}

// =================================================================
// === MONTAGEM DO PDF COM FPDF ====================================
// =================================================================

class PDF extends FPDF
{
    // Cabeçalho
    function Header() {
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(30, 30, 30); // Cinza escuro
        $this->Cell(0, 10, utf8_decode('Relatório de Acompanhamento - Firme Apoio'), 0, 1, 'C');
        $this->Ln(5);
    }
    
    // Rodapé
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(150, 150, 150); // Cinza claro
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Título da Seção
    function ChapterTitle($label) {
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(220, 220, 220); // Cinza claro
        $this->Cell(0, 8, utf8_decode($label), 0, 1, 'L', true);
        $this->Ln(3);
    }
    
    // Texto Simples
    function SimpleText($label, $value) {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(50, 6, utf8_decode($label . ':'), 0);
        $this->SetFont('Arial', '', 10);
        $this->MultiCell(0, 6, utf8_decode($value), 0, 'L');
    }

    // Bloco de Resposta
    function ResponseBlock($date, $p1, $p2, $p3) {
        $this->SetFillColor(240, 240, 240);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 7, utf8_decode("Registro de: ") . date('d/m/Y H:i', strtotime($date)), 1, 1, 'L', true);

        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, utf8_decode("1. O que aconteceu?"), 0, 1);
        $this->MultiCell(0, 5, utf8_decode($p1), 0, 'L');
        $this->Ln(2);
        
        $this->Cell(0, 5, utf8_decode("2. O que pensou/sentiu?"), 0, 1);
        $this->MultiCell(0, 5, utf8_decode($p2), 0, 'L');
        $this->Ln(2);

        $this->Cell(0, 5, utf8_decode("3. Como lidou?"), 0, 1);
        $this->MultiCell(0, 5, utf8_decode($p3), 0, 'L');
        $this->Ln(5);
    }

    // Progresso
    function ProgressItem($name, $concluido) {
        $status = $concluido ? 'CONCLUÍDO' : 'PENDENTE';
        $fillColor = $concluido ? array(200, 255, 200) : array(255, 230, 230);
        
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor($fillColor[0], $fillColor[1], $fillColor[2]);
        $this->Cell(130, 6, utf8_decode($name), 1, 0, 'L', true);
        
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor($concluido ? 0 : 255, $concluido ? 128 : 0, 0); // Verde ou Vermelho
        $this->Cell(0, 6, utf8_decode($status), 1, 1, 'C', true);
        $this->SetTextColor(30, 30, 30); // Volta ao preto
    }
}

// Instanciação e configuração
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 20); // Break de página automático

// --- DADOS DO RELATÓRIO ---
$pdf->SetFont('Arial', '', 11);
$pdf->SimpleText('Paciente', $nomeUsuario);
$pdf->SimpleText('ID do Paciente', $usuarioID);
$pdf->SimpleText('Voluntário Responsável', $_SESSION['nome']);
$pdf->SimpleText('Data de Geração', date('d/m/Y H:i:s'));
$pdf->Ln(10);


// --- SEÇÃO 1: MÉTRICAS DE HUMOR (Autoavaliação) ---
$pdf->ChapterTitle('Métricas de Humor');

if ($dadosHumor['totalAvaliacoes'] > 0) {
    $media = number_format($dadosHumor['mediaHumor'], 1);
    $total = $dadosHumor['totalAvaliacoes'];
    
    $descricaoHumor = '';
    if ($media >= 4.5) $descricaoHumor = ' (Excelente)';
    else if ($media >= 3.5) $descricaoHumor = ' (Bom)';
    else if ($media >= 2.5) $descricaoHumor = ' (Razoável)';
    else $descricaoHumor = ' (Preocupante)';

    $pdf->SetFont('Arial', '', 10);
    $pdf->Write(6, utf8_decode("Total de avaliações realizadas: {$total}"));
    $pdf->Ln(7);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Write(6, utf8_decode("Média de Humor (Escala 1 a 5): "));
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetTextColor(50, 150, 50); // Verde
    $pdf->Write(6, utf8_decode("{$media}"));
    $pdf->SetTextColor(30, 30, 30);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Write(6, utf8_decode("{$descricaoHumor}"));
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 6, utf8_decode('Nenhuma autoavaliação de humor encontrada.'), 0, 1);
}
$pdf->Ln(10);


// --- SEÇÃO 2: ÚLTIMAS RESPOSTAS ESCRITAS ---
$pdf->ChapterTitle('Últimas 3 Respostas de Reflexão');
if (empty($ultimasRespostas)) {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 6, utf8_decode('Nenhuma resposta escrita de autoavaliação encontrada.'), 0, 1);
} else {
    foreach ($ultimasRespostas as $resp) {
        $pdf->ResponseBlock(
            $resp['dataRealizacao'], 
            $resp['PerguntaUm'], 
            $resp['PerguntaDois'], 
            $resp['PerguntaTres']
        );
    }
}
$pdf->Ln(5);


// --- SEÇÃO 3: PROGRESSO DE HÁBITOS ---
$pdf->ChapterTitle('Progresso de Hábitos');
if (empty($habitos)) {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 6, utf8_decode('Nenhum hábito cadastrado.'), 0, 1);
} else {
    foreach ($habitos as $habito) {
        $pdf->ProgressItem($habito['nome'], $habito['concluido']);
    }
}
$pdf->Ln(5);


// --- SEÇÃO 4: PROGRESSO DE EXERCÍCIOS ---
$pdf->ChapterTitle('Progresso de Exercícios');
if (empty($exercicios)) {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 6, utf8_decode('Nenhum exercício cadastrado.'), 0, 1);
} else {
    foreach ($exercicios as $exercicio) {
        $pdf->ProgressItem($exercicio['nome'], $exercicio['concluido']);
    }
}

// --- FIM ---
$pdf->Output('D', 'Relatorio_FirmeApoio_' . $usuarioID . '_' . date('Ymd') . '.pdf');
exit;
?>