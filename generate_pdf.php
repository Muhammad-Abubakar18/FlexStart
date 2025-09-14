<?php
ob_start(); // Add this at the very top
require_once 'db_connect.php';
require_once __DIR__ . '/fpdf/fpdf.php'; // Make sure to include FPDF library

class PDF extends FPDF {
    function Header() {
        // Logo or title
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'Professional Resume', 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

// Get resume data from database
$resume_id = isset($_POST['resume_id']) ? $_POST['resume_id'] : null;

if (!$resume_id) {
    die('Resume ID not provided');
}

// Create PDF
$pdf = new PDF();
$pdf->AddPage();

// Get personal information
$sql = "SELECT * FROM personal_info WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $resume_id);
$stmt->execute();
$result = $stmt->get_result();
$personal = $result->fetch_assoc();

if ($personal) {
    // Personal Information
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, $personal['full_name'], 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 6, $personal['email'], 0, 1);
    $pdf->Cell(0, 6, $personal['phone'], 0, 1);
    $pdf->Cell(0, 6, $personal['address'], 0, 1);
    $pdf->Ln(10);

    // Professional Summary
    $sql = "SELECT content FROM summary WHERE resume_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $resume_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $summary = $result->fetch_assoc();

    if ($summary) {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Professional Summary', 0, 1);
        $pdf->SetFont('Arial', '', 11);
        $pdf->MultiCell(0, 6, $summary['content']);
        $pdf->Ln(10);
    }

    // Education
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Education', 0, 1);
    $pdf->SetFont('Arial', '', 11);

    $sql = "SELECT * FROM education WHERE resume_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $resume_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($edu = $result->fetch_assoc()) {
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 6, $edu['degree'], 0, 1);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 6, $edu['university'] . ', ' . $edu['location'], 0, 1);
        $pdf->Cell(0, 6, 'Graduation Year: ' . $edu['grad_year'], 0, 1);
        $pdf->Ln(5);
    }

    // Experience
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Work Experience', 0, 1);
    $pdf->SetFont('Arial', '', 11);

    $sql = "SELECT * FROM experience WHERE resume_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $resume_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($exp = $result->fetch_assoc()) {
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 6, $exp['job_title'], 0, 1);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 6, $exp['company'] . ' | ' . $exp['duration'], 0, 1);
        $pdf->MultiCell(0, 6, $exp['description']);
        $pdf->Ln(5);
    }

    // Skills
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Skills', 0, 1);
    $pdf->SetFont('Arial', '', 11);

    $sql = "SELECT skill_name FROM skills WHERE resume_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $resume_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $skills = [];
    while ($skill = $result->fetch_assoc()) {
        $skills[] = $skill['skill_name'];
    }
    $pdf->Cell(0, 6, implode(', ', $skills), 0, 1);
    $pdf->Ln(5);

    // Languages
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Languages', 0, 1);
    $pdf->SetFont('Arial', '', 11);

    $sql = "SELECT * FROM languages WHERE resume_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $resume_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($lang = $result->fetch_assoc()) {
        $pdf->Cell(0, 6, $lang['language'] . ' - ' . $lang['proficiency'], 0, 1);
    }
    $pdf->Ln(5);

    // Activities
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Activities & Interests', 0, 1);
    $pdf->SetFont('Arial', '', 11);

    $sql = "SELECT activity FROM activities WHERE resume_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $resume_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($activity = $result->fetch_assoc()) {
        $pdf->Cell(0, 6, '• ' . $activity['activity'], 0, 1);
    }
}

// Output PDF
// if (headers_sent($file, $line)) {
//     error_log("Headers already sent in $file on line $line");
// }
$pdf->Output('D', 'resume.pdf');
// ob_end_flush(); // Add this at the end
?> 