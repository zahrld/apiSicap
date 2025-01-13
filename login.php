<?php
require 'koneksi.php';

// Enable error reporting untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Logging untuk debugging
error_log("Raw POST data: " . file_get_contents('php://input'));
error_log("POST array: " . print_r($_POST, true));

// Terima request JSON
$input = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Coba ambil data dari $_POST atau JSON input
        $email = isset($_POST['email']) ? $_POST['email'] : (isset($input['email']) ? $input['email'] : '');
        $password = isset($_POST['password']) ? $_POST['password'] : (isset($input['password']) ? $input['password'] : '');

        // Log data yang diterima
        error_log("Data yang diterima:");
        error_log("email: $email");
        error_log("password: " . (!empty($password) ? "terisi" : "kosong"));

        // Validasi data
        if (empty($email) || empty($password)) {
            throw new Exception('Email dan password harus diisi');
        }

        // Escape string untuk mencegah SQL injection
        $email = mysqli_real_escape_string($koneksi, $email);

        // Cek email dan ambil data user
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($koneksi, $query);

        if (mysqli_num_rows($result) === 0) {
            throw new Exception('Email tidak terdaftar');
        }

        $user = mysqli_fetch_assoc($result);

        // Verifikasi password
        if (!password_verify($password, $user['password'])) {
            throw new Exception('Password salah');
        }

        // Jika berhasil login
        $response = [
            'success' => true,
            'message' => 'Login berhasil!',
            'data' => [
                'id' => $user['id'],
                'nama' => $user['nama'],
                'email' => $user['email'],
                'no_telepon' => $user['no_telepon']
            ]
        ];
        error_log("Login berhasil untuk email: $email");
        echo json_encode($response);

    } catch (Exception $e) {
        error_log("Error in login.php: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Login gagal: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Method tidak diizinkan'
    ]);
}
?>
