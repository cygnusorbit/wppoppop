<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wppoppop-ratings-section" style="margin-bottom:24px;">
    <h3 style="font-size:16px;font-weight:700;color:#1e293b;margin:0 0 14px 0;">Star Ratings & Customer Satisfaction</h3>
    
    <?php if ($total_rating_count === 0) : ?>
        <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:24px;text-align:center;color:#64748b;">
            No 5-star rating element data submitted yet.
        </div>
    <?php else : ?>
        <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,0.05);display:flex;flex-wrap:wrap;gap:24px;align-items:center;">
            <!-- Overall Score Card -->
            <div style="text-align:center;min-width:180px;border-right:1px solid #e2e8f0;padding-right:24px;">
                <div style="font-size:42px;font-weight:800;color:#1e293b;line-height:1;"><?php echo esc_html($avg_rating); ?></div>
                <div style="color:#f59e0b;font-size:20px;margin:6px 0;">
                    &#9733;&#9733;&#9733;&#9733;&#9733;
                </div>
                <span style="font-size:12px;color:#64748b;">Based on <?php echo number_format($total_rating_count); ?> ratings</span>
            </div>

            <!-- 5-Star Histogram Distribution -->
            <div style="flex:1;display:flex;flex-direction:column;gap:8px;min-width:280px;">
                <?php for ($star = 5; $star >= 1; $star--) : 
                    $star_votes = $rating_histogram[$star] ?? 0;
                    $star_pct   = $total_rating_count > 0 ? round(($star_votes / $total_rating_count) * 100, 1) : 0;
                    ?>
                    <div style="display:flex;align-items:center;gap:10px;font-size:12px;">
                        <span style="width:50px;font-weight:600;color:#334155;"><?php echo $star; ?> Stars</span>
                        <div style="flex:1;height:10px;background:#f1f5f9;border-radius:5px;overflow:hidden;">
                            <div style="width:<?php echo esc_attr($star_pct); ?>%;height:100%;background:#f59e0b;border-radius:5px;transition:width 0.4s ease;"></div>
                        </div>
                        <span style="width:70px;text-align:right;color:#64748b;"><?php echo number_format($star_votes); ?> (<?php echo esc_html($star_pct); ?>%)</span>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
