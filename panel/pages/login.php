<title>CBT SMK AL QODIRIYAH </title>

<!-- Bootstrap Core CSS -->
<link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

<!-- MetisMenu CSS -->
<link href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

<!-- Custom CSS -->
<link href="../dist/css/sb-admin-2.css" rel="stylesheet">
</head>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Untitled Document</title>
</head>
<!-- Custom Fonts -->
<link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<style>
    :root {
        --page-bg-1: #0c2f74;
        --page-bg-2: #0e57aa;
        --panel-bg: #f3f6ff;
        --card-bg: #ffffff;
        --accent: #23c0ff;
        --accent-deep: #0a52c9;
        --ink: #0d1c3f;
        --muted: #5f6f90;
        --shadow: 0 30px 80px rgba(7, 18, 50, 0.28);
    }

    * {
        box-sizing: border-box;
    }

    html,
    body {
        height: 100%;
        margin: 0;
        padding: 0;
    }

    body {
        background: radial-gradient(900px 600px at 10% 20%, rgba(37, 147, 255, 0.65) 0%, rgba(37, 147, 255, 0) 60%),
            radial-gradient(500px 400px at 80% 80%, rgba(23, 191, 255, 0.35) 0%, rgba(23, 191, 255, 0) 70%),
            linear-gradient(135deg, var(--page-bg-1), var(--page-bg-2));
        color: var(--ink);
        font-family: "Trebuchet MS", "Candara", sans-serif;
    }

    .login-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 32px 16px;
    }

    .login-shell {
        width: min(980px, 100%);
        display: grid;
        grid-template-columns: minmax(260px, 45%) minmax(320px, 55%);
        border-radius: 18px;
        overflow: hidden;
        background: var(--card-bg);
        box-shadow: var(--shadow);
        animation: shellIn 600ms ease;
    }

    .login-aside {
        position: relative;
        padding: 40px 36px;
        color: #f7fbff;
        background: linear-gradient(145deg, #1480ff, #0a54c6 55%, #0a2c7f);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 24px;
        overflow: hidden;
    }

    .login-aside::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(120deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0) 40%),
            repeating-linear-gradient(135deg, rgba(255, 255, 255, 0.18) 0 2px, rgba(255, 255, 255, 0) 2px 14px);
        opacity: 0.45;
        pointer-events: none;
    }

    .login-aside::after {
        content: "";
        position: absolute;
        width: 240px;
        height: 240px;
        right: -80px;
        bottom: -80px;
        background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.65), rgba(255, 255, 255, 0) 65%);
        opacity: 0.7;
        filter: blur(2px);
    }

    .login-aside>* {
        position: relative;
        z-index: 1;
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.85);
        animation: fadeUp 600ms ease 60ms both;
    }

    .brand-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #ffffff;
        box-shadow: 0 0 0 6px rgba(255, 255, 255, 0.15);
    }

    .logo-mark {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
        font-size: 13px;
        padding: 6px 10px;
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 999px;
    }

    .logo-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #ffffff;
    }

    .welcome {
        animation: fadeUp 600ms ease 120ms both;
    }

    .welcome h1 {
        margin: 12px 0 8px;
        font-size: 34px;
        line-height: 1.05;
    }

    .welcome p {
        margin: 0;
        color: rgba(255, 255, 255, 0.85);
        font-size: 14px;
    }

    .aside-footer {
        display: flex;
        flex-direction: column;
        gap: 12px;
        animation: fadeUp 600ms ease 220ms both;
    }

    .btn-ghost {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 16px;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.5);
        color: #ffffff;
        background: rgba(13, 41, 100, 0.2);
        text-decoration: none;
        font-weight: 600;
        letter-spacing: 0.03em;
        width: fit-content;
    }

    .btn-ghost:hover {
        background: rgba(255, 255, 255, 0.18);
    }

    .aside-note {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.75);
    }

    .login-panel {
        background: var(--panel-bg);
        padding: 42px 46px;
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .panel-head {
        animation: fadeUp 600ms ease 160ms both;
    }

    .panel-head .panel-kicker {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: var(--muted);
    }

    .panel-head h2 {
        margin: 6px 0 6px;
        font-size: 28px;
    }

    .panel-head p {
        margin: 0;
        color: var(--muted);
        font-size: 14px;
    }

    .alert-card {
        display: none;
        border-left: 4px solid #ff4f5a;
        background: #ffe9ed;
        color: #982b32;
        border-radius: 10px;
        padding: 12px 14px;
    }

    .alert-title {
        font-weight: 700;
        margin-bottom: 4px;
    }

    .alert-body {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
        font-size: 13px;
    }

    .alert-card .btn-alert {
        display: inline-flex;
        padding: 6px 10px;
        border-radius: 999px;
        background: #ff4f5a;
        color: #fff;
        text-decoration: none;
        font-size: 12px;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 14px;
        animation: fadeUp 600ms ease 220ms both;
    }

    .form-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-field label {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--muted);
        font-weight: 700;
    }

    .form-field input {
        border: 1px solid #d8e1f2;
        background: #fff;
        border-radius: 12px;
        padding: 12px 14px;
        font-size: 14px;
        color: var(--ink);
        box-shadow: 0 6px 16px rgba(6, 22, 56, 0.08);
        transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    }

    .form-field input:focus {
        outline: none;
        border-color: #4ea1ff;
        box-shadow: 0 10px 24px rgba(4, 46, 122, 0.15);
        transform: translateY(-1px);
    }

    .switch-field {
        display: flex;
        gap: 6px;
        align-items: center;
        background: #fff;
        border-radius: 999px;
        padding: 4px;
        border: 1px solid #d8e1f2;
        width: fit-content;
    }

    .switch-field input {
        display: none;
    }

    .switch-field label {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 88px;
        padding: 8px 12px;
        border-radius: 999px;
        color: var(--muted);
        font-size: 12px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .switch-field input:checked+label {
        background: linear-gradient(120deg, #0b51c3, #24b6ff);
        color: #fff;
        box-shadow: 0 6px 16px rgba(10, 70, 160, 0.25);
    }

    .form-actions {
        margin-top: 6px;
    }

    .btn-login {
        width: 100%;
        border: none;
        border-radius: 12px;
        padding: 12px 16px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #fff;
        background: linear-gradient(120deg, #0b2f86, #19a7ff);
        box-shadow: 0 12px 26px rgba(7, 36, 102, 0.25);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .btn-login:hover {
        transform: translateY(-1px);
        box-shadow: 0 16px 30px rgba(7, 36, 102, 0.3);
    }

    @keyframes shellIn {
        from {
            opacity: 0;
            transform: translateY(12px) scale(0.98);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 900px) {
        .login-shell {
            grid-template-columns: 1fr;
        }

        .login-aside {
            min-height: 240px;
        }

        .login-panel {
            padding: 32px;
        }

        .panel-head h2 {
            font-size: 24px;
        }
    }

    @media (max-width: 520px) {
        .login-aside {
            padding: 28px;
        }

        .login-panel {
            padding: 28px 22px;
        }

        .switch-field {
            width: 100%;
            justify-content: space-between;
        }

        .switch-field label {
            flex: 1;
        }
    }
</style>
<script>
    function disableBackButton() {
        window.history.forward();
    }
    setTimeout("disableBackButton()", 0);
</script>
<script src="js/jquery-1.11.0.min.js"></script>
<script src="script.js"></script>
<script>function validateForm() {
        var x = document.forms["loginform"]["userz"].value;
        var y = document.forms["loginform"]["passz"].value;
        var peluru = '\u2022';
        if (x == null || x == "" || y == null || y == "") {
            //        alert("Name must be filled out");
            document.getElementById("ingat").style.display = "block";
            document.getElementById("isine").textContent = peluru + "Username dan Password harus diisi";
            return false;
        }


    }

</script>
<?php
// Connect to MySQL
include "../../config/server.php";

if (isset($sqlconn)) {
    //echo "Database $sqlconn";
} else {
    $pesan1 = "Tidak dapat Koneksi Database.";
}
if (!$sqlconn) {
    die('Could not connect: ' . mysql_error());
}

// Make my_db the current database
$db_selected = mysql_select_db('beesmartv3', $sqlconn);

if (!$db_selected) {
    // If we couldn't, then it either doesn't exist, or we can't see it.
    $sql = 'CREATE DATABASE beesmartv3';

    if (mysql_query($sql, $sqlconn)) {
        //    echo "Database my_db created successfully\n";


    } else {
        //    echo 'Error creating database: ' . mysql_error() . "\n";
    }
}
$val = mysql_query('select 1 from `cbt_admin` LIMIT 1');
?>

<div class="login-page">
    <div class="login-shell">
        <div class="login-aside">
            <div class="brand">
                <span class="brand-dot"></span>
                <span>CBT SMK AL QODIRIYAH</span>
            </div>
            <div class="welcome">
                <div class="logo-mark">
                    <span class="logo-dot"></span>
                    <span>Panel Admin</span>
                </div>
                <h1>Halo, selamat datang!</h1>
                <p>Masuk ke panel admin/guru untuk mengelola ujian, bank soal, dan peserta.</p>
            </div>
            <div class="aside-footer">
                <a class="btn-ghost" href="https://instagram.com/em_miftahussyarif" target="_blank"
                    rel="noopener">Developer Contact</a>
                <div class="aside-note">Develop by | Muhammad Miftahus Syarif</div>
            </div>
        </div>
        <div class="login-panel">
            <div class="panel-head">
                <div class="panel-kicker">CBT SMK AL QODIRIYAH</div>
                <h2>Login Panel</h2>
                <p>Silahkan masukkan username dan password untuk melanjutkan.</p>
            </div>
            <div id="ingat" class="alert-card" style="display:none">
                <div class="alert-title">Peringatan</div>
                <div class="alert-body">
                    <span id="isine">
                        <?php
                        if ($val == FALSE) { ?>
                            <script>
                                $(document).ready(function () {
                                    var peluru = '\u2022';
                                    document.getElementById("ingat").style.display = "block";
                                    document.getElementById("isine").textContent = peluru + " <?php echo "Database belum Terbentuk, Klik disini untuk Proses Buat Database"; ?>";
                                    return false;
                                });
                            </script>
                            <?php
                        }
                        ?>
                    </span>
                    <?php
                    if ($val == FALSE) { ?><a href="buat_database.php" class="btn-alert">Buat Database</a>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <form id="loginform" name="loginform" onSubmit="return validateForm();" action="../pages/ceklogin.php"
                method="post">
                <div class="form-field">
                    <label for="userz">Username</label>
                    <input type="text" id="userz" name="userz" placeholder="Username">
                </div>
                <div class="form-field">
                    <label for="passz">Password</label>
                    <input type="password" id="passz" name="passz" placeholder="Password">
                </div>
                <div class="switch-field">
                    <input type="radio" id="switch_left" name="login" value="admin" checked />
                    <label for="switch_left">Admin</label>
                    <input type="radio" id="switch_right" name="login" value="guru" />
                    <label for="switch_right">Guru</label>
                    <input type="radio" id="switch_pengawas" name="login" value="pengawas" />
                    <label for="switch_pengawas">Pengawas</label>
                </div>
                <?php
                if (!$val == FALSE) { ?>
                    <div class="form-actions">
                        <input type="submit" class="btn-login" value="Login">
                    </div>
                    <?php
                }
                ?>
            </form>
        </div>
    </div>
</div>

</body>

</html>


<script src="../../js/jquery.wallform.js"></script>

<!-- jQuery -->
<script src="../vendor/jquery/jquery.min.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="../vendor/bootstrap/js/bootstrap.min.js"></script>

<!-- Metis Menu Plugin JavaScript -->
<script src="../vendor/metisMenu/metisMenu.min.js"></script>

<!-- Morris Charts JavaScript -->
<script src="../vendor/raphael/raphael.min.js"></script>
<script src="../vendor/morrisjs/morris.min.js"></script>
<script src="../data/morris-data.js"></script>

<!-- Custom Theme JavaScript -->
<script src="../dist/js/sb-admin-2.js"></script>


<!-- DataTables JavaScript -->
<script src="../vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="../vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
<script src="../vendor/datatables-responsive/dataTables.responsive.js"></script>
</body>

</html>
