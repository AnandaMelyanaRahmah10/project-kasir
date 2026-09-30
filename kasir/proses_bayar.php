<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {

    header("Location: ../auth/login.php");

    exit;
}


if (
    !isset($_SESSION['keranjang']) ||
    count($_SESSION['keranjang']) == 0
) {

    echo "
    <script>

        alert(
            'Keranjang masih kosong.'
        );

        window.location='index.php';

    </script>
    ";

    exit;
}


$bayar = isset($_POST['bayar'])
    ? (float) $_POST['bayar']
    : 0;


$total = 0;


foreach (
    $_SESSION['keranjang']
    as $item
) {

    $total +=
        $item['harga'] *
        $item['qty'];

}


if ($bayar < $total) {

    echo "
    <script>

        alert(
            'Uang pembayaran kurang.'
        );

        window.location='index.php';

    </script>
    ";

    exit;
}


$kembalian =
    $bayar - $total;


// ==========================================
// NOMOR TRANSAKSI
// ==========================================

$nomor_transaksi =
    'TRX-'
    . date('YmdHis')
    . '-'
    . rand(100, 999);


$user_id =
    (int) $_SESSION['user_id'];


// ==========================================
// MULAI TRANSAKSI DATABASE
// ==========================================

mysqli_begin_transaction($conn);


try {


    // ======================================
    // SIMPAN TRANSAKSI
    // ======================================

    $nomor_db =
        mysqli_real_escape_string(
            $conn,
            $nomor_transaksi
        );


    $insert_transaksi = mysqli_query(
        $conn,
        "INSERT INTO transaksi
        (
            nomor_transaksi,
            user_id,
            total,
            bayar,
            kembalian
        )
        VALUES
        (
            '$nomor_db',
            $user_id,
            $total,
            $bayar,
            $kembalian
        )"
    );


    if (!$insert_transaksi) {

        throw new Exception(
            mysqli_error($conn)
        );

    }


    $transaksi_id =
        mysqli_insert_id($conn);


    // ======================================
    // SIMPAN DETAIL + KURANGI STOK
    // ======================================

    foreach (
        $_SESSION['keranjang']
        as $item
    ) {

        $produk_id =
            (int) $item['id'];

        $barcode =
            mysqli_real_escape_string(
                $conn,
                $item['barcode']
            );

        $nama_produk =
            mysqli_real_escape_string(
                $conn,
                $item['nama_produk']
            );

        $harga =
            (float) $item['harga'];

        $qty =
            (int) $item['qty'];

        $subtotal =
            $harga * $qty;


        // ------------------------------
        // CEK STOK TERBARU
        // ------------------------------

        $cek_stok = mysqli_query(
            $conn,
            "SELECT stok
             FROM produk
             WHERE id = $produk_id
             FOR UPDATE"
        );


        if (
            !$cek_stok ||
            mysqli_num_rows($cek_stok) == 0
        ) {

            throw new Exception(
                "Produk tidak ditemukan."
            );

        }


        $data_stok =
            mysqli_fetch_assoc(
                $cek_stok
            );


        if (
            $data_stok['stok']
            < $qty
        ) {

            throw new Exception(
                "Stok produk "
                . $nama_produk
                . " tidak mencukupi."
            );

        }


        // ------------------------------
        // DETAIL TRANSAKSI
        // ------------------------------

        $insert_detail = mysqli_query(
            $conn,
            "INSERT INTO detail_transaksi
            (
                transaksi_id,
                produk_id,
                barcode,
                nama_produk,
                harga,
                qty,
                subtotal
            )
            VALUES
            (
                $transaksi_id,
                $produk_id,
                '$barcode',
                '$nama_produk',
                $harga,
                $qty,
                $subtotal
            )"
        );


        if (!$insert_detail) {

            throw new Exception(
                mysqli_error($conn)
            );

        }


        // ------------------------------
        // KURANGI STOK
        // ------------------------------

        $update_stok = mysqli_query(
            $conn,
            "UPDATE produk
             SET stok = stok - $qty
             WHERE id = $produk_id"
        );


        if (!$update_stok) {

            throw new Exception(
                mysqli_error($conn)
            );

        }

    }


    // ======================================
    // COMMIT
    // ======================================

    mysqli_commit($conn);


    // Simpan informasi transaksi untuk struk

    $_SESSION['transaksi_terakhir'] = [

        'id' =>
            $transaksi_id,

        'nomor_transaksi' =>
            $nomor_transaksi,

        'total' =>
            $total,

        'bayar' =>
            $bayar,

        'kembalian' =>
            $kembalian

    ];


    // Kosongkan keranjang

    $_SESSION['keranjang'] = [];


    // Ke halaman struk

    header(
        "Location: cetak_struk.php?id="
        . $transaksi_id
    );

    exit;


} catch (Exception $e) {


    mysqli_rollback($conn);


    echo "

    <script>

        alert(
            'Transaksi gagal: "
            . addslashes($e->getMessage())
            . "'
        );

        window.location='index.php';

    </script>

    ";

    exit;

}