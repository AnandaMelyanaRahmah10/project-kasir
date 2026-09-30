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


$query_produk = mysqli_query(
    $conn,
    "SELECT * FROM produk
     WHERE id = $id"
);


if (mysqli_num_rows($query_produk) == 0) {

    die("Produk tidak ditemukan.");

}


$produk = mysqli_fetch_assoc($query_produk);


$kategori = mysqli_query(
    $conn,
    "SELECT * FROM kategori
     ORDER BY nama_kategori ASC"
);


if (isset($_POST['update'])) {

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

    $status = mysqli_real_escape_string(
        $conn,
        $_POST['status']
    );


    $cek = mysqli_query(
        $conn,
        "SELECT id FROM produk
         WHERE barcode = '$barcode'
         AND id != $id"
    );


    if (mysqli_num_rows($cek) > 0) {

        $error = "Barcode sudah digunakan.";

    } else {

        $update = mysqli_query(
            $conn,
            "UPDATE produk SET

                barcode = '$barcode',

                kode_produk = '$kode_produk',

                nama_produk = '$nama_produk',

                kategori_id = $kategori_id,

                harga_beli = $harga_beli,

                harga_jual = $harga_jual,

                stok = $stok,

                stok_minimal = $stok_minimal,

                satuan = '$satuan',

                status = '$status'

             WHERE id = $id"
        );


        if ($update) {

            header("Location: index.php");

            exit;

        } else {

            $error =
                "Gagal mengubah produk: "
                . mysqli_error($conn);

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

    <title>Edit Produk</title>

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

                <h1>Edit Produk</h1>

                <p>
                    Ubah informasi produk.
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

                        <label>Barcode *</label>

                        <input
                            type="text"
                            name="barcode"
                            value="<?= htmlspecialchars(
                                $produk['barcode']
                            ); ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>Kode Produk *</label>

                        <input
                            type="text"
                            name="kode_produk"
                            value="<?= htmlspecialchars(
                                $produk['kode_produk']
                            ); ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>Nama Produk *</label>

                        <input
                            type="text"
                            name="nama_produk"
                            value="<?= htmlspecialchars(
                                $produk['nama_produk']
                            ); ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>Kategori *</label>

                        <select
                            name="kategori_id"
                            required
                        >

                            <?php while (
                                $kat =
                                mysqli_fetch_assoc(
                                    $kategori
                                )
                            ): ?>

                                <option
                                    value="<?= $kat['id']; ?>"
                                    <?= $kat['id']
                                        == $produk['kategori_id']
                                        ? 'selected'
                                        : ''; ?>
                                >

                                    <?= htmlspecialchars(
                                        $kat['nama_kategori']
                                    ); ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>


                    <div class="form-group">

                        <label>Harga Beli *</label>

                        <input
                            type="number"
                            name="harga_beli"
                            min="0"
                            value="<?= $produk['harga_beli']; ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>Harga Jual *</label>

                        <input
                            type="number"
                            name="harga_jual"
                            min="0"
                            value="<?= $produk['harga_jual']; ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>Stok *</label>

                        <input
                            type="number"
                            name="stok"
                            min="0"
                            value="<?= $produk['stok']; ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>Stok Minimal *</label>

                        <input
                            type="number"
                            name="stok_minimal"
                            min="0"
                            value="<?= $produk['stok_minimal']; ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>Satuan *</label>

                        <select
                            name="satuan"
                            required
                        >

                            <?php

                            $satuan_list = [
                                'pcs',
                                'botol',
                                'dus',
                                'pack',
                                'kg',
                                'liter'
                            ];

                            foreach (
                                $satuan_list
                                as $satuan
                            ):

                            ?>

                                <option
                                    value="<?= $satuan; ?>"
                                    <?= $produk['satuan']
                                        == $satuan
                                        ? 'selected'
                                        : ''; ?>
                                >

                                    <?= $satuan; ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <div class="form-group">

                        <label>Status *</label>

                        <select
                            name="status"
                            required
                        >

                            <option
                                value="aktif"
                                <?= $produk['status']
                                    == 'aktif'
                                    ? 'selected'
                                    : ''; ?>
                            >
                                Aktif
                            </option>

                            <option
                                value="nonaktif"
                                <?= $produk['status']
                                    == 'nonaktif'
                                    ? 'selected'
                                    : ''; ?>
                            >
                                Nonaktif
                            </option>

                        </select>

                    </div>

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