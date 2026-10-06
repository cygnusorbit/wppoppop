<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-library-header" style="margin-bottom:24px;padding-top:10px;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-bottom:18px;">
        <div>
            <h1 class="wp-heading-inline" style="margin:0;font-size:24px;font-weight:700;color:#1e293b;">Popups & Templates Library</h1>
            <p style="margin:4px 0 0;color:#64748b;font-size:13px;">Browse pre-configured, high-converting templates and import them directly into the visual builder.</p>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            <input type="text" id="wppoppop-lib-search" placeholder="Search templates by keyword..." style="width:260px;padding:7px 12px;font-size:13px;border-radius:6px;border:1px solid #cbd5e1;">
        </div>
    </div>

    <!-- Category Filter Pills -->
    <div class="wppoppop-lib-categories" style="display:flex;gap:8px;flex-wrap:wrap;">
        <button type="button" class="button wppoppop-cat-btn active" data-cat="all" style="background:#2563eb;color:#fff;border-color:#1d4ed8;font-weight:600;border-radius:20px;padding:4px 14px;">All Templates</button>
        <button type="button" class="button wppoppop-cat-btn" data-cat="lead-gen" style="border-radius:20px;padding:4px 14px;">Lead Generation</button>
        <button type="button" class="button wppoppop-cat-btn" data-cat="ecommerce" style="border-radius:20px;padding:4px 14px;">E-Commerce & Coupons</button>
        <button type="button" class="button wppoppop-cat-btn" data-cat="gamified" style="border-radius:20px;padding:4px 14px;">Gamified & Interactive</button>
        <button type="button" class="button wppoppop-cat-btn" data-cat="feedback" style="border-radius:20px;padding:4px 14px;">Surveys & Feedback</button>
        <button type="button" class="button wppoppop-cat-btn" data-cat="announcement" style="border-radius:20px;padding:4px 14px;">Announcements</button>
    </div>
</div>
