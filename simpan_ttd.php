<?php
include 'koneksi.php';

// Memastikan data ID kontrak dan Gambar dikirim melalui AJAX
if (isset($_POST['id']) && isset($_POST['image'])) {
    $id = $_POST['id'];
    $img_base64 = $_POST['image'];

    if (empty($id) || empty($img_base64)) {
        echo "Gagal: Data ID atau Gambar kosong.";
        exit;
    }

    // Memecah string base64 data URL canvas
    $image_parts = explode(";base64,", $img_base64);
    if (!isset($image_parts[1])) {
        echo "Gagal: Format gambar tidak valid.";
        exit;
    }

    // Decode data string base64 menjadi binary gambar asli
    $image_base64_decoded = base64_decode($image_parts[1]);

    // Membuat nama file tanda tangan unik agar tidak saling tertimpa
    $nama_file_ttd = "ttd_" . $id . "_" . time() . ".png";
    $folder_target = "uploads/" . $nama_file_ttd;

    // Antisipasi jika folder uploads belum terbuat
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }

    // Simpan file gambar fisik ke dalam folder uploads
    if (file_put_contents($folder_target, $image_base64_decoded)) {
        // Jalankan Query UPDATE database: set nama file ttd & set status = 1 (Disetujui)
        $query = "UPDATE contracts SET status = 1, tanda_tangan = '$nama_file_ttd' WHERE id = '$id'";
        if (mysqli_query($koneksi, $query)) {
            echo "sukses"; // Mengembalikan text 'sukses' murni tanpa HTML untuk ditangkap AJAX
        } else {
            echo "Gagal update database: " . mysqli_error($koneksi);
        }
    } else {
        echo "Gagal menulis file ke folder uploads. Periksa izin folder Anda.";
    }
} else {
    echo "Gagal: Metode request tidak sah atau data kurang.";
}
?>