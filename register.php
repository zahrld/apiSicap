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
        $nama = isset($_POST['nama']) ? $_POST['nama'] : (isset($input['nama']) ? $input['nama'] : '');
        $email = isset($_POST['email']) ? $_POST['email'] : (isset($input['email']) ? $input['email'] : '');
        $no_telepon = isset($_POST['no_telepon']) ? $_POST['no_telepon'] : (isset($input['no_telepon']) ? $input['no_telepon'] : '');
        $password = isset($_POST['password']) ? $_POST['password'] : (isset($input['password']) ? $input['password'] : '');

        // Log data yang diterima
        error_log("Data yang diterima:");
        error_log("nama: $nama");
        error_log("email: $email");
        error_log("no_telepon: $no_telepon");
        error_log("password: " . (!empty($password) ? "terisi" : "kosong"));

        // Validasi data
        if (empty($nama) || empty($email) || empty($no_telepon) || empty($password)) {
            throw new Exception('Semua field harus diisi. Data yang kosong: ' . 
                (empty($nama) ? 'nama ' : '') .
                (empty($email) ? 'email ' : '') .
                (empty($no_telepon) ? 'no_telepon ' : '') .
                (empty($password) ? 'password' : '')
            );
        }

        // Escape string untuk mencegah SQL injection
        $nama = mysqli_real_escape_string($koneksi, $nama);
        $email = mysqli_real_escape_string($koneksi, $email);
        $no_telepon = mysqli_real_escape_string($koneksi, $no_telepon);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Cek apakah email sudah terdaftar
        $cek_email = mysqli_query($koneksi, "SELECT * FROM users WHERE email = '$email'");
        if (mysqli_num_rows($cek_email) > 0) {
            throw new Exception('Email sudah terdaftar!');
        }

        $query = "INSERT INTO users (nama, email, no_telepon, password) 
                VALUES ('$nama', '$email', '$no_telepon', '$hashedPassword')";

        if (mysqli_query($koneksi, $query)) {
            $response = [
                'success' => true,
                'message' => 'Registrasi berhasil!',
                'data' => [
                    'nama' => $nama,
                    'email' => $email,
                    'no_telepon' => $no_telepon
                ]
            ];
            error_log("Registrasi berhasil untuk email: $email");
            echo json_encode($response);
        } else {
            throw new Exception(mysqli_error($koneksi));
        }
    } catch (Exception $e) {
        error_log("Error in register.php: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Registrasi gagal: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Method tidak diizinkan'
    ]);
}
?> 