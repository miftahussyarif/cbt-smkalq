<?php
if (!isset($_COOKIE['beeuser'])) {
    header("Location: login.php");
    exit;
}

include "../../config/server.php";

$userRole = isset($_COOKIE['beelogin']) ? $_COOKIE['beelogin'] : '';
if ($userRole !== 'admin') {
    echo "Akses ditolak.";
    exit;
}

$filename = 'data_siswa_' . date('Ymd_His') . '.xls';

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

if (function_exists('bee_log')) {
    bee_log('INFO', 'EXPORT_SISWA_EXCEL', 'Export data siswa ke excel', array(
        'by' => isset($_COOKIE['beeuser']) ? $_COOKIE['beeuser'] : '-'
    ));
}

$sql = mysql_query("SELECT
    XNomerUjian,
    XNamaSiswa,
    XNIK,
    XSesi,
    XRuang,
    XKodeLevel,
    XKodeKelas,
    XJenisKelamin,
    XPassword,
    XKodeJurusan,
    XFoto,
    XAgama,
    XPilihan
    FROM cbt_siswa
    ORDER BY XNomerUjian ASC");
?>
<table border="1">
    <tr>
        <td colspan="13"><strong>DATA SISWA (HASIL EXPORT DATABASE)</strong></td>
    </tr>
    <tr>
        <th>NOMER_UJIAN</th>
        <th>NAMA_SISWA</th>
        <th>NIK_NISN</th>
        <th>SESI</th>
        <th>RUANG</th>
        <th>LEVEL</th>
        <th>KELAS</th>
        <th>JENIS_KELAMIN</th>
        <th>PASSWORD</th>
        <th>JURUSAN</th>
        <th>FOTO</th>
        <th>AGAMA</th>
        <th>PILIHAN</th>
    </tr>
    <?php
    if ($sql) {
        while ($row = mysql_fetch_assoc($sql)) {
            ?>
            <tr>
                <td><?php echo htmlspecialchars($row['XNomerUjian']); ?></td>
                <td><?php echo htmlspecialchars($row['XNamaSiswa']); ?></td>
                <td><?php echo htmlspecialchars($row['XNIK']); ?></td>
                <td><?php echo htmlspecialchars($row['XSesi']); ?></td>
                <td><?php echo htmlspecialchars($row['XRuang']); ?></td>
                <td><?php echo htmlspecialchars($row['XKodeLevel']); ?></td>
                <td><?php echo htmlspecialchars($row['XKodeKelas']); ?></td>
                <td><?php echo htmlspecialchars($row['XJenisKelamin']); ?></td>
                <td><?php echo htmlspecialchars($row['XPassword']); ?></td>
                <td><?php echo htmlspecialchars($row['XKodeJurusan']); ?></td>
                <td><?php echo htmlspecialchars($row['XFoto']); ?></td>
                <td><?php echo htmlspecialchars($row['XAgama']); ?></td>
                <td><?php echo htmlspecialchars($row['XPilihan']); ?></td>
            </tr>
            <?php
        }
    }
    ?>
</table>
