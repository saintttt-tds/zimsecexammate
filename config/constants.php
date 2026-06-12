<?php
/**
 * ZimsecExamMate — Site Constants (InfinityFree)
 */

if (!defined('APP_LOADED')) {
    define('APP_LOADED', true);
}

// ─── Paths ────────────────────────────────────────────
define('ROOT_DIR',       dirname(__DIR__));
define('CORE_DIR',       ROOT_DIR . '/core');
define('TEMPLATES_DIR',  ROOT_DIR . '/templates');
define('DATA_DIR',       ROOT_DIR . '/data');
define('ASSETS_DIR',     ROOT_DIR . '/assets');
define('CONFIG_DIR',     ROOT_DIR . '/config');

// PDF Storage
define('PDFS_DIR',       ASSETS_DIR . '/pdfs');

// Resource type directories
define('PASTPAPERS_DIR',     PDFS_DIR . '/pastpapers');
define('MARKSCHEMES_DIR',    PDFS_DIR . '/markingschemes');
define('TOPICALPAPERS_DIR',  PDFS_DIR . '/topicalpapers');
define('NOTES_DIR',          PDFS_DIR . '/notesandtextbooks');
define('SYLLABI_DIR',        PDFS_DIR . '/syllabi');
define('PROJECTS_DIR',       PDFS_DIR . '/projects');

// Moderation directories
define('APPROVED_DIR',   PDFS_DIR . '/approved');
define('PENDING_DIR',    PDFS_DIR . '/pending');
define('REJECTED_DIR',   PDFS_DIR . '/rejected');

// System storage
define('VOTES_DIR',      PDFS_DIR . '/.system/votes');
define('HASHES_DIR',     PDFS_DIR . '/.system/hashes');
define('METADATA_DIR',   PDFS_DIR . '/.system/metadata');
define('CACHE_DIR',      PDFS_DIR . '/.system/cache');
define('DOWNLOADS_DIR',  PDFS_DIR . '/.system/downloads');
define('LOGS_DIR',       PDFS_DIR . '/.system/logs');

// ─── Site Settings ────────────────────────────────────
define('SITE_NAME',      'ZIMSEC ExamMate');
define('SITE_URL',       'https://zimsecexammate.xo.je');
define('SITE_EMAIL',     'zimsecexammate@gmail.com');
define('SITE_PHONE',     '+263 71 491 2600');

// ─── Verification System ──────────────────────────────
define('VERIFICATION_THRESHOLD',   3);
define('REJECTION_THRESHOLD',      3);
define('PENDING_EXPIRY_DAYS',      7);
define('REJECTED_EXPIRY_DAYS',    30);

// ─── Upload Settings ──────────────────────────────────
define('MAX_UPLOAD_SIZE',  10 * 1024 * 1024);  // 10MB
define('ALLOWED_EXTENSIONS', ['pdf']);
define('ALLOWED_MIME_TYPES', [
    'application/pdf',
    'application/x-pdf',
    'application/octet-stream'
]);

// ─── Rate Limiting ────────────────────────────────────
define('UPLOAD_RATE_LIMIT',      5);
define('VOTE_RATE_LIMIT',       30);
define('SEARCH_RATE_LIMIT',     60);
define('DOWNLOAD_RATE_LIMIT',   30);
define('RATE_LIMIT_WINDOW',   3600);

// ─── Cache Settings ───────────────────────────────────
define('CACHE_TTL',            3600);
define('HOMEPAGE_CACHE_TTL',   1800);
define('SEARCH_CACHE_TTL',      600);

// ─── Education Levels ─────────────────────────────────
define('LEVELS', ['grade7', 'zjc', 'olevel', 'alevel']);
define('LEVEL_DISPLAY', [
    'grade7' => 'Grade 7',
    'zjc'    => 'ZJC',
    'olevel' => 'O Level',
    'alevel' => 'A Level'
]);

// ─── Resource Types ───────────────────────────────────
define('RESOURCE_TYPES', [
    'PP' => 'past_paper',
    'MS' => 'marking_scheme',
    'TP' => 'topical_paper',
    'NT' => 'notes',
    'SY' => 'syllabus',
    'PR' => 'project'
]);

define('RESOURCE_TYPE_DISPLAY', [
    'past_paper'      => 'Past Paper',
    'marking_scheme'  => 'Marking Scheme',
    'topical_paper'   => 'Topical Paper',
    'notes'           => 'Notes & Textbooks',
    'syllabus'        => 'Syllabus',
    'project'         => 'Project'
]);

// Resource type to directory mapping
define('TYPE_DIR_MAP', [
    'PP' => 'pastpapers',
    'MS' => 'markingschemes',
    'TP' => 'topicalpapers',
    'NT' => 'notesandtextbooks',
    'SY' => 'syllabi',
    'PR' => 'projects',
]);

// Valid subtypes per resource type
define('VALID_SUBTYPES', [
    'PP' => ['PAPER1', 'PAPER2', 'PAPER3', 'PAPER4', 'COMBINED'],
    'MS' => ['PAPER1', 'PAPER2', 'PAPER3', 'PAPER4', 'COMBINED'],
    'TP' => [],  // TOPIC1, TOPIC2 etc — dynamic
    'NT' => ['NOTES', 'TEXTBOOK', 'GUIDE', 'SUMMARY', 'REVISION', 'WORKBOOK', 'COMBINED'],
    'SY' => ['COMBINED'],
    'PR' => ['GUIDE', 'SAMPLE', 'PROJECT', 'TEMPLATE', 'REPORT'],
]);

// ─── Chatbot ──────────────────────────────────────────
define('CHATBOT_NAME', 'TalubaMMVII');

// ─── Timezone ─────────────────────────────────────────
date_default_timezone_set('Africa/Harare');