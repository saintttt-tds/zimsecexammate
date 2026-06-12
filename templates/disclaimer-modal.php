<?php
$dismissed = isset($_COOKIE['zimsec_disclaimer']) && $_COOKIE['zimsec_disclaimer'] === '1';
if ($dismissed) return;
?>

<div id="disclaimerModal" style="position:fixed;inset:0;background:rgba(0,0,0,0.6);display:flex;align-items:center;justify-content:center;z-index:99999;">
    <div style="background:#fff;padding:2rem;border-radius:12px;max-width:450px;width:90%;text-align:center;box-shadow:0 10px 40px rgba(0,0,0,0.3);">
        
        <span style="background:#fff3e0;color:#e65100;display:inline-block;padding:0.25rem 0.8rem;border-radius:20px;font-size:0.7rem;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:0.8rem;">
            <i class="fas fa-info-circle"></i> Disclaimer!
        </span>
        
        <h2 style="color:#1a1a1a;font-size:1.1rem;margin-bottom:0.7rem;">Independent Platform</h2>
        
        <p style="color:#555;font-size:0.85rem;line-height:1.5;margin-bottom:0.5rem;">
            <strong>ZIMSEC ExamMate is not affiliated with the exam board.</strong>
        </p>
        <p style="color:#666;font-size:0.82rem;line-height:1.5;margin-bottom:0.5rem;">
            We are an independent, community-driven platform providing free educational resources. 
            We are not endorsed by or connected to ZIMSEC.
        </p>
        <p style="color:#888;font-size:0.8rem;margin-bottom:1rem;">
            Official site: <a href="https://www5.zimsec.co.zw" target="_blank" rel="noopener" style="color:#1e3c72;">www.zimsec.co.zw</a>
        </p>
        <p style="color:#666;font-size:0.85rem;margin-bottom:1rem;">All the best 🎓</p>

        <div style="display:flex;align-items:center;justify-content:center;gap:8px;padding-top:1rem;border-top:1px solid #eee;margin-bottom:1rem;font-size:0.8rem;color:#888;">
            <span style="width:24px;height:24px;background:#1e3c72;color:#fff;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:0.75rem;">Z</span>
            ZIMSEC ExamMate
        </div>
        
        <button onclick="document.getElementById('disclaimerModal').style.display='none';var d=new Date();d.setTime(d.getTime()+(365*24*60*60*1000));document.cookie='zimsec_disclaimer=1;path=/;expires='+d.toUTCString()+';SameSite=Lax';" 
                style="background:#1e3c72;color:#fff;border:none;padding:0.7rem 2rem;border-radius:8px;font-size:0.95rem;cursor:pointer;font-weight:600;width:100%;">
            Continue <i class="fas fa-arrow-right"></i>
        </button>
    </div>
</div>