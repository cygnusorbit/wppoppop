<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap wppoppop-admin-page">
    <h1>WpPopPop Tools: Import Popup</h1>
    <p>Paste the exported popup JSON schema below to restore or migrate a popup campaign.</p>

    <div class="postbox" style="padding: 20px; max-width: 800px;">
        <textarea id="import-json-area" class="widefat" rows="12" placeholder="Paste exported JSON here..."></textarea>
        <p style="margin-top: 15px;">
            <button type="button" id="btn-run-import" class="button button-primary">Import Popup</button>
        </p>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#btn-run-import').on('click', function() {
        const data = $('#import-json-area').val().trim();
        if (!data) {
            alert('Please paste valid JSON.');
            return;
        }

        $.post(wppoppop_vars.ajax_url, {
            action: 'wppoppop_import_popup',
            nonce: wppoppop_vars.nonce,
            import_data: data
        }, function(res) {
            if (res.success) {
                alert(res.data.message);
                window.location.href = '<?php echo admin_url('admin.php?page=wppoppop'); ?>';
            } else {
                alert(res.data.message);
            }
        });
    });
});
</script>
