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

// --- HELPER FUNCTION ---
function prepareMessage($message) {
    $message = strtolower(trim($message));
    $fillerWords = ['what', 'where', 'when', 'who', 'how', 'can', 'could', 'would', 'should', 
                   'is', 'are', 'do', 'does', 'did', 'the', 'a', 'an', 'for', 'to', 'with', 
                   'about', 'tell', 'me', 'show', 'give', 'find', 'please', 'kindly', 'i', 'want'];
    $message = preg_replace('/\b(' . implode('|', $fillerWords) . ')\b/', '', $message);
    return trim(preg_replace('/\s+/', ' ', $message));
}

function getSuggestionQuestions() {
    $suggestions = [
        "What trips are available?",
        "Show me your travel agents",
        "Price for Murree",
        "How to book a trip?",
        "Contact info"
    ];
    shuffle($suggestions);
    return array_slice($suggestions, 0, 4);
}

// --- MAIN BOT LOGIC ---
function getBotResponse($message, $con) {
    $cleanMessage = prepareMessage($message);
    $rawMessage = strtolower($message);

    // --- CHECK 1: AGENT QUERIES ---
    if (preg_match('/(agent|team|expert|staff|guide|representative)/', $rawMessage)) {
        // FIXED: Only selecting columns that exist in your table (a_name, a_profetion)
        $sql = "SELECT a_name, a_profetion FROM agents"; 
        $result = mysqli_query($con, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $resp = "👥 **Our Expert Team:**\n\n";
            while ($row = mysqli_fetch_assoc($result)) {
                $resp .= "👤 **" . $row['a_name'] . "**\n";
                $resp .= "   💼 " . $row['a_profetion'] . "\n\n";
            }
            return $resp;
        }
    }

    // --- CHECK 2: SPECIFIC DESTINATIONS (Using 'budget') ---
    $sql_dest = "SELECT * FROM trips WHERE available = 1";
    $result_dest = mysqli_query($con, $sql_dest);
    
    if ($result_dest) {
        while ($row = mysqli_fetch_assoc($result_dest)) {
            $destName = strtolower($row['destination']);
            if (strpos($rawMessage, $destName) !== false) {
                $resp = "📍 **Trip to " . $row['destination'] . "**\n\n";
                // Uses 'budget' because 'price' is NULL
                $resp .= "💰 **Price:** Rs " . number_format($row['budget']) . "\n";
                $resp .= "📅 **Date:** " . date('d M Y', strtotime($row['starts_date'])) . "\n";
                $resp .= "⏰ **Duration:** " . $row['duration_days'] . " days\n"; 
                $resp .= "🎟️ **Seats:** " . $row['seats_available'] . " available\n\n";
                $resp .= "Type **'Book " . $row['destination'] . "'** to start booking!";
                return $resp;
            }
        }
    }

    // --- CHECK 3: GENERAL TRIP QUERIES ---
    if (preg_match('/(trip|tour|package|destination|available|price|cost)/', $rawMessage)) {
        $sql = "SELECT trip_name, budget, destination FROM trips WHERE available = 1 ORDER BY starts_date ASC LIMIT 5";
        $result = mysqli_query($con, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            $resp = "✈️ **Available Trips:**\n\n";
            while ($row = mysqli_fetch_assoc($result)) {
                $resp .= "🔹 **" . $row['destination'] . "** - Rs " . number_format($row['budget']) . "\n";
            }
            $resp .= "\nAsk for a specific place like *'Price for Murree'*";
            return $resp;
        }
    }

    // --- CHECK 4: CONTACT ---
    if (preg_match('/(contact|phone|email|address|location)/', $rawMessage)) {
        return "📞 **Contact Us:**\n\n📍 Office: Quaid-e-Awam University\n📱 Phone: +92 318 889 3863\n📧 Email: alizamemonnn@gmail.com";
    }
    
    // --- CHECK 5: BOOKING ---
    if (preg_match('/(book|reserve)/', $rawMessage)) {
        return "📝 **How to Book:**\n\n1. Choose your destination.\n2. Click the 'Book Now' button on the package page.\n3. Or call us at +92 318 889 3863.";
    }

    return "I didn't understand that. 🤔\n\nTry asking:\n- 'Show me agents'\n- 'Price for Murree'\n- 'Contact info'";
}

// --- HANDLE REQUESTS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['get_suggestions'])) {
        echo json_encode(['suggestions' => getSuggestionQuestions(), 'status' => 'success']);
        exit;
    }

    if (isset($_POST['message'])) {
        $userMessage = $_POST['message'];
        $response = getBotResponse($userMessage, $con);
        
        echo json_encode([
            'response' => $response,
            'suggestions' => getSuggestionQuestions(),
            'status' => 'success'
        ]);
        exit;
    }
}
?>