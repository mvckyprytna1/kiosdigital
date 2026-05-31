<?php
/**
 * Dashboard Router
 * KiosDigital PPOB
 */
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    redirect('/login.php');
}

$role = $_SESSION['role'];

switch ($role) {
    case 'owner':
        redirect('/owner/index.php');
        break;
    case 'admin':
        redirect('/admin/index.php');
        break;
    case 'staff':
        redirect('/staff/index.php');
        break;
    case 'user':
    default:
        redirect('/user/index.php');
        break;
}
?>
