/**
 * ZimsecExamMate — Main JavaScript
 */
(function () {
    'use strict';

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function () {

        // ─── Disclaimer Modal ──────────────────────
        var modal = document.getElementById('disclaimerModal');
        var modalBtn = document.getElementById('modalOk');

        if (modal && modalBtn) {
            // Continue button
            modalBtn.addEventListener('click', function () {
                modal.style.display = 'none';
                // Set cookie for 1 year
                var d = new Date();
                d.setTime(d.getTime() + (365 * 24 * 60 * 60 * 1000));
                document.cookie = 'zimsec_disclaimer=1;path=/;expires=' + d.toUTCString() + ';SameSite=Lax';
            });

            // Click outside modal to close
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                    var d = new Date();
                    d.setTime(d.getTime() + (365 * 24 * 60 * 60 * 1000));
                    document.cookie = 'zimsec_disclaimer=1;path=/;expires=' + d.toUTCString() + ';SameSite=Lax';
                }
            });

            // Escape key to close
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && modal.style.display !== 'none') {
                    modal.style.display = 'none';
                    var d = new Date();
                    d.setTime(d.getTime() + (365 * 24 * 60 * 60 * 1000));
                    document.cookie = 'zimsec_disclaimer=1;path=/;expires=' + d.toUTCString() + ';SameSite=Lax';
                }
            });
        }

        // ─── Mobile Menu Toggle ────────────────────
        var menuToggle = document.getElementById('mobileMenuToggle');
        var mainNav = document.getElementById('mainNav');

        if (menuToggle && mainNav) {
            menuToggle.addEventListener('click', function () {
                var isOpen = mainNav.classList.contains('open');
                if (isOpen) {
                    mainNav.classList.remove('open');
                    menuToggle.setAttribute('aria-expanded', 'false');
                } else {
                    mainNav.classList.add('open');
                    menuToggle.setAttribute('aria-expanded', 'true');
                }
            });

            // Close menu when clicking outside
            document.addEventListener('click', function (e) {
                if (!menuToggle.contains(e.target) && !mainNav.contains(e.target)) {
                    mainNav.classList.remove('open');
                    menuToggle.setAttribute('aria-expanded', 'false');
                }
            });
        }

        // ─── Dropdown Menus on Mobile ──────────────
        var dropdownBtns = document.querySelectorAll('.nav-dropdown > .nav-link');
        dropdownBtns.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                if (window.innerWidth <= 768) {
                    e.preventDefault();
                    var dropdown = this.closest('.nav-dropdown');
                    var content = dropdown.querySelector('.dropdown-content');
                    if (content) {
                        if (content.style.display === 'block') {
                            content.style.display = 'none';
                        } else {
                            content.style.display = 'block';
                        }
                    }
                }
            });
        });

        // ─── Flash Messages Auto-Dismiss ───────────
        var flashMsgs = document.querySelectorAll('.success-message, .error-message');
        flashMsgs.forEach(function (msg) {
            // Don't auto-dismiss moderation messages
            if (!msg.closest('.moderation-card')) {
                setTimeout(function () {
                    msg.style.transition = 'opacity 0.5s ease';
                    msg.style.opacity = '0';
                    setTimeout(function () {
                        if (msg.parentNode) {
                            msg.parentNode.removeChild(msg);
                        }
                    }, 500);
                }, 5000);
            }
        });

        // ─── Search Input Focus on Ctrl+K ──────────
        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                var searchInput = document.querySelector('input[name="q"], input[name="search"], #searchInput');
                if (searchInput) {
                    searchInput.focus();
                }
            }
        });

        // ─── Preview Buttons ───────────────────────
        var previewBtns = document.querySelectorAll('.preview-btn');
        previewBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var path = this.getAttribute('data-path');
                if (path) {
                    window.open(path, '_blank');
                }
            });
        });

    });

})();