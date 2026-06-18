<?php
// Aktifkan pelaporan error internal PHP secara penuh untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'koneksi.php';

// Proteksi Halaman
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['simpan'])) {
    // Mengamankan inputan form dari SQL Injection
    $nama_proyek = mysqli_real_escape_string($koneksi, $_POST['nama_proyek']);
    $nilai_kontrak = mysqli_real_escape_string($koneksi, $_POST['nilai_kontrak']);

    // Array untuk menampung nama file yang berhasil diunggah
    $nama_file_uploaded = [];

    // Memastikan folder uploads sudah tersedia
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }

    // Proses unggah banyak berkas (Multiple File Upload)
    if (!empty($_FILES['file_lampiran']['name'][0])) {
        $total_files = count($_FILES['file_lampiran']['name']);
        for ($i = 0; $i < $total_files; $i++) {
            $nama_asli = $_FILES['file_lampiran']['name'][$i];
            $tmp_name = $_FILES['file_lampiran']['tmp_name'][$i];
            $error = $_FILES['file_lampiran']['error'][$i];

            // Validasi jika ukuran file melebihi batas php.ini server
            if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
                echo "<script>
                    alert('Gagal! Ukuran file \"$nama_asli\" terlalu besar untuk diunggah.');
                    window.location.href='dashboard.php';
                </script>";
                exit;
            }

            if ($error === 0) {
                // Membuat nama file unik dengan prefix waktu timestamp
                $nama_baru = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $nama_asli);
                $target_path = "uploads/" . $nama_baru;

                // Memindahkan file dari folder sementara ke folder uploads utama
                if (move_uploaded_file($tmp_name, $target_path)) {
                    $nama_file_uploaded[] = $nama_baru;
                }
            }
        }
    }

    // Menggabungkan nama-nama file menjadi string tunggal dipisahkan koma
    $string_lampiran = implode(',', $nama_file_uploaded);

    // Perintah SQL memasukkan data kontrak baru (Status awal: 0 = Pending)
    $query = "INSERT INTO contracts (nama_proyek, nilai_kontrak, status, file_lampiran, tanda_tangan) 
              VALUES ('$nama_proyek', '$nilai_kontrak', 0, '$string_lampiran', '')";

    if (mysqli_query($koneksi, $query)) {
        header("Location: dashboard.php");
        exit;
    } else {
        // Mode Debugging jika query gagal masuk database
        echo "<div style='padding: 20px; border: 3px solid #f44336; background-color: #ffebee; font-family: sans-serif; border-radius: 5px; margin: 20px;'>";
        echo "<h2 style='color: #d32f2f; margin-top: 0;'>🚨 Depurasi: Query GAGAL Masuk Database!</h2>";
        echo "<p><strong>Pesan Error MySQL:</strong> <code style='background: #fff; padding: 4px 8px; border: 1px solid #ccc; display: block; margin-top: 5px; color: #b71c1c;'>" . mysqli_error($koneksi) . "</code></p>";
        echo "<p><strong>Isi Query yang Dikirim:</strong> <pre style='background: #fff; padding: 10px; border: 1px solid #ccc; overflow-x: auto;'>" . htmlspecialchars($query) . "</pre></p>";
        echo "<hr style='border: 0; border-top: 1px solid #ccc; margin: 20px 0;'>";
        echo "<a href='dashboard.php' style='display: inline-block; background: #2196F3; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; font-weight: bold;'>⬅️ Kembali ke Dashboard</a>";
        echo "</div>";
        exit;
    }
} else {
    header("Location: dashboard.php");
    exit;
}
?>