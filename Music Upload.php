<?php
header('Content-Type: application/json');

$targetDir = "uploads/";
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_FILES['music']) && !empty($_FILES['image'])) {
        $musicFile = $_FILES['music'];
        $imageFile = $_FILES['image'];
        
        $musicExt = pathinfo($musicFile['name'], PATHINFO_EXTENSION);
        $imageExt = pathinfo($imageFile['name'], PATHINFO_EXTENSION);
        
        $musicName = uniqid("music_") . "." . $musicExt;
        $imageName = uniqid("image_") . "." . $imageExt;
        
        $musicPath = $targetDir . $musicName;
        $imagePath = $targetDir . $imageName;
        
        if (move_uploaded_file($musicFile['tmp_name'], $musicPath) && move_uploaded_file($imageFile['tmp_name'], $imagePath)) {
            $musicData = json_decode(file_get_contents("music_data.json"), true) ?? [];
            
            $musicData[] = [
                "name" => pathinfo($musicFile['name'], PATHINFO_FILENAME),
                "musicURL" => $musicPath,
                "imageURL" => $imagePath
            ];
            
            file_put_contents("music_data.json", json_encode($musicData, JSON_PRETTY_PRINT));
            
            echo json_encode(["success" => true, "message" => "Upload realizado com sucesso!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao salvar arquivos."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Arquivos ausentes."]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo file_get_contents("music_data.json");
} else {
    echo json_encode(["success" => false, "message" => "Método não permitido."]);
}
