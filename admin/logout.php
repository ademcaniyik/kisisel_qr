<?php
session_start();

// Oturum değişkenlerini temizle
$_SESSION = array();

// Oturum çerezini sil
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Oturumu sonlandır
session_destroy();

// Ana sayfaya yönlendir
header('Location: /kisisel_qr_canli/admin/login.php');
exit();
?>
