// chatbot.js - Fixed & Professional
document.addEventListener('DOMContentLoaded', function() {
    
    const chatbotToggle = document.getElementById('chatbot-toggle');
    const chatbotContainer = document.getElementById('chatbot-container');
    const chatbotClose = document.getElementById('chatbot-close');
    const chatbotMessages = document.getElementById('chatbot-messages');
    const chatbotInput = document.getElementById('chatbot-input');
    const chatbotSend = document.getElementById('chatbot-send');
    const chatbotForm = document.getElementById('chatbot-form');
    const quickQuestions = document.getElementById('chatbot-quick-questions');
    
    // 1. Toggle Chat Window
    if (chatbotToggle) {
        chatbotToggle.addEventListener('click', () => {
            chatbotContainer.classList.add('active');
            if (chatbotMessages.children.length === 0) {
                initChat();
            }
        });
    }

    if (chatbotClose) {
        chatbotClose.addEventListener('click', () => {
            chatbotContainer.classList.remove('active');
        });
    }

    // 2. Initialize Chat
    function initChat() {
        // Add greeting
        addMessage("Hello! 👋 I'm your ExpenseVoyage assistant. How can I help you plan your trip today?", 'bot');
        loadSuggestions();
    }

    // 3. Load Suggestions via AJAX
    function loadSuggestions() {
        fetch('chatbot-ajax.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'get_suggestions=1'
        })
        .then(response => response.json())
        .then(data => {
            if (data.suggestions) {
                renderSuggestions(data.suggestions);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function renderSuggestions(suggestions) {
        quickQuestions.innerHTML = '';
        suggestions.forEach(text => {
            const btn = document.createElement('button');
            btn.className = 'quick-question-btn';
            btn.innerText = text;
            btn.onclick = () => sendMessage(text);
            quickQuestions.appendChild(btn);
        });
    }

    // 4. Send Message Logic
    function sendMessage(text = null) {
        const msg = text || chatbotInput.value.trim();
        if (!msg) return;

        // User Message
        addMessage(msg, 'user');
        chatbotInput.value = '';
        quickQuestions.innerHTML = ''; // Clear buttons while thinking

        // Show Typing Indicator
        const typingId = showTyping();

        // AJAX Request
        fetch('chatbot-ajax.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'message=' + encodeURIComponent(msg)
        })
        .then(response => response.json())
        .then(data => {
            removeTyping(typingId);
            addMessage(data.response, 'bot');
            if (data.suggestions) {
                renderSuggestions(data.suggestions);
            }
        })
        .catch(error => {
            removeTyping(typingId);
            addMessage("Sorry, I'm having trouble connecting right now.", 'bot');
        });
    }

    // 5. UI Helper Functions
    function addMessage(text, sender) {
        const div = document.createElement('div');
        div.className = `chat-message ${sender}`;
        
        // Format bold text (**text**) to <strong>
        const formattedText = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
        
        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        
        div.innerHTML = `
            <div class="message-content">
                ${formattedText}
                <div class="message-time">${time}</div>
            </div>
        `;
        chatbotMessages.appendChild(div);
        scrollToBottom();
    }

    function showTyping() {
        const id = 'typing-' + Date.now();
        const div = document.createElement('div');
        div.id = id;
        div.className = 'chat-message bot';
        div.innerHTML = `
            <div class="message-content" style="background: #f1f1f1;">
                <div class="typing-indicator">
                    <span></span><span></span><span></span>
                </div>
            </div>
        `;
        chatbotMessages.appendChild(div);
        scrollToBottom();
        return id;
    }

    function removeTyping(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }

    function scrollToBottom() {
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }

    // Event Listeners for Input
    chatbotForm.addEventListener('submit', (e) => {
        e.preventDefault();
        sendMessage();
    });
});