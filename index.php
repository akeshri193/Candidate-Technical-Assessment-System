<?php 
session_start();
if (isset($_SESSION["quiz_complete"])) {
    header("Location: results.php");
    exit();
}
elseif (isset($_SESSION["current_question"])) {
    header("Location: Assess.php");
    exit();
}
?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['languages']) && is_array($_POST['languages']) && count($_POST['languages']) > 0) {   
      $_SESSION["quescount"]=count($_POST['languages'])*10;
      $_SESSION['selected_languages']=$_POST['languages'];
      $langs=$_POST["languages"];
      $intg=range(0,19);
      shuffle($intg);
      $unique_index=array_slice($intg,0,10);
      $quesfile=file_get_contents("question.json");
      $ques=json_decode($quesfile, true);
      foreach($langs as $lang){
        $queslang=$ques[$lang];
        foreach($unique_index as $indx){
          $selected_questions[]=$queslang[$indx];
        }
      }
      $_SESSION["questions"]=$selected_questions;
      $_SESSION["quescount"]=count($selected_questions);

        header('Location: Assess.php');
        exit();
    }
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Technical Assessment System</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">       <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class=" bg-gradient-to-r from-blue-500 to-purple-600 min-h-screen flex items-center justify-center">
<div class="card shadow-lg" style="width: 20rem;">
  <div class="card-body">
    <h5 class="card-title" style="display: flex; align-items: center; gap: 10px;"><img src="pc_display.svg" height="16" width="16">Technical Assessment</h5><hr><br>
    <p>
    Select your preferred programming languages:
    </p>
    <form method="post" id="selsubj" action="index.php">
    <div class="form-check">
  <input class="form-check-input" name="languages[]" type="checkbox" value="Python" id="checkChecked">
  <label class="form-check-label" for="checkChecked">
    Python
  </label>
</div>
<div class="form-check">
  <input class="form-check-input" name="languages[]" type="checkbox" value="JavaScript" id="checkChecked" >
  <label class="form-check-label" for="checkChecked">
    JavaScript
  </label>
</div>
<div class="form-check">
  <input class="form-check-input" name="languages[]" type="checkbox" value="Java" id="checkChecked" >
  <label class="form-check-label" for="checkChecked">
    Java
  </label>
</div>
<div class="form-check">
  <input class="form-check-input" name="languages[]" type="checkbox" value="C" id="checkChecked" >
  <label class="form-check-label" for="checkChecked">
    C
  </label>
</div><br>
    <input type="submit" class="btn btn-primary" value="Start Test">
    </form>
  </div>
</div>
 

<div class="toast-container position-fixed bottom-0 end-0 p-3" >
  <div id="liveToast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        Please select at least one programming language to proceed.
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>


</body>
</html>
<script>
    const form = document.getElementById('selsubj');
    form.addEventListener('submit', (e) => {
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        for(const checkbox of checkboxes) {
            if(checkbox.checked) {
                var checkedOne = true;
                break;
            }
        }

        if (!checkedOne) {
            e.preventDefault();

            const toastLiveExample = document.getElementById('liveToast')
            const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLiveExample)
            toastBootstrap.show();
            // alert("Please select at least one programming language to proceed.");
        }
    });
</script>
