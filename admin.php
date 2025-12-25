<?php
session_start();

$timeout_duration = 1800; 
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout_duration)) {
    session_unset();
    session_destroy();
    header("Location: index.php");
}
$_SESSION['LAST_ACTIVITY'] = time();

$hashed_password ="$2y$10$0/1n7HpB57Vr./mowjz3T.G8JF88Vo9831cf5bgo5AkXU4nevZIM.";

if (!isset($_SESSION['admin_logged_in'])) {
    if (isset($_POST['password']) && password_verify($_POST['password'], $hashed_password)) {
        $_SESSION['admin_logged_in'] = true;
        session_regenerate_id(true);
    } else {
        die('
        <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">       <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class=" bg-gradient-to-r from-blue-500 to-purple-600 min-h-screen flex items-center justify-center">
<div class="card shadow-lg" style="width: 20rem;">
  <div class="card-body">
    <h5 class="card-title">Admin Login</h5><hr>
        <div >
            <form method="post" action="admin.php">
            <div class="mb-3">
            <label for="password" class="form-label" ><h6>Password</h6></label>
            <input type="password" class="form-control" name="password" id="password" placeholder="Enter Password" required>
            </div>
            <button class="btn btn-primary" type="submit">Login</button>
            </form>
        </div>
        </div>
</div>
        </body>
</html>');
    }
}

if (isset($_POST['delete_file']) && isset($_SESSION['admin_logged_in'])) {
    $file_to_delete = "uploads/" . basename($_POST['delete_file']); 
    if (file_exists($file_to_delete)) {
        unlink($file_to_delete);
        $message = "File deleted successfully.";
    } else {
        $error = "File not found.";
    }
}

if(isset($_GET['logout'])) {
     session_destroy(); 
     header("Location: admin.php"); 
     exit();
    }
$files = glob("uploads/*.{pdf,doc,docx}", GLOB_BRACE);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">       <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class=" bg-gradient-to-r from-blue-500 to-purple-600 min-h-screen flex items-center justify-center">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Candidate Resumes</h2>
            <a href="?logout=1" class="btn btn-sm btn-outline-danger">Logout</a>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Upload Date</th>
                            <th>File Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($files)): ?>
                            <tr><td colspan="3" class="text-center">No resumes uploaded yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($files as $file): ?>
                                <tr>
                                    <td><?= date("F d, Y H:i", filemtime($file)) ?></td>
                                    <td><?= htmlspecialchars(basename($file)) ?></td>
                                    <td class="text-end">
                                        <div class="btn-group" >
                                        <a href="<?= $file ?>" class="btn btn-primary btn-sm" download>Download</a>
                                        
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this resume?');">
                                        <input type="hidden" name="delete_file" value="<?= htmlspecialchars(basename($file)) ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>