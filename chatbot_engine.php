<?php
/**
 * Chatbot Intelligence Engine v2.3
 * Secure, OO-based, and context-aware.
 */
class ChatbotEngine {
    private $db;

    public function __construct($con) {
        $this->db = $con;
    }

    public function process($message) {
        $msg = strtolower(trim($message));
        if (empty($msg)) return "How can I help you today? 😊";

        // 1. Dynamic Check: Available Trips
        if (preg_match('/\b(available|trips|where|show|destinations|places)\b/i', $msg)) {
            $sql = "SELECT destination, budget, seats_available, departure_time FROM trips WHERE seats_available > 0 LIMIT 5";
            $res = $this->db->query($sql);
            if ($res && $res->num_rows > 0) {
                $out = "🗺️ **Latest Expeditions:**\n\n";
                while($row = $res->fetch_assoc()) {
                    $seats = ($row['seats_available'] > 0) ? "🎟️ " . $row['seats_available'] . " left" : "❌ Sold Out";
                    $out .= "📍 **" . $row['destination'] . "**\n";
                    $out .= "   💰 Rs " . number_format($row['budget']) . " | ⏰ " . date('h:i A', strtotime($row['departure_time'])) . "\n";
                    $out .= "   " . $seats . "\n\n";
                }
                return $out . "Mention a place for more details!";
            }
        }

        // 2. Dynamic Check: Vehicle & Transport
        if (preg_match('/\b(vehicle|bus|ac|car|transport|travel|timing)\b/i', $msg)) {
            $sql = "SELECT destination, vehicle_name, is_ac, departure_time FROM trips WHERE seats_available > 0";
            $res = $this->db->query($sql);
            if ($res && $res->num_rows > 0) {
                while($row = $res->fetch_assoc()) {
                    if (strpos($msg, strtolower($row['destination'])) !== false) {
                        $ac = $row['is_ac'] ? "Full AC ❄️" : "Non-AC 🍃";
                        return "For the **" . $row['destination'] . "** trip, we use a **" . $row['vehicle_name'] . "** (" . $ac . "). It departs at **" . date('h:i A', strtotime($row['departure_time'])) . "**. We ensure a premium journey!";
                    }
                }
                // General response if no specific city mentioned
                return "🚐 **Transport Fleet:** We use high-end Coasters, Grand Cabins, and AC Buses. Type a destination (e.g., 'ac for Murree') for specific info.";
            }
        }

        // 3. Dynamic Check: Pricing
        if (preg_match('/\b(price|cost|how much|budget)\b/i', $msg)) {
            $sql = "SELECT destination, budget FROM trips WHERE seats_available > 0";
            $res = $this->db->query($sql);
            if ($res && $res->num_rows > 0) {
                while($row = $res->fetch_assoc()) {
                    if (strpos($msg, strtolower($row['destination'])) !== false) {
                        return "The cost for the **" . $row['destination'] . "** expedition is **Rs " . number_format($row['budget']) . "** per seat.";
                    }
                }
            }
        }

        // 4. Knowledge Base (Pre-trained patterns)
        $res = $this->db->query("SELECT question_pattern, answer_template FROM chatbot_knowledge");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $pattern = str_replace('/', '\/', $row['question_pattern']);
                if (preg_match('/' . $pattern . '/i', $msg)) {
                    return $row['answer_template'];
                }
            }
        }

        // 5. Fallback
        return "I'm still learning about that! 📚\n\nTry asking about:\n- **Available Trips**\n- **AC Bus details**\n- **Pricing** for a city\n- **Safety** or **Booking help**";
    }
}
?>
