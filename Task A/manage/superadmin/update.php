<?php
    
    session_start();

    if(empty($_SESSION['role_id'])){
        header('Location: '. BASE_URL);
    }

    if($_SESSION['role_id'] !== 1){
        header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );
        die ("Access Denied!");
    }

    $err = '';
    $succ = '';
    $errors = [];

    require_once '../../includes/DataBase.php';

    $db = new DataBase;

    $user_data = $db->get_user($_GET['userID']);

    if(!$user_data){
        die('User not found');
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        $chkErrors = $db->registerFormValidation();

        if(count($chkErrors) > 0){
            $errors = $chkErrors;
        }else{

            $reg = $db->update_user($_GET['userID']);

            if($reg === true){
                header('Location: ' . BASE_URL . 'manage/superadmin/?updateUser=success');
                //$succ = 'User Details Successfully Updated';
            }else{
                 $err = $reg;
            }

        }

    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once 'navbar.php'; ?>
    <!-- Content -->
     <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Update Registration</h4>
                    </div>
                    <div class="card-body">
                        <p style="color:red;"><?php echo $err; ?></p>
                        <p style="color:green;"><?php echo $succ; ?></p>
                        <form id="registrationForm" action="" method="POST" enctype="multipart/form-data" autocomplete="off">
                            <input type="hidden" name="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input value="<?php echo $user_data['username']; ?>" type="text" class="form-control" id="username" name="username" required>
                                <span class="error"><?php echo (!empty($errors['username'])) ? $errors['username'] : ''; ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <span class="error"><?php echo (!empty($errors['password'])) ? $errors['password'] : ''; ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="mobile" class="form-label">Mobile</label>
                                <input value="<?php echo $user_data['mobile']; ?>" type="tel" class="form-control" id="mobile" name="mobile" required>
                                <span class="error"><?php echo (!empty($errors['mobile'])) ? $errors['mobile'] : ''; ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input value="<?php echo $user_data['email']; ?>" type="email" class="form-control" id="email" name="email" required>
                                <span class="error"><?php echo (!empty($errors['email'])) ? $errors['email'] : ''; ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required><?php echo $user_data['address']; ?></textarea>
                                <span class="error"><?php echo (!empty($errors['address'])) ? $errors['address'] : ''; ?></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-check-label">Gender</label>
                                <div class="form-check">
                                    <input <?php echo $user_data['gender'] == 'Male' ? 'checked' : ''; ?> class="form-check-input" type="radio" name="gender" id="male" value="Male" required>
                                    <label class="form-check-label" for="male">Male</label>
                                </div>
                                <div class="form-check">
                                    <input <?php echo $user_data['gender'] == 'Female' ? 'checked' : ''; ?> class="form-check-input" type="radio" name="gender" id="female" value="Female" required>
                                    <label class="form-check-label" for="female">Female</label>
                                </div>
                                <div class="form-check">
                                    <input <?php echo $user_data['gender'] == 'Other' ? 'checked' : ''; ?> class="form-check-input" type="radio" name="gender" id="other" value="Other" required>
                                    <label class="form-check-label" for="other">Other</label>
                                </div>
                                <span class="error"><?php echo (!empty($errors['gender'])) ? $errors['gender'] : ''; ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="dob" class="form-label">Date of Birth</label>
                                <input value="<?php echo $user_data['dob']; ?>" type="date" class="form-control" id="dob" name="dob" required>
                                <span class="error"><?php echo (!empty($errors['dob'])) ? $errors['dob'] : ''; ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="profilePicture" class="form-label">Profile Picture</label>
                                <input type="file" class="form-control" id="profilePicture" name="profilePicture" accept="image/*" required>
                                <span class="error"><?php echo (!empty($errors['profilePicture'])) ? $errors['profilePicture'] : ''; ?></span>
                            </div>
                            <div class="mb-3">
                                <label for="signature" class="form-label">Signature Picture</label>
                                <input type="file" class="form-control" id="signature" name="signature" accept="image/*" required>
                                <span class="error"><?php echo (!empty($errors['signature'])) ? $errors['signature'] : ''; ?></span>
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>