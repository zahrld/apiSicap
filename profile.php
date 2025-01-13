<?php
require 'koneksi.php';

// Enable error reporting untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Logging untuk debugging
error_log("GET request for profile data");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        // Ambil user_id dari parameter URL
        $user_id = isset($_GET['id']) ? $_GET['id'] : null;

        if (empty($user_id)) {
            throw new Exception('ID user diperlukan');
        }

        // Escape string untuk mencegah SQL injection
        $user_id = mysqli_real_escape_string($koneksi, $user_id);

        // Query untuk mengambil data user
        $query = "SELECT id, nama, email, no_telepon FROM users WHERE id = '$user_id'";
        $result = mysqli_query($koneksi, $query);

        if (mysqli_num_rows($result) === 0) {
            throw new Exception('User tidak ditemukan');
        }

        $user = mysqli_fetch_assoc($result);

        // Kirim response
        $response = [
            'success' => true,
            'message' => 'Data profil berhasil diambil',
            'data' => [
                'id' => $user['id'],
                'nama' => $user['nama'],
                'email' => $user['email'],
                'no_telepon' => $user['no_telepon']
            ]
        ];
        error_log("Profile data retrieved for user ID: $user_id");
        echo json_encode($response);

    } catch (Exception $e) {
        error_log("Error in profile.php: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Gagal mengambil data profil: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Method tidak diizinkan'
    ]);
}
?> 