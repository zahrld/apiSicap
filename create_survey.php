<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lokasi = $_POST['lokasi'] ?? '';
    $keterangan = $_POST['keterangan'] ?? '';
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d H:i:s');

    $sql = "INSERT INTO surveys (lokasi, keterangan, tanggal) VALUES (?, ?, ?)";
    
    try {
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$lokasi, $keterangan, $tanggal]);
        
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Survey berhasil disimpan' : 'Gagal menyimpan survey'
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} 