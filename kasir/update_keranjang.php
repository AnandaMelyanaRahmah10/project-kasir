<?php

session_start();

if (!isset($_SESSION['user_id'])) {

    header("Location: ../auth/login.php");

    exit;
}


$index = isset($_POST['index'])
    ? (int) $_POST['index']
    : -1;


$qty = isset($_POST['qty'])
    ? (int) $_POST['qty']
    : 0;


if (
    $index < 0 ||
    !isset($_SESSION['keranjang'][$index])
) {

    header("Location: index.php");

    exit;
}


if ($qty <= 0) {

    unset(
        $_SESSION['keranjang'][$index]
    );

    $_SESSION['keranjang'] =
        array_values(
            $_SESSION['keranjang']
        );

    header("Location: index.php");

    exit;
}


$stok =
    $_SESSION['keranjang'][$index]['stok'];


if ($qty > $stok) {

    echo "
    <script>

        alert(
            'Jumlah melebihi stok tersedia.'
        );

        window.location='index.php';

    </script>
    ";

    exit;
}


$_SESSION['keranjang'][$index]['qty'] =
    $qty;


header("Location: index.php");

exit;