<?php
session_start();
// Proteksi: Jika belum login, kembalikan ke login.php
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panduan Penggunaan - E-Contract System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php"><i class="fa-solid fa-file-signature me-2"></i>E-Contract Laragon</a>
        <div class="navbar-nav ms-auto">
            <a class="btn btn-outline-light btn-sm" href="dashboard.php"><i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard</a>
        </div>
    </div>
</nav>

<div class="container mb-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="fa-solid fa-video me-2"></i>Video Tutorial Penggunaan Sistem</h5>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="ratio ratio-16x9 mb-4 border rounded shadow-sm bg-black">
                        <video controls poster="https://images.unsplash.com/photo-1450133064473-71024230f91b?q=80&w=800">
                            <source src="https://www.w3schools.com/html/mov_bbb.mp4" type="video/mp4">
                            Browser Anda tidak mendukung pemutar video HTML5.
                        </video>
                    </div>
                    <div class="text-start mt-4">
                        <h5 class="fw-bold text-dark"><i class="fa-solid fa-circle-info text-primary me-2"></i>Alur Pengoperasian Sistem:</h5>
                        <ul class="list-group list-group-flush mt-2">
                            <li class="list-group-item px-0"><i class="fa-solid fa-circle-check text-success me-2"></i> Klik tombol <span class="badge bg-primary">+ Tambah Kontrak</span> untuk mengunggah proyek dan dokumen baru.</li>
                            <li class="list-group-item px-0"><i class="fa-solid fa-circle-check text-success me-2"></i> Sistem mendukung fitur <strong>Multiple File Upload</strong>, Anda bisa memilih beberapa lampiran sekaligus.</li>
                            <li class="list-group-item px-0"><i class="fa-solid fa-circle-check text-success me-2"></i> Klik tombol biru <span class="badge bg-info text-white"><i class="fa-solid fa-eye"></i> Sign / Detail</span> pada baris tabel yang berstatus <span class="badge bg-warning text-dark">Pending</span>.</li>
                            <li class="list-group-item px-0"><i class="fa-solid fa-circle-check text-success me-2"></i> Bubuhkan tanda tangan digital Anda pada area kotak canvas HTML5, lalu klik <strong>Simpan TTD</strong>.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>