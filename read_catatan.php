<?php
require 'koneksi.php';

// Enable error reporting untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start output buffering
ob_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        // Ambil user_id dari parameter URL
        $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

        if (empty($user_id)) {
            throw new Exception('User ID diperlukan');
        }

        // Query untuk mengambil semua aktivitas user dari tabel activities
        $query = "SELECT a.*, u.nama as nama_user 
                 FROM activities a 
                 LEFT JOIN users u ON a.user_id = u.id 
                 WHERE a.user_id = ? 
                 ORDER BY a.tanggal DESC";

        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $activities = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // Jika ada gambar, ubah string gambar menjadi array
            if (!empty($row['gambar'])) {
                $gambar_array = explode(',', $row['gambar']);
                // Tambahkan base URL untuk setiap gambar
                $gambar_array = array_map(function($gambar) {
                    return "images_upload/" . $gambar;
                }, $gambar_array);
                $row['gambar'] = $gambar_array;
            } else {
                $row['gambar'] = [];
            }

            $activities[] = [
                'id' => $row['id'],
                'user_id' => $row['user_id'],
                'nama_user' => $row['nama_user'],
                'judul' => $row['judul'],
                'tanggal' => $row['tanggal'],
                'tempat' => $row['tempat'],
                'deskripsi' => $row['deskripsi'],
                'anggota' => $row['anggota'],
                'gambar' => $row['gambar']
            ];
        }

        if (empty($activities)) {
            echo json_encode([
                'success' => true,
                'message' => 'Belum ada aktivitas',
                'data' => []
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'Data aktivitas berhasil diambil',
                'data' => $activities
            ]);
        }

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal mengambil data aktivitas: ' . $e->getMessage()
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
