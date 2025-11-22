<?php
require_once __DIR__ . '/_head.php';

// public/register.php
view_head('Daftar - Perpustakaan');

$err=''; $success='';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($nama === '' || $username === '' || $password === '') {
        $err = 'Semua field harus diisi.';
    } else {
        $stmt = mysqli_prepare($koneksi, "SELECT id FROM tb_user WHERE username = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if (mysqli_fetch_assoc($res)) {
            $err = 'Username sudah digunakan.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user';
            $ins = mysqli_prepare($koneksi, "INSERT INTO tb_user (nama, username, password, role) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($ins, "ssss", $nama, $username, $hash, $role);
            mysqli_stmt_execute($ins);
            $newid = mysqli_insert_id($koneksi);
            addLog($koneksi, $newid, 'Register');
            $success = 'Registrasi berhasil. Silakan login.';
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body">
        <h4 class="card-title mb-3">Daftar Akun</h4>
        <?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
        <form method="post">
          <div class="mb-3"><label>Nama Lengkap</label><input name="nama" class="form-control" required></div>
          <div class="mb-3"><label>Username</label><input name="username" class="form-control" required></div>
          <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
          <button class="btn btn-success w-100">Daftar</button>
        </form>
        <div class="mt-3 text-center"><a href="index.php?url=login">Sudah punya akun? Login</a></div>
      </div>
    </div>
  </div>
</div>

<?php view_footer(); ?>