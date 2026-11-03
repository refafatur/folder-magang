<?php
session_start();
session_destroy();
$_SESSION = array();
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}
header("Location: ../login.php");
exit();
?>
