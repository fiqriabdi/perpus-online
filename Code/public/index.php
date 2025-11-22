<?php
// public/index.php
session_start();
require_once __DIR__ . '/_head.php';

// simple router using ?url=
$url = $_GET['url'] ?? 'home';

// public pages
$publicRoutes = ['login','register','logout','home'];

// route map
switch ($url) {
    case 'login': require_once __DIR__.'/login.php'; break;
    case 'register': require_once __DIR__.'/register.php'; break;
    case 'logout': require_once __DIR__.'/logout.php'; break;
    case 'buku_list': require_once __DIR__.'/buku_list.php'; break;
    // user area
    case 'dashboard_user': require_once __DIR__ . '/../user/dashboard_user.php'; break;
    case 'pinjam': require_once __DIR__ . '/../user/pinjam.php'; break;
    case 'kembali': require_once __DIR__ . '/../user/kembali.php'; break;
    case 'perpanjang_ajukan': require_once __DIR__ . '/../user/perpanjang_ajukan.php'; break;
    case 'process_perpanjangan': require_once __DIR__ . '/../user/perpanjang_process.php'; break;
    // petugas area
    case 'dashboard_petugas': require_once __DIR__ . '/../petugas/dashboard_petugas.php'; break;
    case 'petugas_list': require_once __DIR__ . '/../petugas/petugas_list.php'; break;
    case 'petugas_acc': require_once __DIR__ . '/../petugas/petugas_acc.php'; break;
    // admin area
    case 'dashboard_admin':
    require_once __DIR__ . '/../admin/dashboard_admin.php';
    break;

case 'admin_buku_list':
    require_once __DIR__ . '/../admin/buku_list.php';
    break;

case 'admin_buku_add':
    require_once __DIR__ . '/../admin/buku_add.php';
    break;

case 'admin_buku_edit':
    require_once __DIR__ . '/../admin/buku_edit.php';
    break;

case 'admin_buku_delete':
    require_once __DIR__ . '/../admin/buku_delete.php';
    break;

case 'admin_user_list':
    require_once __DIR__ . '/../admin/user_list.php';
    break;

case 'logs':
    require_once __DIR__ . '/../admin/logs.php';
    break;
    case 'home':
    default:
        view_head('Beranda Perpustakaan');
        echo '<div class="card"><div class="card-body"><h4>Beranda</h4>
              <p><a href="index.php?url=buku_list" class="btn btn-primary">Lihat Buku</a>
              <a href="index.php?url=login" class="btn btn-secondary">Login</a></p></div></div>';
        view_footer();
        break;
}
