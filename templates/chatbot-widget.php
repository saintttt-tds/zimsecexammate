<?php
/**
 * ZimsecExamMate — Chatbot Widget (TalubaMMVII)
 * 
 * Floating chatbot button and window.
 * No user accounts — chat is in-memory only.
 */

namespace Core;
?>
<div class="taluba-chatbot" id="chatbotWidget">
    <!-- Toggle Button -->
    <button class="chatbot-toggle" id="chatbotToggle" aria-label="Open chat assistant">
        <i class="fas fa-robot"></i>
        <span class="notification-badge" id="chatbotBadge" style="display: none;">1</span>
    </button>

    <!-- Chat Window -->
    <div class="chatbot-window" id="chatbotWindow">
        <!-- Header -->
        <div class="chat-header">
            <div class="chat-avatar">Z</div>
            <div class="chat-header-info">
                <h3>TalubaMMVII</h3>
                <p>Exam Prep Assistant</p>
            </div>
            <button class="chat-close" id="chatbotClose" aria-label="Close chat">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Messages -->
        <div class="chat-body" id="chatbotBody">
            <!-- Welcome Message -->
            <div class="welcome-message">
                <h4>👋 Hello! I'm TalubaMMVII</h4>
                <p>Your ZIMSEC exam preparation assistant. I can help with:</p>
                <ul>
                    <li>Exam preparation strategies</li>
                    <li>Subject-specific guidance</li>
                    <li>Study tips and techniques</li>
                    <li>Understanding ZIMSEC requirements</li>
                </ul>
                <p><small>Conversations are not saved between visits.</small></p>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <button class="quick-action" onclick="sendQuickMessage('Exam preparation tips')">
                    📚 Exam Tips
                </button>
                <button class="quick-action" onclick="sendQuickMessage('How to use past papers effectively')">
                    📝 Past Papers
                </button>
                <button class="quick-action" onclick="sendQuickMessage('Study schedule advice')">
                    📅 Study Plan
                </button>
                <button class="quick-action" onclick="sendQuickMessage('Help with Mathematics')">
                    🔢 Mathematics
                </button>
                <button class="quick-action" onclick="sendQuickMessage('What subjects are available')">
                    📋 Subjects
                </button>
                <button class="quick-action" onclick="sendQuickMessage('help')">
                    🛠️ All Commands
                </button>
            </div>
        </div>

        <!-- Typing Indicator -->
        <div class="typing-indicator" id="typingIndicator">
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
        </div>

        <!-- Input Area -->
        <div class="chat-input-area">
            <input type="text" 
                   class="chat-input" 
                   id="chatbotInput" 
                   placeholder="Ask me anything about ZIMSEC exams..."
                   aria-label="Chat message">
            <button class="chat-send" id="chatbotSend" aria-label="Send message">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>