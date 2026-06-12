<?php
/**
 * ZimsecExamMate — Contact Page
 */

require_once __DIR__ . '/core/App.php';
appInit();

$pageTitle = 'Contact - ' . SITE_NAME;
$currentPage = 'contact.php';

$messageSent = false;
$messageError = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCsrfRequest()) {
        $messageError = 'Security check failed. Please try again.';
    } elseif (!Security::checkRateLimit('contact', 3, 3600)) {
        $messageError = 'Too many messages. Please wait a while before sending another.';
    } else {
        $name = Security::sanitize($_POST['name'] ?? '');
        $email = Security::sanitize($_POST['email'] ?? '');
        $subject = Security::sanitize($_POST['subject'] ?? '');
        $message = Security::sanitize($_POST['message'] ?? '');

        if (empty($name) || empty($email) || empty($message)) {
            $messageError = 'Please fill in all required fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $messageError = 'Please enter a valid email address.';
        } else {
            // Log the message
            $logMessage = date('[Y-m-d H:i:s]') . " Contact from {$name} <{$email}>: {$subject}\n{$message}\n\n";
            error_log($logMessage, 3, LOGS_DIR . '/contact.log');
            $messageSent = true;
        }
    }
}

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Contact', 'url' => null],
];

ob_start();
?>

<?php include TEMPLATES_DIR . '/breadcrumb.php'; ?>

<section class="contact-hero">
    <div class="container">
        <h1>Contact Us</h1>
        <p>Have suggestions, found an issue, or want to contribute resources? We'd love to hear from you!</p>
    </div>
</section>

<section class="contact-section">
    <div class="container">
        <div class="contact-grid">
            <!-- Contact Form -->
            <div class="contact-form-container">
                <?php if ($messageSent): ?>
                    <div class="success-message">
                        <div class="success-icon">✅</div>
                        <h3>Message Sent!</h3>
                        <p>Thank you for reaching out. We'll get back to you as soon as possible.</p>
                        <a href="contact.php" class="btn btn-outline">Send Another Message</a>
                    </div>
                <?php else: ?>
                    <?php if ($messageError): ?>
                        <div class="error-message">
                            <p><?= Helpers::h($messageError) ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="contact.php" class="contact-form">
                        <?= Security::csrfField() ?>
                        
                        <div class="form-group">
                            <label for="name">Name *</label>
                            <input type="text" name="name" id="name" required 
                                   value="<?= Helpers::h($_POST['name'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" name="email" id="email" required
                                   value="<?= Helpers::h($_POST['email'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" name="subject" id="subject"
                                   value="<?= Helpers::h($_POST['subject'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea name="message" id="message" rows="6" required><?= Helpers::h($_POST['message'] ?? '') ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                <?php endif; ?>
            </div>
            
            <!-- Contact Info -->
            <div class="contact-info-sidebar">
                <h3>Get in Touch</h3>
                
                <div class="contact-method">
                    <div class="contact-icon">📧</div>
                    <div>
                        <strong>Email</strong>
                        <p><a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a></p>
                    </div>
                </div>
                
                <div class="contact-method">
                    <div class="contact-icon">💬</div>
                    <div>
                        <strong>WhatsApp</strong>
                        <p><a href="https://wa.me/263714912600">+263 71 491 2600</a></p>
                    </div>
                </div>
                
                <div class="contact-method">
                    <div class="contact-icon">📘</div>
                    <div>
                        <strong>Facebook</strong>
                        <p><a href="https://www.facebook.com/zimsecexammate">@zimsecexammate</a></p>
                    </div>
                </div>
                
                <div class="contact-method">
                    <div class="contact-icon">📷</div>
                    <div>
                        <strong>Instagram</strong>
                        <p><a href="https://www.instagram.com/zimsecexammate">@zimsecexammate</a></p>
                    </div>
                </div>
                
                <div class="contact-disclaimer">
                    <p><strong>Note:</strong> We are an independent platform not affiliated with ZIMSEC.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$pageContent = ob_get_clean();
include TEMPLATES_DIR . '/layout.php';