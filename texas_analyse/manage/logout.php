<?php
session_start();
unset($_SESSION['uu_auth']);
session_unset();
session_destroy(); 
header("location:login.php");
?>