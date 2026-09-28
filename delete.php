<?php

session_start();

$data = mysqli_connect("localhost","root","","collegepr",3307);

if($_GET['student_id'])
{
    $user_id = $_GET['student_id'];

    $sql = "DELETE FROM user WHERE id = '$user_id' ";

    $result = mysqli_query($data,$sql);

    if($result)
    {

        $_SESSION['message'] = 'Student Data Delete Is Successful !';
        header("location:view_student.php");
    }

}

?>
