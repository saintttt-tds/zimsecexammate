<?php
$inSubDir = (strpos(($currentPage ?? ''), '/') !== false);
$base = $inSubDir ? '../' : '';
?>
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>ZIMSEC ExamMate</h3>
                <p>Your trusted digital companion for ZIMSEC exam preparation.</p>
            </div>
            <div class="footer-section">
                <h3>Exam Levels</h3>
                <a href="<?= $base ?>grade7.php">Grade 7</a>
                <a href="<?= $base ?>zjc.php">ZJC</a>
                <a href="<?= $base ?>olevel.php">O Level</a>
                <a href="<?= $base ?>alevel.php">A Level</a>
            </div>
            <div class="footer-section">
                <h3>Resources</h3>
                <a href="<?= $base ?>pastpapers.php">Past Papers</a>
                <a href="<?= $base ?>marking-schemes.php">Marking Schemes</a>
                <a href="<?= $base ?>topical-papers.php">Topical Papers</a>
                <a href="<?= $base ?>notes.php">Notes & Textbooks</a>
                <a href="<?= $base ?>syllabi.php">Syllabi</a>
                <a href="<?= $base ?>timetables.php">Timetables</a>
            </div>
            <div class="footer-section">
                <h3>Community</h3>
                <a href="<?= $base ?>uploadindex.php">Upload Files</a>
                <a href="<?= $base ?>moderateindex.php">Moderate Files</a>
                <a href="<?= $base ?>faq.php">FAQ</a>
                <a href="<?= $base ?>about.php">About Us</a>
                <a href="<?= $base ?>contact.php">Contact</a>
            </div>
        </div>
        <div class="footer-contact">
            <p>
                <a href="mailto:zimsecexammate@gmail.com"><i class="fas fa-envelope"></i> zimsecexammate@gmail.com</a> |
                <a href="https://wa.me/263714912600"><i class="fab fa-whatsapp"></i> +263 71 491 2600</a> |
                <a href="https://www.facebook.com/zimsecexammate"><i class="fab fa-facebook"></i> @zimsecexammate</a> |
                <a href="https://www.instagram.com/zimsec_exammate"><i class="fab fa-instagram"></i> @zimsecexammate</a>
            </p>
        </div>
        <div class="footer-disclaimer">
            <p><strong>Disclaimer:</strong> ZIMSEC ExamMate is an independent platform not affiliated with ZIMSEC. Official site: <a href="https://www5.zimsec.co.zw" target="_blank">www.zimsec.co.zw</a></p>
        </div>
        <div class="copyright">
            <p>&copy; <?= date('Y') ?> ZIMSEC ExamMate | Built for Greatness</p>
        </div>
    </div>
</footer>