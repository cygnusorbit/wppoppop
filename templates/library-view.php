<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap wppoppop-library-wrap">
    <div class="library-header-bar">
        <h1>Popups Library</h1>
        <p class="description">Browse curated high-converting templates. Click any design to preview or import directly into your campaigns.</p>
    </div>

    <!-- Category Filter Navigation -->
    <div class="library-filter-bar">
        <button type="button" class="lib-filter-btn active" data-cat="all">All Templates</button>
        <button type="button" class="lib-filter-btn" data-cat="leadgen">Lead Generation</button>
        <button type="button" class="lib-filter-btn" data-cat="ecommerce">E-Commerce & Coupons</button>
        <button type="button" class="lib-filter-btn" data-cat="gamified">Gamified & Interactive</button>
        <button type="button" class="lib-filter-btn" data-cat="feedback">Surveys & Feedback</button>
        <button type="button" class="lib-filter-btn" data-cat="notification">Notification Bars</button>
    </div>

    <!-- Templates Grid -->
    <div class="library-grid" id="wppoppop-lib-grid">
        <!-- Template Card 1 -->
        <div class="lib-card" data-cat="leadgen" data-template="minimal_newsletter">
            <div class="lib-card-preview" style="background: linear-gradient(135deg, #1e293b, #0f172a);">
                <div class="mock-popup" style="background: #ffffff; color: #111;">
                    <div class="mock-title">Join Newsletter</div>
                    <div class="mock-input"></div>
                    <div class="mock-btn" style="background:#2271b1;">Subscribe</div>
                </div>
            </div>
            <div class="lib-card-footer">
                <div class="lib-card-info">
                    <h3>Minimalist Newsletter</h3>
                    <span class="lib-badge">Lead Generation</span>
                </div>
                <div class="lib-card-actions">
                    <button type="button" class="button button-primary btn-import-tpl" data-tpl="minimal_newsletter">Import & Edit</button>
                </div>
            </div>
        </div>

        <!-- Template Card 2 -->
        <div class="lib-card" data-cat="ecommerce" data-template="discount_coupon">
            <div class="lib-card-preview" style="background: linear-gradient(135deg, #be185d, #831843);">
                <div class="mock-popup" style="background: #ffffff; color: #111;">
                    <div class="mock-title" style="color:#d63638;">SAVE 20% NOW</div>
                    <div class="mock-coupon">SAVE20</div>
                    <div class="mock-btn" style="background:#00a32a;">Claim Coupon</div>
                </div>
            </div>
            <div class="lib-card-footer">
                <div class="lib-card-info">
                    <h3>Flash Sale Coupon</h3>
                    <span class="lib-badge">E-Commerce</span>
                </div>
                <div class="lib-card-actions">
                    <button type="button" class="button button-primary btn-import-tpl" data-tpl="discount_coupon">Import & Edit</button>
                </div>
            </div>
        </div>

        <!-- Template Card 3 -->
        <div class="lib-card" data-cat="gamified" data-template="spin_wheel">
            <div class="lib-card-preview" style="background: linear-gradient(135deg, #4338ca, #312e81);">
                <div class="mock-wheel-circle"></div>
            </div>
            <div class="lib-card-footer">
                <div class="lib-card-info">
                    <h3>Lucky Prize Wheel</h3>
                    <span class="lib-badge">Gamified</span>
                </div>
                <div class="lib-card-actions">
                    <button type="button" class="button button-primary btn-import-tpl" data-tpl="spin_wheel">Import & Edit</button>
                </div>
            </div>
        </div>

        <!-- Template Card 4 -->
        <div class="lib-card" data-cat="feedback" data-template="star_review">
            <div class="lib-card-preview" style="background: linear-gradient(135deg, #065f46, #064e3b);">
                <div class="mock-popup" style="background: #ffffff; color: #111;">
                    <div class="mock-title">Rate Experience</div>
                    <div style="color:#f59e0b; font-size:18px; text-align:center;">★★★★★</div>
                    <div class="mock-btn" style="background:#2271b1; margin-top:8px;">Send Feedback</div>
                </div>
            </div>
            <div class="lib-card-footer">
                <div class="lib-card-info">
                    <h3>5-Star Customer Review</h3>
                    <span class="lib-badge">Feedback</span>
                </div>
                <div class="lib-card-actions">
                    <button type="button" class="button button-primary btn-import-tpl" data-tpl="star_review">Import & Edit</button>
                </div>
            </div>
        </div>

        <!-- Template Card 5 -->
        <div class="lib-card" data-cat="ecommerce" data-template="calculator_quote">
            <div class="lib-card-preview" style="background: linear-gradient(135deg, #0369a1, #0c4a6e);">
                <div class="mock-popup" style="background: #ffffff; color: #111;">
                    <div class="mock-title">Instant Price Quote</div>
                    <div style="font-size:11px; text-align:center; color:#059669; font-weight:700;">Formula: Qty x $25</div>
                    <div class="mock-btn" style="background:#0284c7; margin-top:8px;">Order Now</div>
                </div>
            </div>
            <div class="lib-card-footer">
                <div class="lib-card-info">
                    <h3>Dynamic Pricing Estimator</h3>
                    <span class="lib-badge">Calculators</span>
                </div>
                <div class="lib-card-actions">
                    <button type="button" class="button button-primary btn-import-tpl" data-tpl="calculator_quote">Import & Edit</button>
                </div>
            </div>
        </div>

        <!-- Template Card 6 -->
        <div class="lib-card" data-cat="notification" data-template="cookie_banner">
            <div class="lib-card-preview" style="background: linear-gradient(135deg, #374151, #1f2937);">
                <div class="mock-popup" style="background: #ffffff; color: #111; height: 75px;">
                    <div style="font-size:10px; color:#475569;">We respect your privacy on our platform.</div>
                    <div class="mock-btn" style="background:#059669; margin-top:6px; height:20px; line-height:20px;">Accept All</div>
                </div>
            </div>
            <div class="lib-card-footer">
                <div class="lib-card-info">
                    <h3>GDPR Consent Bar</h3>
                    <span class="lib-badge">Notifications</span>
                </div>
                <div class="lib-card-actions">
                    <button type="button" class="button button-primary btn-import-tpl" data-tpl="cookie_banner">Import & Edit</button>
                </div>
            </div>
        </div>
    </div>
</div>
