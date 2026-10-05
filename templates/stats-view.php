<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$table_items = $wpdb->prefix . 'wppoppop_items';
$table_subs  = $wpdb->prefix . 'wppoppop_submissions';

$total_views = (int)$wpdb->get_var("SELECT SUM(impressions) FROM {$table_items}");
$total_leads = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$table_subs}");
$total_conf  = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$table_subs} WHERE status = 'confirmed'");
$global_rate = ($total_views > 0) ? round(($total_leads / $total_views) * 100, 2) : 0;

$top_campaigns = $wpdb->get_results("SELECT title, impressions, submissions FROM {$table_items} ORDER BY submissions DESC LIMIT 5");
?>
<div class="wrap wppoppop-stats-page">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h1 style="margin:0;">Visual Performance & Trends</h1>
        <div class="stats-period-badge">Real-Time Data</div>
    </div>

    <!-- KPI Summary Row -->
    <div style="display: flex; gap: 20px; margin-bottom: 25px;">
        <div class="postbox" style="flex: 1; padding: 20px; text-align: center; border-radius: 6px;">
            <div style="font-size: 13px; color: #646970; text-transform: uppercase; font-weight: 600;">Total Impressions</div>
            <div style="font-size: 32px; font-weight: 700; color: #2271b1; margin-top: 5px;"><?php echo number_format($total_views); ?></div>
        </div>
        <div class="postbox" style="flex: 1; padding: 20px; text-align: center; border-radius: 6px;">
            <div style="font-size: 13px; color: #646970; text-transform: uppercase; font-weight: 600;">Form Submissions</div>
            <div style="font-size: 32px; font-weight: 700; color: #00a32a; margin-top: 5px;"><?php echo number_format($total_leads); ?></div>
        </div>
        <div class="postbox" style="flex: 1; padding: 20px; text-align: center; border-radius: 6px;">
            <div style="font-size: 13px; color: #646970; text-transform: uppercase; font-weight: 600;">Confirmed Leads</div>
            <div style="font-size: 32px; font-weight: 700; color: #0284c7; margin-top: 5px;"><?php echo number_format($total_conf); ?></div>
        </div>
        <div class="postbox" style="flex: 1; padding: 20px; text-align: center; border-radius: 6px;">
            <div style="font-size: 13px; color: #646970; text-transform: uppercase; font-weight: 600;">Global Conversion Rate</div>
            <div style="font-size: 32px; font-weight: 700; color: #d63638; margin-top: 5px;"><?php echo $global_rate; ?>%</div>
        </div>
    </div>

    <!-- Performance Chart Mockup (Pure Scalable SVG) -->
    <div class="postbox" style="padding: 24px; border-radius: 6px; margin-bottom: 25px;">
        <h2 style="margin: 0 0 15px 0; font-size: 16px;">Conversion Trend Graph (Last 7 Days)</h2>
        <div style="width: 100%; height: 200px; background: #fafafa; border: 1px solid #e2e8f0; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
            <svg viewBox="0 0 600 160" style="width: 100%; height: 100%; padding: 10px;">
                <line x1="40" y1="130" x2="560" y2="130" stroke="#cbd5e1" stroke-width="1" />
                <line x1="40" y1="80" x2="560" y2="80" stroke="#f1f5f9" stroke-width="1" />
                <line x1="40" y1="30" x2="560" y2="30" stroke="#f1f5f9" stroke-width="1" />
                
                <!-- Polyline Impressions -->
                <polyline fill="none" stroke="#2271b1" stroke-width="3" points="40,110 120,90 200,95 280,60 360,70 440,40 520,50" />
                <!-- Polyline Submissions -->
                <polyline fill="none" stroke="#00a32a" stroke-width="3" points="40,125 120,120 200,115 280,100 360,105 440,90 520,95" />
            </svg>
        </div>
        <div style="display:flex; justify-content:center; gap:20px; margin-top:12px; font-size:12px; font-weight:600;">
            <span style="color:#2271b1;">&mdash; Impressions</span>
            <span style="color:#00a32a;">&mdash; Submissions</span>
        </div>
    </div>

    <!-- Top Performing Popups -->
    <div class="postbox" style="padding: 24px; border-radius: 6px;">
        <h2 style="margin: 0 0 15px 0; font-size: 16px;">Top Performing Campaigns</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Campaign Title</th>
                    <th style="width: 140px;">Impressions</th>
                    <th style="width: 140px;">Submissions</th>
                    <th style="width: 140px;">Conversion %</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($top_campaigns)) : ?>
                    <tr><td colspan="4">No campaigns recorded yet.</td></tr>
                <?php else : ?>
                    <?php foreach ($top_campaigns as $camp) : 
                        $rate = ($camp->impressions > 0) ? round(($camp->submissions / $camp->impressions) * 100, 1) : 0;
                        ?>
                        <tr>
                            <td><strong><?php echo esc_html($camp->title); ?></strong></td>
                            <td><?php echo number_format($camp->impressions); ?></td>
                            <td><?php echo number_format($camp->submissions); ?></td>
                            <td><strong style="color: #00a32a;"><?php echo $rate; ?>%</strong></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
