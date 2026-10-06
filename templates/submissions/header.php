<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-subs-header" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;padding-top:10px;">
    <div>
        <h1 class="wp-heading-inline" style="margin:0;font-size:24px;font-weight:700;color:#1e293b;">Submissions & Captured Leads</h1>
        <p style="margin:4px 0 0;color:#64748b;font-size:13px;">Review user submissions, audit signatures, and export collected leads.</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        <input type="text" id="wppoppop-subs-search" placeholder="Search by email or campaign..." style="width:250px;padding:6px 12px;font-size:13px;border-radius:4px;border:1px solid #cbd5e1;">
        <button type="button" class="button button-primary" id="wppoppop-btn-export-csv" style="display:inline-flex;align-items:center;gap:6px;">
            <span class="dashicons dashicons-download" style="font-size:16px;width:16px;height:16px;"></span> Export to CSV
        </button>
    </div>
</div>
<div id="wppoppop-subs-notice" style="display:none;" class="notice is-dismissible"></div>
