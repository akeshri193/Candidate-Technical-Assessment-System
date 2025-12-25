<?php
session_start();

$timeout_duration = 1800; 
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout_duration)) {
    session_unset();
    session_destroy();
    header("Location: index.php");
}
$_SESSION['LAST_ACTIVITY'] = time();

if (isset($_SESSION["quiz_complete"])) {
    header("Location: results.php");
    exit();
}


if (!isset($_SESSION["current_question"])) {
    $_SESSION["current_question"] = 0;
    $_SESSION["points"] = 0;
    $_SESSION["user_answers"] = [];
}

if (!isset($_SESSION["answered_questions"])) {
    $_SESSION["answered_questions"] = [];
}

$total = $_SESSION["quescount"];
$current = $_SESSION["current_question"];
$seconds_per_question = 10;

$is_locked = in_array($current, $_SESSION["answered_questions"]);


$is_complete = isset($_SESSION["quiz_complete"]);

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'next' && $current < $total - 1) {
        $_SESSION["current_question"]++;
    } elseif ($_GET['action'] == 'prev' && $current > 0) {
        $_SESSION["current_question"]--;
    }
    header("Location: Assess.php");
    exit();
}

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$is_locked) {
    $user_choice = $_POST['optionsel'] ?? 'TIMED_OUT';

    $_SESSION["user_answers"][$current] = $user_choice;
    $_SESSION["answered_questions"][] = $current;

    $_SESSION["answered_questions"][] = $current;

    $correct_answer = $_SESSION["questions"][$current]["answer"];

    if ($user_choice === $correct_answer) {
        $_SESSION["points"]++;
    }

    if ($current < $total - 1) {
        $_SESSION["current_question"]++;
    } else {
        $_SESSION["quiz_complete"] = true;
    }

    unset($_SESSION['q_start_time']); // Reset timer
    header("Location: Assess.php");
    exit();
}

if (!isset($_SESSION['q_start_time'])) {
    $_SESSION['q_start_time'] = time();
}

$elapsed = time() - $_SESSION['q_start_time'];
$time_remaining = max(0, $seconds_per_question - $elapsed);

if ($time_remaining <= 0 && !isset($_SESSION["quiz_complete"])) {
    if ($current < $total - 1) {
        $_SESSION["current_question"]++;
    } else {
        $_SESSION["quiz_complete"] = true;
    }
    unset($_SESSION['q_start_time']);
    header("Location: Assess.php");
    exit();
}

$curr_idx = $_SESSION["current_question"];
$currques = $_SESSION["questions"][$curr_idx];

if ($time_remaining <= 0 && !isset($_SESSION["quiz_complete"])) {
    if (!in_array($current, $_SESSION["answered_questions"])) {
        $_SESSION["user_answers"][$current] = 'TIMED_OUT';
        $_SESSION["answered_questions"][] = $current;
    }

    if ($current < $total - 1) {
        $_SESSION["current_question"]++;
    } else {
        $_SESSION["quiz_complete"] = true;
        header("Location: Results.php");
    exit();
    }

    unset($_SESSION['q_start_time']);
    header("Location: Assess.php");
    exit();
}
?>
<?php 
  $accuracy = ($current > 0) ? ($_SESSION["points"] / $current) * 100 : 0;
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
  
  <div>
    <div class="card shadow-lg" style="width: 22rem;height: auto;">
        <?php 
$disabled = in_array($current, $_SESSION["answered_questions"]) ? 'disabled' : ''; 
?>
    <form method="post" id="quest" action="Assess.php">
            <?php 
  $progress = ($current / $total) * 100; 
?>
<div class="progress mb-4" style="height: 10px;">
  <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" 
       role="progressbar" 
       style="width: <?= $progress ?>%;" 
       aria-valuenow="<?= $progress ?>" 
       aria-valuemin="0" 
       aria-valuemax="100">
  </div>
</div>

<div class="text-center mb-2">
    <small class="text-muted">Current Accuracy: <?= round($accuracy) ?>%</small>
</div>


<?php
$is_answered = in_array($current, $_SESSION["answered_questions"] ?? []);
$saved_choice = $_SESSION["user_answers"][$current] ?? null;
$disabled = $is_answered ? 'disabled' : '';
?>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <span class="badge bg-light text-dark border">Question <?= $current + 1 ?></span>

    <span class="badge bg-success p-2">
        Score: <?= $_SESSION["points"] ?>
    </span>

    <div id="timer-container" class="badge bg-danger p-2 fs-6">
        Time Left: <span id="seconds-display"><?= $time_remaining ?></span>s
    </div>
</div>

<?php
    $questionss = $_SESSION["questions"];
    $currquesdet=$_SESSION["current_question"];
    $currques=$questionss[$currquesdet];
    $options=$currques["options"];
  echo '<div class="card-header h-25">'.
  ($_SESSION["current_question"]+1).". ".$currques["question"].
  '</div>"' ?>

  <div class="form-check">
  <ul class="list-group list-group-flush">
    <?php foreach ($options as $index => $opt): ?>
        <?php 
                $is_checked = ($saved_choice === $opt) ? 'checked' : '';
            ?>
            <li class="list-group-item">
                <input class="form-check-input" type="radio" 
                       value="<?= ($opt) ?>" 
                       name="optionsel" 
                       id="radio<?= $index ?>"
                       <?= $is_checked ?> 
                       <?= $disabled ?>>
                <label class="form-check-label" for="radio<?= $index ?>">
                    <?= htmlspecialchars($opt) ?>
                </label>
            </li>
        <?php endforeach; ?>
        <br>
<div class="d-flex justify-content-center mt-3 p-3">
            <?php if (!$is_answered): ?>
                <input type="submit" class="btn btn-primary w-50" value="Submit">
<?php else: ?>
        <?php if ($saved_choice === 'TIMED_OUT'): ?>
            <span class="badge bg-warning text-dark p-2">Time Expired - No Answer Recorded</span>
        <?php else: ?>
            <span class="badge bg-secondary p-2">Question Submitted</span>
     <?php endif; ?>
     <?php endif; ?>
        </div>
  
</ul></div>';
  </form>
</div>
<br>
<?php if (!$is_complete): ?>
<div class="container text-center">
  <div class="row">
    <div class="col">
<a href="?action=prev" class="btn btn-success w-100 <?= ($current == 0) ? 'disabled' : '' ?>">Previous</a>
    </div>
    <div class="col">
<a href="?action=next" class="btn btn-danger w-100 <?= ($current == $total - 1) ? 'disabled' : '' ?>">Skip/Next</a>    </div>
  </div>
  </div>
  <?php endif; ?>
  <div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <strong class="me-auto">Error</strong>
      <small>few seconds ago</small>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
      Please select Option to proceed
    </div>
  </div>
</div>

</body>
</html>
<script>
    const form = document.getElementById('quest');
    form.addEventListener('submit', (e) => {
        const radios = document.querySelectorAll('input[type="radio"]');
        for(const radio of radios) {
            if(radio.checked) {
                var checkedOne = true;
                break;
            }
        }

        if (!checkedOne) {
            e.preventDefault();

            const toastLiveExample = document.getElementById('liveToast')
            const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample)
            toastBootstrap.show();        }
    });

    let timeLeft = <?= $time_remaining ?>;
    const display = document.querySelector('#seconds-display');
    const timerContainer = document.querySelector('#timer-container');
    const quizForm = document.getElementById('quest');

    const countdown = setInterval(() => {
        timeLeft--;
        display.textContent = timeLeft;

        if (timeLeft <= 5) {
            timerContainer.classList.add('animate-pulse'); 
            timerContainer.style.backgroundColor = "#ff0000";
        }

        if (timeLeft <= 0) {
            clearInterval(countdown);
            
            const selected = document.querySelector('input[name="optionsel"]:checked');
            if (!selected) {
                const timeoutInput = document.createElement('input');
                timeoutInput.type = 'hidden';
                timeoutInput.name = 'optionsel';
                timeoutInput.value = 'TIMED_OUT';
                quizForm.appendChild(timeoutInput);
            }
            quizForm.submit();
        }
    }, 1000);

    
</script>
