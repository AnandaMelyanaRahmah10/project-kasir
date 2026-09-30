<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

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
// PROSES SIMPAN
// ==========================================

if (isset($_POST['simpan'])) {

    $nama_kategori = mysqli_real_escape_string(
        $conn,
        $_POST['nama_kategori']
    );


    // Cek apakah kategori sudah ada

    $cek = mysqli_query(
        $conn,
        "SELECT id
         FROM kategori
         WHERE nama_kategori = '$nama_kategori'"
    );


    if (mysqli_num_rows($cek) > 0) {

        $error = "Kategori tersebut sudah ada.";

    } else {

        $insert = mysqli_query(
            $conn,
            "INSERT INTO kategori
            (nama_kategori)
            VALUES
            ('$nama_kategori')"
        );


        if ($insert) {

            header("Location: index.php");
            exit;

        } else {

            $error =
                "Gagal menambahkan kategori: "
                . mysqli_error($conn);

        }

    }

}

?>

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Tambah Kategori - POS Retail</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body>


<!-- NAVBAR -->

<nav class="navbar">

    <strong>POS RETAIL</strong>

    <div>

        <?= htmlspecialchars($_SESSION['nama']); ?>

        |

        <a href="../auth/logout.php">
            Logout
        </a>

    </div>

</nav>



<div class="dashboard-layout">


    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="sidebar-title">
            MENU ADMIN
        </div>


        <a href="../dashboard/index.php">
            Dashboard
        </a>


        <a href="../produk/index.php">
            Produk
        </a>


        <a
            href="index.php"
            class="active"
        >
            Kategori
        </a>


        <a href="../laporan/penjualan.php">
            Transaksi & Laporan
        </a>


        <a href="../kasir/index.php">
            Kasir
        </a>

    </aside>



    <!-- CONTENT -->

    <main class="dashboard-content">


        <div class="page-header">

            <div>

                <h1>Tambah Kategori</h1>

                <p>
                    Tambahkan kategori produk baru.
                </p>

            </div>

        </div>



        <?php if (isset($error)): ?>

            <div class="alert">

                <?= htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>



        <div class="dashboard-panel">

            <form method="POST">


                <div class="form-group">

                    <label>
                        Nama Kategori
                    </label>

                    <input
                        type="text"
                        name="nama_kategori"
                        placeholder="Contoh: Minuman"
                        required
                    >

                </div>


                <div style="margin-top:20px">

                    <button
                        type="submit"
                        name="simpan"
                        class="btn-kasir"
                    >
                        Simpan Kategori
                    </button>


                    <a
                        href="index.php"
                        class="btn-secondary"
                    >
                        Kembali
                    </a>

                </div>

            </form>

        </div>

    </main>

</div>


</body>

</html>