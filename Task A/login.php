<?php

    require_once 'includes/GeneralConfig.php';

    $errors = [];
    $err = '';

    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        require_once 'includes/DataBase.php';

        $db = new DataBase;

        $chkErrors = $db->loginFormValidation();

        if(count($chkErrors) > 0){
            $errors = $chkErrors;
        }else{
            $err = $db->login();
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>

    <style type="text/css">
        .error {
            color: red;
        }
    </style>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Login</h4>
                    </div>
                    <div class="card-body">
                        <p style="color:red;"><?php echo $err; ?></p>
                        <form id="loginForm" action="" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                                <span class="error"><?php echo (!empty($errors['username'])) ? $errors['username'] : ''; ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <span class="error"><?php echo (!empty($errors['password'])) ? $errors['password'] : ''; ?></span>
                            </div>
                            <button type="submit" class="btn btn-primary">Login</button>
                            <a href="<?php echo BASE_URL; ?>registration.php" class="btn btn-info">Register</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>