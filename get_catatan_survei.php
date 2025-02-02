<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    
    $query = "SELECT 
                n.id,
                n.judul,
                n.deskripsi,
                n.tempat,
                n.tanggal,
                n.anggota,
                n.gambar,
                u.nama as username
              FROM notes n
              JOIN users u ON n.user_id = u.id
              WHERE n.user_id = :user_id
              ORDER BY n.tanggal DESC";

    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
    
    try {
        $stmt->execute();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $notes_arr = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                // Konversi string gambar menjadi array
                $gambar_array = !empty($gambar) ? explode(',', $gambar) : [];
                
                $note_item = array(
                    "id" => $id,
                    "judul" => $judul,
                    "deskripsi" => $deskripsi,
                    "tempat" => $tempat,
                    "tanggal" => $tanggal,
                    "anggota" => $anggota,
                    "gambar" => $gambar_array,
                    "username" => $username,
                    "status" => $status,
                    "nama_user" => $nama_user
                );


                array_push($notes_arr, $note_item);
            }

            http_response_code(200);
            echo json_encode($notes_arr);
        } else {
            http_response_code(200);
            echo json_encode(array());
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error: " . $e->getMessage()));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "User ID tidak ditemukan"));
}
?>