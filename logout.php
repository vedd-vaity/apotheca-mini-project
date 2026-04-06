<?php
session_start();
session_destroy();
header("Location: login.php");
setcookie("remember_user", "", time() - 3600, "/");
exit();
?>
