<?php

session_start();

require_once "../config/database.php";


// ==========================================
// CEK LOGIN
// ==========================================

if (!isset($_SESSION['user_id'])) {

    header("Location: ../auth/login.php");
    exit;

}


// ==========================================
// CEK ROLE
// ==========================================

if ($_SESSION['role'] != 'admin') {

    header("Location: ../kasir/index.php");
    exit;

}


// ==========================================
// AMBIL ID KATEGORI
// ==========================================

$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;


if ($id <= 0) {

    header("Location: index.php");
    exit;

}


// ==========================================
// CEK APAKAH KATEGORI DIGUNAKAN PRODUK
// ==========================================

$cek_produk = mysqli_query(
    $conn,
    "SELECT id
     FROM produk
     WHERE kategori_id = $id
     LIMIT 1"
);


if (!$cek_produk) {

    die(
        "Terjadi kesalahan database: "
        . mysqli_error($conn)
    );

}


// ==========================================
// JIKA MASIH DIGUNAKAN PRODUK
// ==========================================

if (mysqli_num_rows($cek_produk) > 0) {

    echo "

    <script>

        alert(
            'Kategori tidak dapat dihapus karena masih digunakan oleh produk.'
        );

        window.location.href = 'index.php';

    </script>

    ";

    exit;

}


// ==========================================
// HAPUS KATEGORI
// ==========================================

$hapus = mysqli_query(
    $conn,
    "DELETE FROM kategori
     WHERE id = $id"
);


// ==========================================
// HASIL HAPUS
// ==========================================

if ($hapus) {

    echo "

    <script>

        alert(
            'Kategori berhasil dihapus.'
        );

        window.location.href = 'index.php';

    </script>

    ";

    exit;

} else {

    echo "

    <script>

        alert(
            'Kategori gagal dihapus: "
            . mysqli_error($conn) .
            "'
        );

        window.location.href = 'index.php';

    </script>

    ";

    exit;

}