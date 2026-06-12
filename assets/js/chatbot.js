/**
 * ZimsecExamMate — TalubaMMVII Chatbot
 */
(function () {
    'use strict';

    // Wait for DOM
    document.addEventListener('DOMContentLoaded', function () {

        var widget = document.getElementById('chatbotWidget');
        if (!widget) return;

        var toggle = document.getElementById('chatbotToggle');
        var windowEl = document.getElementById('chatbotWindow');
        var closeBtn = document.getElementById('chatbotClose');
        var body = document.getElementById('chatbotBody');
        var input = document.getElementById('chatbotInput');
        var sendBtn = document.getElementById('chatbotSend');
        var typingIndicator = document.getElementById('typingIndicator');
        var badge = document.getElementById('chatbotBadge');

        var isOpen = false;

        // ─── Open Chat ────────────────────────────
        function openChat() {
            isOpen = true;
            windowEl.classList.add('active');
            toggle.classList.add('active');
            if (badge) badge.style.display = 'none';
            if (input) input.focus();
            scrollToBottom();
        }

        // ─── Close Chat ───────────────────────────
        function closeChat() {
            isOpen = false;
            windowEl.classList.remove('active');
            toggle.classList.remove('active');
        }

        // ─── Toggle Chat ──────────────────────────
        if (toggle) {
            toggle.addEventListener('click', function () {
                if (isOpen) {
                    closeChat();
                } else {
                    openChat();
                }
            });
        }

        // ─── Close Button ─────────────────────────
        if (closeBtn) {
            closeBtn.addEventListener('click', closeChat);
        }

        // ─── Send Message ─────────────────────────
        function sendMessage(message) {
            var text = message || (input ? input.value.trim() : '');
            if (!text) return;

            // Add user message to chat
            addMessage(text, 'user');

            // Clear input
            if (input) {
                input.value = '';
                input.focus();
            }

            // Show typing indicator
            if (typingIndicator) typingIndicator.classList.add('active');
            if (sendBtn) sendBtn.disabled = true;

            scrollToBottom();

            // Send to server
            fetch('chatbot.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    message: text
                })
            })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                // Hide typing
                if (typingIndicator) typingIndicator.classList.remove('active');
                if (sendBtn) sendBtn.disabled = false;

                if (data.success) {
                    addMessage(data.response, 'bot');
                } else {
                    addMessage('Sorry, something went wrong. Please try again.', 'bot');
                }
                scrollToBottom();
            })
            .catch(function () {
                if (typingIndicator) typingIndicator.classList.remove('active');
                if (sendBtn) sendBtn.disabled = false;
                addMessage('Network error. Please check your connection and try again.', 'bot');
                scrollToBottom();
            });
        }

        // ─── Add Message to Chat ──────────────────
        function addMessage(text, sender) {
            if (!body) return;

            var msgDiv = document.createElement('div');
            msgDiv.className = 'message ' + sender;

            // Convert newlines to <br>
            var formatted = text.replace(/\n/g, '<br>');
            // Convert **bold** to <strong>
            formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            msgDiv.innerHTML = formatted;

            // Add timestamp
            var timeSpan = document.createElement('span');
            timeSpan.className = 'message-time';
            var now = new Date();
            var hours = now.getHours().toString().padStart(2, '0');
            var minutes = now.getMinutes().toString().padStart(2, '0');
            timeSpan.textContent = hours + ':' + minutes;
            msgDiv.appendChild(timeSpan);

            body.appendChild(msgDiv);
        }

        // ─── Scroll to Bottom ─────────────────────
        function scrollToBottom() {
            if (body) {
                body.scrollTop = body.scrollHeight;
            }
        }

        // ─── Send Button Click ────────────────────
        if (sendBtn) {
            sendBtn.addEventListener('click', function () {
                sendMessage();
            });
        }

        // ─── Enter Key to Send ────────────────────
        if (input) {
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
        }

        // ─── Quick Action Buttons ─────────────────
        if (body) {
            body.addEventListener('click', function (e) {
                var target = e.target;
                if (target.classList.contains('quick-action')) {
                    var text = target.textContent.trim();
                    sendMessage(text);
                }
            });
        }

        // ─── Global Quick Message Function ────────
        window.sendQuickMessage = function (text) {
            if (!isOpen) openChat();
            sendMessage(text);
        };

        // ─── Hide badge after 10 seconds ──────────
        if (badge && !isOpen) {
            setTimeout(function () {
                badge.style.display = 'none';
            }, 10000);
        }

        // ─── Initial scroll ───────────────────────
        scrollToBottom();

    });

})();