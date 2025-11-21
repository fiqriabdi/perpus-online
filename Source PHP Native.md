## config/koneksi.php
```
<?php
$koneksi = mysqli_connect("localhost", "root", "", "perpustakaan");

if (mysqli_connect_errno()) {
    die("Gagal terkoneksi: " . mysqli_connect_error());
}
?>
```
## public/login.php
```
<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $q = mysqli_prepare($koneksi, "SELECT * FROM tb_user WHERE username = ? LIMIT 1");
    mysqli_stmt_bind_param($q, "s", $username);
    mysqli_stmt_execute($q);
    $res = mysqli_stmt_get_result($q);
    $user = mysqli_fetch_assoc($res);

    if ($user && password_verify($password, $user['password'])) {
        unset($user['password']);
        $_SESSION['user'] = $user;

        header("Location: index.php");
        exit;
    } else {
        $error = "Username atau password salah";
    }
}
?>

<h2>Login</h2>

<?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post">
    <label>Username:<br>
        <input name="username" required>
    </label><br>

    <label>Password:<br>
        <input name="password" type="password" required>
    </label><br>
```

## public/logout.php
```
<?php
session_start();

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}

session_destroy();

header("Location: index.php?url=login");
exit;
?>
```
## user/perpanjang_form.php
```
<?php
if (!isset($_SESSION['user'])) {
    header("Location: index.php?url=login");
    exit;
}

require_once __DIR__ . '/../config/koneksi.php';

$user = $_SESSION['user'];

$q = mysqli_prepare($koneksi,
    "SELECT id, buku_judul, tgl_jatuh_tempo 
     FROM tb_peminjaman 
     WHERE user_id = ? AND status = 'dipinjam'"
);
mysqli_stmt_bind_param($q, "i", $user['id']);
mysqli_stmt_execute($q);
$res = mysqli_stmt_get_result($q);
?>

<h2>Ajukan Perpanjangan</h2>

<?php if (mysqli_num_rows($res) == 0): ?>
    <p>Tidak ada peminjaman aktif.</p>
<?php else: ?>

<form method="post" action="index.php?url=user_perpanjang_process">
<table border="1" cellpadding="6">
    <tr>
        <th>Pilih</th>
        <th>Buku</th>
        <th>Tgl Jatuh Tempo</th>
    </tr>

    <?php while ($r = mysqli_fetch_assoc($res)): ?>
    <tr>
        <td>
            <input type="radio" name="peminjaman_id"
                   value="<?= $r['id'] ?>" required>
        </td>
        <td><?= htmlspecialchars($r['buku_judul']) ?></td>
        <td><?= htmlspecialchars($r['tgl_jatuh_tempo']) ?></td>
    </tr>
    <?php endwhile; ?>
</table>

<p><button type="submit">Ajukan Perpanjangan</button></p>
</form>

<?php endif; ?>
```
## petugas/perpanjang_request.php
```
<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'petugas') {
    header('Location: ../public/login.php');
    exit;
}

$sql = "
    SELECT 
        p.id AS req_id,
        p.request_date,
        pm.buku_judul,
        u.nama
    FROM tb_perpanjangan p
    JOIN tb_peminjaman pm ON pm.id = p.peminjaman_id
    JOIN tb_user u ON u.id = p.user_id
    WHERE p.status = 'pending'
";

$result = mysqli_query($koneksi, $sql);
?>

<h2>Permintaan Perpanjangan (Pending)</h2>

<?php if (mysqli_num_rows($result) == 0): ?>
    <p>Tidak ada permintaan.</p>
<?php endif; ?>

<?php while ($r = mysqli_fetch_assoc($result)): ?>
    <div style="border:1px solid #ccc;padding:8px;margin:8px;">
        <b><?= htmlspecialchars($r['nama']) ?></b> -
        <?= htmlspecialchars($r['buku_judul']) ?> -
        requested: <?= htmlspecialchars($r['request_date']) ?>

        <a href="perpanjang_acc.php?id=<?= $r['req_id'] ?>">ACC</a>
    </div>
<?php endwhile; ?>
```
## petugas/perpanjang_acc.php
```
<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'petugas') {
    header('Location: ../public/login.php');
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { die("ID tidak valid."); }

// Ambil request
$q = mysqli_prepare($koneksi,
    "SELECT * FROM tb_perpanjangan WHERE id = ? AND status = 'pending'"
);
mysqli_stmt_bind_param($q, "i", $id);
mysqli_stmt_execute($q);
$res = mysqli_stmt_get_result($q);
$req = mysqli_fetch_assoc($res);

if (!$req) {
    die("Request tidak ditemukan atau sudah diproses.");
}

// Ambil peminjaman
$q = mysqli_prepare($koneksi,
    "SELECT * FROM tb_peminjaman WHERE id = ? LIMIT 1"
);
mysqli_stmt_bind_param($q, "i", $req['peminjaman_id']);
mysqli_stmt_execute($q);
$pmRes = mysqli_stmt_get_result($q);
$pin = mysqli_fetch_assoc($pmRes);

if (!$pin) { die("Data peminjaman tidak ditemukan."); }

$today = new DateTime();
$due   = new DateTime($pin['tgl_jatuh_tempo']);

if ($today > $due) {
    die("Gagal: sudah lewat jatuh tempo.");
}

// Tambah 7 hari
$due->modify('+7 days');
$newDue = $due->format('Y-m-d');

// Update peminjaman
$u = mysqli_prepare($koneksi,
   "UPDATE tb_peminjaman SET tgl_jatuh_tempo = ? WHERE id = ?"
);
mysqli_stmt_bind_param($u, "si", $newDue, $pin['id']);
mysqli_stmt_execute($u);

// Update status perpanjangan
$u2 = mysqli_prepare($koneksi,
   "UPDATE tb_perpanjangan SET status='approved', processed_date=NOW() WHERE id=?"
);
mysqli_stmt_bind_param($u2, "i", $id);
mysqli_stmt_execute($u2);

echo "<p>Perpanjangan berhasil disetujui.<br>Jatuh tempo baru: <b>$newDue</b></p>";
echo '<p><a href="index.php">Kembali</a></p>';
```


    <button type="submit">Login</button>
</form>
