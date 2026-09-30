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
// FILTER TANGGAL
// ==========================================

$tanggal_mulai = $_GET['tanggal_mulai'] ?? date('Y-m-01');

$tanggal_selesai = $_GET['tanggal_selesai'] ?? date('Y-m-d');


// ==========================================
// AMBIL DATA RINGKASAN
// ==========================================

$tanggal_mulai_db = mysqli_real_escape_string(
    $conn,
    $tanggal_mulai
);

$tanggal_selesai_db = mysqli_real_escape_string(
    $conn,
    $tanggal_selesai
);


$query_ringkasan = mysqli_query(
    $conn,

    "SELECT

        COUNT(*) AS jumlah_transaksi,

        COALESCE(SUM(total), 0) AS total_penjualan,

        COALESCE(SUM(bayar), 0) AS total_pembayaran,

        COALESCE(SUM(kembalian), 0) AS total_kembalian

     FROM transaksi

     WHERE DATE(tanggal)
     BETWEEN '$tanggal_mulai_db'
     AND '$tanggal_selesai_db'"
);


if (!$query_ringkasan) {

    die(
        "Error: "
        . mysqli_error($conn)
    );

}


$ringkasan =
    mysqli_fetch_assoc(
        $query_ringkasan
    );


// ==========================================
// JUMLAH PRODUK TERJUAL
// ==========================================

$query_produk = mysqli_query(
    $conn,

    "SELECT

        COALESCE(SUM(dt.qty), 0)
        AS jumlah_produk

     FROM detail_transaksi dt

     INNER JOIN transaksi t
        ON t.id = dt.transaksi_id

     WHERE DATE(t.tanggal)
     BETWEEN '$tanggal_mulai_db'
     AND '$tanggal_selesai_db'"
);


$data_produk =
    mysqli_fetch_assoc(
        $query_produk
    );


// ==========================================
// TRANSAKSI PER HARI
// ==========================================

$query_harian = mysqli_query(
    $conn,

    "SELECT

        DATE(tanggal) AS tanggal,

        COUNT(*) AS jumlah_transaksi,

        SUM(total) AS total_penjualan

     FROM transaksi

     WHERE DATE(tanggal)
     BETWEEN '$tanggal_mulai_db'
     AND '$tanggal_selesai_db'

     GROUP BY DATE(tanggal)

     ORDER BY tanggal DESC"
);


// ==========================================
// PRODUK TERLARIS
// ==========================================

$query_produk_terlaris = mysqli_query(
    $conn,

    "SELECT

        dt.nama_produk,

        SUM(dt.qty) AS jumlah_terjual,

        SUM(dt.subtotal) AS total_penjualan

     FROM detail_transaksi dt

     INNER JOIN transaksi t
        ON t.id = dt.transaksi_id

     WHERE DATE(t.tanggal)
     BETWEEN '$tanggal_mulai_db'
     AND '$tanggal_selesai_db'

     GROUP BY
        dt.produk_id,
        dt.nama_produk

     ORDER BY
        jumlah_terjual DESC

     LIMIT 10"
);


// ==========================================
// TRANSAKSI TERBARU
// ==========================================

$query_transaksi = mysqli_query(
    $conn,

    "SELECT

        t.id,

        t.nomor_transaksi,

        t.total,

        t.bayar,

        t.kembalian,

        t.tanggal,

        u.nama AS nama_kasir

     FROM transaksi t

     LEFT JOIN users u
        ON u.id = t.user_id

     WHERE DATE(t.tanggal)
     BETWEEN '$tanggal_mulai_db'
     AND '$tanggal_selesai_db'

     ORDER BY t.tanggal DESC

     LIMIT 20"
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

    <title>
        Laporan Penjualan - POS Retail
    </title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            font-family:
                Arial,
                sans-serif;

            background: #f4f6f8;

        }


        .container {

            padding: 25px;

        }


        /* ===============================
           HEADER
        ================================ */

        .header {

            background: white;

            padding: 20px;

            border-radius: 10px;

            margin-bottom: 20px;

        }


        .header h1 {

            margin:
                0 0 5px 0;

        }


        .header p {

            margin: 0;

            color: #777;

        }


        /* ===============================
           FILTER
        ================================ */

        .filter {

            background: white;

            padding: 20px;

            border-radius: 10px;

            margin-bottom: 20px;

        }


        .filter-form {

            display: flex;

            align-items: end;

            gap: 10px;

            flex-wrap: wrap;

        }


        .form-group {

            display: flex;

            flex-direction: column;

            gap: 5px;

        }


        .form-group label {

            font-size: 13px;

            font-weight: bold;

        }


        input {

            padding: 11px;

            border:
                1px solid #ddd;

            border-radius: 6px;

        }


        .btn {

            padding:
                11px 16px;

            border: none;

            border-radius: 6px;

            text-decoration: none;

            cursor: pointer;

            display: inline-block;

        }


        .btn-primary {

            background: #2563eb;

            color: white;

        }


        .btn-success {

            background: #16a34a;

            color: white;

        }


        .btn-secondary {

            background: #6b7280;

            color: white;

        }


        /* ===============================
           CARD STATISTIK
        ================================ */

        .cards {

            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 15px;

            margin-bottom: 20px;

        }


        .card {

            background: white;

            padding: 20px;

            border-radius: 10px;

        }


        .card-title {

            color: #777;

            font-size: 14px;

            margin-bottom: 10px;

        }


        .card-value {

            font-size: 25px;

            font-weight: bold;

        }


        .card-info {

            margin-top: 5px;

            color: #777;

            font-size: 12px;

        }


        /* ===============================
           GRID
        ================================ */

        .grid {

            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 20px;

            margin-bottom: 20px;

        }


        .box {

            background: white;

            padding: 20px;

            border-radius: 10px;

        }


        .box h2 {

            margin-top: 0;

            font-size: 18px;

        }


        /* ===============================
           TABLE
        ================================ */

        table {

            width: 100%;

            border-collapse: collapse;

        }


        th,
        td {

            padding: 11px;

            border-bottom:
                1px solid #eee;

            text-align: left;

        }


        th {

            background: #f8fafc;

        }


        .right {

            text-align: right;

        }


        .center {

            text-align: center;

        }


        /* ===============================
           PRINT
        ================================ */

        .print-header {

            display: none;

        }


        @media(max-width: 1000px) {

            .cards {

                grid-template-columns:
                    repeat(2, 1fr);

            }

        }


        @media(max-width: 700px) {

            .cards {

                grid-template-columns:
                    1fr;

            }


            .grid {

                grid-template-columns:
                    1fr;

            }

        }


        @media print {

            body {

                background: white;

            }


            .filter,
            .no-print {

                display: none;

            }


            .container {

                padding: 0;

            }


            .print-header {

                display: block;

                text-align: center;

                margin-bottom: 20px;

            }


            .cards {

                grid-template-columns:
                    repeat(4, 1fr);

            }


            .box,
            .card {

                box-shadow: none;

                border:
                    1px solid #ddd;

            }

        }

    </style>

</head>


<body>


<div class="container">


    <!-- ==================================
         HEADER
    =================================== -->

    <div class="header no-print">

        <h1>
            Laporan Penjualan
        </h1>

        <p>
            Laporan penjualan berdasarkan periode
        </p>

    </div>


    <!-- ==================================
         HEADER KHUSUS PRINT
    =================================== -->

    <div class="print-header">

        <h1>
            POS RETAIL
        </h1>

        <h2>
            LAPORAN PENJUALAN
        </h2>

        <p>

            Periode:

            <?= date(
                'd/m/Y',
                strtotime($tanggal_mulai)
            ); ?>

            -

            <?= date(
                'd/m/Y',
                strtotime($tanggal_selesai)
            ); ?>

        </p>

    </div>


    <!-- ==================================
         FILTER TANGGAL
    =================================== -->

    <div class="filter no-print">

        <form
            method="GET"
            class="filter-form"
        >


            <div class="form-group">

                <label>
                    Tanggal Mulai
                </label>

                <input
                    type="date"
                    name="tanggal_mulai"
                    value="<?= htmlspecialchars(
                        $tanggal_mulai
                    ); ?>"
                >

            </div>


            <div class="form-group">

                <label>
                    Tanggal Selesai
                </label>

                <input
                    type="date"
                    name="tanggal_selesai"
                    value="<?= htmlspecialchars(
                        $tanggal_selesai
                    ); ?>"
                >

            </div>


            <button
                type="submit"
                class="btn btn-primary"
            >
                Tampilkan
            </button>


        </form>

    </div>


    <!-- ==================================
         STATISTIK
    =================================== -->

    <div class="cards">


        <div class="card">

            <div class="card-title">

                Total Penjualan

            </div>

            <div class="card-value">

                Rp
                <?= number_format(
                    $ringkasan['total_penjualan'],
                    0,
                    ',',
                    '.'
                ); ?>

            </div>

            <div class="card-info">

                Periode laporan

            </div>

        </div>


        <div class="card">

            <div class="card-title">

                Jumlah Transaksi

            </div>

            <div class="card-value">

                <?= number_format(
                    $ringkasan['jumlah_transaksi'],
                    0,
                    ',',
                    '.'
                ); ?>

            </div>

            <div class="card-info">

                Transaksi berhasil

            </div>

        </div>


        <div class="card">

            <div class="card-title">

                Produk Terjual

            </div>

            <div class="card-value">

                <?= number_format(
                    $data_produk['jumlah_produk'],
                    0,
                    ',',
                    '.'
                ); ?>

            </div>

            <div class="card-info">

                Total jumlah barang

            </div>

        </div>


        <div class="card">

            <div class="card-title">

                Rata-rata Transaksi

            </div>

            <div class="card-value">

                <?php

                if (
                    $ringkasan['jumlah_transaksi']
                    > 0
                ) {

                    $rata_rata =
                        $ringkasan['total_penjualan']
                        /
                        $ringkasan['jumlah_transaksi'];

                } else {

                    $rata_rata = 0;

                }

                ?>

                Rp
                <?= number_format(
                    $rata_rata,
                    0,
                    ',',
                    '.'
                ); ?>

            </div>

            <div class="card-info">

                Rata-rata per transaksi

            </div>

        </div>


    </div>


    <!-- ==================================
         TRANSAKSI HARIAN + PRODUK TERLARIS
    =================================== -->

    <div class="grid">


        <!-- TRANSAKSI HARIAN -->

        <div class="box">

            <h2>
                Penjualan Per Hari
            </h2>


            <table>

                <thead>

                    <tr>

                        <th>
                            Tanggal
                        </th>

                        <th>
                            Transaksi
                        </th>

                        <th class="right">
                            Penjualan
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php

                if (
                    mysqli_num_rows(
                        $query_harian
                    ) > 0
                ):

                    while (
                        $hari =
                        mysqli_fetch_assoc(
                            $query_harian
                        )
                    ):

                ?>

                    <tr>

                        <td>

                            <?= date(
                                'd/m/Y',
                                strtotime(
                                    $hari['tanggal']
                                )
                            ); ?>

                        </td>


                        <td>

                            <?= $hari[
                                'jumlah_transaksi'
                            ]; ?>

                        </td>


                        <td class="right">

                            Rp
                            <?= number_format(
                                $hari[
                                    'total_penjualan'
                                ],
                                0,
                                ',',
                                '.'
                            ); ?>

                        </td>

                    </tr>


                <?php

                    endwhile;

                else:

                ?>

                    <tr>

                        <td
                            colspan="3"
                            class="center"
                        >

                            Belum ada data.

                        </td>

                    </tr>

                <?php endif; ?>


                </tbody>

            </table>

        </div>



        <!-- PRODUK TERLARIS -->

        <div class="box">

            <h2>
                10 Produk Terlaris
            </h2>


            <table>

                <thead>

                    <tr>

                        <th>
                            Produk
                        </th>

                        <th>
                            Terjual
                        </th>

                        <th class="right">
                            Penjualan
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php

                if (
                    mysqli_num_rows(
                        $query_produk_terlaris
                    ) > 0
                ):

                    while (
                        $produk =
                        mysqli_fetch_assoc(
                            $query_produk_terlaris
                        )
                    ):

                ?>


                    <tr>

                        <td>

                            <?= htmlspecialchars(
                                $produk[
                                    'nama_produk'
                                ]
                            ); ?>

                        </td>


                        <td>

                            <?= $produk[
                                'jumlah_terjual'
                            ]; ?>

                        </td>


                        <td class="right">

                            Rp
                            <?= number_format(
                                $produk[
                                    'total_penjualan'
                                ],
                                0,
                                ',',
                                '.'
                            ); ?>

                        </td>

                    </tr>


                <?php

                    endwhile;

                else:

                ?>


                    <tr>

                        <td
                            colspan="3"
                            class="center"
                        >

                            Belum ada data.

                        </td>

                    </tr>


                <?php endif; ?>


                </tbody>

            </table>

        </div>


    </div>


    <!-- ==================================
         DETAIL TRANSAKSI
    =================================== -->

    <div class="box">


        <h2>
            Detail Transaksi
        </h2>


        <table>

            <thead>

                <tr>

                    <th>
                        No
                    </th>

                    <th>
                        Nomor Transaksi
                    </th>

                    <th>
                        Kasir
                    </th>

                    <th>
                        Tanggal
                    </th>

                    <th class="right">
                        Total
                    </th>

                    <th class="right">
                        Bayar
                    </th>

                    <th class="right">
                        Kembalian
                    </th>

                </tr>

            </thead>


            <tbody>


            <?php

            $no = 1;


            if (
                mysqli_num_rows(
                    $query_transaksi
                ) > 0
            ):

                while (
                    $transaksi =
                    mysqli_fetch_assoc(
                        $query_transaksi
                    )
                ):

            ?>


                <tr>

                    <td>

                        <?= $no++; ?>

                    </td>


                    <td>

                        <?= htmlspecialchars(
                            $transaksi[
                                'nomor_transaksi'
                            ]
                        ); ?>

                    </td>


                    <td>

                        <?= htmlspecialchars(
                            $transaksi[
                                'nama_kasir'
                            ] ?? '-'
                        ); ?>

                    </td>


                    <td>

                        <?= date(
                            'd/m/Y H:i',
                            strtotime(
                                $transaksi[
                                    'tanggal'
                                ]
                            )
                        ); ?>

                    </td>


                    <td class="right">

                        Rp
                        <?= number_format(
                            $transaksi[
                                'total'
                            ],
                            0,
                            ',',
                            '.'
                        ); ?>

                    </td>


                    <td class="right">

                        Rp
                        <?= number_format(
                            $transaksi[
                                'bayar'
                            ],
                            0,
                            ',',
                            '.'
                        ); ?>

                    </td>


                    <td class="right">

                        Rp
                        <?= number_format(
                            $transaksi[
                                'kembalian'
                            ],
                            0,
                            ',',
                            '.'
                        ); ?>

                    </td>

                </tr>


            <?php

                endwhile;

            else:

            ?>


                <tr>

                    <td
                        colspan="7"
                        class="center"
                    >

                        Belum ada transaksi
                        pada periode ini.

                    </td>

                </tr>


            <?php endif; ?>


            </tbody>

        </table>


    </div>


    <br>


    <!-- ==================================
         BUTTON
    =================================== -->

    <div class="no-print">


        <button
            onclick="window.print()"
            class="btn btn-success"
        >

            🖨️ Cetak Laporan

        </button>


        <a
            href="../dashboard/index.php"
            class="btn btn-secondary"
        >

            ← Kembali ke Dashboard

        </a>


    </div>


</div>


</body>

</html>