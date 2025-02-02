<?php
require_once 'config.php';

// Set header untuk JSON response
header('Content-Type: application/json');

// Periksa apakah metode request adalah GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        // Query untuk mengambil data dari tabel activities dan users
        $sql = "SELECT activities.*, users.nama AS nama_pengguna 
                FROM activities 
                JOIN users ON activities.user_id = users.id 
                ORDER BY activities.created_at DESC";

        $stmt = $pdo->query($sql);
        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format gambar menjadi array
        foreach ($activities as &$activity) {
            if (!empty($activity['gambar'])) {
                $gambar_array = explode(',', $activity['gambar']);
                $gambar_array = array_map(function($gambar) {
                    return "images_upload/" . $gambar;
                }, $gambar_array);
                $activity['gambar'] = $gambar_array;
            } else {
                $activity['gambar'] = [];
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'Data aktivitas berhasil diambil',
            'data' => $activities
        ]);

    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Method tidak diizinkan'
    ]);
}
?>
