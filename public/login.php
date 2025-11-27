<?php
require '../config/koneksi.php';
if (isset($_SESSION['petugas'])) {
    header("Location: dashboard.php");
    exit;
}
$error = "";
if ($_POST) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $q = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND rule='petugas'");
    if ($row = mysqli_fetch_assoc($q)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['petugas'] = $row;
            header("Location: dashboard.php");
            exit;
        }
    }
    $error = "Username atau password salah!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Petugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .card-login {
            max-width: 380px;
            margin: auto;
            border-radius: 18px !important;
            overflow: hidden;
            box-shadow: 0 12px 35px rgba(0,0,0,0.5);
        }
        .card-header {
            background: #2c3e50;
            color: white;
            padding: 1.4rem 1.5rem;
            text-align: center;
            border-radius: 18px 18px 0 0 !important;
        }
        .card-header i { 
            font-size: 38px; 
            margin-bottom: 8px;
        }
        .card-header h1 { 
            margin: 0; 
            font-size: 20px; 
            font-weight: 700; 
            letter-spacing: 0.5px;
        }

        .card-body { 
            padding: 1.8rem 2rem; 
            background: white; 
        }

        /* INPUT GROUP YANG SEMPURNA */
        .input-group {
            margin-bottom: 1rem;
        }
        .input-group-text {
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 10px 0 0 10px !important;
            padding: 10 18px;
            font-size: 15px;
        }
        .form-control {
            border-left: none !important;
            border-radius: 0 10px 10px 0 !important;
            padding: 12px 16px 12px 12px;   /* JARAK NYAMAN DARI IKON */
            font-size: 15px;
            height: auto;
        }
        .form-control:focus {
            border-color: #2c3e50;
            box-shadow: 0 0 0 0.22rem rgba(44,62,80,0.25);
        }
        .input-group:focus-within .input-group-text {
            background: #1a252f;
        }

        .btn-login {
            background: linear-gradient(135deg, #2c3e50, #1a252f);
            border: none;
            padding: 13px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 10px;
            transition: all 0.3s;
            margin-top: 8px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(44,62,80,0.4);
        }

        .alert-danger {
            padding: 12px;
            font-size: 14px;
            border-radius: 10px;
            margin-bottom: 1rem;
            border: none;
        }

        .back-link {
            color: #bbb;
            font-size: 13.5px;
            text-decoration: none;
            transition: color 0.3s;
        }
        .back-link:hover {
            color: white;
            text-decoration: underline;
        }

        .card-footer {
            background: #2c3e50;
            color: #aaa;
            padding: 1rem;
            text-align: center;
            font-size: 12.5px;
            border-radius: 0 0 18px 18px !important;
            line-height: 1.4;
        }
        .card-footer strong {
            color: #ddd;
        }
    </style>
</head>
<body>

<div class="card-login">
    <!-- HEADER -->
    <div class="card-header">
        <i class="fas fa-user-lock"></i>
        <h1>LOGIN PETUGAS</h1>
    </div>

    <!-- BODY -->
    <div class="card-body">
        <?php if($error): ?>
            <div class="alert alert-danger text-center">
                <i class="fas fa-exclamation-triangle me-2"></i><?= $error ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <!-- Username -->
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-user"></i>
                </span>
                <input type="text" name="username" class="form-control" 
                       placeholder="Username" required autofocus>
            </div>

            <!-- Password -->
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-lock"></i>
                </span>
                <input type="password" name="password" class="form-control" 
                       placeholder="Password" required>
            </div>

            <!-- Tombol Login -->
            <button type="submit" class="btn btn-login text-white w-100">
                <i class="fas fa-sign-in-alt me-2"></i>
                MASUK
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="index.php" class="back-link">
                <i class="fas fa-arrow-left me-1"></i>
                Kembali ke beranda
            </a>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="card-footer">
        Sistem Perpanjangan & Pengembalian Buku<br>
        <strong>Perpustakaan Kampus</strong>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>