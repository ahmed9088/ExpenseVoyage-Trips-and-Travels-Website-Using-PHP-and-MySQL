<?php
include 'admin/config.php';

echo "--- Chatbot Intelligence Migration ---\n";

$queries = [
    // 1. Add metadata to trips
    "ALTER TABLE trips ADD COLUMN vehicle_name VARCHAR(100) DEFAULT 'Standard Bus' AFTER description",
    "ALTER TABLE trips ADD COLUMN is_ac TINYINT(1) DEFAULT 1 AFTER vehicle_name",
    "ALTER TABLE trips ADD COLUMN departure_time TIME DEFAULT '08:00:00' AFTER is_ac",
    
    // 2. Create knowledge base table
    "CREATE TABLE IF NOT EXISTS chatbot_knowledge (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question_pattern TEXT NOT NULL,
        answer_template TEXT NOT NULL,
        category VARCHAR(50) DEFAULT 'general',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

foreach ($queries as $q) {
    if (mysqli_query($con, $q)) {
        echo "Successfully executed: " . substr($q, 0, 50) . "...\n";
    } else {
        echo "Error: " . mysqli_error($con) . "\n";
    }
}

// 3. Seed some initial knowledge
$seed = [
    ['safety', 'is it safe|safety|security', 'Your safety is our priority. All trips are monitored, and we use certified drivers.'],
    ['policy', 'refund|cancellation|cancel', 'Cancellations made 48h before the trip get a 80% refund. Check your profile for details.'],
    ['booking', 'how to book|process|steps', '1. Select a trip 2. Click Book Now 3. Pay via Card or Mobile Wallet. Easy!'],
    ['facilities', 'food|meal|lunch|dinner', 'Most trips include lunch and refreshments. Check the specific trip description for meal plans.'],
    ['vehicle', 'what car|transport|bus|hiace', 'We use Premium Hiace for groups of 10-12, and Luxury Coasters for larger groups.']
];

foreach ($seed as $s) {
    $stmt = $con->prepare("INSERT INTO chatbot_knowledge (category, question_pattern, answer_template) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $s[0], $s[1], $s[2]);
    $stmt->execute();
}

echo "--- Migration & Seeding Complete ---\n";
?>
