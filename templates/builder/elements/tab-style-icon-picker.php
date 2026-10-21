<?php if (!defined('ABSPATH')) exit; ?>
<!-- DECOMPOSED MODULE: Font Awesome icon picker popup (Input Icons & Offsets) -->
<style>
#wppoppop-fa-picker-backdrop{position:fixed;inset:0;background:rgba(2,6,23,.6);z-index:1000001;display:none;}
#wppoppop-fa-picker{position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:420px;max-width:94vw;max-height:80vh;background:#0f172a;border:1px solid #334155;border-radius:10px;z-index:1000002;display:none;flex-direction:column;box-shadow:0 20px 50px rgba(0,0,0,.5);}
#wppoppop-fa-picker.open,#wppoppop-fa-picker-backdrop.open{display:flex;}
#wppoppop-fa-picker-backdrop.open{display:block;}
#wppoppop-fa-picker .fa-pk-head{display:flex;align-items:center;gap:8px;padding:10px 12px;border-bottom:1px solid #1e293b;}
#wppoppop-fa-picker .fa-pk-head input{flex:1;background:#090d16;border:1px solid #334155;color:#f1f5f9;border-radius:6px;padding:6px 8px;font-size:12px;}
#wppoppop-fa-picker .fa-pk-grid{display:grid;grid-template-columns:repeat(8,1fr);gap:6px;padding:12px;overflow:auto;}
#wppoppop-fa-picker .fa-pk-item{height:38px;display:flex;align-items:center;justify-content:center;background:#1e293b;border:1px solid #334155;border-radius:6px;color:#e2e8f0;font-size:16px;cursor:pointer;padding:0;}
#wppoppop-fa-picker .fa-pk-item:hover{background:#0284c7;border-color:#38bdf8;color:#fff;}
#wppoppop-fa-picker .fa-pk-item.active{background:#0ea5e9;border-color:#38bdf8;color:#fff;}
#wppoppop-fa-picker .fa-pk-x{background:transparent;border:0;color:#94a3b8;font-size:20px;cursor:pointer;line-height:1;}
</style>
<script>
(function($){
    'use strict';
    if (window.__wppoppopFaPickerBound) return;
    window.__wppoppopFaPickerBound = true;

    var ICONS = ('envelope user phone lock key at id-card address-book building briefcase globe link map-marker-alt location-dot home search eye eye-slash check check-circle circle-check times calendar calendar-alt clock credit-card dollar-sign euro-sign pound-sign tag tags gift shopping-cart shopping-bag heart star bell comment comments paper-plane pen edit file file-alt image camera video music headphones microphone wifi mobile-alt desktop laptop print download upload cloud cog wrench shield-alt user-shield user-circle user-tie user-plus users hashtag percent info-circle question-circle exclamation-circle exclamation-triangle thumbs-up flag bookmark car plane truck coffee leaf fire bolt sun moon graduation-cap hospital medkit paw anchor fingerprint qrcode barcode language signature sliders-h arrow-right arrow-left chevron-down chevron-right plus minus').split(' ');
    var BRANDS = ['facebook','twitter','instagram','linkedin','youtube','whatsapp','telegram','google','apple','paypal','stripe','github'];
    var $target = null;

    function build() {
        if ($('#wppoppop-fa-picker').length) return;
        var items = '';
        ICONS.forEach(function(n){ items += '<button type="button" class="fa-pk-item" data-cls="fas fa-' + n + '" data-name="' + n + '" title="' + n + '"><i class="fas fa-' + n + '"></i></button>'; });
        BRANDS.forEach(function(n){ items += '<button type="button" class="fa-pk-item" data-cls="fab fa-' + n + '" data-name="' + n + '" title="' + n + '"><i class="fab fa-' + n + '"></i></button>'; });
        $('body').append(
            '<div id="wppoppop-fa-picker-backdrop" class="wppoppop-modal"></div>' +
            '<div id="wppoppop-fa-picker" class="wppoppop-modal" role="dialog" aria-modal="true" aria-label="Select icon">' +
              '<div class="fa-pk-head"><input type="search" id="wppoppop-fa-pk-search" placeholder="Search icons..." autocomplete="off"><button type="button" class="fa-pk-x" aria-label="Close">&times;</button></div>' +
              '<div class="fa-pk-grid">' + items + '</div>' +
            '</div>'
        );
    }

    function refreshBtn($input) {
        var $btn = $('.wppoppop-fa-pick-btn[data-target="#' + $input.attr('id') + '"]');
        var v = $.trim($input.val() || '');
        var cls = /^(fas|far|fab|fa)\s/.test(v) ? v : 'fas fa-icons';
        $btn.find('i').attr('class', cls);
    }

    function openPicker($input) {
        build();
        $target = $input;
        var cur = $.trim($input.val() || '');
        $('#wppoppop-fa-picker .fa-pk-item').removeClass('active').filter(function(){ return $(this).attr('data-cls') === cur; }).addClass('active');
        $('#wppoppop-fa-pk-search').val('');
        $('#wppoppop-fa-picker .fa-pk-item').show();
        $('#wppoppop-fa-picker-backdrop, #wppoppop-fa-picker').addClass('open');
        setTimeout(function(){ $('#wppoppop-fa-pk-search').trigger('focus'); }, 20);
    }

    function closePicker() { $('#wppoppop-fa-picker-backdrop, #wppoppop-fa-picker').removeClass('open'); $target = null; }

    $(document).on('click', '.wppoppop-fa-pick-btn', function(e){
        e.preventDefault(); e.stopPropagation();
        openPicker($($(this).attr('data-target')));
    });
    $(document).on('click', '.wppoppop-fa-clear-btn', function(e){
        e.preventDefault();
        var $i = $($(this).attr('data-target'));
        $i.val('').trigger('input').trigger('change'); refreshBtn($i);
    });
    $(document).on('click', '#wppoppop-fa-picker .fa-pk-item', function(e){
        e.preventDefault(); e.stopPropagation();
        if (!$target) return;
        var $i = $target;
        $i.val($(this).attr('data-cls')).trigger('input').trigger('change');
        refreshBtn($i); closePicker();
    });
    $(document).on('input', '#wppoppop-fa-pk-search', function(){
        var q = $.trim($(this).val()).toLowerCase();
        $('#wppoppop-fa-picker .fa-pk-item').each(function(){
            $(this).toggle(!q || $(this).attr('data-name').indexOf(q) !== -1);
        });
    });
    $(document).on('click', '#wppoppop-fa-picker-backdrop, #wppoppop-fa-picker .fa-pk-x', closePicker);
    $(document).on('keydown', function(e){ if (e.key === 'Escape' && $target) closePicker(); });
    $(document).on('input change', '#prop-form-icon-left, #prop-form-icon-right, #prop-icon-class', function(){ refreshBtn($(this)); });
    $(document).on('click', '#wppoppop-inspector-drawer [data-tab], .wppoppop-canvas-item, .wppoppop-layer-item', function(){
        setTimeout(function(){ refreshBtn($('#prop-form-icon-left')); refreshBtn($('#prop-form-icon-right')); refreshBtn($('#prop-icon-class')); }, 80);
    });
    $(function(){ refreshBtn($('#prop-form-icon-left')); refreshBtn($('#prop-form-icon-right')); refreshBtn($('#prop-icon-class')); });
})(jQuery);
</script>
