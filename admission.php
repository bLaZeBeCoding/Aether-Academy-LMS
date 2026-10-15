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

$sql = "SELECT * FROM admission ORDER BY created_at DESC";

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
                            <th style="min-width: 180px;">Submitted At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($info=$result->fetch_assoc()) { 
                            $timestamp = !empty($info['created_at']) ? strtotime($info['created_at']) : 0;
                            $formatted_date = !empty($info['created_at']) ? date('M d, Y - h:i A', $timestamp) : 'N/A';
                        ?>
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
                            <td data-order="<?php echo $timestamp; ?>">
                                <span class="badge py-2 px-3 rounded-pill" style="background: rgba(249,115,22,0.12); color: var(--accent-orange); border: 1px solid rgba(249,115,22,0.3); font-size: 0.82rem; font-weight: 600;">
                                    <i class="fas fa-clock me-1"></i><?php echo $formatted_date; ?>
                                </span>
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
            "order": [[4, "desc"]],
            "pageLength": 10,
            "language": {
                "search": "Quick Search:"
            }
        });
    });
</script>
    
</body>
</html>
