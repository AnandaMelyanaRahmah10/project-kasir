<?php

session_start();

if (!isset($_SESSION['user_id'])) {

    header("Location: ../auth/login.php");

    exit;
}


$_SESSION['keranjang'] = [];


header("Location: index.php");

exit;