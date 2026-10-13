<?php
$hasilTersedia = false;

// Konfigurasi dipusatkan di satu tempat
const BOBOT = ["Tugas" => 0.30, "UTS" => 0.30, "UAS" => 0.40];
const TOTAL_PERTEMUAN = 16;
const MIN_KEHADIRAN   = 75;   // persen

// Validasi: total bobot harus 100%
if (abs(array_sum(BOBOT) - 1.0) > 0.0001) {
    die("Konfigurasi bobot tidak valid: totalnya harus 100%.");
}

// Function menghitung nilai akhir berdasarkan array bobot
function hitungNilaiAkhir(array $nilai, array $bobot): float {
    $total = 0;
    foreach ($bobot as $komponen => $b) {
        $total += $nilai[$komponen] * $b;
    }
    return round($total, 2);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $namaMahasiswa   = trim($_POST["nama"]);
    $jumlahKehadiran = (int) $_POST["kehadiran"];
    $statusAktif     = isset($_POST["status_aktif"]);
    $komponenNilai   = [
        "Tugas" => (float) $_POST["nilai_tugas"],
        "UTS"   => (float) $_POST["nilai_uts"],
        "UAS"   => (float) $_POST["nilai_uas"]
    ];

    $nilaiAkhir  = hitungNilaiAkhir($komponenNilai, BOBOT);
    $persenHadir = round($jumlahKehadiran / TOTAL_PERTEMUAN * 100, 1);

    if     ($nilaiAkhir >= 85) { $grade = "A"; }
    elseif ($nilaiAkhir >= 75) { $grade = "B"; }
    elseif ($nilaiAkhir >= 65) { $grade = "C"; }
    elseif ($nilaiAkhir >= 50) { $grade = "D"; }
    else                       { $grade = "E"; }

    // Kelulusan ditentukan oleh nilai, kehadiran, dan status aktif
    $alasan = [];
    if (in_array($grade, ["D", "E"])) {
        $alasan[] = "Nilai akhir di bawah 65 (grade $grade)";
    }
    if ($persenHadir < MIN_KEHADIRAN) {
        $alasan[] = "Kehadiran $persenHadir% (minimal " . MIN_KEHADIRAN . "%)";
    }
    if (!$statusAktif) {
        $alasan[] = "Mahasiswa berstatus tidak aktif";
    }
    $statusLulus = empty($alasan);

    $hasilTersedia = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Input & Perhitungan Nilai Mahasiswa</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 60px 20px; background-color: #fdffc6; color: #310000; }
        .container { display: flex; flex-wrap: wrap; justify-content: center; gap: 24px; max-width: 980px; margin: 0 auto; }
        .card { background: #ffb6d0a7; padding: 24px; border-radius: 8px; box-shadow: 0 2px 8px rgba(10, 9, 9, 0.69); flex: 0 1 460px; min-width: 320px; box-sizing: border-box; }
        h2 { margin-top: 0; color: #075b40; font-size: 25px; }
        .form-group { margin-bottom: 14px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; font-size: 14px; }
        input[type="text"], input[type="number"] { width: 100%; padding: 8px; border: 1px solid #fdffc6a4; border-radius: 4px; box-sizing: border-box; }
        .checkbox-group { display: flex; align-items: center; gap: 8px; margin: 16px 0; }
        button { background-color: #fdffc6; color: #075b40; border: none; padding: 10px 18px; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: bold; width: 100%; }
        button:hover { background-color: #9bfbdc; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        th, td { text-align: left; padding: 8px; border-bottom: 3px solid #fdffc6; }
        .badge-lulus { color: #166534; background: #dcfce7; padding: 4px 8px; border-radius: 4px; font-weight: bold; }
        .badge-gagal { color: #991b1b; background: #fee2e2; padding: 4px 8px; border-radius: 4px; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <!-- Form Input Data -->
    <div class="card">
        <h2>Input Data Mahasiswa</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="nama">Nama Mahasiswa:</label>
                <input type="text" id="nama" name="nama" required placeholder="Contoh: Khaila Salinaza">
            </div>

            <div class="form-group">
                <label for="kehadiran">Jumlah Kehadiran (Pertemuan):</label>
                <input type="number" id="kehadiran" name="kehadiran" min="0" max="16" required placeholder="0 - 16">
            </div>

            <div class="form-group">
                <label for="nilai_tugas">Nilai Tugas (0 - 100):</label>
                <input type="number" id="nilai_tugas" name="nilai_tugas" step="0.1" min="0" max="100" required placeholder="Contoh: 85.5">
            </div>

            <div class="form-group">
                <label for="nilai_uts">Nilai UTS (0 - 100):</label>
                <input type="number" id="nilai_uts" name="nilai_uts" step="0.1" min="0" max="100" required placeholder="Contoh: 78.0">
            </div>

            <div class="form-group">
                <label for="nilai_uas">Nilai UAS (0 - 100):</label>
                <input type="number" id="nilai_uas" name="nilai_uas" step="0.1" min="0" max="100" required placeholder="Contoh: 90.0">
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="status_aktif" name="status_aktif" value="1" checked>
                <label for="status_aktif" style="margin-bottom:0; font-weight:normal;">Mahasiswa Berstatus Aktif</label>
            </div>

            <button type="submit">Hitung Nilai</button>
        </form>
    </div>

    <!-- Output Hasil Perhitungan -->
    <?php if ($hasilTersedia): ?>
    <div class="card">
        <h2>Hasil Penilaian Mahasiswa</h2>
        <p><strong>Nama:</strong> <?= htmlspecialchars($namaMahasiswa); ?></p>
        <p><strong>Kehadiran:</strong> <?= $jumlahKehadiran; ?> dari <?= TOTAL_PERTEMUAN; ?> pertemuan (<?= $persenHadir; ?>%)</p>
        <p><strong>Status Mahasiswa:</strong> <?= $statusAktif ? "Aktif" : "Tidak Aktif"; ?></p>

        <table>
            <thead>
                <tr>
                    <th>Komponen</th>
                    <th>Nilai Input</th>
                </tr>
            </thead>
            <tbody>
                <!-- 4. Perulangan untuk menampilkan setiap komponen nilai -->
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
            <span class="<?= $statusLulus ? 'badge-lulus' : 'badge-gagal'; ?>">
                <?= $statusLulus ? "LULUS" : "TIDAK LULUS"; ?>
            </span>
        </p>

        <?php if (!$statusLulus): ?>
        <p><strong>Alasan:</strong></p>
        <ul>
            <?php foreach ($alasan as $a): ?>
                <li><?= htmlspecialchars($a); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
<?php endif; ?>
</div>

</body>
</html>