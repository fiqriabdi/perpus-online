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
echo '<p><a href="index.php">Kembali</a></p>'
    <button type="submit">Login</button> </form>
```
## includes/mail.php
```
<?php
// includes/mail.php

/**
 * send_mail($to, $subject, $body, $fromName = '', $fromEmail = null)
 * - by default uses PHP mail()
 * - if you want SMTP/PHPMailer, replace implementation with PHPMailer
 */
function send_mail(string $to, string $subject, string $body, string $fromName = 'Perpustakaan', ?string $fromEmail = null): bool {
    // basic headers
    $fromEmail = $fromEmail ?? 'noreply@localhost';
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
    $headers .= "From: {$fromName} <{$fromEmail}>\r\n";

    // PHP mail() (requires php.ini SMTP configured)
    return mail($to, $subject, $body, $headers);

    /*
    // If you want to use PHPMailer with SMTP, uncomment and implement below:
    // require_once __DIR__ . '/../vendor/autoload.php';
    // $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    // try {
    //    $mail->isSMTP();
    //    $mail->Host = 'smtp.example.com';
    //    $mail->SMTPAuth = true;
    //    $mail->Username = 'smtp_user';
    //    $mail->Password = 'smtp_pass';
    //    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    //    $mail->Port = 587;
    //    $mail->setFrom($fromEmail, $fromName);
    //    $mail->addAddress($to);
    //    $mail->isHTML(true);
    //    $mail->Subject = $subject;
    //    $mail->Body    = $body;
    //    return $mail->send();
    // } catch (Exception $e) {
    //    error_log("Mailer error: " . $mail->ErrorInfo);
    //    return false;
    // }
    */
}
```
## public/index.php
```
<?php
// public/index.php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/mail.php';

$url = $_GET['url'] ?? 'login';

// simple layout header & footer helpers
function view_head($title = 'Perpustakaan') {
    echo <<<HTML
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{$title}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
HTML;
}

function view_footer() {
    echo <<<HTML
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
}

// ROUTES
switch ($url) {
    case 'login':
        require __DIR__ . '/login.php';
        break;

    case 'logout':
        require __DIR__ . '/logout.php';
        break;

    case 'dashboard_user':
        require __DIR__ . '/dashboard_user.php';
        break;

    case 'dashboard_petugas':
        require __DIR__ . '/dashboard_petugas.php';
        break;

    case 'dashboard_admin':
        require __DIR__ . '/dashboard_admin.php';
        break;

    case 'ajukan':
        require __DIR__ . '/perpanjang_ajukan.php';
        break;

    case 'process_ajukan':
        require __DIR__ . '/perpanjang_process.php';
        break;

    case 'petugas_list':
        require __DIR__ . '/admin_perpanjangan_list.php';
        break;

    case 'petugas_acc':
        require __DIR__ . '/admin_perpanjangan_acc.php';
        break;

    default:
        view_head('404');
        echo "<div class='card p-4'><h3>404 - Halaman tidak ditemukan</h3></div>";
        view_footer();
        break;
}
```
## public/perpanjang_process.php (insert + kirim email notif ke petugas)
```
<?php
// public/perpanjang_process.php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/mail.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$id_peminjaman = intval($_POST['id_peminjaman'] ?? 0);
$user = $_SESSION['user'];

// validasi peminjaman milik user
$stmt = mysqli_prepare($conn, "SELECT id, judul_buku, tgl_jatuh_tempo, status FROM peminjaman WHERE id = ? AND user_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "ii", $id_peminjaman, $user['id']);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$pm = mysqli_fetch_assoc($res);

if (!$pm) {
    die('Peminjamanan tidak ditemukan.');
}

if ($pm['status'] !== 'dipinjam') {
    die('Tidak dapat diperpanjang: status bukan dipinjam.');
}

// cek telat
$now = new DateTime();
$due = new DateTime($pm['tgl_jatuh_tempo']);
if ($now > $due) {
    die('Tidak dapat diperpanjang: sudah melewati jatuh tempo.');
}

// cek pending existing
$q = mysqli_prepare($conn, "SELECT id FROM perpanjangan WHERE peminjaman_id = ? AND status = 'pending' LIMIT 1");
mysqli_stmt_bind_param($q, "i", $id_peminjaman);
mysqli_stmt_execute($q);
$exists = mysqli_stmt_get_result($q);
if (mysqli_fetch_assoc($exists)) {
    die('Sudah ada permintaan perpanjangan pending.');
}

// insert request
$ins = mysqli_prepare($conn, "INSERT INTO perpanjangan (peminjaman_id, user_id, request_date, status) VALUES (?, ?, NOW(), 'pending')");
mysqli_stmt_bind_param($ins, "ii", $id_peminjaman, $user['id']);
mysqli_stmt_execute($ins);

// kirim email ke semua petugas / admin
// Ambil email petugas atau admin
$roleTarget = 'petugas'; // kamu bisa juga kirim ke admin
$q = mysqli_prepare($conn, "SELECT email, nama FROM users WHERE role = ? AND email <> ''");
mysqli_stmt_bind_param($q, "s", $roleTarget);
mysqli_stmt_execute($q);
$rez = mysqli_stmt_get_result($q);

$subject = "Permintaan Perpanjangan Baru";
$body = "<p>Ada permintaan perpanjangan baru dari <strong>".htmlspecialchars($user['nama'])."</strong></p>
         <p>Buku: ".htmlspecialchars($pm['judul_buku'])."<br/>
         Jatuh tempo: ".htmlspecialchars($pm['tgl_jatuh_tempo'])."</p>
         <p><a href='http://localhost/perpustakaan/public/index.php?url=petugas_list'>Klik untuk lihat</a></p>";

while ($row = mysqli_fetch_assoc($rez)) {
    // gunakan send_mail helper
    if (!empty($row['email'])) {
        send_mail($row['email'], $subject, $body, 'Perpustakaan', 'noreply@perpus.local');
    }
}

view_head('Pengajuan Berhasil');
?>
<div class="card">
  <div class="card-body">
    <div class="alert alert-success">Permintaan perpanjangan terkirim. Petugas akan memeriksa.</div>
    <a href="index.php?url=dashboard_user" class="btn btn-primary">Kembali ke Dashboard</a>
  </div>
</div>
<?php view_footer(); ?>
```



