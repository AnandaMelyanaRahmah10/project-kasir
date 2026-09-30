<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    header("Location: ../kasir/index.php");
    exit;
}

$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($id > 0) {

    mysqli_query(
        $conn,
        "DELETE FROM produk
         WHERE id = $id"
    );

}

header("Location: index.php");

exit;