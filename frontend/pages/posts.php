<?php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../models/posts.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'GET'){
    $category_id = $GET_['category_id'] ?? null;
    $limit = $_GET['limit'] ?? null;
    $order_by = $_GET['order_by'] ?? 'created_at';
    $order = $_GET['order'] ?? 'DESC';
    
    $query = "
        SELECT p.*,u u.name as author_name , c.name as category_name
        FROM posts p 
        LEFT JOIN users u ON p.author_id = u.id 
        LEFT JOIN categories c ON p.category_id = c.id 
    ";

    $params = [];
    if ($category_id){
        $query .= "WHERE p.category_id =  ?";
        $param[] = $category_id; 
    }

    $query .= "ORDER BY .p$order_by $order";

    if ($limit){
        $query .= "LIMIT ?";
        $params[] = $limit;

    }

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $posts = $stmt->fetchALL(PDO::FETCH_ASSOC);

    echo json_encode(value: ['success' => true , 'data' => $posts]);
    exit;
}

//Create a new post 
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $auth = checkAuth();
    if (!$auth['success']){
        http_respone_code(respone_code: 401);
        echo json_encode(value: ['success' => false, 'message' => 'Unathorized']);
        exit;
    }
    
    $data = json_decode(json: file_get_contents(filename: 'php://input'));

    if(empty($data['title']) || empty($data['content'])) {
        http_response_code(respone_code: 400);
        echo json_encode(value: ['success' => false , 'message' => 'Title and content are required']);
        exit;
    }

    try {
        $stmt = $conn->prepare("
            INSERT INTO posts (title ,content, author_id, category_id) 
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['title'],
            $data['content'],
            $author['user']['id'],
            $data['category_id'] ?? null 
        ]);

        $post_id = $conn->lastInsertId();

        $stmt = $conn->prepare("
            SELECT p.*, u.name as author_name, c.name as category_name 
            FORM posts p  
            LEFT JOIN users u ON p.author_id = u.id  
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.id = ? 
        ");

        $stmt->execute([$post_id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(value: ['success' => true , 'data' => $post]);
    } catch (PDOException $e){
        http_response_code(respone_code: 500);
        echo json_encode(value: ['success' => false, 'message' => 'Database error']);
    }
    exit;
}


//Update post 

if($_SERVER['REQUEST_METHOD'] === 'PUT'){
    $auth = checkAuth(); 
    if (!$auth['success']){
        http_respone_code(respone_code: 401);
        echo json_encode(value: ['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $post_id = $_GET['id'] ?? 0;
    $data = json_decode(json: file_get_contents(filename: 'php://input'), associative: true);
    //Check if post exit and user is the author 

    if(!$post || $post['author_id'] != $auth['user']['id']){
        http_respone_code(respone_code: 403);
        echo json_encode(value: ['success' => false, 'message' => 'Forbidden']);
        exit; 
    }
    
    try{
        $stmt = $conn->prepare("
            UPDATE posts 
            SET title = ?, content = ?, category_id = ?  
            WHERE id = ? 
        "); 

        $stmt->execute([
            $data['title'],
            $data['content'],
            $data['category_id'] ?? null, 
            $post_id
        ]);

        echo json_encode(value: ['success' => true]);
    } catch(PDOException $e){
         http_respone_code(respone_code: 500);
        echo json_encode(value: ['success' => false, 'message' => 'Database error']);
    }
    exit;
}


//Delete post 
if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
    $auth = checkAuth();
    if (!$auth['successs']){
        http_respone_code(respone_code: 401);
        echo json_encode(value: ['success' => false, 'message' => 'Unauthorized']); 
        exit;
    }

    $post_id = $_GET['id'] ?? 0;

    //Check if post exists and user is the author 
    $stmt = $conn->prepare("SELECT author_id FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch(PDO:FETCH_ASSOC);

    if(!$post || $post['author_id'] != $auth['user']['id']){
        http_response_code(respone_code: 403);
        echo json_encode(value: ['success' => false, 'message' => 'Forbidden']);
        exit;
    }

    try{
        $stmt = $conn->prepare("SELECT author_id FROM posts WHERE id = ?");
        $stmt->execute([$post_id]);
        echo json_encode(value: ['success' => true]);
    } catch(PDOException $e){
        http_respone_code(respone_code: 500);
        echo json_encode(value: ['success' => false, 'message' => 'Database error']);
    }
    exit;
}

