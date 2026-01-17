<?php

function db_parse_host($host)
{
    $host = trim($host);
    $port = null;

    if ($host === '') {
        return array('', null);
    }

    if (strpos($host, ':') !== false && substr_count($host, ':') === 1) {
        $parts = explode(':', $host, 2);
        $host = $parts[0];
        $port = $parts[1] !== '' ? (int) $parts[1] : null;
    }

    return array($host, $port);
}

function db_build_dsn($host, $port, $dbname, $charset)
{
    $dsn = 'mysql:host=' . $host;
    if (!empty($port)) {
        $dsn .= ';port=' . (int) $port;
    }
    if (!empty($dbname)) {
        $dsn .= ';dbname=' . $dbname;
    }
    if (!empty($charset)) {
        $dsn .= ';charset=' . $charset;
    }

    return $dsn;
}

function db_connect($host, $user, $pass, $dbname, $charset)
{
    list($host, $port) = db_parse_host($host);
    $dsn = db_build_dsn($host, $port, $dbname, $charset);

    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    );

    return new PDO($dsn, $user, $pass, $options);
}

function db_local($allow_create = false)
{
    $db_host = 'localhost:3306';
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'beesmartv3';
    $db_charset = 'latin1';

    try {
        return db_connect($db_host, $db_user, $db_pass, $db_name, $db_charset);
    } catch (PDOException $e) {
        $error_code = isset($e->errorInfo[1]) ? (int) $e->errorInfo[1] : 0;
        if (!$allow_create || $error_code !== 1049) {
            throw $e;
        }

        $admin = db_connect($db_host, $db_user, $db_pass, '', $db_charset);
        $admin->exec('CREATE DATABASE IF NOT EXISTS `' . $db_name . '`');
        return db_connect($db_host, $db_user, $db_pass, $db_name, $db_charset);
    }
}

function db_pusat()
{
    require __DIR__ . '/ipserver.php';

    $db_name = isset($database) ? $database : 'beesmartv3';
    $db_charset = 'latin1';

    return db_connect($host_name, $user_name, $password, $db_name, $db_charset);
}

function db_query($db, $sql, $params)
{
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function db_fetch_one($stmt)
{
    $row = $stmt->fetch();
    return $row === false ? null : $row;
}

function db_fetch_value($stmt)
{
    $value = $stmt->fetchColumn();
    return $value === false ? null : $value;
}
