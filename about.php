<?php
/**
 * ZimsecExamMate — About Page
 */

require_once __DIR__ . '/core/App.php';
appInit();

$pageTitle = 'About - ' . SITE_NAME;
$currentPage = 'about.php';

// Get stats for the page
$stats = Scanner::getStats();

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'About', 'url' => null],
];

ob_start();
?>

<?php include TEMPLATES_DIR . '/breadcrumb.php'; ?>

<section class="about-hero">
    <div class="container">
        <h1>About ZIMSEC ExamMate</h1>
        <p>Your trusted digital companion for ZIMSEC exam preparation. We're dedicated to helping students excel in their academic journey.</p>
    </div>
</section>

<!-- Mission -->
<section class="card">
    <div class="container">
        <h3>🎯 Our Mission</h3>
        <p>ZIMSEC ExamMate was created to bridge the gap between learners and essential exam preparation resources. We believe every student deserves access to quality educational materials, regardless of their location or background.</p>
        <p>Our mission is to empower Zimbabwean students to study smarter, not harder, by providing organized collections of Grade 7, ZJC, O Level, and A Level materials in one convenient platform.</p>
    </div>
</section>

<!-- Features -->
<section class="card">
    <div class="container">
        <h3>📚 What We Offer</h3>
        <ul class="features-list">
            <li>
                <div class="feature-icon">📄</div>
                <div>
                    <strong>Past Exam Papers:</strong> Comprehensive collection from recent years with organized subject categories
                </div>
            </li>
            <li>
                <div class="feature-icon">✓</div>
                <div>
                    <strong>Marking Schemes:</strong> Official marking schemes for better understanding of exam requirements
                </div>
            </li>
            <li>
                <div class="feature-icon">📝</div>
                <div>
                    <strong>Topical Papers:</strong> Topic-specific practice questions to master individual concepts
                </div>
            </li>
            <li>
                <div class="feature-icon">📖</div>
                <div>
                    <strong>Study Notes & Textbooks:</strong> Subject-specific study materials, summaries, and revision guides
                </div>
            </li>
            <li>
                <div class="feature-icon">📋</div>
                <div>
                    <strong>Syllabi:</strong> Official ZIMSEC syllabi for all subjects across all levels
                </div>
            </li>
            <li>
                <div class="feature-icon">⏰</div>
                <div>
                    <strong>Exam Timetables:</strong> Up-to-date examination schedules and countdown timers
                </div>
            </li>
            <li>
                <div class="feature-icon">🤖</div>
                <div>
                    <strong>AI Study Assistant:</strong> TalubaMMVII chatbot for instant exam preparation guidance
                </div>
            </li>
            <li>
                <div class="feature-icon">👥</div>
                <div>
                    <strong>Community Verified:</strong> All uploads reviewed by community members to ensure quality
                </div>
            </li>
        </ul>
    </div>
</section>

<!-- Stats -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number"><?= $stats['total_resources'] ?? '0' ?>+</div>
                <div class="stat-label">Educational Resources</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= $stats['total_subjects'] ?? '50' ?>+</div>
                <div class="stat-label">Subjects Covered</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">4</div>
                <div class="stat-label">Education Levels</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Access Availability</div>
            </div>
        </div>
    </div>
</section>

<!-- Vision -->
<section class="card">
    <div class="container">
        <h3>🔭 Our Vision</h3>
        <p>We envision a future where every Zimbabwean student has equal access to quality exam preparation materials, where learning is made easier through technology, and where academic success is within everyone's reach.</p>
        <p>Our goal is to become the most trusted and comprehensive digital resource hub for ZIMSEC examination preparation across the nation.</p>
    </div>
</section>

<!-- How It Works -->
<section class="card">
    <div class="container">
        <h3>🤝 How It Works</h3>
        <div class="community-steps">
            <div class="step">
                <div class="step-number">1</div>
                <h4>Upload</h4>
                <p>Community members share past papers, notes, and resources</p>
            </div>
            <div class="step-arrow">→</div>
            <div class="step">
                <div class="step-number">2</div>
                <h4>Verify</h4>
                <p>Files receive 3 community approvals to become publicly available</p>
            </div>
            <div class="step-arrow">→</div>
            <div class="step">
                <div class="step-number">3</div>
                <h4>Download</h4>
                <p>Everyone accesses verified resources for free, no account needed</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact -->
<section class="card">
    <div class="container">
        <h3>📞 Contact Us</h3>
        <p>Have suggestions, found an issue, or want to contribute resources? We'd love to hear from you!</p>
        
        <div class="contact-info">
            <p><strong>Email:</strong> <a href="mailto:zimsecexammate@gmail.com ">support@zimsecexammate.co.zw</a></p>
            <p><strong>WhatsApp:</strong> <a href="https://wa.me/263714912600">+263 71 491 2600</a></p>
            <p><strong>Facebook:</strong> <a href="https://www.facebook.com/zimsecexammate">@zimsecexammate</a></p>
            <p><strong>Instagram:</strong> <a href="https://www.instagram.com/zimsecexammate">@zimsecexammate</a></p>
        </div>
        
        <p class="disclaimer-note">
            <strong>Note:</strong> We are an independent platform not affiliated with ZIMSEC. 
            Official ZIMSEC website: <a href="https://www5.zimsec.co.zw" target="_blank" rel="noopener noreferrer">www.zimsec.co.zw</a>
        </p>
    </div>
</section>

<?php
$pageContent = ob_get_clean();
include TEMPLATES_DIR . '/layout.php';