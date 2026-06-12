<?php
/**
 * ZimsecExamMate — Shared Header
 * 
 * No user accounts — simplified navigation.
 */

$currentPage = $currentPage ?? Helpers::currentPage();
?>
<header class="header">
    <div class="container header-container">
        <!-- Logo -->
        <a href="index.php" class="logo">
            <div class="logo-icon">Z</div>
            <div>
                <h1>ZIMSEC ExamMate</h1>
                <p class="tagline">Your trusted exam preparation companion</p>
            </div>
        </a>

        <!-- Mobile menu toggle -->
        <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle navigation menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <!-- Navigation -->
        <nav class="main-nav" id="mainNav">
            <a href="index.php" class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>">
                <i class="fas fa-home"></i> Home
            </a>

            <!-- Levels Dropdown -->
            <div class="nav-dropdown">
                <button class="nav-link <?= in_array($currentPage, ['grade7.php', 'zjc.php', 'olevel.php', 'alevel.php']) ? 'active' : '' ?>">
                    <i class="fas fa-layer-group"></i> Levels
                </button>
                <div class="dropdown-content">
                    <a href="grade7.php">Grade 7</a>
                    <a href="zjc.php">ZJC</a>
                    <a href="olevel.php">O Level</a>
                    <a href="alevel.php">A Level</a>
                </div>
            </div>

            <!-- Resources Dropdown -->
            <div class="nav-dropdown">
                <button class="nav-link <?= in_array($currentPage, ['pastpapers.php', 'marking-schemes.php', 'topical-papers.php', 'notes.php', 'revision-notes.php', 'syllabi.php', 'timetables.php', 'projects.php']) ? 'active' : '' ?>">
                    <i class="fas fa-folder-open"></i> Resources
                </button>
                <div class="dropdown-content">
                    <a href="pastpapers.php">Past Papers</a>
                    <a href="marking-schemes.php">Marking Schemes</a>
                    <a href="topical-papers.php">Topical Papers</a>
                    <a href="notes.php">Notes & Textbooks</a>
                    <a href="revision-notes.php">Revision Notes</a>
                    <a href="syllabi.php">Syllabi</a>
                    <a href="timetables.php">Timetables</a>
                    <a href="projects.php">Projects</a>
                </div>
            </div>

            <!-- Community Dropdown -->
            <div class="nav-dropdown">
                <button class="nav-link <?= in_array($currentPage, ['uploadindex.php', 'moderateindex.php']) ? 'active' : '' ?>">
                    <i class="fas fa-users"></i> Community
                </button>
                <div class="dropdown-content">
                    <a href="uploadindex.php"><i class="fas fa-upload"></i> Upload Files</a>
                    <a href="moderateindex.php"><i class="fas fa-check-circle"></i> Moderate Files</a>
                </div>
            </div>

            <a href="about.php" class="nav-link <?= $currentPage === 'about.php' ? 'active' : '' ?>">
                <i class="fas fa-info-circle"></i> About
            </a>
            <a href="faq.php" class="nav-link <?= $currentPage === 'faq.php' ? 'active' : '' ?>">
                <i class="fas fa-question-circle"></i> FAQ
            </a>
            <a href="contact.php" class="nav-link <?= $currentPage === 'contact.php' ? 'active' : '' ?>">
                <i class="fas fa-envelope"></i> Contact
            </a>
        </nav>
    </div>
</header>
