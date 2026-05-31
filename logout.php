<?php
/**
 * Logout Page
 * KiosDigital PPOB
 */
session_start();
session_unset();
session_destroy();

header("Location: /login.php");
exit();
?>
