/**
 * WpPopPop Builder Registry Module: registry-label
 * WordPress value tokens + URL link support for the Label element.
 */
(function(window, $) {
    'use strict';

    var TOKENS = [
        { t: "{{record-id}}", l: "Record ID" },
        { t: "{{ip}}", l: "IP Address" },
        { t: "{{user-agent}}", l: "User Agent" },
        { t: "{{date}}", l: "Date" },
        { t: "{{time}}", l: "Time" },
        { t: "{{wp-user-login}}", l: "WP User Login" },
        { t: "{{wp-user-email}}", l: "WP User Email" },
        { t: "{{url}}", l: "Current URL" },
        { t: "{{page-title}}", l: "Current Page Title" }
    ];

    function buildMenu() {
        var $m = $("#wppoppop-label-token-menu");
        if (!$m.length || $m.children().length) return;
        $.each(TOKENS, function(i, o) {
            $m.append($('<div class="wppoppop-token-item" role="menuitem" tabindex="0"></div>')
                .attr("data-token", o.t).text(o.l)
                .css({ padding: "8px 12px", cursor: "pointer", color: "#e2e8f0", fontSize: "12px", borderBottom: "1px solid #1e293b" }));
        });
    }

    function insertAtCursor(token) {
        var ta = document.getElementById("prop-label-content");
        if (!ta) return;
        var v = ta.value || "";
        var s = (typeof ta.selectionStart === "number") ? ta.selectionStart : v.length;
        var e = (typeof ta.selectionEnd === "number") ? ta.selectionEnd : v.length;
        ta.value = v.substring(0, s) + token + v.substring(e);
        var pos = s + token.length;
        try { ta.focus(); ta.setSelectionRange(pos, pos); } catch (err) {}
        // Triggers the shared gp-bind handler: JS model (el.content) + stage re-render
        $(ta).trigger("input").trigger("change");
        if (window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === "function") {
            window.WpPopPopBuilderCore.pushHistory();
        }
    }

    function bind() {
        buildMenu();
        $(document).off("click.wppoppopLabelTok");
        $(document).on("click.wppoppopLabelTok", "#wppoppop-label-token-btn", function(ev) {
            ev.preventDefault(); ev.stopPropagation();
            buildMenu();
            $("#wppoppop-label-token-menu").toggle();
        });
        $(document).on("click.wppoppopLabelTok", "#wppoppop-label-token-menu .wppoppop-token-item", function(ev) {
            ev.preventDefault(); ev.stopPropagation();
            insertAtCursor($(this).attr("data-token"));
            $("#wppoppop-label-token-menu").hide();
        });
        $(document).on("mouseenter.wppoppopLabelTok", "#wppoppop-label-token-menu .wppoppop-token-item", function() {
            $(this).css("background", "#a8325e");
        }).on("mouseleave.wppoppopLabelTok", "#wppoppop-label-token-menu .wppoppop-token-item", function() {
            $(this).css("background", "transparent");
        });
        $(document).on("click.wppoppopLabelTok", function(ev) {
            if (!$(ev.target).closest("#wppoppop-label-token-btn, #wppoppop-label-token-menu").length) {
                $("#wppoppop-label-token-menu").hide();
            }
        });
        // Keep url/targetBlank aliases in the model (data lifecycle)
        $(document).on("input.wppoppopLabelTok change.wppoppopLabelTok", "#prop-label-url, #prop-label-blank", function() {
            var Reg = window.WpPopPopElementsRegistry;
            var el = Reg && typeof Reg.resolveElement === "function" ? Reg.resolveElement() : null;
            if (!el) return;
            if (this.id === "prop-label-url") { el.url = $.trim($(this).val()); el.linkUrl = el.url; }
            else { el.targetBlank = $(this).is(":checked"); el.linkBlank = el.targetBlank; }
        });
    }

    $(function() { bind(); setTimeout(bind, 150); });
})(window, jQuery);
