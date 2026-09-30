<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once "../config/database.php";


// CEK LOGIN

if (!isset($_SESSION['user_id'])) {

    header("Location: ../auth/login.php");
    exit;

}


// CEK ROLE

if ($_SESSION['role'] != 'admin') {

    header("Location: ../kasir/index.php");
    exit;

}


// AMBIL ID

$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;


// AMBIL DATA

$query = mysqli_query(
    $conn,
    "SELECT *
     FROM kategori
     WHERE id = $id"
);


if (mysqli_num_rows($query) == 0) {

    die("Kategori tidak ditemukan.");

}


$kategori = mysqli_fetch_assoc($query);


// PROSES UPDATE

if (isset($_POST['update'])) {

    $nama_kategori = mysqli_real_escape_string(
        $conn,
        $_POST['nama_kategori']
    );


    // Cek duplikat

    $cek = mysqli_query(
        $conn,
        "SELECT id
         FROM kategori
         WHERE nama_kategori = '$nama_kategori'
         AND id != $id"
    );


    if (mysqli_num_rows($cek) > 0) {

        $error = "Nama kategori sudah digunakan.";

    } else {

        $update = mysqli_query(
            $conn,
            "UPDATE kategori

             SET nama_kategori = '$nama_kategori'

             WHERE id = $id"
        );


        if ($update) {

            header("Location: index.php");
            exit;

        } else {

            $error =
                "Gagal mengubah kategori: "
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

    <title>Edit Kategori - POS Retail</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body>


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



    <main class="dashboard-content">


        <div class="page-header">

            <div>

                <h1>Edit Kategori</h1>

                <p>
                    Ubah nama kategori produk.
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
                        value="<?= htmlspecialchars(
                            $kategori['nama_kategori']
                        ); ?>"
                        required
                    >

                </div>


                <div style="margin-top:20px">

                    <button
                        type="submit"
                        name="update"
                        class="btn-kasir"
                    >
                        Simpan Perubahan
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