<?php

    require_once '../../includes/GeneralConfig.php';

    session_start();

    if(empty($_SESSION['role_id'])){
        header('Location: '. BASE_URL);
    }

    if($_SESSION['role_id'] !== 1){
        header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );
        die ("Access Denied!");
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once 'navbar.php'; ?>
    <!-- Content -->
    <div class="container mt-5">
        <?php

        if(isset($_GET['deleteUser'])){
            if($_GET['deleteUser'] == 'success'){
                echo '<span style="color: green;">User Successfully Deleted</span>';
            }else{
                echo '<span style="color: red;">Failed to Delete User</span>';
            }
        }

        if(isset($_GET['updateUser'])){
            if($_GET['updateUser'] == 'success'){
                echo '<span style="color: green;">User Details Successfully Updated</span>';
            }
        }

        ?>

        <span></span>
        <h1>Users</h1>
        <table id="data-table" class="table table-striped">
            <thead>

                <tr>
                    <th>Sl.No</th>
                    <th>Username</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Gender</th>
                    <th>DOB</th>
                    <th>Profile</th>
                    <th>Signature</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="10">...loading</td></tr>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function () {
        $.ajax({
            url: '<?php echo BASE_URL; ?>json/handler.php?users',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                var tableBody = $('#data-table tbody');
                    tableBody.html('');
                $.each(data, function (index, item) {

                    let proPic = `<a target="_blank" href="../../uploads/${item.profile_picture}"><img style="height: 20px;" src="../../uploads/${item.profile_picture}"></a>`;

                    let signature = `<a target="_blank" href="../../uploads/${item.signature}"><img style="height: 20px;" src="../../uploads/${item.signature}"></a>`;

                    let update = `<a href="./update.php?userID=${item.id}" class="btn btn-primary btn-sm">Update</a>`;

                    let deleteBtn = `<a onclick="confirm('Are you sure, Do you want to delete?')"  href="./handler.php?deleteUser=${item.id}" class="btn btn-danger btn-sm">Delete</a>`;

                    let btn =  update + ' ' + deleteBtn;

                    var row = $('<tr>');
                    row.append($('<td>').text(index + 1));
                    row.append($('<td>').text(item.username));
                    row.append($('<td>').text(item.mobile));
                    row.append($('<td>').text(item.email));
                    row.append($('<td>').text(item.address));
                    row.append($('<td>').text(item.gender));
                    row.append($('<td>').text(item.dob));
                    row.append($('<td>').html(proPic));
                    row.append($('<td>').html(signature));
                    row.append($('<td>').text(item.approval_status));
                    row.append($('<td>').html(btn));

                    tableBody.append(row);
                });
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });
    </script>
</body>
</html>