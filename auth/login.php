<?php

session_start();

require_once "../config/database.php";

$error = "";

if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string(
        $conn,
        $_POST['username']
    );

    $password = md5($_POST['password']);

    $query = mysqli_query(
        $conn,
        "SELECT * FROM users
         WHERE username = '$username'
         AND password = '$password'"
    );

    if (mysqli_num_rows($query) > 0) {

        $user = mysqli_fetch_assoc($query);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header("Location: ../dashboard/index.php");
        exit;

    } else {

        $error = "Username atau password salah.";

    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login - POS Retail</title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

</head>

<body>

<div class="login-container">

    <div class="login-box">

        <h2>POS RETAIL</h2>

        <p>Silakan login untuk masuk ke sistem</p>

        <?php if ($error != ""): ?>

            <div class="alert">
                <?= $error; ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="form-group">

                <label>Username</label>

                <input
                    type="text"
                    name="username"
                    placeholder="Masukkan username"
                    required
                >

            </div>

            <div class="form-group">

                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    placeholder="Masukkan password"
                    required
                >

            </div>

            <button
                type="submit"
                name="login"
                class="btn-login"
            >
                LOGIN
            </button>

        </form>

        <div class="login-info">

            <p><b>Admin:</b> admin / admin123</p>

            <p><b>Kasir:</b> kasir / kasir123</p>

        </div>

    </div>

</div>

</body>

</html>