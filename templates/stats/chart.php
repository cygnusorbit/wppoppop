<?php
if (!defined('ABSPATH')) {
    exit;
}

// Compute dynamic chart scaling
$chart_width  = 900;
$chart_height = 240;
$padding      = 40;
$plot_width   = $chart_width - ($padding * 2);
$plot_height  = $chart_height - ($padding * 2);

$max_val = 1;
foreach ($timeline as $day) {
    if ($day['impressions'] > $max_val) $max_val = $day['impressions'];
    if ($day['submissions'] > $max_val) $max_val = $day['submissions'];
}

$point_count = count($timeline);
$step_x = $point_count > 1 ? $plot_width / ($point_count - 1) : $plot_width;

$points_impr = [];
$points_subs = [];

$idx = 0;
foreach ($timeline as $day) {
    $x = $padding + ($idx * $step_x);
    $y_impr = $chart_height - $padding - (($day['impressions'] / $max_val) * $plot_height);
    $y_subs = $chart_height - $padding - (($day['submissions'] / $max_val) * $plot_height);
    $points_impr[] = round($x, 1) . ',' . round($y_impr, 1);
    $points_subs[] = round($x, 1) . ',' . round($y_subs, 1);
    $idx++;
}

$str_impr = implode(' ', $points_impr);
$str_subs = implode(' ', $points_subs);

// Area fill coordinates
$area_impr = $str_impr . ' ' . ($padding + $plot_width) . ',' . ($chart_height - $padding) . ' ' . $padding . ',' . ($chart_height - $padding);
$area_subs = $str_subs . ' ' . ($padding + $plot_width) . ',' . ($chart_height - $padding) . ' ' . $padding . ',' . ($chart_height - $padding);
?>
<div class="wppoppop-chart-box" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);margin-bottom:24px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
        <h3 style="margin:0;font-size:15px;font-weight:700;color:#1e293b;">Performance Timeline</h3>
        <div style="display:flex;gap:16px;font-size:12px;font-weight:600;">
            <div style="display:flex;align-items:center;gap:6px;">
                <span style="display:inline-block;width:12px;height:3px;background:#3b82f6;border-radius:2px;"></span>
                <span style="color:#475569;">Impressions</span>
            </div>
            <div style="display:flex;align-items:center;gap:6px;">
                <span style="display:inline-block;width:12px;height:3px;background:#10b981;border-radius:2px;"></span>
                <span style="color:#475569;">Leads</span>
            </div>
        </div>
    </div>

    <div style="width:100%;overflow-x:auto;">
        <svg viewBox="0 0 <?php echo $chart_width; ?> <?php echo $chart_height; ?>" style="width:100%;height:auto;display:block;">
            <defs>
                <linearGradient id="gradImpr" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.25" />
                    <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.0" />
                </linearGradient>
                <linearGradient id="gradSubs" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#10b981" stop-opacity="0.25" />
                    <stop offset="100%" stop-color="#10b981" stop-opacity="0.0" />
                </linearGradient>
            </defs>

            <!-- Grid Lines -->
            <line x1="<?php echo $padding; ?>" y1="<?php echo $padding; ?>" x2="<?php echo $chart_width - $padding; ?>" y2="<?php echo $padding; ?>" stroke="#f1f5f9" stroke-width="1" />
            <line x1="<?php echo $padding; ?>" y1="<?php echo $padding + ($plot_height / 2); ?>" x2="<?php echo $chart_width - $padding; ?>" y2="<?php echo $padding + ($plot_height / 2); ?>" stroke="#f1f5f9" stroke-width="1" />
            <line x1="<?php echo $padding; ?>" y1="<?php echo $chart_height - $padding; ?>" x2="<?php echo $chart_width - $padding; ?>" y2="<?php echo $chart_height - $padding; ?>" stroke="#cbd5e1" stroke-width="1" />

            <!-- Y-Axis Value Labels -->
            <text x="<?php echo $padding - 10; ?>" y="<?php echo $padding + 4; ?>" font-size="10" fill="#94a3b8" text-anchor="end"><?php echo number_format($max_val); ?></text>
            <text x="<?php echo $padding - 10; ?>" y="<?php echo $chart_height - $padding + 4; ?>" font-size="10" fill="#94a3b8" text-anchor="end">0</text>

            <!-- Area Fills -->
            <polygon points="<?php echo esc_attr($area_impr); ?>" fill="url(#gradImpr)" />
            <polygon points="<?php echo esc_attr($area_subs); ?>" fill="url(#gradSubs)" />

            <!-- Metric Trend Lines -->
            <polyline points="<?php echo esc_attr($str_impr); ?>" fill="none" stroke="#3b82f6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
            <polyline points="<?php echo esc_attr($str_subs); ?>" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />

            <!-- X-Axis Date Nodes -->
            <?php 
            $skip = ceil($point_count / 7);
            $n_idx = 0;
            foreach ($timeline as $day) :
                $x = $padding + ($n_idx * $step_x);
                if ($n_idx % $skip === 0 || $n_idx === $point_count - 1) :
                    ?>
                    <text x="<?php echo round($x, 1); ?>" y="<?php echo $chart_height - $padding + 16; ?>" font-size="10" fill="#64748b" text-anchor="middle">
                        <?php echo esc_html($day['date']); ?>
                    </text>
                    <?php
                endif;
                $n_idx++;
            endforeach; 
            ?>
        </svg>
    </div>
</div>
