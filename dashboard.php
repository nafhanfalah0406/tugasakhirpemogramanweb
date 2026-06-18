<?php
session_start();
include 'koneksi.php';

// Proteksi Halaman: Jika belum Login, kembalikan ke Login.php
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - E-Contract System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        .canvas-container { 
            border: 2px dashed #ccc; 
            background: #fff; 
            border-radius: 4px; 
            width: 100%; 
            max-width: 350px; 
            height: 200px; 
            margin: 0 auto; 
            position: relative; 
        }
        #canvasTtd { 
            width: 100%; 
            height: 100%; 
            display: block; 
            cursor: crosshair; 
        }
        /* Merapikan posisi layout tombol export bawaan DataTables */
        .dt-buttons {
            float: left;
            margin-bottom: 15px;
        }
        .dataTables_filter {
            float: right;
            margin-bottom: 15px;
        }
    </style>
</head>
<body class="bg-light">
<audio id="suaraSukses" src="https://assets.mixkit.co/active_storage/sfx/2013/2013-84.wav" preload="auto"></audio>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="#"><i class="fa-solid fa-file-signature me-2"></i>E-Contract Laragon</a>
        <div class="navbar-nav ms-auto d-flex align-items-center">
            <span class="nav-link text-white me-3">Halo, <strong><?= $_SESSION['username']; ?></strong> (<?= $_SESSION['role']; ?>)</span>
            <a class="btn btn-warning btn-sm me-2 text-dark fw-bold" href="panduan.php">
                <i class="fa-solid fa-video me-1"></i> Tutorial System
            </a>
            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalLogout">
                <i class="fa-solid fa-right-from-bracket"></i> Keluar
            </button>
        </div>
    </div>
</nav>

<div class="container mb-5">
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h2>Daftar Kontrak Proyek</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fa-solid fa-plus me-1"></i> Tambah Kontrak
            </button>
        </div>
    </div>
    <div class="card shadow-sm mt-3">
        <div class="card-body">
            <table id="tabelKontrak" class="table table-striped table-bordered" style="width:100%">
                <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama Proyek</th>
                    <th>Nilai Kontrak</th>
                    <th>Lampiran File</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $no = 1;
                $query = mysqli_query($koneksi, "SELECT * FROM contracts ORDER BY id DESC");
                while ($row = mysqli_fetch_assoc($query)) {
                    $format_rupiah = "Rp " . number_format($row['nilai_kontrak'], 0, ',', '.');
                    if ($row['status'] == 1) {
                        $status_badge = '<span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>Disetujui</span>';
                    } else {
                        $status_badge = '<span class="badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i>Pending</span>';
                    }
                ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $row['nama_proyek']; ?></td>
                    <td><?= $format_rupiah; ?></td>
                    <td>
                        <?php
                        if (!empty($row['file_lampiran'])) {
                            $files = explode(',', $row['file_lampiran']);
                            foreach ($files as $file) {
                                echo "<a href='uploads/$file' target='_blank' class='btn btn-xs btn-outline-secondary p-1 m-1' style='font-size: 11px;'><i class='fa-solid fa-file me-1'></i>$file</a><br>";
                            }
                        } else {
                            echo "<span class='text-muted small'>Tidak ada file</span>";
                        }
                        ?>
                    </td>
                    <td><?= $status_badge; ?></td>
                    <td>
                        <button class="btn btn-info btn-sm text-white btn-detail" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalDetail" 
                                data-id="<?= $row['id']; ?>" 
                                data-proyek="<?= $row['nama_proyek']; ?>" 
                                data-nilai="<?= $format_rupiah; ?>" 
                                data-ttd="<?= $row['tanda_tangan']; ?>">
                            <i class="fa-solid fa-eye"></i> Sign / Detail
                        </button>
                    </td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTambahLabel"><i class="fa-solid fa-file-circle-plus me-2"></i>Tambah Kontrak Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-toggle="modal" aria-label="Close"></button>
            </div>
            <form action="proses_tambah.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Proyek</label>
                        <input type="text" name="nama_proyek" class="form-control" placeholder="Contoh: Pembuatan Aplikasi Web" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nilai Kontrak (Rp)</label>
                        <input type="number" name="nilai_kontrak" class="form-control" placeholder="Contoh: 50000000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload Lampiran Dokumen</label>
                        <input type="file" name="file_lampiran[]" class="form-control" multiple required>
                        <div class="form-text text-danger">*Anda bisa memilih lebih dari 1 file sekaligus.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="simpan" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalDetailLabel"><i class="fa-solid fa-file-contract me-2"></i>Detail & Persetujuan Kontrak</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Informasi Proyek</h5>
                        <hr>
                        <p><strong>Nama Proyek:</strong> <br><span id="detailProyek" class="text-muted"></span></p>
                        <p><strong>Nilai Kontrak:</strong> <br><span id="detailNilai" class="text-muted"></span></p>
                    </div>
                    <div class="col-md-6 text-center">
                        <h5>Tanda Tangan Digital (Canvas)</h5>
                        <hr>
                        <div id="areaTtdSelesai" class="d-none">
                            <img id="imgTtd" src="" class="img-fluid border rounded bg-white shadow-sm" alt="Tanda Tangan" style="max-height: 200px;">
                            <p class="text-success mt-2 fw-bold"><i class="fa-solid fa-circle-check"></i> Kontrak Telah Disetujui</p>
                        </div>
                        <div id="areaCanvasBaru">
                            <div class="canvas-container shadow-sm">
                                <canvas id="canvasTtd"></canvas>
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-warning btn-sm" id="btnClear"><i class="fa-solid fa-eraser"></i> Bersihkan</button>
                                <button type="button" class="btn btn-success btn-sm" id="btnSaveTtd"><i class="fa-solid fa-pen-fancy"></i> Simpan TTD</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalLogout" tabindex="-1" aria-labelledby="modalLogoutLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalLogoutLabel"><i class="fa-solid fa-circle-question me-2"></i>Konfirmasi Keluar</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fa-solid fa-arrow-right-from-bracket text-danger mb-3" style="font-size: 50px;"></i>
                <h5>Apakah Anda yakin ingin keluar dari sistem?</h5>
                <p class="text-muted small mb-0">Anda harus memasukkan username dan password kembali untuk masuk.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="logout.php" class="btn btn-danger fw-bold px-4">Ya, Keluar</a>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>

<script src="signature_pad.umd.min.js"></script>

<script>
$(document).ready(function () {
    // 1. Inisialisasi Otomatis DataTables dengan Tombol Export Langsung Aktif
    $('#tabelKontrak').DataTable({
        "language": { 
            "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json" 
        },
        "dom": 'Bfrtip',
        "buttons": [
            {
                extend: 'excelHtml5',
                text: '<i class="fa-solid fa-file-excel me-1"></i> Export Excel',
                className: 'btn btn-success btn-sm fw-bold me-2 shadow-sm',
                exportOptions: {
                    columns: [0, 1, 2, 4]
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa-solid fa-file-pdf me-1"></i> Export PDF',
                className: 'btn btn-danger btn-sm fw-bold shadow-sm',
                exportOptions: {
                    columns: [0, 1, 2, 4]
                }
            }
        ]
    });

    // 2. Logic Mengurus Sistem Kotak Canvas Tanda Tangan
    const canvas = document.getElementById('canvasTtd');
    let signaturePad = null;
    
    function resizeCanvas() {
        if (!canvas) return;
        
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        
        if (signaturePad) {
            signaturePad.clear(); 
        } else {
            signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgba(255, 255, 255, 0)',
                penColor: 'rgb(0, 0, 0)'
            });
        }
    }

    let idKontrakAktif = null;

    // Aksi Klik Sign / Detail
    $('.btn-detail').on('click', function() {
        idKontrakAktif = $(this).data('id');
        const namaProyek = $(this).data('proyek');
        const nilaiKontrak = $(this).data('nilai');
        const namaFileTtd = $(this).data('ttd');

        $('#detailProyek').text(namaProyek);
        $('#detailNilai').text(nilaiKontrak);

        if (namaFileTtd && namaFileTtd !== "") {
            $('#areaCanvasBaru').addClass('d-none');
            $('#areaTtdSelesai').removeClass('d-none');
            $('#imgTtd').attr('src', 'uploads/' + namaFileTtd);
        } else {
            $('#areaCanvasBaru').removeClass('d-none');
            $('#areaTtdSelesai').addClass('d-none');
        }
    });

    // Memastikan canvas aktif menggambar saat modal terbuka penuh
    $('#modalDetail').on('shown.bs.modal', function () {
        resizeCanvas();
    });

    // Tombol Hapus Coretan Canvas
    $('#btnClear').on('click', function() { 
        if (signaturePad) signaturePad.clear(); 
    });

    // Mengirim Tanda Tangan ke Server via AJAX
    $('#btnSaveTtd').on('click', function(e) {
        e.preventDefault();

        if (!signaturePad || signaturePad.isEmpty()) {
            alert("Silakan bubuhkan tanda tangan terlebih dahulu pada kotak canvas!");
            return;
        }

        const dataUrlBase64 = signaturePad.toDataURL('image/png');

        $.ajax({
            url: 'simpan_ttd.php',
            type: 'POST',
            data: { 
                id: idKontrakAktif, 
                image: dataUrlBase64 
            },
            success: function (response) {
                if (response.trim() === "sukses") {
                    const audio = document.getElementById('suaraSukses');
                    if(audio) audio.play();
                    
                    setTimeout(function(){
                        alert("Tanda Tangan Digital Berhasil Disimpan!");
                        window.location.reload();
                    }, 500);
                } else {
                    alert("Gagal memproses tanda tangan: " + response);
                }
            },
            error: function() {
                alert("Terjadi kesalahan sistem/jaringan saat mengirim data.");
            }
        });
    });
});
</script>
</body>
</html>