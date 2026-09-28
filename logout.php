<?php
session_start();
session_destroy();

if (isset($_GET['reason']) && $_GET['reason'] == 'timeout') {
    header("location:login.php?timeout=1");
} else {
    header("location:login.php");
}
?>
