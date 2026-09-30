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
// AMBIL FILTER
// ==========================================

$keyword = $_GET['keyword'] ?? '';

$tanggal_mulai = $_GET['tanggal_mulai'] ?? '';

$tanggal_selesai = $_GET['tanggal_selesai'] ?? '';


// ==========================================
// QUERY DASAR
// ==========================================

$sql = "
    SELECT
        transaksi.id,
        transaksi.nomor_transaksi,
        transaksi.total,
        transaksi.bayar,
        transaksi.kembalian,
        transaksi.tanggal,
        users.nama AS nama_kasir
    FROM transaksi

    LEFT JOIN users
        ON users.id = transaksi.user_id

    WHERE 1=1
";


// ==========================================
// FILTER NOMOR TRANSAKSI
// ==========================================

if ($keyword != '') {

    $keyword_db = mysqli_real_escape_string(
        $conn,
        $keyword
    );

    $sql .= "
        AND transaksi.nomor_transaksi
        LIKE '%$keyword_db%'
    ";
}


// ==========================================
// FILTER TANGGAL MULAI
// ==========================================

if ($tanggal_mulai != '') {

    $tanggal_mulai_db = mysqli_real_escape_string(
        $conn,
        $tanggal_mulai
    );

    $sql .= "
        AND DATE(transaksi.tanggal)
        >= '$tanggal_mulai_db'
    ";
}


// ==========================================
// FILTER TANGGAL SELESAI
// ==========================================

if ($tanggal_selesai != '') {

    $tanggal_selesai_db = mysqli_real_escape_string(
        $conn,
        $tanggal_selesai
    );

    $sql .= "
        AND DATE(transaksi.tanggal)
        <= '$tanggal_selesai_db'
    ";
}


// ==========================================
// URUTKAN
// ==========================================

$sql .= "
    ORDER BY transaksi.tanggal DESC
";


$query = mysqli_query(
    $conn,
    $sql
);


if (!$query) {

    die(
        "Query error: "
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
        Riwayat Transaksi - POS Retail
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


        .header {

            background: white;

            padding: 20px;

            border-radius: 10px;

            margin-bottom: 20px;

        }


        .header h1 {

            margin: 0 0 5px;

        }


        .header p {

            margin: 0;

            color: #777;

        }


        .filter {

            background: white;

            padding: 20px;

            border-radius: 10px;

            margin-bottom: 20px;

        }


        .filter-form {

            display: grid;

            grid-template-columns:
                1fr 180px 180px auto;

            gap: 10px;

        }


        input {

            padding: 11px;

            border:
                1px solid #ddd;

            border-radius: 6px;

        }


        button,
        .btn {

            padding: 11px 16px;

            border: none;

            border-radius: 6px;

            cursor: pointer;

            text-decoration: none;

            display: inline-block;

        }


        .btn-primary {

            background: #2563eb;

            color: white;

        }


        .btn-secondary {

            background: #6b7280;

            color: white;

        }


        .btn-success {

            background: #16a34a;

            color: white;

        }


        .btn-danger {

            background: #dc2626;

            color: white;

        }


        .table-card {

            background: white;

            padding: 20px;

            border-radius: 10px;

            overflow-x: auto;

        }


        table {

            width: 100%;

            border-collapse: collapse;

        }


        th,
        td {

            padding: 13px;

            border-bottom:
                1px solid #eee;

            text-align: left;

        }


        th {

            background: #f8fafc;

        }


        .nominal {

            text-align: right;

        }


        .status {

            display: inline-block;

            padding: 5px 10px;

            border-radius: 20px;

            background: #dcfce7;

            color: #166534;

            font-size: 12px;

        }


        .aksi {

            white-space: nowrap;

        }


        .kosong {

            text-align: center;

            padding: 40px;

            color: #777;

        }


        @media(max-width: 800px) {

            .filter-form {

                grid-template-columns: 1fr;

            }

        }

    </style>

</head>


<body>


<div class="container">


    <!-- =====================================
         HEADER
    ====================================== -->

    <div class="header">

        <h1>
            Riwayat Transaksi
        </h1>

        <p>
            Daftar seluruh transaksi penjualan
        </p>

    </div>



    <!-- =====================================
         FILTER
    ====================================== -->

    <div class="filter">

        <form
            method="GET"
            class="filter-form"
        >


            <input
                type="text"
                name="keyword"
                placeholder="Cari nomor transaksi..."
                value="<?= htmlspecialchars(
                    $keyword
                ); ?>"
            >


            <input
                type="date"
                name="tanggal_mulai"
                value="<?= htmlspecialchars(
                    $tanggal_mulai
                ); ?>"
            >


            <input
                type="date"
                name="tanggal_selesai"
                value="<?= htmlspecialchars(
                    $tanggal_selesai
                ); ?>"
            >


            <button
                type="submit"
                class="btn-primary"
            >
                Filter
            </button>


        </form>


        <br>


        <a
            href="index.php"
            class="btn-secondary"
        >
            Reset Filter
        </a>


    </div>



    <!-- =====================================
         TABEL TRANSAKSI
    ====================================== -->

    <div class="table-card">


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

                    <th>
                        Total
                    </th>

                    <th>
                        Bayar
                    </th>

                    <th>
                        Kembalian
                    </th>

                    <th>
                        Status
                    </th>

                    <th>
                        Aksi
                    </th>

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
                    $row =
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
                                $row['nomor_transaksi']
                            ); ?>

                        </strong>

                    </td>


                    <td>

                        <?= htmlspecialchars(
                            $row['nama_kasir']
                            ?? '-'
                        ); ?>

                    </td>


                    <td>

                        <?= date(
                            'd/m/Y H:i',
                            strtotime(
                                $row['tanggal']
                            )
                        ); ?>

                    </td>


                    <td class="nominal">

                        Rp
                        <?= number_format(
                            $row['total'],
                            0,
                            ',',
                            '.'
                        ); ?>

                    </td>


                    <td class="nominal">

                        Rp
                        <?= number_format(
                            $row['bayar'],
                            0,
                            ',',
                            '.'
                        ); ?>

                    </td>


                    <td class="nominal">

                        Rp
                        <?= number_format(
                            $row['kembalian'],
                            0,
                            ',',
                            '.'
                        ); ?>

                    </td>


                    <td>

                        <span class="status">

                            Berhasil

                        </span>

                    </td>


                    <td class="aksi">


                        <a
                            href="../kasir/cetak_struk.php?id=<?= $row['id']; ?>"
                            target="_blank"
                            class="btn btn-success"
                        >
                            Struk
                        </a>


                    </td>


                </tr>


            <?php

                endwhile;

            else:

            ?>


                <tr>

                    <td
                        colspan="9"
                        class="kosong"
                    >

                        Belum ada transaksi.

                    </td>

                </tr>


            <?php endif; ?>


            </tbody>


        </table>


    </div>


    <br>


    <a
        href="../dashboard/index.php"
        class="btn btn-secondary"
    >
        ← Kembali ke Dashboard
    </a>


</div>


</body>

</html>