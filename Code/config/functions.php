<?php
// config/functions.php
// helper functions: addLog, send_mail, safe output

function addLog($koneksi, $user_id, $activity) {
    $stmt = mysqli_prepare($koneksi, "INSERT INTO audit_log (user_id, activity) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "is", $user_id, $activity);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function send_mail_simple($to, $subject, $htmlBody, $from='noreply@localhost') {
    // basic mail wrapper - requires mail() configured in php.ini
    $headers  = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
    $headers .= "From: Perpustakaan <".$from .">" . "\r\n";
    return mail($to, $subject, $htmlBody, $headers);
}

function e($v) {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}
?>