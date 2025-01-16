<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $user_id = $_GET['user_id'] ?? null;

    if ($user_id === null) {
        echo json_encode([
            'success' => false,
            'message' => 'User ID tidak disediakan'
        ]);
        exit;
    }

    $sql = "SELECT COUNT(*) as total FROM activities WHERE user_id = ?";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['total' => $result['total']]);
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