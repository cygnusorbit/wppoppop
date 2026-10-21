<?php
if (!defined('ABSPATH')) exit;
?>
<div id="wppoppop-ribbon" class="wppoppop-ribbon-bar">
    <div class="wppoppop-ribbon-scroll" style="display:flex;align-items:center;gap:4px;overflow-x:auto;padding:4px 8px;width:100%;box-sizing:border-box;">
        <span style="font-size:10px;font-weight:800;text-transform:uppercase;color:#f48fb1;padding:0 6px;letter-spacing:0.5px;flex-shrink:0;">Media</span>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="shape" title="1. Rectangle / Square (shape)"><span class="dashicons dashicons-screenoptions"></span><span class="btn-label">1. Rectangle</span></button>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="image" title="2. Image (image)"><span class="dashicons dashicons-format-image"></span><span class="btn-label">Image</span></button>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="video" title="3. Video (video)"><span class="dashicons dashicons-video-alt3"></span><span class="btn-label">Video</span></button>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="label" title="4. Label (label)"><span class="dashicons dashicons-editor-textcolor"></span><span class="btn-label">Label</span></button>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="html" title="5. HTML Content (html)"><span class="dashicons dashicons-editor-code"></span><span class="btn-label">HTML</span></button>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="close" title="6. Close Icon (close)"><span class="dashicons dashicons-no-alt"></span><span class="btn-label">Close</span></button>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="icon" title="7. Font Awesome (icon)"><span class="dashicons dashicons-star-filled"></span><span class="btn-label">Icon</span></button>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="progress" title="8. Progress Bar (progress)"><span class="dashicons dashicons-chart-bar"></span><span class="btn-label">Progress</span></button>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="button" title="9. Button / Link (button)"><span class="dashicons dashicons-admin-links"></span><span class="btn-label">Button</span></button>

        <span style="width:1px;height:28px;background:rgba(255,255,255,0.16);margin:0 6px;flex-shrink:0;"></span>
        <span style="font-size:10px;font-weight:800;text-transform:uppercase;color:#80cbc4;padding:0 6px;letter-spacing:0.5px;flex-shrink:0;">Form</span>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="text" title="10. Text (text)"><span class="dashicons dashicons-edit"></span><span class="btn-label">Text</span></button>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="email" title="11. Email Address (email)"><span class="dashicons dashicons-email"></span><span class="btn-label">Email</span></button>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="number" title="12. Number (number)"><span class="dashicons dashicons-calculator"></span><span class="btn-label">Number</span></button>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="phone" title="13. Phone / Tel (phone)"><span class="dashicons dashicons-phone"></span><span class="btn-label">Phone</span></button>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="textarea" title="14. Textarea (textarea)"><span class="dashicons dashicons-text"></span><span class="btn-label">Textarea</span></button>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="select" title="15. Select (select)"><span class="dashicons dashicons-menu-alt"></span><span class="btn-label">Select</span></button>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="checkboxgroup" title="16. Checkbox (checkboxgroup)"><span class="dashicons dashicons-yes-alt"></span><span class="btn-label">Checkbox</span></button>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="radio" title="17. Radio Button (radio)"><span class="dashicons dashicons-marker"></span><span class="btn-label">Radio</span></button>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="multiselect" title="18. Multiselect (multiselect)"><span class="dashicons dashicons-list-view"></span><span class="btn-label">Multi</span></button>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="imageselect" title="19. Image Select (imageselect)"><span class="dashicons dashicons-images-alt2"></span><span class="btn-label">ImgSel</span></button>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="tile" title="20. Tile (tile)"><span class="dashicons dashicons-grid-view"></span><span class="btn-label">Tile</span></button>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="date" title="21. Date (date)"><span class="dashicons dashicons-calendar-alt"></span><span class="btn-label">Date</span></button>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="time" title="22. Time (time)"><span class="dashicons dashicons-clock"></span><span class="btn-label">Time</span></button>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="upload" title="23. Upload / File (upload)"><span class="dashicons dashicons-upload"></span><span class="btn-label">Upload</span></button>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="password" title="24. Password (password)"><span class="dashicons dashicons-lock"></span><span class="btn-label">Password</span></button>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="rating" title="25. Rating (rating)"><span class="dashicons dashicons-star-half"></span><span class="btn-label">Rating</span></button>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="checkbox" title="Agreement Checkbox (checkbox)"><span class="dashicons dashicons-privacy"></span><span class="btn-label">Agreement</span></button>
        <button type="button" class="wppoppop-ribbon-btn wppoppop-ribbon-tool wppoppop-draggable-tool" data-type="submit" title="26. Button Submit (submit)"><span class="dashicons dashicons-yes"></span><span class="btn-label">Submit</span></button>
    </div>
</div>
