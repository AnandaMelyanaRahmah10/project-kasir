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
// AMBIL DATA KATEGORI
// ==========================================

$query = mysqli_query(
    $conn,
    "SELECT *
     FROM kategori
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Kategori - POS Retail</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>


<body>


<!-- ==========================================
     NAVBAR
========================================== -->

<nav class="navbar">

    <div>

        <strong>POS RETAIL</strong>

    </div>


    <div>

        <?= htmlspecialchars($_SESSION['nama']); ?>

        |

        <a href="../auth/logout.php">
            Logout
        </a>

    </div>

</nav>



<div class="dashboard-layout">


    <!-- ======================================
         SIDEBAR
    ======================================= -->

    <aside class="sidebar">

        <div class="sidebar-title">
            MENU ADMIN
        </div>


        <a href="../dashboard/index.php">
            Dashboard
        </a>
        
        <a href="../kasir/index.php">
            Kasir
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

    </aside>



    <!-- ======================================
         CONTENT
    ======================================= -->

    <main class="dashboard-content">


        <div class="page-header">

            <div>

                <h1>Kategori Produk</h1>

                <p>
                    Kelola kategori produk toko.
                </p>

            </div>


            <a
                href="tambah.php"
                class="btn-kasir"
            >

                + Tambah Kategori

            </a>

        </div>



        <!-- ==================================
             TABEL KATEGORI
        =================================== -->

        <div class="dashboard-panel">

            <div class="table-responsive">

                <table>

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>Nama Kategori</th>

                            <th>Tanggal Dibuat</th>

                            <th>Aksi</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php

                    $no = 1;

                    if (
                        mysqli_num_rows($query)
                        > 0
                    ):

                        while (
                            $kategori =
                            mysqli_fetch_assoc($query)
                        ):

                    ?>

                        <tr>

                            <td>
                                <?= $no++; ?>
                            </td>


                            <td>

                                <strong>

                                    <?= htmlspecialchars(
                                        $kategori[
                                            'nama_kategori'
                                        ]
                                    ); ?>

                                </strong>

                            </td>

                            <td>

                                <?= htmlspecialchars(
                                    $kategori[
                                        'created_at'
                                    ] ?? '-'
                                ); ?>

                            </td>


                            <td>

                                <a
                                    href="edit.php?id=<?= $kategori['id']; ?>"
                                >
                                    Edit
                                </a>

                                |

                                <a
                                    href="hapus.php?id=<?= $kategori['id']; ?>"
                                    onclick="return confirm(
                                        'Yakin ingin menghapus kategori ini?'
                                    )"
                                >
                                    Hapus
                                </a>

                            </td>

                        </tr>

                    <?php

                        endwhile;

                    else:

                    ?>

                        <tr>

                            <td
                                colspan="5"
                                style="text-align:center"
                            >

                                Belum ada kategori.

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </main>

</div>


</body>

</html>