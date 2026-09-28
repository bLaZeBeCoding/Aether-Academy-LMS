<?php
error_reporting(0);
session_start(); // Initializes or resumes a session for the user

// 1. DATABASE CONNECTION
$data = mysqli_connect('localhost','root','','collegepr',3307);

if($data===false){
    die("connection error!");
}

// 2. HTTP METHOD CHECK
if($_SERVER["REQUEST_METHOD"]=="POST")
{
    // 3. INPUT VALIDATION & SECURITY
    $name = mysqli_real_escape_string($data, trim($_POST['username']));
    $pass = $_POST['password'];

    // EASTER EGG
    if($name === 'uday_creator') {
        $_SESSION['easter_egg'] = true;
        header("location:login.php");
        exit;
    }

    // 4. DATABASE QUERY
    $sql = "SELECT * FROM user WHERE username = '$name'";
    $result = mysqli_query($data, $sql);

    // Fetch the associative array containing the user's data
    if($row = mysqli_fetch_assoc($result))
    {
        $isAuthenticated = false;

        // 5. PASSWORD VERIFICATION
        if(password_verify($pass, $row['password'])) {
            $isAuthenticated = true;
        } 
        // 6. LEGACY FALLBACK & AUTO-UPGRADE
        elseif ($row['password'] === $pass) {
            $isAuthenticated = true;
            $newHash = password_hash($pass, PASSWORD_DEFAULT); // Generate secure hash
            $updateSql = "UPDATE user SET password = '$newHash' WHERE id = '{$row['id']}'";
            mysqli_query($data, $updateSql); // Update DB with new hash
        }

        // 7. SESSION MANAGEMENT & ROUTING
        if ($isAuthenticated) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['usertype'] = $row['usertype'];

            if($row["usertype"] == "student") {
                header("location:studenthome.php");
            }
            elseif($row["usertype"] == "admin") {
                header("location:adminhome.php");
            }
        }
        else {
            $message= "username or password do not match";
            $_SESSION['loginMessage']=$message;
            header("location:login.php");
        }
    }
    else {
        $message= "username or password do not match";
        $_SESSION['loginMessage']=$message;
        header("location:login.php");
    }
}
?>
