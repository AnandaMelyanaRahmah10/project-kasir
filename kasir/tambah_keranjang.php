<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}


if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}


$barcode = $_POST['barcode'] ?? '';

$barcode = trim($barcode);


if ($barcode == '') {

    header("Location: index.php");
    exit;

}


$barcode_db = mysqli_real_escape_string(
    $conn,
    $barcode
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
     WHERE barcode = '$barcode_db'
     AND status = 'aktif'
     LIMIT 1"
);


if (mysqli_num_rows($query) == 0) {

    echo "
    <script>
        alert('Produk dengan barcode tersebut tidak ditemukan.');
        window.location='index.php';
    </script>
    ";

    exit;
}


$produk = mysqli_fetch_assoc($query);


if ($produk['stok'] <= 0) {

    echo "
    <script>
        alert('Stok produk habis.');
        window.location='index.php';
    </script>
    ";

    exit;
}


// ==========================================
// CEK APAKAH SUDAH ADA DI KERANJANG
// ==========================================

$ditemukan = false;


foreach (
    $_SESSION['keranjang']
    as $index => $item
) {

    if ($item['id'] == $produk['id']) {

        $qty_baru =
            $item['qty'] + 1;


        if ($qty_baru > $produk['stok']) {

            echo "
            <script>
                alert('Jumlah melebihi stok tersedia.');
                window.location='index.php';
            </script>
            ";

            exit;
        }


        $_SESSION['keranjang'][$index]['qty'] =
            $qty_baru;


        $ditemukan = true;

        break;
    }
}


// ==========================================
// JIKA BELUM ADA
// ==========================================

if (!$ditemukan) {

    $_SESSION['keranjang'][] = [

        'id' => $produk['id'],

        'barcode' => $produk['barcode'],

        'nama_produk' =>
            $produk['nama_produk'],

        'harga' =>
            (float) $produk['harga_jual'],

        'stok' =>
            (int) $produk['stok'],

        'qty' => 1

    ];

}


header("Location: index.php");

exit;