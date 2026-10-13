<?php
// 1. Variabel dengan tipe data yang berbeda
$namaMahasiswa   = "Khaila Salinaza"; // string
$jumlahKehadiran = 20;                 // integer
$bobotUas        = 0.40;               // float
$statusAktif     = true;               // boolean
$komponenNilai   = [                   // array
    "Tugas" => 85.0,
    "UTS"   => 78.5,
    "UAS"   => 90.0,
];

// 2. Function buatan sendiri untuk menghitung nilai akhir
function hitungNilaiAkhir(array $nilai, float $bobotAkhir): float {
    $bobotTugas = 0.30;
    $bobotUts   = 0.30;

    $total = ($nilai["Tugas"] * $bobotTugas) + 
             ($nilai["UTS"] * $bobotUts) + 
             ($nilai["UAS"] * $bobotAkhir);
             
    return round($total, 2);
}

// Menjalankan fungsi perhitungan nilai
$nilaiAkhir = hitungNilaiAkhir($komponenNilai, $bobotUas);

// 3. Percabangan untuk menentukan grade dan status kelulusan
if ($nilaiAkhir >= 85) {
    $grade = "A";
    $statusLulus = true;
} elseif ($nilaiAkhir >= 75) {
    $grade = "B";
    $statusLulus = true;
} elseif ($nilaiAkhir >= 65) {
    $grade = "C";
    $statusLulus = true;
} elseif ($nilaiAkhir >= 50) {
    $grade = "D";
    $statusLulus = false;
} else {
    $grade = "E";
    $statusLulus = false;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Perhitungan Nilai Mahasiswa</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #FFC0CB; }
        .card { background: #fff; padding: 24px; border-radius: 8px; max-width: 500px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { margin-top: 0; color: #333; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        th, td { text-align: left; padding: 8px; border-bottom: 1px solid #ddd; }
        .status-lulus { color: #7BCCB5; font-weight: bold; }
        .status-tidak-lulus { color: #931314; font-weight: bold; }
    </style>
</head>
<body>
    
<div class="card">
    <h2>Hasil Penilaian Mahasiswa</h2>
    <p><strong>Nama:</strong> <?= $namaMahasiswa; ?></p>
    <p><strong>Kehadiran:</strong> <?= $jumlahKehadiran; ?> Pertemuan</p>
    <p><strong>Status Mahasiswa:</strong> <?= $statusAktif ? "Aktif" : "Tidak Aktif"; ?></p>

    <table>
        <thead>
            <tr>
                <th>Komponen</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            <!-- 4. Perulangan untuk menampilkan daftar komponen nilai -->
            <?php foreach ($komponenNilai as $komponen => $nilai): ?>
            <tr>
                <td><?= $komponen; ?></td>
                <td><?= number_format($nilai, 1); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p><strong>Nilai Akhir:</strong> <?= $nilaiAkhir; ?></p>
    <p><strong>Grade:</strong> <?= $grade; ?></p>
    <p>
        <strong>Keterangan:</strong>
        <span class="<?= $statusLulus ? 'status-lulus' : 'status-tidak-lulus'; ?>">
            <?= $statusLulus ? "LULUS" : "TIDAK LULUS"; ?>
        </span>
    </p>
</div>

</body>
</html>