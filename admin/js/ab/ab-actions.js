(function(window, $) {
    'use strict';
    window.WpPopPopAB = window.WpPopPopAB || {};

    var Actions = {
        init: function() {
            this.bindSubmit();
            this.bindCopyShortcode();
            this.bindDelete();
        },

        getVars: function() {
            return window.wppoppop_vars || {
                ajax_url: ajaxurl || '',
                nonce: ''
            };
        },

        bindSubmit: function() {
            var self = this;
            $('#wppoppop-create-ab-submit').on('click', function() {
                var title = $('#wppoppop-ab-title-input').val().trim();
                var selectedUids = [];
                $('.wppoppop-ab-popup-checkbox:checked').each(function() {
                    selectedUids.push($(this).val());
                });

                if (!title) {
                    alert('Please enter a campaign title.');
                    return;
                }

                if (selectedUids.length < 2) {
                    alert('Please select at least 2 popups for split-testing.');
                    return;
                }

                var $btn = $(this);
                $btn.prop('disabled', true).text('Launching...');

                var vars = self.getVars();

                $.post(vars.ajax_url, {
                    action: 'wppoppop_save_campaign',
                    nonce: vars.nonce,
                    title: title,
                    popup_uids: selectedUids
                }).done(function(res) {
                    if (res.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + (res.data ? res.data.message : 'Unable to create campaign.'));
                        $btn.prop('disabled', false).text('Launch Campaign');
                    }
                }).fail(function() {
                    alert('Network error while saving campaign.');
                    $btn.prop('disabled', false).text('Launch Campaign');
                });
            });
        },

        bindCopyShortcode: function() {
            $('.wppoppop-copy-ab-sc-btn').on('click', function() {
                var sc = $(this).data('shortcode');
                var $btn = $(this);
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(sc);
                }
                var orig = $btn.text();
                $btn.text('Copied!');
                setTimeout(function() { $btn.text(orig); }, 1500);
            });
        },

        bindDelete: function() {
            var self = this;
            $('.wppoppop-del-ab-btn').on('click', function() {
                if (!confirm('Are you sure you want to delete this A/B testing campaign?')) {
                    return;
                }

                var uid = $(this).data('uid');
                var $row = $(this).closest('tr');
                var vars = self.getVars();

                $.post(vars.ajax_url, {
                    action: 'wppoppop_delete_campaign',
                    nonce: vars.nonce,
                    uid: uid
                }).done(function(res) {
                    if (res.success) {
                        $row.fadeOut(200, function() { $(this).remove(); });
                    } else {
                        alert('Deletion Error: ' + (res.data ? res.data.message : 'Unable to delete campaign.'));
                    }
                }).fail(function() {
                    alert('Network error while deleting campaign.');
                });
            });
        }
    };

    window.WpPopPopAB.Actions = Actions;
})(window, jQuery);
