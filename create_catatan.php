<?php
require 'koneksi.php';

// Enable error reporting untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start output buffering
ob_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Ambil data dari request multipart form
        $user_id = $_POST['user_id'] ?? null;
        $judul = $_POST['judul'] ?? null;
        $tanggal = $_POST['tanggal'] ?? null;
        $tempat = $_POST['tempat'] ?? null;
        $deskripsi = $_POST['deskripsi'] ?? null;
        $anggota = $_POST['anggota'] ?? null;

        // Validasi input
        if (empty($user_id) || empty($judul) || empty($tanggal) || 
            empty($tempat) || empty($deskripsi)) {
            throw new Exception('Field wajib harus diisi (user_id, judul, tanggal, tempat, deskripsi)');
        }

        // Handle file upload jika ada
        $gambar_paths = [];
        if (isset($_FILES['images'])) {
            $upload_path = 'images_upload/';
            
            // Buat direktori jika belum ada
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $file_name = $_FILES['images']['name'][$key];
                $file_tmp = $_FILES['images']['tmp_name'][$key];
                
                // Generate unique filename
                $new_file_name = uniqid() . '_' . $file_name;
                $file_path = $upload_path . $new_file_name;
                
                if (move_uploaded_file($file_tmp, $file_path)) {
                    $gambar_paths[] = $file_path;
                }
            }
        }

        $gambar_string = !empty($gambar_paths) ? implode(',', $gambar_paths) : '';

        // Query insert
        $query = "INSERT INTO activities (user_id, judul, tanggal, tempat, deskripsi, anggota, gambar) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "issssss", 
            $user_id, 
            $judul, 
            $tanggal, 
            $tempat, 
            $deskripsi, 
            $anggota,
            $gambar_string
        );

        if (mysqli_stmt_execute($stmt)) {
            $activity_id = mysqli_insert_id($koneksi);
            $response = [
                'success' => true,
                'message' => 'Aktivitas berhasil ditambahkan',
                'data' => [
                    'id' => $activity_id,
                    'user_id' => $user_id,
                    'judul' => $judul,
                    'tanggal' => $tanggal,
                    'tempat' => $tempat,
                    'deskripsi' => $deskripsi,
                    'anggota' => $anggota,
                    'gambar' => $gambar_string
                ]
            ];
            echo json_encode([
                'success' => false,
                'message' => 'yes'
            ]); 
        } else {
            throw new Exception(mysqli_error($koneksi));
        }

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal menambahkan aktivitas: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Method tidak diizinkan'
    ]);
}

// End output buffering and clean output
ob_end_clean();
?>
