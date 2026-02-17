<?php
if (isset($_SERVER['HTTP_COOKIE'])) {
    $kue = $_SERVER['HTTP_COOKIE'];
    $cookies = explode(';', $kue);
    foreach ($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $user = trim($parts[0]);
        setcookie($user, '', time() - 1000);
        setcookie($user, '', time() - 1000, '/');
        setcookie("user", '', time() - 1000);
        setcookie("apl", '', time() - 1000);
        unset($_COOKIE['user']);
        setcookie('user', '', time() - 3600, '/');
    }
}

include "config/server.php";
$sql = mysql_query("select * from cbt_admin");
$r = mysql_fetch_array($sql);

$school_name = "SMK AL QODIRIYAH";
if (is_array($r) && isset($r['XSekolah']) && $r['XSekolah'] !== '') {
    $school_name = $r['XSekolah'];
}
$brand_name = "CBT " . $school_name;

$error_messages = array();
if (isset($_REQUEST['salah'])) {
    if ($_REQUEST['salah'] == 2) {
        $error_messages[] = "Database belum tersedia, hubungi Administrator Ujian.";
    }
    elseif ($_REQUEST['salah'] == 1) {
        $error_messages[] = "Username atau Password anda salah.";
    }
    elseif ($_REQUEST['salah'] == 3) {
        $error_messages[] = "Anda sudah login di tempat lain.";
    }
}
$has_server_error = !empty($error_messages);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>
        <?php echo htmlspecialchars($brand_name, ENT_QUOTES); ?> | Login Ujian
    </title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        function disableBackButton() {
            window.history.forward();
        }
        setTimeout(disableBackButton, 0);
    </script>

    <link rel="stylesheet" href="css/bootstrap2.min.css">
    <link rel="stylesheet" href="css/klien.css">

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

        .alert-card.is-visible {
            display: block;
        }

        .alert-title {
            font-weight: 700;
            margin-bottom: 4px;
        }

        .alert-body {
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-size: 13px;
        }

        .alert-list {
            margin: 0;
            padding-left: 18px;
        }

        .alert-list li {
            margin: 0;
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

        .password-wrap {
            position: relative;
        }

        .password-wrap input {
            padding-right: 44px;
        }

        .password-wrap #inputPassword,
        .password-wrap #inputPasswordText {
            width: 100%;
            border: 1px solid #d8e1f2;
            background: #fff;
            border-radius: 12px;
            padding: 12px 14px;
            padding-right: 44px;
            font-size: 14px;
            color: var(--ink);
            box-shadow: 0 6px 16px rgba(6, 22, 56, 0.08);
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .password-wrap #inputPasswordText {
            position: absolute;
            top: 0;
            left: 0;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #6b7a99;
            font-size: 18px;
            line-height: 1;
            padding: 4px;
            cursor: pointer;
        }

        .toggle-password:focus {
            outline: none;
            color: #0a52c9;
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
        }
    </style>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/inline.js"></script>
    <script src="js/jquery.js"></script>
    <script src="js/jquery.validate.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            var $errorCard = $("#myerror");
            var hasServerError = parseInt($errorCard.attr("data-server-error"), 10) === 1;
            var $passwordInput = $("#inputPassword");
            var $togglePassword = $("#togglePassword");

            $("#form1").validate({
                errorLabelContainer: "#myerror .alert-list",
                wrapper: "li",
                rules: {
                    UserName: "required",
                    Password: "required",
                    email: {
                        required: true,
                        email: true
                    },
                    url: {
                        required: true,
                        url: true
                    },
                    comment: {
                        required: true
                    }
                },
                messages: {
                    UserName: "Masukkan Username",
                    Password: "Masukkan Password",
                    comment: "Please enter a comment.",
                    url: "Please Enter Correct URL"
                },
                showErrors: function (errorMap, errorList) {
                    this.defaultShowErrors();
                    if (errorList.length || hasServerError) {
                        $errorCard.addClass("is-visible");
                    } else {
                        $errorCard.removeClass("is-visible");
                    }
                }
            });

            // Simple vanilla JS toggle - change input type directly
            var passwordInput = document.getElementById('inputPassword');
            var toggleButton = document.getElementById('togglePassword');

            if (toggleButton && passwordInput) {
                toggleButton.addEventListener('click', function (e) {
                    e.preventDefault();

                    if (passwordInput.type === 'password') {
                        // Show password
                        passwordInput.type = 'text';
                        toggleButton.textContent = '🙈';
                        toggleButton.setAttribute('aria-label', 'Sembunyikan password');
                        toggleButton.setAttribute('title', 'Sembunyikan password');
                    } else {
                        // Hide password
                        passwordInput.type = 'password';
                        toggleButton.textContent = '👁';
                        toggleButton.setAttribute('aria-label', 'Tampilkan password');
                        toggleButton.setAttribute('title', 'Tampilkan password');
                    }
                });
            }
        });
    </script>
</head>

<body class="font-medium">
    <div class="login-page">
        <div class="login-shell">
            <div class="login-aside">
                <div class="brand">
                    <span class="brand-dot"></span>
                    <span>
                        <?php echo htmlspecialchars($brand_name, ENT_QUOTES); ?>
                    </span>
                </div>
                <div class="welcome">
                    <div class="logo-mark">
                        <span class="logo-dot"></span>
                        <span>Login Ujian</span>
                    </div>
                    <h1>Halo, selamat datang!</h1>
                    <p>Masuk sebagai peserta ujian menggunakan username dan password yang diberikan.</p>
                </div>
                <div class="aside-footer">
                    <div class="aside-note">
                        <?php echo htmlspecialchars($brand_name, ENT_QUOTES); ?> 2026 | Developed by
                        Miftahussyarif
                    </div>
                </div>
            </div>
            <div class="login-panel">
                <div class="panel-head">
                    <div class="panel-kicker">
                        <?php echo htmlspecialchars($brand_name, ENT_QUOTES); ?>
                    </div>
                    <h2>Login Siswa</h2>
                    <p>Silakan masukkan username dan password untuk mulai ujian.</p>
                </div>
                <div id="myerror" class="alert-card<?php echo $has_server_error ? ' is-visible' : ''; ?>"
                    data-server-error="<?php echo $has_server_error ? '1' : '0'; ?>">
                    <div class="alert-title">Peringatan</div>
                    <div class="alert-body">
                        <ul class="alert-list">
                            <?php
foreach ($error_messages as $message) {
    echo "<li>" . $message . "</li>";
}
?>
                        </ul>
                    </div>
                </div>
                <form action="konfirm.php" method="post" data-toggle="validator" id="form1">
                    <div class="form-field">
                        <label for="inputUsername">Username</label>
                        <input id="inputUsername" name="UserName" placeholder="Username" type="text">
                    </div>
                    <div class="form-field">
                        <label for="inputPassword">Password</label>
                        <div class="password-wrap">
                            <input id="inputPassword" name="Password" placeholder="Password" type="password">
                            <button type="button" id="togglePassword" class="toggle-password"
                                aria-label="Tampilkan password" title="Tampilkan password">👁</button>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-login">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="js/jquery.cookie.js"></script>
    <script src="js/common.js"></script>
    <script src="js/main.js"></script>
    <script src="js/cookieList.js"></script>
    <script src="js/backend.js"></script>
</body>

</html>