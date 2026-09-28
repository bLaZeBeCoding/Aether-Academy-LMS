<?php

session_start();

// if no username, go to (login.php)
if(!isset($_SESSION['username']))
{
    header("location:login.php");
}

// student type redirected to login.php
elseif($_SESSION['usertype']=='student')
{
    header("location:login.php");
}


$data = mysqli_connect("localhost","root","","collegepr",3307);

$sql = "SELECT * FROM admission";

$result = mysqli_query($data,$sql);


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <?php include 'admin_css.php'; ?>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="content">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h1 class="mb-4">Applied For Admissions</h1>

            <div class="table-responsive">
                <table id="admissionTable" class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($info=$result->fetch_assoc()) { ?>
                        <tr>
                            <td class="fw-bold">
                                <?php echo htmlspecialchars($info['name']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($info['email']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($info['phone']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($info['message']); ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#admissionTable').DataTable({
            "pageLength": 10,
            "language": {
                "search": "Quick Search:"
            }
        });
    });
</script>
    
</body>
</html>
