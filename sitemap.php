<?php
require_once __DIR__ . '/core/App.php';
appInit();

header('Content-Type: application/xml; charset=utf-8');

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    
    <!-- Main Pages -->
    <url><loc><?= SITE_URL ?>/</loc><priority>1.0</priority><changefreq>daily</changefreq></url>
    <url><loc><?= SITE_URL ?>/grade7.php</loc><priority>0.9</priority><changefreq>weekly</changefreq></url>
    <url><loc><?= SITE_URL ?>/zjc.php</loc><priority>0.9</priority><changefreq>weekly</changefreq></url>
    <url><loc><?= SITE_URL ?>/olevel.php</loc><priority>0.9</priority><changefreq>weekly</changefreq></url>
    <url><loc><?= SITE_URL ?>/alevel.php</loc><priority>0.9</priority><changefreq>weekly</changefreq></url>
    
    <!-- Resource Pages -->
    <url><loc><?= SITE_URL ?>/pastpapers.php</loc><priority>0.9</priority><changefreq>daily</changefreq></url>
    <url><loc><?= SITE_URL ?>/marking-schemes.php</loc><priority>0.8</priority><changefreq>daily</changefreq></url>
    <url><loc><?= SITE_URL ?>/topical-papers.php</loc><priority>0.8</priority><changefreq>daily</changefreq></url>
    <url><loc><?= SITE_URL ?>/notes.php</loc><priority>0.8</priority><changefreq>daily</changefreq></url>
    <url><loc><?= SITE_URL ?>/syllabi.php</loc><priority>0.7</priority><changefreq>weekly</changefreq></url>
    <url><loc><?= SITE_URL ?>/timetables.php</loc><priority>0.8</priority><changefreq>weekly</changefreq></url>
    <url><loc><?= SITE_URL ?>/projects.php</loc><priority>0.7</priority><changefreq>weekly</changefreq></url>
    
    <!-- Other Pages -->
    <url><loc><?= SITE_URL ?>/search.php</loc><priority>0.6</priority><changefreq>weekly</changefreq></url>
    <url><loc><?= SITE_URL ?>/about.php</loc><priority>0.5</priority><changefreq>monthly</changefreq></url>
    <url><loc><?= SITE_URL ?>/faq.php</loc><priority>0.5</priority><changefreq>monthly</changefreq></url>
    <url><loc><?= SITE_URL ?>/contact.php</loc><priority>0.4</priority><changefreq>monthly</changefreq></url>
    
    <!-- Subject Pages -->
    <?php foreach (Config::getAllSubjects() as $subject): ?>
    <url>
        <loc><?= SITE_URL ?>/subject.php?code=<?= $subject['code'] ?>&level=<?= $subject['level'] ?></loc>
        <priority>0.7</priority>
        <changefreq>weekly</changefreq>
    </url>
    <?php endforeach; ?>
    
</urlset>