<?php
/**
 * ZimsecExamMate — Exam Timetables
 * 
 * Countdown timers and downloadable timetable PDFs.
 */

require_once __DIR__ . '/core/App.php';
appInit();

$pageTitle = 'ZIMSEC Exam Timetables 2026 - ' . SITE_NAME;
$pageDescription = 'View ZIMSEC examination timetables and countdown to June and November exam sessions. Download O Level, A Level, and Grade 7 timetables.';
$pageKeywords = 'ZIMSEC timetable 2026, ZIMSEC exam dates, O Level timetable, A Level timetable, Grade 7 exam dates, ZIMSEC exam schedule, June exams, November exams';
$currentPage = 'timetables.php';

// Calculate exam dates
$currentYear = date('Y');

// June session: estimate May 27
$juneDate = date('Y-05-27');
if (time() > strtotime($juneDate)) {
    $juneDate = date('Y-05-27', strtotime('+1 year'));
}

// November session: estimate October 14
$novemberDate = date('Y-10-14');
if (time() > strtotime($novemberDate)) {
    $novemberDate = date('Y-10-14', strtotime('+1 year'));
}

$juneYear = date('Y', strtotime($juneDate));
$novemberYear = date('Y', strtotime($novemberDate));
$juneDisplay = date('F j, Y', strtotime($juneDate));
$novemberDisplay = date('F j, Y', strtotime($novemberDate));

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Timetables', 'url' => null],
];

ob_start();
?>

<?php include __DIR__ . '/templates/breadcrumb.php'; ?>

<section class="level-hero">
    <div class="container">
        <h1>Examination Timetables</h1>
        <p class="level-description">
            Official ZIMSEC examination schedules and countdown to exams
        </p>
    </div>
</section>

<!-- Countdown Section -->
<section class="countdown-section">
    <div class="container">
        <h2>Countdown to Examination Sessions</h2>
        
        <div class="countdown-container">
            <div class="countdown-box">
                <h3>June Session <?= $juneYear ?></h3>
                <div class="countdown-timer" id="countdownJune">--</div>
                <p class="countdown-label">Starts <?= Helpers::h($juneDisplay) ?></p>
            </div>
            
            <div class="countdown-box">
                <h3>November Session <?= $novemberYear ?></h3>
                <div class="countdown-timer" id="countdownNovember">--</div>
                <p class="countdown-label">Starts <?= Helpers::h($novemberDisplay) ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Session Tabs -->
<div class="session-tabs">
    <button class="session-tab active" onclick="showTimetable('june')">
        June <?= $juneYear ?> Session
    </button>
    <button class="session-tab" onclick="showTimetable('november')">
        November <?= $novemberYear ?> Session
    </button>
</div>

<!-- June Timetable -->
<section class="timetable-section" id="juneTimetable">
    <div class="timetable-card">
        <div class="timetable-header">
            <h3>June <?= $juneYear ?> Examination Timetable</h3>
            <span class="exam-period">June Session</span>
        </div>
        
        <div class="timetable-notice">
            <p><strong>Please note:</strong> These are estimated dates based on historical patterns. 
            Always confirm with the official ZIMSEC timetable when released.</p>
        </div>
        
        <div class="download-actions">
            <h3>Download Timetables</h3>
            <div class="download-buttons">
                <a href="assets/pdfs/syllabi/olevel/timetable_june_<?= $juneYear ?>.pdf" class="btn btn-primary" download>
                    <i class="fas fa-download"></i> O Level Timetable
                </a>
                <a href="assets/pdfs/syllabi/alevel/timetable_june_<?= $juneYear ?>.pdf" class="btn btn-primary" download>
                    <i class="fas fa-download"></i> A Level Timetable
                </a>
                <a href="assets/pdfs/syllabi/grade7/timetable_june_<?= $juneYear ?>.pdf" class="btn btn-primary" download>
                    <i class="fas fa-download"></i> Grade 7 Timetable
                </a>
            </div>
        </div>
    </div>
</section>

<!-- November Timetable -->
<section class="timetable-section" id="novemberTimetable" style="display: none;">
    <div class="timetable-card">
        <div class="timetable-header">
            <h3>November <?= $novemberYear ?> Examination Timetable</h3>
            <span class="exam-period">November Session</span>
        </div>
        
        <div class="timetable-notice">
            <p><strong>Please note:</strong> These are estimated dates based on historical patterns. 
            Always confirm with the official ZIMSEC timetable when released.</p>
        </div>
        
        <div class="download-actions">
            <h3>Download Timetables</h3>
            <div class="download-buttons">
                <a href="assets/pdfs/syllabi/olevel/timetable_november_<?= $novemberYear ?>.pdf" class="btn btn-primary" download>
                    <i class="fas fa-download"></i> O Level Timetable
                </a>
                <a href="assets/pdfs/syllabi/alevel/timetable_november_<?= $novemberYear ?>.pdf" class="btn btn-primary" download>
                    <i class="fas fa-download"></i> A Level Timetable
                </a>
                <a href="assets/pdfs/syllabi/grade7/timetable_november_<?= $novemberYear ?>.pdf" class="btn btn-primary" download>
                    <i class="fas fa-download"></i> Grade 7 Timetable
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Important Notices -->
<section class="resources-section">
    <div class="container">
        <h3>Important Examination Information</h3>
        <div class="resources-grid">
            <div class="resource-card">
                <div class="resource-icon">⚠️</div>
                <h4>Examination Rules</h4>
                <p>Important rules and regulations for candidates</p>
                <p class="resource-note">Check the official ZIMSEC website for the latest exam regulations.</p>
            </div>
            <div class="resource-card">
                <div class="resource-icon">🎒</div>
                <h4>What to Bring</h4>
                <p>Required materials and prohibited items for examination day</p>
                <p class="resource-note">ID, admission letter, stationery. No phones or smart watches.</p>
            </div>
        </div>
    </div>
</section>

<script>
(function() {
    // Pass dates from PHP to JavaScript as proper strings
    var JUNE_DATE = "<?= $juneDate ?>";
    var NOVEMBER_DATE = "<?= $novemberDate ?>";

    function updateCountdowns() {
        var now = new Date().getTime();
        var juneEl = document.getElementById('countdownJune');
        var novEl = document.getElementById('countdownNovember');
        
        // June countdown
        if (juneEl) {
            var juneTime = new Date(JUNE_DATE).getTime();
            var juneDistance = juneTime - now;
            
            if (juneDistance < 0) {
                juneEl.innerHTML = 'SESSION STARTED';
            } else {
                var d = Math.floor(juneDistance / (1000 * 60 * 60 * 24));
                var h = Math.floor((juneDistance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var m = Math.floor((juneDistance % (1000 * 60 * 60)) / (1000 * 60));
                var s = Math.floor((juneDistance % (1000 * 60)) / 1000);
                juneEl.innerHTML = d + 'd ' + h + 'h ' + m + 'm ' + s + 's';
            }
        }
        
        // November countdown
        if (novEl) {
            var novTime = new Date(NOVEMBER_DATE).getTime();
            var novDistance = novTime - now;
            
            if (novDistance < 0) {
                novEl.innerHTML = 'SESSION STARTED';
            } else {
                var d = Math.floor(novDistance / (1000 * 60 * 60 * 24));
                var h = Math.floor((novDistance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var m = Math.floor((novDistance % (1000 * 60 * 60)) / (1000 * 60));
                var s = Math.floor((novDistance % (1000 * 60)) / 1000);
                novEl.innerHTML = d + 'd ' + h + 'h ' + m + 'm ' + s + 's';
            }
        }
    }

    // Start the timer immediately
    updateCountdowns();
    setInterval(updateCountdowns, 1000);

    // Tab switching
    window.showTimetable = function(session) {
        var tabs = document.querySelectorAll('.session-tab');
        for (var i = 0; i < tabs.length; i++) {
            tabs[i].classList.remove('active');
        }
        
        if (session === 'june') {
            tabs[0].classList.add('active');
            document.getElementById('juneTimetable').style.display = 'block';
            document.getElementById('novemberTimetable').style.display = 'none';
        } else {
            tabs[1].classList.add('active');
            document.getElementById('juneTimetable').style.display = 'none';
            document.getElementById('novemberTimetable').style.display = 'block';
        }
    };
})();
</script>

<?php
$pageContent = ob_get_clean();
include __DIR__ . '/templates/layout.php';