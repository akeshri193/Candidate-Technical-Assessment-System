<?php
session_start();
$timeout_duration = 1800; 
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout_duration)) {
    session_unset();
    session_destroy();
    header("Location: index.php");
}
$_SESSION['LAST_ACTIVITY'] = time();
$total_questions = $_SESSION["quescount"];
$questions = $_SESSION["questions"];
$user_answers = $_SESSION["user_answers"];

$correct = 0;
$wrong = 0;
$missed = 0;

foreach ($questions as $index => $q) {
    $user_choice = $user_answers[$index] ?? 'TIMED_OUT';
    
    if ($user_choice === $q['answer']) {
        $correct++;
    } elseif ($user_choice === 'TIMED_OUT') {
        $missed++;
    } else {
        $wrong++;
    }
}

$score_percent = ($correct / $total_questions) * 100;

$eligible_for_resume = ($score_percent >= 70);

?>

<?php 
    $is_already_submitted = isset($_SESSION['resume_submitted']) && $_SESSION['resume_submitted'] === true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">       <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class=" bg-gradient-to-r from-blue-500 to-purple-600 min-h-screen flex items-center justify-center">

<div class="card shadow-lg" style="width: 28rem;">
    <div class="card-body text-center">
        <h2 class="card-title mb-4">Quiz Results</h2>
        
        <div class="rounded-circle border border-5 border-primary d-flex flex-column align-items-center justify-content-center mx-auto mb-4" 
             style="width: 120px; height: 120px;">
            <span class="fs-2 fw-bold text-primary"><?= round($score_percent,2) ?>%</span>
        </div>

        <table class="table table-borderless text-start">
            <tr>
                <td><span class="badge bg-success">Correct</span></td>
                <td class="text-end fw-bold"><?= $correct ?></td>
            </tr>
            <tr>
                <td><span class="badge bg-danger">Incorrect</span></td>
                <td class="text-end fw-bold"><?= $wrong ?></td>
            </tr>
            <tr>
                <td><span class="badge bg-warning text-dark">Timed Out</span></td>
                <td class="text-end fw-bold"><?= $missed ?></td>
            </tr>
            <tr class="border-top">
                <td class="fw-bold">Total Questions</td>
                <td class="text-end fw-bold"><?= $total_questions ?></td>
            </tr>
        </table>
        <hr>
        <?php
        $_SESSION['resume_submitted'] = true;
        ?>
        <?php if ($eligible_for_resume): ?>
    <div class="mt-4 p-3 border border-success rounded bg-light">
        <?= $is_already_submitted ? 'Submission Received' : 'Great Job! You\'ve Qualified.' ?>
        <p class="small text-muted">Since you scored above 70%, you can submit your resume for consideration.</p>
        <?php if (!$is_already_submitted): ?>
        <form id="resumeForm" action="upload_resume.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="MAX_FILE_SIZE" value="1048576">
            <div class="mb-3">
                <input class="form-control form-control-sm" type="file" name="resume" accept=".pdf,.doc,.docx" required>
            <div id="fileError" class="text-danger small mt-1 d-none">File is too large. Max size is 1MB.</div>
            </div>
            <button type="submit" id="uploadBtn" class="btn btn-success btn-sm w-100">Upload Resume</button>
        </form>
    </div>
    <?php else: ?>
            <div class="alert alert-success d-flex align-items-center mb-0" role="alert">
                <svg class="bi flex-shrink-0 me-2" width="24" height="24" fill="currentColor"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97 3.03a.75.75 0 0 0-1.08.022L7.477 14.41l-2.323-2.323a.75.75 0 0 0-1.06 1.06L6.97 16.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>
                <div>Your resume has been saved. Thank you!</div>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="mt-4 p-3 border border-secondary rounded bg-light text-center">
            <p class="text-muted mb-0">Score at least 70% to unlock the resume upload option.</p>
        </div>
    <?php endif; ?>
        <hr>
        <a href="clearcache.php" class="btn btn-primary w-100">Try Again</a>
    </div>
</div>


</body>
</html>
<script>
    document.getElementById('resumeForm').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('resumeFile');
    const file = fileInput.files[0];
    const errorDiv = document.getElementById('fileError');
    const maxSize = 1 * 1024 * 1024;

    if (file && file.size > maxSize) {
        e.preventDefault();
        errorDiv.classList.remove('d-none');
        fileInput.classList.add('is-invalid');
        return false;
    }

    errorDiv.classList.add('d-none');
    fileInput.classList.remove('is-invalid');
});
</script>