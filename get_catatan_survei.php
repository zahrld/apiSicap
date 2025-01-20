<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    
    // Ubah query untuk hanya mengambil catatan user yang login
    $query = "SELECT 
                notes.id,
                notes.title,
                notes.description,
                notes.location,
                notes.created_at,
                users.username as created_by
              FROM notes 
              JOIN users ON notes.user_id = users.id
              WHERE notes.user_id = :user_id  /* Hanya ambil catatan user ini */
              ORDER BY notes.created_at DESC";

    $stmt = $db->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
    
    try {
        $stmt->execute();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $notes_arr = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $note_item = array(
                    "id" => $id,
                    "title" => $title,
                    "description" => $description,
                    "location" => $location,
                    "created_at" => $created_at,
                    "created_by" => $created_by
                );

                array_push($notes_arr, $note_item);
            }

            http_response_code(200);
            echo json_encode($notes_arr);
        } else {
            http_response_code(200);
            echo json_encode(array()); // Kembalikan array kosong jika tidak ada catatan
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error: " . $e->getMessage()));
    }
} else {
    http_response_code(400);

?>