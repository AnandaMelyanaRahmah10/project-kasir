<?php

session_start();

if (!isset($_SESSION['user_id'])) {

    header("Location: ../auth/login.php");

    exit;
}


$index = isset($_GET['index'])
    ? (int) $_GET['index']
    : -1;


if (
    $index >= 0 &&
    isset($_SESSION['keranjang'][$index])
) {

    unset(
        $_SESSION['keranjang'][$index]
    );


    $_SESSION['keranjang'] =
        array_values(
            $_SESSION['keranjang']
        );
}


header("Location: index.php");

exit;