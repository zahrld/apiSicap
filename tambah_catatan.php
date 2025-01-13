<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $tempat = $_POST['tempat'];
    $anggota = $_POST['anggota'];
    $user_id = $_POST['user_id'];
    
    // Handle upload gambar
    $gambar = '';
    if (isset($_POST['gambar'])) {
        $image_data = base64_decode($_POST['gambar']);
        $file_name = uniqid() . '.jpg';
        $upload_path = 'uploads/' . $file_name;
        
        if (file_put_contents($upload_path, $image_data)) {
            $gambar = $file_name;
        }
    }

    $query = "INSERT INTO activities (user_id, judul, deskripsi, tempat, anggota, gambar) 
              VALUES ('$user_id', '$judul', '$deskripsi', '$tempat', '$anggota', '$gambar')";

    if (mysqli_query($koneksi, $query)) {
        echo json_encode([
            'success' => true,
            'message' => 'Catatan berhasil disimpan!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal menyimpan catatan: ' . mysqli_error($koneksi)
        ]);
    }
}
?> 