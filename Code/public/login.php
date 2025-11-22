<?php
require_once __DIR__ . '/_head.php';

// public/login.php
view_head('Login - Perpustakaan');

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare($koneksi, "SELECT id, nama, username, password, role FROM tb_user WHERE username = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if ($user && password_verify($password, $user['password'])) {
        unset($user['password']);
        $_SESSION['user'] = $user;
        addLog($koneksi, $user['id'], 'Login');
        // redirect by role
        if ($user['role'] === 'admin') header('Location: index.php?url=dashboard_admin');
        elseif ($user['role'] === 'petugas') header('Location: index.php?url=dashboard_petugas');
        else header('Location: index.php?url=dashboard_user');
        exit;
    } else {
        $err = 'Username atau password salah';
    }
}
?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body">
        <h4 class="card-title mb-3">Login</h4>
        <?php if ($err): ?>
          <div class="alert alert-danger"><?= e($err) ?></div>
        <?php endif; ?>
        <form method="post">
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input name="username" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input name="password" type="password" class="form-control" required>
          </div>
          <button class="btn btn-primary">Login</button>
        </form>
        <div class="mt-3">
            <a href="index.php?url=register">Belum punya akun? Daftar</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php view_footer(); ?>