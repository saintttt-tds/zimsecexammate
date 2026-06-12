<?php
/**
 * ZimsecExamMate — FAQ Page
 */

require_once __DIR__ . '/core/App.php';
appInit();

$pageTitle = 'FAQ - ' . SITE_NAME;
$currentPage = 'faq.php';

$faqCategories = Config::get('faq', []);

// Calculate total questions
$totalQuestions = 0;
foreach ($faqCategories as $category) {
    $totalQuestions += count($category['questions'] ?? []);
}

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'FAQ', 'url' => null],
];

ob_start();
?>

<?php include TEMPLATES_DIR . '/breadcrumb.php'; ?>

<section class="faq-hero">
    <div class="container">
        <h1>Frequently Asked Questions</h1>
        <p>Find quick answers to common questions about ZIMSEC ExamMate. Can't find what you're looking for? Contact our support team.</p>
    </div>
</section>

<!-- Stats -->
<div class="stats-bar">
    <p><?= $totalQuestions ?> questions in <?= count($faqCategories) ?> categories</p>
</div>

<!-- Category Navigation -->
<div class="categories-nav">
    <button class="category-btn active" data-category="all">📋 All Questions</button>
    <?php foreach ($faqCategories as $categoryId => $category): ?>
    <button class="category-btn" data-category="<?= Helpers::h($categoryId) ?>">
        <?= Helpers::h($category['icon'] ?? '❓') ?> <?= Helpers::h($category['name'] ?? '') ?>
    </button>
    <?php endforeach; ?>
</div>

<!-- FAQ Content -->
<div id="faqContent">
    <?php foreach ($faqCategories as $categoryId => $category): ?>
    <div class="faq-category" data-category="<?= Helpers::h($categoryId) ?>">
        <div class="category-header">
            <div class="category-icon"><?= Helpers::h($category['icon'] ?? '❓') ?></div>
            <h2><?= Helpers::h($category['name'] ?? '') ?></h2>
        </div>
        
        <div class="faq-list">
            <?php foreach ($category['questions'] ?? [] as $faq): ?>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span><?= Helpers::h($faq['question'] ?? '') ?></span>
                    <span class="faq-toggle">+</span>
                </div>
                <div class="faq-answer">
                    <?= $faq['answer'] ?? '' ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Contact Promo -->
<section class="contact-promo">
    <div class="container">
        <h3>Still have questions?</h3>
        <p>We're here to help. Reach out to us anytime.</p>
        <a href="contact.php" class="btn btn-primary">Contact Us</a>
    </div>
</section>

<script>
function toggleFAQ(element) {
    const faqItem = element.closest('.faq-item');
    const wasActive = faqItem.classList.contains('active');
    
    // Close all in the same category
    const category = faqItem.closest('.faq-category');
    if (category) {
        category.querySelectorAll('.faq-item.active').forEach(item => {
            if (item !== faqItem) {
                item.classList.remove('active');
                item.querySelector('.faq-toggle').textContent = '+';
            }
        });
    }
    
    faqItem.classList.toggle('active');
    faqItem.querySelector('.faq-toggle').textContent = faqItem.classList.contains('active') ? '−' : '+';
}

// Category filtering
document.querySelectorAll('.category-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const category = this.dataset.category;
        document.querySelectorAll('.faq-category').forEach(cat => {
            cat.style.display = (category === 'all' || cat.dataset.category === category) ? 'block' : 'none';
        });
    });
});

// Auto-open first FAQ in each category
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.faq-category').forEach(category => {
        const firstFaq = category.querySelector('.faq-item');
        if (firstFaq) {
            firstFaq.classList.add('active');
            firstFaq.querySelector('.faq-toggle').textContent = '−';
        }
    });
});
</script>

<?php
$pageContent = ob_get_clean();
include TEMPLATES_DIR . '/layout.php';