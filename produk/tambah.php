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


// Ambil kategori
$kategori = mysqli_query(
    $conn,
    "SELECT * FROM kategori
     ORDER BY nama_kategori ASC"
);


if (isset($_POST['simpan'])) {

    $barcode = mysqli_real_escape_string(
        $conn,
        $_POST['barcode']
    );

    $kode_produk = mysqli_real_escape_string(
        $conn,
        $_POST['kode_produk']
    );

    $nama_produk = mysqli_real_escape_string(
        $conn,
        $_POST['nama_produk']
    );

    $kategori_id = (int) $_POST['kategori_id'];

    $harga_beli = (float) $_POST['harga_beli'];

    $harga_jual = (float) $_POST['harga_jual'];

    $stok = (int) $_POST['stok'];

    $stok_minimal = (int) $_POST['stok_minimal'];

    $satuan = mysqli_real_escape_string(
        $conn,
        $_POST['satuan']
    );


    // Cek barcode
    $cek_barcode = mysqli_query(
        $conn,
        "SELECT id FROM produk
         WHERE barcode = '$barcode'"
    );

    if (mysqli_num_rows($cek_barcode) > 0) {

        $error = "Barcode sudah digunakan.";

    } else {

        // Cek kode produk
        $cek_kode = mysqli_query(
            $conn,
            "SELECT id FROM produk
             WHERE kode_produk = '$kode_produk'"
        );

        if (mysqli_num_rows($cek_kode) > 0) {

            $error = "Kode produk sudah digunakan.";

        } else {

            $insert = mysqli_query(
                $conn,
                "INSERT INTO produk
                (
                    barcode,
                    kode_produk,
                    nama_produk,
                    kategori_id,
                    harga_beli,
                    harga_jual,
                    stok,
                    stok_minimal,
                    satuan,
                    status
                )
                VALUES
                (
                    '$barcode',
                    '$kode_produk',
                    '$nama_produk',
                    $kategori_id,
                    $harga_beli,
                    $harga_jual,
                    $stok,
                    $stok_minimal,
                    '$satuan',
                    'aktif'
                )"
            );


            if ($insert) {

                header("Location: index.php");

                exit;

            } else {

                $error =
                    "Gagal menyimpan produk: "
                    . mysqli_error($conn);

            }

        }

    }

}

?>

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Tambah Produk</title>

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

        <a href="index.php" class="active">
            Produk
        </a>

        <a href="../kategori/index.php">
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

                <h1>Tambah Produk</h1>

                <p>
                    Tambahkan produk baru ke sistem.
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


                <div class="form-grid">


                    <div class="form-group">

                        <label>
                            Barcode *
                        </label>

                        <input
                            type="text"
                            name="barcode"
                            placeholder="Contoh: 899100210011"
                            required
                        >

                        <small>
                            Masukkan barcode produk.
                        </small>

                    </div>


                    <div class="form-group">

                        <label>
                            Kode Produk *
                        </label>

                        <input
                            type="text"
                            name="kode_produk"
                            placeholder="Contoh: PRD011"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Nama Produk *
                        </label>

                        <input
                            type="text"
                            name="nama_produk"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Kategori *
                        </label>

                        <select
                            name="kategori_id"
                            required
                        >

                            <option value="">
                                -- Pilih Kategori --
                            </option>

                            <?php while (
                                $kat =
                                mysqli_fetch_assoc(
                                    $kategori
                                )
                            ): ?>

                                <option
                                    value="<?= $kat['id']; ?>"
                                >

                                    <?= htmlspecialchars(
                                        $kat['nama_kategori']
                                    ); ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>


                    <div class="form-group">

                        <label>
                            Harga Beli *
                        </label>

                        <input
                            type="number"
                            name="harga_beli"
                            min="0"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Harga Jual *
                        </label>

                        <input
                            type="number"
                            name="harga_jual"
                            min="0"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Stok Awal *
                        </label>

                        <input
                            type="number"
                            name="stok"
                            min="0"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Stok Minimal *
                        </label>

                        <input
                            type="number"
                            name="stok_minimal"
                            min="0"
                            value="5"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Satuan *
                        </label>

                        <select
                            name="satuan"
                            required
                        >

                            <option value="pcs">
                                pcs
                            </option>

                            <option value="botol">
                                botol
                            </option>

                            <option value="dus">
                                dus
                            </option>

                            <option value="pack">
                                pack
                            </option>

                            <option value="kg">
                                kg
                            </option>

                            <option value="liter">
                                liter
                            </option>

                        </select>

                    </div>

                </div>


                <div style="margin-top:20px">

                    <button
                        type="submit"
                        name="simpan"
                        class="btn-kasir"
                    >
                        Simpan Produk
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