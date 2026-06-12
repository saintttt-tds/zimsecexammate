<?php
$currentPage = $currentPage ?? Helpers::currentPage();

// Detect if we're in a subdirectory
$inSubDir = (strpos($currentPage, '/') !== false);
$base = $inSubDir ? '../' : '';
?>
<header class="header">
    <div class="container header-container">
        <a href="<?= $base ?>index.php" class="logo">
            <div class="logo-icon">Z</div>
            <div>
                <h1>ZIMSEC ExamMate</h1>
                <p class="tagline">Your trusted exam preparation companion</p>
            </div>
        </a>

        <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle navigation">
            <span></span><span></span><span></span>
        </button>

        <nav class="main-nav" id="mainNav">
            <a href="<?= $base ?>index.php" class="nav-link <?= in_array($currentPage, ['index.php']) ? 'active' : '' ?>">
                <i class="fas fa-home"></i> Home
            </a>

            <div class="nav-dropdown">
                <button class="nav-link <?= in_array($currentPage, ['grade7.php','zjc.php','olevel.php','alevel.php']) ? 'active' : '' ?>">
                    <i class="fas fa-layer-group"></i> Levels
                </button>
                <div class="dropdown-content">
                    <a href="<?= $base ?>grade7.php">Grade 7</a>
                    <a href="<?= $base ?>zjc.php">ZJC</a>
                    <a href="<?= $base ?>olevel.php">O Level</a>
                    <a href="<?= $base ?>alevel.php">A Level</a>
                </div>
            </div>

            <div class="nav-dropdown">
                <button class="nav-link <?= in_array($currentPage, ['pastpapers.php','marking-schemes.php','topical-papers.php','notes.php','revision-notes.php','syllabi.php','timetables.php','projects.php']) ? 'active' : '' ?>">
                    <i class="fas fa-folder-open"></i> Resources
                </button>
                <div class="dropdown-content">
                    <a href="<?= $base ?>pastpapers.php">Past Papers</a>
                    <a href="<?= $base ?>marking-schemes.php">Marking Schemes</a>
                    <a href="<?= $base ?>topical-papers.php">Topical Papers</a>
                    <a href="<?= $base ?>notes.php">Notes & Textbooks</a>
                    <a href="<?= $base ?>revision-notes.php">Revision Notes</a>
                    <a href="<?= $base ?>syllabi.php">Syllabi</a>
                    <a href="<?= $base ?>timetables.php">Timetables</a>
                    <a href="<?= $base ?>projects.php">Projects</a>
                </div>
            </div>

            <div class="nav-dropdown">
                <button class="nav-link <?= in_array($currentPage, ['uploadindex.php','moderateindex.php']) ? 'active' : '' ?>">
                    <i class="fas fa-users"></i> Community
                </button>
                <div class="dropdown-content">
                    <a href="<?= $base ?>uploadindex.php"><i class="fas fa-upload"></i> Upload Files</a>
                    <a href="<?= $base ?>moderateindex.php"><i class="fas fa-check-circle"></i> Moderate Files</a>
                </div>
            </div>

            <a href="<?= $base ?>about.php" class="nav-link <?= $currentPage === 'about.php' ? 'active' : '' ?>">
                <i class="fas fa-info-circle"></i> About
            </a>
            <a href="<?= $base ?>faq.php" class="nav-link <?= $currentPage === 'faq.php' ? 'active' : '' ?>">
                <i class="fas fa-question-circle"></i> FAQ
            </a>
            <a href="<?= $base ?>contact.php" class="nav-link <?= $currentPage === 'contact.php' ? 'active' : '' ?>">
                <i class="fas fa-envelope"></i> Contact
            </a>
        </nav>
    </div>
</header>