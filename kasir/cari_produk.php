<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {

    header("Location: ../auth/login.php");

    exit;
}


$keyword = $_GET['keyword'] ?? '';

$keyword = trim($keyword);


if ($keyword == '') {

    header("Location: index.php");

    exit;
}


$keyword_db = mysqli_real_escape_string(
    $conn,
    $keyword
);


$query = mysqli_query(
    $conn,
    "SELECT
        id,
        barcode,
        nama_produk,
        harga_jual,
        stok
     FROM produk
     WHERE nama_produk LIKE '%$keyword_db%'
     AND status = 'aktif'
     ORDER BY nama_produk ASC"
);


// Redirect ke halaman kasir
// dengan keyword tetap tampil

header(
    "Location: index.php?keyword="
    . urlencode($keyword)
);

exit;