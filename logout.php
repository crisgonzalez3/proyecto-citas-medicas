<?php
session_start();
session_unset();
session_destroy();
setcookie('user', '', time() - 3600, '/', '', true, true); 
header('Location: login.php');
exit();
?>
