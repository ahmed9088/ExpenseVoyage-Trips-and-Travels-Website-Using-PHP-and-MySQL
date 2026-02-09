<?php
// chatbot-ajax.php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

// --- DATABASE CONNECTION ---
$path = 'admin/config.php';

if (file_exists($path)) {
    include $path;
} else {
    if (file_exists('db.php')) {
        include 'db.php';
    } else {
        echo json_encode(['response' => "System Error: Database file not found."]);
        exit;
    }
}

if (!isset($con) || !$con) {
    echo json_encode(['response' => "System Error: Connection failed."]);
    exit;
}

// --- INTELLIGENCE ENGINE ---
require_once 'chatbot_engine.php';

function getSuggestionQuestions() {
    $suggestions = [
        "What trips are available?",
        "Do you have AC buses?",
        "Price for Murree",
        "How to book?",
        "Contact info"
    ];
    shuffle($suggestions);
    return array_slice($suggestions, 0, 4);
}

// --- HANDLE REQUESTS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $engine = new ChatbotEngine($con);
    
    if (isset($_POST['get_suggestions'])) {
        echo json_encode(['suggestions' => getSuggestionQuestions(), 'status' => 'success']);
        exit;
    }

    if (isset($_POST['message'])) {
        $userMessage = $_POST['message'];
        $response = $engine->process($userMessage);
        
        echo json_encode([
            'response' => $response,
            'suggestions' => getSuggestionQuestions(),
            'status' => 'success'
        ]);
        exit;
    }
}
?>