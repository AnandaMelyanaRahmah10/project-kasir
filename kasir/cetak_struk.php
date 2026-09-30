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
// AMBIL ID TRANSAKSI
// ==========================================

$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;


if ($id <= 0) {

    echo "ID transaksi tidak valid.";
    exit;

}


// ==========================================
// AMBIL DATA TRANSAKSI
// ==========================================

$query = mysqli_query(
    $conn,
    "SELECT
        transaksi.*,
        users.nama AS nama_kasir
     FROM transaksi
     LEFT JOIN users
        ON users.id = transaksi.user_id
     WHERE transaksi.id = $id
     LIMIT 1"
);


if (!$query) {

    die(
        "Error transaksi: "
        . mysqli_error($conn)
    );

}


if (mysqli_num_rows($query) == 0) {

    die("Data transaksi tidak ditemukan.");

}


$transaksi = mysqli_fetch_assoc($query);


// ==========================================
// AMBIL DETAIL TRANSAKSI
// ==========================================

$query_detail = mysqli_query(
    $conn,
    "SELECT *
     FROM detail_transaksi
     WHERE transaksi_id = $id
     ORDER BY id ASC"
);


if (!$query_detail) {

    die(
        "Error detail transaksi: "
        . mysqli_error($conn)
    );

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

    <title>
        Struk -
        <?= htmlspecialchars(
            $transaksi['nomor_transaksi']
        ); ?>
    </title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            padding: 30px;

            background: #eeeeee;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

        }


        .struk {

            width: 380px;

            max-width: 100%;

            margin: auto;

            background: white;

            padding: 25px;

            box-shadow:
                0 3px 15px
                rgba(0,0,0,0.15);

        }


        /* =================================
           HEADER
        ================================= */

        .header {

            text-align: center;

        }


        .header h1 {

            font-size: 24px;

            margin:
                0 0 5px 0;

        }


        .header p {

            margin:
                3px 0;

            font-size: 13px;

            color: #555;

        }


        .garis {

            border-top:
                1px dashed #555;

            margin:
                15px 0;

        }


        /* =================================
           INFORMASI TRANSAKSI
        ================================= */

        .info {

            font-size: 13px;

        }


        .info-row {

            display: flex;

            justify-content:
                space-between;

            margin-bottom: 5px;

        }


        .info-row span:first-child {

            color: #555;

        }


        /* =================================
           PRODUK
        ================================= */

        .produk {

            font-size: 13px;

        }


        .produk-item {

            margin-bottom: 12px;

        }


        .produk-nama {

            font-weight: bold;

            margin-bottom: 4px;

        }


        .produk-detail {

            display: flex;

            justify-content:
                space-between;

        }


        .produk-harga {

            color: #555;

        }


        /* =================================
           TOTAL
        ================================= */

        .total {

            font-size: 14px;

        }


        .total-row {

            display: flex;

            justify-content:
                space-between;

            margin-bottom: 8px;

        }


        .total-utama {

            font-size: 18px;

            font-weight: bold;

            margin-top: 10px;

        }


        /* =================================
           FOOTER
        ================================= */

        .footer {

            text-align: center;

            font-size: 13px;

            line-height: 1.6;

        }


        .footer strong {

            display: block;

            margin-bottom: 5px;

        }


        /* =================================
           BUTTON
        ================================= */

        .button-area {

            margin-top: 20px;

        }


        .btn {

            display: block;

            width: 100%;

            padding: 12px;

            border: none;

            border-radius: 6px;

            cursor: pointer;

            text-align: center;

            text-decoration: none;

            font-size: 15px;

            margin-bottom: 10px;

        }


        .btn-print {

            background: #2563eb;

            color: white;

        }


        .btn-kembali {

            background: #6b7280;

            color: white;

        }


        /* =================================
           PRINT
        ================================= */

        @media print {

            body {

                padding: 0;

                background: white;

            }


            .struk {

                width: 80mm;

                max-width: 80mm;

                box-shadow: none;

                padding: 10px;

            }


            .button-area {

                display: none;

            }

        }

    </style>

</head>


<body>


<div class="struk">


    <!-- =====================================
         HEADER TOKO
    ====================================== -->

    <div class="header">

        <h1>POS RETAIL</h1>

        <p>
            Sistem Point of Sale
        </p>

        <p>
            Terima kasih telah berbelanja
        </p>

    </div>


    <div class="garis"></div>


    <!-- =====================================
         INFORMASI TRANSAKSI
    ====================================== -->

    <div class="info">

        <div class="info-row">

            <span>
                No. Transaksi
            </span>

            <span>

                <?= htmlspecialchars(
                    $transaksi['nomor_transaksi']
                ); ?>

            </span>

        </div>


        <div class="info-row">

            <span>
                Tanggal
            </span>

            <span>

                <?= date(
                    'd/m/Y H:i',
                    strtotime(
                        $transaksi['tanggal']
                    )
                ); ?>

            </span>

        </div>


        <div class="info-row">

            <span>
                Kasir
            </span>

            <span>

                <?= htmlspecialchars(
                    $transaksi['nama_kasir']
                    ?? '-'
                ); ?>

            </span>

        </div>

    </div>


    <div class="garis"></div>


    <!-- =====================================
         DAFTAR PRODUK
    ====================================== -->

    <div class="produk">


        <?php

        while (
            $item =
            mysqli_fetch_assoc(
                $query_detail
            )
        ):

        ?>


            <div class="produk-item">


                <div class="produk-nama">

                    <?= htmlspecialchars(
                        $item['nama_produk']
                    ); ?>

                </div>


                <div class="produk-detail">


                    <span class="produk-harga">

                        <?= $item['qty']; ?>

                        x

                        Rp
                        <?= number_format(
                            $item['harga'],
                            0,
                            ',',
                            '.'
                        ); ?>

                    </span>


                    <span>

                        Rp
                        <?= number_format(
                            $item['subtotal'],
                            0,
                            ',',
                            '.'
                        ); ?>

                    </span>


                </div>


            </div>


        <?php endwhile; ?>


    </div>


    <div class="garis"></div>


    <!-- =====================================
         PEMBAYARAN
    ====================================== -->

    <div class="total">


        <div class="total-row">

            <span>
                Total
            </span>

            <span>

                Rp
                <?= number_format(
                    $transaksi['total'],
                    0,
                    ',',
                    '.'
                ); ?>

            </span>

        </div>


        <div class="total-row">

            <span>
                Bayar
            </span>

            <span>

                Rp
                <?= number_format(
                    $transaksi['bayar'],
                    0,
                    ',',
                    '.'
                ); ?>

            </span>

        </div>


        <div class="total-row total-utama">

            <span>
                Kembalian
            </span>

            <span>

                Rp
                <?= number_format(
                    $transaksi['kembalian'],
                    0,
                    ',',
                    '.'
                ); ?>

            </span>

        </div>


    </div>


    <div class="garis"></div>


    <!-- =====================================
         FOOTER
    ====================================== -->

    <div class="footer">

        <strong>
            TERIMA KASIH
        </strong>

        Semoga Anda puas dengan pelayanan kami.

        <br>

        Barang yang sudah dibeli
        tidak dapat dikembalikan.

    </div>


    <!-- =====================================
         BUTTON
    ====================================== -->

    <div class="button-area">


        <button
            class="btn btn-print"
            onclick="window.print()"
        >

            🖨️ CETAK STRUK

        </button>


        <a
            href="index.php"
            class="btn btn-kembali"
        >

            ← Kembali ke Kasir

        </a>


    </div>


</div>


</body>

</html>