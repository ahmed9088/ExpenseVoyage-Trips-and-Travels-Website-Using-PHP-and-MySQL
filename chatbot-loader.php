<?php
// chatbot-loader.php
// Add this line at the bottom of your website pages: include 'chatbot-loader.php';
?>
<button id="chatbot-toggle" class="chatbot-toggle">
    <i class="fas fa-comment-dots"></i>
</button>

<div id="chatbot-container" class="chatbot-container">
    <div class="chatbot-header">
        <h3>Travel Assistant</h3>
        <button id="chatbot-close" class="chatbot-close">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div id="chatbot-messages" class="chatbot-messages">
        </div>
    
    <div id="chatbot-quick-questions" class="chatbot-quick-questions">
        </div>
    
    <div class="chat-bot-footer">
        <form id="chatbot-form" class="chatbot-form">
            <input type="text" id="chatbot-input" class="chatbot-input" placeholder="Ask about trips, agents..." autocomplete="off">
            <button type="submit" id="chatbot-send" class="chatbot-send">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>

<link rel="stylesheet" href="chatbot.css">
<script src="chatbot.js"></script>