<?php
session_start();

$score = $_SESSION['points'];
$total = $_SESSION['quescount'];

$timeout_duration = 1800; 
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout_duration)) {
    session_unset();
    session_destroy();
    header("Location: index.php");
}
$_SESSION['LAST_ACTIVITY'] = time();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['resume'])) {

    $file = $_FILES['resume'];
    $maxSize = 1 * 1024 * 1024; // 1MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo "Upload error code: " . $file['error'];
        exit;
    }

    if ($file['size'] > $maxSize) {
        http_response_code(400);
        echo "File too large. Max 0.2MB allowed.";
        exit;
        
    }

    $target_dir = "uploads/";
    
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

    $file_name = time() . "_" . basename($_FILES["resume"]["name"]);
    $new_name = "Score_" . $score . "outOf" . $total . "_" . $file_name;
    $target_file = $target_dir . $new_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $allowed_types = ['pdf', 'doc', 'docx'];
    if (in_array($file_type, $allowed_types)) {
        if (move_uploaded_file($_FILES["resume"]["tmp_name"], $target_file)) {
            $_SESSION['upload_msg'] = "Resume uploaded successfully!";
            $_SESSION['resume_submitted'] = true;
        } else {
            $_SESSION['upload_msg'] = "Error uploading file.";
        }
    } else {
        $_SESSION['upload_msg'] = "Invalid file type. Please upload PDF or DOC.";
    }
    
    header("Location: results.php");
    exit();
}
?>