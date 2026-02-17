<?php
// 1. Connect ke database
$sqlconn=@mysql_connect("localhost:3306","root","");
// 2. Pilih database
date_default_timezone_set("Asia/Jakarta");
mysql_select_db("beesmartv3", $sqlconn);
$mode = "lokal"; // pilih 'lokal' atau 'pusat'

if (!defined('BEE_LOG_DIR')) {
    define('BEE_LOG_DIR', sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'cbt-smkalq-log');
}
if (!defined('BEE_LOG_FILE')) {
    define('BEE_LOG_FILE', BEE_LOG_DIR . DIRECTORY_SEPARATOR . 'admin.log');
}

if (!function_exists('bee_log_bootstrap')) {
    function bee_log_bootstrap()
    {
        if (!is_dir(BEE_LOG_DIR)) {
            @mkdir(BEE_LOG_DIR, 0777, true);
        }
    }
}

if (!function_exists('bee_get_log_file')) {
    function bee_get_log_file()
    {
        bee_log_bootstrap();
        return BEE_LOG_FILE;
    }
}

if (!function_exists('bee_error_level_name')) {
    function bee_error_level_name($errno)
    {
        switch ($errno) {
            case E_ERROR: return 'E_ERROR';
            case E_WARNING: return 'E_WARNING';
            case E_PARSE: return 'E_PARSE';
            case E_NOTICE: return 'E_NOTICE';
            case E_CORE_ERROR: return 'E_CORE_ERROR';
            case E_CORE_WARNING: return 'E_CORE_WARNING';
            case E_COMPILE_ERROR: return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING: return 'E_COMPILE_WARNING';
            case E_USER_ERROR: return 'E_USER_ERROR';
            case E_USER_WARNING: return 'E_USER_WARNING';
            case E_USER_NOTICE: return 'E_USER_NOTICE';
            case E_STRICT: return 'E_STRICT';
            case E_RECOVERABLE_ERROR: return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED: return 'E_DEPRECATED';
            case E_USER_DEPRECATED: return 'E_USER_DEPRECATED';
            default: return 'E_UNKNOWN';
        }
    }
}

if (!function_exists('bee_log')) {
    function bee_log($level, $action, $message, $context = array())
    {
        bee_log_bootstrap();
        $entry = array(
            'time' => date('Y-m-d H:i:s'),
            'level' => strtoupper((string)$level),
            'action' => (string)$action,
            'message' => (string)$message,
            'user' => isset($_COOKIE['beeuser']) ? $_COOKIE['beeuser'] : '-',
            'role' => isset($_COOKIE['beelogin']) ? $_COOKIE['beelogin'] : '-',
            'ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '-',
            'uri' => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '-',
            'context' => is_array($context) ? $context : array('raw' => (string)$context)
        );
        @file_put_contents(bee_get_log_file(), json_encode($entry) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

if (!defined('BEE_ERROR_HANDLER_REGISTERED')) {
    define('BEE_ERROR_HANDLER_REGISTERED', true);

    set_error_handler(function ($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            return false;
        }
        bee_log('ERROR', 'PHP_ERROR', $errstr, array(
            'errno' => bee_error_level_name($errno),
            'file' => $errfile,
            'line' => $errline
        ));
        return false;
    });

    set_exception_handler(function ($exception) {
        bee_log('ERROR', 'PHP_EXCEPTION', $exception->getMessage(), array(
            'file' => $exception->getFile(),
            'line' => $exception->getLine()
        ));
    });

    register_shutdown_function(function () {
        $err = error_get_last();
        if (!$err) {
            return;
        }
        $fatalTypes = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR);
        if (in_array($err['type'], $fatalTypes, true)) {
            bee_log('FATAL', 'PHP_FATAL', $err['message'], array(
                'errno' => bee_error_level_name($err['type']),
                'file' => $err['file'],
                'line' => $err['line']
            ));
        }
    });
}
?>
