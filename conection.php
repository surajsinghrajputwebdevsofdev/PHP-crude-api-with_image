<?php
require_once 'dbconnect.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    

    //get ke api
 case 'GET':
        $stmt = $pdo->query('SELECT * FROM users'); 
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
        break;


//save ke api
 case 'POST':
            if (!empty($_FILES['image']['name']) && !empty($_POST['name'])
                    && !empty($_POST['ph']) && !empty($_POST['email'])) {
                $name = $_POST['name'];
                $ph = $_POST['ph'];
                $email = $_POST['email'];

                $image = $_FILES['image']['name'];
                $image_tmp = $_FILES['image']['tmp_name']; 
                $ext = pathinfo($image, PATHINFO_EXTENSION);
                $image_name = time() . '.' . $ext;                
                $image_path = 'img/' . $image_name;                
                if (move_uploaded_file($image_tmp, $image_path)) {
                    $stmt = $pdo->prepare('INSERT INTO users (name, email, ph, image) VALUES (?, ?, ?, ?)');
                    if ($stmt->execute([$name, $email, $ph, $image_path])) {
                        echo json_encode(['message' => 'Data added successfully']);
                    } else {
                        http_response_code(500);
                        echo json_encode(['message' => 'Failed to save data']);
                    }
                }
            }
            break;
            
// update ka code
case 'PUT':
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'];
    $name = $data['name'];
    $email = $data['email'];
    $ph = $data['ph'];
    
            $stmt = $pdo->prepare('UPDATE users SET name=?, email=?, ph=? WHERE id=?');
            $stmt->execute([$name, $email, $ph, $id]);
 
    echo json_encode(['message' => 'User updated successfully']);
    break;

//ye jo code hai delete ka hai
case 'DELETE':
    $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'];
        $stmt = $pdo->prepare('SELECT image FROM users WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $image_path = $row['image'];
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([$id]);
        if (!empty($image_path) && file_exists($image_path)) {
        unlink($image_path); // Delete the image file
    }
    echo json_encode(['message' => 'User deleted successfully']);
    break;
 default:
 // ager error hai to code ye reha
 http_response_code(405);
 echo json_encode(['error' => 'erro hai bhai check kero  :( ']);
 break;
}
?>