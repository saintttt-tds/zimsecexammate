<?php
/**
 * ZimsecExamMate — Chatbot API (TalubaMMVII)
 * 
 * JSON API endpoint for the chatbot widget.
 * No user accounts — stateless, in-memory conversations only.
 */

require_once __DIR__ . '/core/App.php';
appInit();

// Set JSON header
header('Content-Type: application/json; charset=utf-8');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Helpers::jsonResponse([
        'success' => false,
        'error' => 'Method not allowed. Use POST.',
    ], 405);
}

// Rate limit
if (!Security::checkRateLimit('chatbot', 30, 60)) {
    Helpers::jsonResponse([
        'success' => false,
        'error' => 'Too many messages. Please wait a moment.',
    ], 429);
}

// Get input
$input = json_decode(file_get_contents('php://input'), true);
$message = trim($input['message'] ?? '');

if (empty($message)) {
    Helpers::jsonResponse([
        'success' => false,
        'error' => 'Please enter a message.',
    ]);
}

// Get response
$response = getChatbotResponse($message);

Helpers::jsonResponse([
    'success' => true,
    'response' => $response,
    'timestamp' => date('H:i'),
]);

/**
 * Generate chatbot response (rule-based, no AI)
 */
function getChatbotResponse(string $message): string
{
    $message = strtolower(trim($message));
    
    // ─── Greetings ────────────────────────────────
    if (preg_match('/^(hello|hi|hey|greetings|good morning|good afternoon|good evening)/i', $message)) {
        $greetings = [
            "Hello! I'm **TalubaMMVII**, your dedicated ZIMSEC ExamMate assistant. I can help with exam preparation tips, subject guidance, study strategies, and understanding how to use this platform. What do you need help with?",
            "Welcome! I'm **TalubaMMVII**, your ZIMSEC exam specialist. Ask me about study techniques, subject-specific advice, or how to find resources on this site. How can I help today?",
        ];
        return $greetings[array_rand($greetings)];
    }
    
    // ─── Help ─────────────────────────────────────
    if (preg_match('/(help|commands|what can you do|features)/i', $message)) {
        return "**🛠️ What I Can Help With**\n\n" .
               "**📚 Academic Assistance**\n" .
               "• Subject-specific guidance (Math, English, Sciences, etc.)\n" .
               "• Exam preparation strategies\n" .
               "• Past paper techniques\n" .
               "• Study schedule creation\n" .
               "• Resource recommendations\n\n" .
               "**📋 Platform Help**\n" .
               "• How to download resources\n" .
               "• How to upload files\n" .
               "• How moderation works\n" .
               "• Understanding verification\n\n" .
               "**🎯 Try asking:**\n" .
               "• \"How do I prepare for O Level Mathematics?\"\n" .
               "• \"What subjects are available for A Level?\"\n" .
               "• \"How does file verification work?\"\n" .
               "• \"Exam preparation tips\"\n\n" .
               "**What would you like assistance with?**";
    }
    
    // ─── Exam Preparation ─────────────────────────
    if (preg_match('/(exam prep|preparation tips|study tips|how to prepare|study strategy)/i', $message)) {
        return "**📚 Exam Preparation Strategy**\n\n" .
               "**Phase 1: Foundation (Weeks 1-4)**\n" .
               "• Complete syllabus coverage using official documents\n" .
               "• Create topic summaries and mind maps\n" .
               "• Identify and address knowledge gaps\n\n" .
               "**Phase 2: Skill Development (Weeks 5-8)**\n" .
               "• Practice past papers from recent years\n" .
               "• Focus on question patterns and formats\n" .
               "• Develop time management skills\n\n" .
               "**Phase 3: Exam Simulation (Weeks 9-10)**\n" .
               "• Full mock exams under timed conditions\n" .
               "• Analyze performance and adjust strategies\n\n" .
               "**Phase 4: Final Prep (Week 11-12)**\n" .
               "• Light revision of key concepts\n" .
               "• Formula and definition memorization\n" .
               "• Reduce study intensity before exams\n\n" .
               "**Need subject-specific advice?** Just ask!";
    }
    
    // ─── Subject Guidance ─────────────────────────
    if (preg_match('/(math|english|biology|chemistry|physics|history|geography|science)/i', $message)) {
        return "**📝 Subject-Specific Guidance**\n\n" .
               "I can provide detailed assistance for all ZIMSEC subjects:\n\n" .
               "**Core Subjects:** Mathematics, English Language, Combined Science\n\n" .
               "**Sciences:** Biology, Chemistry, Physics, Computer Science, Agriculture\n\n" .
               "**Humanities:** History, Geography, Sociology, Heritage Studies, Religious Studies\n\n" .
               "**Commerce:** Economics, Business Studies, Accounting, Commerce\n\n" .
               "**Technical:** Building, Metal, Wood, Textile, Food Technology, Technical Graphics\n\n" .
               "**Arts:** Art, Music, Theatre, Dance\n\n" .
               "**Languages:** Shona, Ndebele, Tonga, French, and Literature subjects\n\n" .
               "**Which specific subject do you need help with?**";
    }
    
    // ─── How Upload/Moderation Works ──────────────
    if (preg_match('/(upload|moderation|verify|approve|reject)/i', $message)) {
        return "**👥 Community Verification System**\n\n" .
               "**How it works:**\n" .
               "1. Anyone can upload a PDF file (past paper, notes, etc.)\n" .
               "2. Uploaded files go to the moderation queue\n" .
               "3. Community members review and vote\n" .
               "4. **3 approvals** → file becomes publicly available\n" .
               "5. **3 rejections** → file is removed\n\n" .
               "**No accounts needed!** Anyone can upload and vote. The system prevents the same person from voting twice on the same file.\n\n" .
               "**To upload:** Go to Community → Upload Files\n" .
               "**To moderate:** Go to Community → Moderate Files";
    }
    
    // ─── Past Papers ──────────────────────────────
    if (preg_match('/(past paper|download|find paper)/i', $message)) {
        return "**📄 Finding Past Papers**\n\n" .
               "1. Go to **Past Papers** in the Resources menu\n" .
               "2. Filter by:\n" .
               "   • Education Level (Grade 7, ZJC, O Level, A Level)\n" .
               "   • Year (papers from multiple years)\n" .
               "   • Subject\n" .
               "3. Click **Download** on any paper\n\n" .
               "**Tip:** Many papers have matching **Marking Schemes** — look for the green badge!\n\n" .
               "You can also **search** for specific subjects or years using the search bar.";
    }
    
    // ─── About the Platform ───────────────────────
    if (preg_match('/(who are you|what is this|about|zimsec|affiliated)/i', $message)) {
        return "**ℹ️ About ZIMSEC ExamMate**\n\n" .
               "ZIMSEC ExamMate is an **independent, community-driven platform** providing free educational resources for Zimbabwean students.\n\n" .
               "**What we offer:**\n" .
               "• Past papers with marking schemes\n" .
               "• Topical practice papers\n" .
               "• Study notes and textbooks\n" .
               "• Official syllabi\n" .
               "• Exam timetables with countdowns\n\n" .
               "**Important:** We are **not affiliated** with the Zimbabwe School Examinations Council (ZIMSEC). The official ZIMSEC website is www.zimsec.co.zw.\n\n" .
               "We're built and maintained by the community, for the community. All resources are free.";
    }
    
    // ─── Default ──────────────────────────────────
    $defaults = [
        "I'm **TalubaMMVII**, your ZIMSEC exam assistant. I can help with study tips, subject guidance, and using this platform. Try asking about exam preparation, specific subjects, or how to find resources!",
        "Need help with your ZIMSEC studies? I can provide exam preparation strategies, subject-specific advice, and help you navigate the platform. What are you looking for?",
        "I'm here to help with your ZIMSEC exam preparation! Ask me about study techniques, past papers, specific subjects, or how the community verification system works.",
    ];
    
    return $defaults[array_rand($defaults)];
}