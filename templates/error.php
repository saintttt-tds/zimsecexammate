<?php
/**
 * ZimsecExamMate — Error Page
 * 
 * Displays HTTP errors with helpful messages.
 */

$errorCode = http_response_code() ?: 404;
$errorCodes = [
    400 => ['title' => 'Bad Request', 'message' => 'The request could not be understood.'],
    403 => ['title' => 'Access Denied', 'message' => 'You do not have permission to access this resource.'],
    404 => ['title' => 'Page Not Found', 'message' => 'The page you are looking for does not exist or has been moved.'],
    405 => ['title' => 'Method Not Allowed', 'message' => 'This request method is not supported.'],
    413 => ['title' => 'File Too Large', 'message' => 'The uploaded file exceeds the maximum allowed size of ' . Helpers::formatFileSize(MAX_UPLOAD_SIZE) . '.'],
    429 => ['title' => 'Too Many Requests', 'message' => 'You have made too many requests. Please wait a moment and try again.'],
    500 => ['title' => 'Server Error', 'message' => 'Something went wrong on our end. Please try again later.'],
];

$error = $errorCodes[$errorCode] ?? $errorCodes[404];
$pageTitle = "{$errorCode} - {$error['title']}";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Helpers::h($pageTitle) ?> | <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="error-page">
        <div class="container">
            <div class="error-content">
                <div class="error-code"><?= $errorCode ?></div>
                <h1><?= Helpers::h($error['title']) ?></h1>
                <p><?= Helpers::h($error['message']) ?></p>
                <div class="error-actions">
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-home"></i> Go Home
                    </a>
                    <a href="search.php" class="btn btn-outline">
                        <i class="fas fa-search"></i> Search Resources
                    </a>
                    <a href="contact.php" class="btn btn-outline">
                        <i class="fas fa-envelope"></i> Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>