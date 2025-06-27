<?php
session_start();
require_once '../config/site.php';

// Oturum değişkenlerini temizle
$_SESSION = array();

// Oturum çerezini sil
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Oturumu sonlandır
session_destroy();

// Ana sayfaya yönlendir
header('Location: ' . getBasePath() . '/admin/login.php');
exit();
?>
