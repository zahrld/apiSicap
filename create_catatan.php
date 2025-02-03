<?php
require 'koneksi.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Debug semua data POST
        error_log("Semua data POST: " . print_r($_POST, true));
        error_log("Semua data FILES: " . print_r($_FILES, true));

        // Ambil data dari request
        $user_id = $_POST['user_id'] ?? null;
        $judul = $_POST['judul'] ?? null;
        $tanggal = $_POST['tanggal'] ?? null;
        $tempat = $_POST['tempat'] ?? null;
        $deskripsi = $_POST['deskripsi'] ?? null;
        $anggota = $_POST['anggota'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$user_id) {
            throw new Exception('User ID tidak boleh kosong');
        }

        // Ambil nama user dari tabel users berdasarkan user_id
        $query_user = "SELECT nama FROM users WHERE id = ?";
        $stmt_user = mysqli_prepare($koneksi, $query_user);
        
        if (!$stmt_user) {
            throw new Exception('Gagal prepare statement untuk users: ' . mysqli_error($koneksi));
        }

        mysqli_stmt_bind_param($stmt_user, "i", $user_id);
        mysqli_stmt_execute($stmt_user);
        $result_user = mysqli_stmt_get_result($stmt_user);

        if (!$result_user || mysqli_num_rows($result_user) == 0) {
            throw new Exception('User tidak ditemukan');
        }

        $user_data = mysqli_fetch_assoc($result_user);
        $nama_user = $user_data['nama'];

        mysqli_stmt_close($stmt_user);

        // Proses Upload Gambar
        $gambar_paths = [];
        if (!empty($_FILES['images'])) {
            $upload_dir = 'images/upload/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true); // Buat folder jika belum ada
            }

            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $file_name = time() . '_' . basename($_FILES['images']['name'][$key]);
                $target_file = $upload_dir . $file_name;

                if (move_uploaded_file($tmp_name, $target_file)) {
                    $gambar_paths[] = $target_file; // Simpan path gambar
                } else {
                    error_log("Gagal upload file: " . $_FILES['images']['name'][$key]);
                }
            }
        }

        // Gabungkan semua gambar menjadi satu string, dipisahkan koma
        $gambar = !empty($gambar_paths) ? implode(',', $gambar_paths) : '';

        // Query insert dengan prepared statement
        $query = "INSERT INTO activities (user_id, judul, tanggal, tempat, deskripsi, anggota, status, nama_user, gambar) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($koneksi, $query);
        if (!$stmt) {
            throw new Exception('Gagal prepare statement untuk activities: ' . mysqli_error($koneksi));
        }

        mysqli_stmt_bind_param($stmt, "issssssss", 
            $user_id,
            $judul,
            $tanggal,
            $tempat,
            $deskripsi,
            $anggota,
            $status,
            $nama_user,
            $gambar // Gambar disimpan sebagai string biasa, dipisahkan dengan koma
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Gagal menambahkan aktivitas: ' . mysqli_error($koneksi));
        }

        $activity_id = mysqli_insert_id($koneksi);
        mysqli_stmt_close($stmt);

        echo json_encode([
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
                'status' => $status,
                'nama_user' => $nama_user,
                'gambar' => $gambar_paths // Data gambar dalam array
            ]
        ]);

    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Method tidak diizinkan'
    ]);
}
?>
