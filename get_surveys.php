<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $sql = "SELECT * FROM surveys ORDER BY tanggal DESC";
    
    try {
        $stmt = $pdo->query($sql);
        $surveys = $stmt->fetchAll();
        echo json_encode($surveys);
    } catch (PDOException $e) {
        echo json_encode([
            'error' => $e->getMessage()
        ]);
    }
} 