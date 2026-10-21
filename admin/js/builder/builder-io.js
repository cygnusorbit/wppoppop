/**
 * WpPopPop Visual Builder: I/O Persistence Engine
 * Handles Save/Load Routines, Per-Canvas Metadata (Name, Size, Color, Animation, Logic) & URL Syncing
 */
(function($) {
    'use strict';

    window.WpPopPopBuilderIO = {
        flushActiveElement: function() {
            if (window.WpPopPopBuilderInspector && typeof window.WpPopPopBuilderInspector.flushActiveElement === 'function') {
                try { window.WpPopPopBuilderInspector.flushActiveElement(); } catch(e) {}
            }
            if (typeof this.flushActiveInputs === 'function') {
                try { this.flushActiveInputs(); } catch(e) {}
            }
            if (window.jQuery) {
                window.jQuery('input:focus, textarea:focus, select:focus').trigger('change').trigger('blur');
            }
        },

        exportConfig: function() {
            if (window.WpPopPopElementsRegistry && typeof window.WpPopPopElementsRegistry.flushElementFromDOM === 'function') {
                window.WpPopPopElementsRegistry.flushElementFromDOM();
            }
            if (window.WpPopPopBuilderInspector) {
                if (typeof window.WpPopPopBuilderInspector.flushSanitizedLayerSettings === "function") {
                    window.WpPopPopBuilderInspector.flushSanitizedLayerSettings();
                }
                if (typeof window.WpPopPopBuilderInspector.flushActiveElement === "function") {
                    window.WpPopPopBuilderInspector.flushActiveElement();
                }
            }
            if (typeof this.flushActiveElement === 'function') {
                this.flushActiveElement();
            }
            if (typeof this.exportPopup === 'function') {
                return this.exportPopup();
            }
            var core = window.WpPopPopBuilderCore;
            if (!core || !core.state) return {};
            return {
                id: core.state.id || (window.jQuery ? window.jQuery('#wppoppop-builder-uid').val() : 0) || 0,
                title: core.state.title || (window.jQuery ? window.jQuery('#wppoppop-builder-title').val() : '') || 'Untitled Popup',
                canvases: JSON.parse(JSON.stringify(core.state.canvases || { 1: [] })),
                canvasMeta: JSON.parse(JSON.stringify(core.state.canvasMeta || {})),
                settings: JSON.parse(JSON.stringify(core.state.settings || {}))
            };
        },

        init: function() {
            this.bindSave();
            this.loadInitialData();
        },

        getUid: function() {
            var urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('uid') || $('#wppoppop-builder-uid').val() || (window.wppoppop_vars ? window.wppoppop_vars.current_uid : '') || '';
        },

        syncUid: function(uid) {
            if (!uid) return;
            if ($('#wppoppop-builder-uid').length === 0) {
                $('<input>').attr({ type: 'hidden', id: 'wppoppop-builder-uid', value: uid }).appendTo('body');
            } else {
                $('#wppoppop-builder-uid').val(uid);
            }

            if (window.wppoppop_vars) {
                window.wppoppop_vars.current_uid = uid;
            }

            var currentUrl = new URL(window.location.href);
            if (currentUrl.searchParams.get('uid') !== uid) {
                currentUrl.searchParams.set('uid', uid);
                window.history.replaceState({ path: currentUrl.toString() }, '', currentUrl.toString());
            }
        },

        bindSave: function() {
            var self = this;
            $('#wppoppop-btn-save').off('click.wppoppop_save').on('click.wppoppop_save', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var origText = $btn.html();

                $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Saving...');

                var core = window.WpPopPopBuilderCore;
                try {
                    if (window.WpPopPopElementsRegistry && typeof window.WpPopPopElementsRegistry.flushElementFromDOM === 'function') { window.WpPopPopElementsRegistry.flushElementFromDOM(); }
                    if (window.WpPopPopBuilderInspector && typeof window.WpPopPopBuilderInspector.flushSanitizedLayerSettings === 'function') { window.WpPopPopBuilderInspector.flushSanitizedLayerSettings(); }
                    self.flushActiveElement();
                } catch (flushErr) {}
                var settings = window.WpPopPopBuilderSettings ? window.WpPopPopBuilderSettings.getSettings() : {};

                
        // Pre-export active canvas animation synchronization
        if (core && core.state) {
            var curCanvas = core.state.currentCanvas || 1;
            if (!core.state.canvasMeta) core.state.canvasMeta = {};
            if (!core.state.canvasMeta[curCanvas]) core.state.canvasMeta[curCanvas] = {};
            var curCanvasMeta = core.state.canvasMeta[curCanvas];

            var liveAnimApp = $('#set-canvas-anim-appearance, #set-anim-appearance').val();
            if (liveAnimApp) curCanvasMeta.anim_appearance = liveAnimApp;

            var liveAnimDur = $('#set-canvas-anim-duration, #set-anim-duration').val();
            if (liveAnimDur !== undefined && liveAnimDur !== '' && !isNaN(liveAnimDur)) {
                curCanvasMeta.anim_duration = parseInt(liveAnimDur, 10);
            }

            var liveAnimDel = $('#set-canvas-anim-delay, #set-anim-delay').val();
            if (liveAnimDel !== undefined && liveAnimDel !== '' && !isNaN(liveAnimDel)) {
                curCanvasMeta.anim_delay = parseInt(liveAnimDel, 10);
            }

            var liveAnimDis = $('#set-canvas-anim-disappearance, #set-anim-disappearance').val();
            if (liveAnimDis) curCanvasMeta.anim_disappearance = liveAnimDis;

            /* WPPOPPOP_DEFAULT_CLOSE: pre-save flush */
            var $dcBtn = $('#set-canvas-close-btn');
            if ($dcBtn.length) { curCanvasMeta.close_btn_enabled = $dcBtn.is(':checked'); }
        }


            var payload = {
                    canvases: (core && core.state && core.state.canvases) ? core.state.canvases : { 1: [], 2: [] },
                    screens: (core && core.state && core.state.canvases) ? core.state.canvases : { 1: [], 2: [] },
                    canvasMeta: (core && core.state && core.state.canvasMeta) ? core.state.canvasMeta : {},
                    canvas_meta: (core && core.state && core.state.canvasMeta) ? core.state.canvasMeta : {},
                    settings: settings,
                    title: $('#wppoppop-builder-title').val() || 'Untitled Popup'
                };

                var serialized = JSON.stringify(payload);
                var postData = {
                    action: 'wppoppop_save_popup',
                    nonce: (window.wppoppop_vars ? window.wppoppop_vars.nonce : '') || $('#wppoppop-builder-nonce').val(),
                    uid: self.getUid(),
                    title: payload.title,
                    data: serialized,
                    config: serialized
                };

                var ajaxUrl = (window.wppoppop_vars && window.wppoppop_vars.ajax_url) ? window.wppoppop_vars.ajax_url : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php');

                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: postData,
                    success: function(response) {
                        if (response && response.success && response.data) {
                            var savedUid = response.data.uid;
                            self.syncUid(savedUid);
                            if (response.data.status) {
                                if ($('#wppoppop-builder-status').length === 0) {
                                    $('<input>').attr({ type: 'hidden', id: 'wppoppop-builder-status' }).appendTo('body');
                                }
                                $('#wppoppop-builder-status').val(response.data.status);
                            }
                            try {
                                var wppM = window.WpPopPopBuilderModals;
                                if (wppM && $('#wppoppop-modal-embed').is(':visible')) { wppM.refresh(); }
                            } catch (wppE) {}
                            $btn.html('<span class="dashicons dashicons-yes"></span> Saved!');
                            setTimeout(function() {
                                $btn.prop('disabled', false).html(origText);
                            }, 1500);
                        } else {
                            var msg = (response && response.data && response.data.message) ? response.data.message : 'Save failed.';
                            alert(msg);
                            $btn.prop('disabled', false).html(origText);
                        }
                    },
                    error: function() {
                        alert('Server error while saving popup.');
                        $btn.prop('disabled', false).html(origText);
                    }
                });
            });
        },

        loadInitialData: function() {
            var core = window.WpPopPopBuilderCore;
            var uid = this.getUid();
            if (!uid) {
                if (core) {
                    core.state.canvases = { 1: [], 2: [] };
                    core.state.canvasMeta = {
                        1: { name: 'Canvas 1', width: 640, height: 400, bg_mode: 'solid', bg_color: '#ffffff', grad_color1: '#3b82f6', grad_color2: '#1d4ed8', grad_angle: 135, anim_appearance: 'fadeIn', anim_duration: 1000, anim_delay: 0, anim_disappearance: 'fadeOut', logic_enabled: false },
                        2: { name: 'Canvas 2', width: 640, height: 400, bg_mode: 'solid', bg_color: '#ffffff', grad_color1: '#3b82f6', grad_color2: '#1d4ed8', grad_angle: 135, anim_appearance: 'fadeIn', anim_duration: 1000, anim_delay: 0, anim_disappearance: 'fadeOut', logic_enabled: false }
                    };
                    core.renderCanvasTabs();
                    core.switchCanvas(1);
                    if (window.WpPopPopBuilderSettings) {
                        window.WpPopPopBuilderSettings.renderCanvasBullets();
                    }
                }
                return;
            }

            var self = this;
            var ajaxUrl = (window.wppoppop_vars && window.wppoppop_vars.ajax_url) ? window.wppoppop_vars.ajax_url : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php');
            var nonce = (window.wppoppop_vars && window.wppoppop_vars.nonce) ? window.wppoppop_vars.nonce : $('#wppoppop-builder-nonce').val();

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'wppoppop_load_popup',
                    nonce: nonce,
                    uid: uid
                },
                success: function(response) {
                    if (response && response.success && response.data) {
                        var row = response.data;
                        var rawData = row.data || row.config || '{}';
                        var cfg = {};

                        if (typeof rawData === 'string') {
                            try { cfg = JSON.parse(rawData); } catch(e) { cfg = {}; }
                        } else if (typeof rawData === 'object' && rawData !== null) {
                            cfg = rawData;
                        }

                        if (core) {
                            var sourceCanvases = cfg.canvases || cfg.screens;
                            if (sourceCanvases && typeof sourceCanvases === 'object') {
                                core.state.canvases = sourceCanvases;
                            } else {
                                core.state.canvases = { 1: [], 2: [] };
                            }

                            core.state.canvasMeta = cfg.canvasMeta || cfg.canvas_meta || {};

                            if (!core.state.canvases[1]) core.state.canvases[1] = [];
                            if (!core.state.canvases[2]) core.state.canvases[2] = [];

                            Object.keys(core.state.canvases).forEach(function(k) {
                                if (!core.state.canvasMeta[k]) {
                                    core.state.canvasMeta[k] = {
                                        name: 'Canvas ' + k,
                                        width: (cfg.settings && cfg.settings.box && cfg.settings.box.width) || 640,
                                        height: (cfg.settings && cfg.settings.box && cfg.settings.box.height) || 400,
                                        bg_mode: (cfg.settings && cfg.settings.box && cfg.settings.box.bg_mode) || 'solid',
                                        bg_color: (cfg.settings && cfg.settings.box && cfg.settings.box.bg_color) || '#ffffff',
                                        grad_color1: (cfg.settings && cfg.settings.box && cfg.settings.box.grad_color1) || '#3b82f6',
                                        grad_color2: (cfg.settings && cfg.settings.box && cfg.settings.box.grad_color2) || '#1d4ed8',
                                        grad_angle: (cfg.settings && cfg.settings.box && cfg.settings.box.grad_angle) || 135,
                                        anim_appearance: 'fadeIn',
                                        anim_duration: 1000,
                                        anim_delay: 0,
                                        anim_disappearance: 'fadeOut',
                                        logic_enabled: false
                                    };
                                } else {
                                    if (!core.state.canvasMeta[k].anim_appearance || core.state.canvasMeta[k].anim_appearance === 'fade') core.state.canvasMeta[k].anim_appearance = 'fadeIn';
                                    if (core.state.canvasMeta[k].anim_duration === undefined) core.state.canvasMeta[k].anim_duration = 1000;
                                    if (core.state.canvasMeta[k].anim_delay === undefined) core.state.canvasMeta[k].anim_delay = 0;
                                    if (!core.state.canvasMeta[k].anim_disappearance || core.state.canvasMeta[k].anim_disappearance === 'fade') core.state.canvasMeta[k].anim_disappearance = 'fadeOut';
                                }
                            });

                            core.renderCanvasTabs();
                        }

                        if (window.WpPopPopBuilderSettings && cfg.settings) {
                            window.WpPopPopBuilderSettings.setSettings(cfg.settings);
                        }

                        var title = row.title || cfg.title || '';
                        if (title) {
                            $('#wppoppop-builder-title').val(title);
                        }

                        if (core) {
                            core.switchCanvas(1);
                        }

                        if (window.WpPopPopBuilderSettings) {
                            window.WpPopPopBuilderSettings.renderCanvasBullets();
                        }
                    }
                }
            });
        }
    };
})(jQuery);

// Delegated click handler for Save Popup button
jQuery(document).off('click.wppoppop_global_save_click', '#wppoppop-btn-save, .wppoppop-btn-save')
    .on('click.wppoppop_global_save_click', '#wppoppop-btn-save, .wppoppop-btn-save', function(e) {
        e.preventDefault();
        if (window.WpPopPopBuilderIO && typeof window.WpPopPopBuilderIO.savePopup === 'function') {
            window.WpPopPopBuilderIO.savePopup();
        }
    });

/* === WPPOPPOP_PHASE5_IO_PRESAVE_FLUSH_START === */
(function(window, $) {
    'use strict';

    function flushActiveDecomposedElement() {
        var $drawer = $('#wppoppop-inspector-drawer');
        if (!$drawer.length || (!$drawer.hasClass('is-open') && !$drawer.hasClass('open'))) {
            return;
        }
        // Trigger change on any focused input inside the inspector drawer so two-way binders run
        var $focused = $drawer.find('input:focus, textarea:focus, select:focus');
        if ($focused.length) {
            $focused.trigger('change');
        }
        // Explicitly flush visible .wppoppop-gp-bind controls into the active element
        var insp = window.WpPopPopBuilderInspector;
        var activeId = (insp && insp.activeElementId) ? String(insp.activeElementId) : (
            window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state ? String(window.WpPopPopBuilderCore.state.activeId || '') : ''
        );
        var cleanId = activeId.replace(/^wppoppop-el-/, '').replace(/^el-/, '');
        if (!cleanId || !window.WpPopPopBuilderCanvas || typeof window.WpPopPopBuilderCanvas.getActiveElements !== 'function') {
            return;
        }
        var els = window.WpPopPopBuilderCanvas.getActiveElements() || [];
        var el = Array.isArray(els) ? els.find(function(item) {
            return item && (String(item.id) === cleanId || String(item.id) === activeId);
        }) : null;
        if (!el) return;

        $drawer.find('.element-panel:visible .wppoppop-gp-bind').each(function() {
            var $inp = $(this);
            var prop = $inp.data('prop');
            if (!prop) return;
            el[prop] = $inp.is(':checkbox') ? $inp.is(':checked') : $inp.val();
        });
    }

    $(function() {
        var io = window.WpPopPopBuilderIO;
        if (io && !io._phase5FlushWrapped && typeof io.exportConfig === 'function') {
            io._phase5FlushWrapped = true;
            var origExport = io.exportConfig;
            io.exportConfig = function() {
                try { flushActiveDecomposedElement(); } catch (e) {}
                return origExport.apply(this, arguments);
            };
        }
    });
})(window, jQuery);
/* === WPPOPPOP_PHASE5_IO_PRESAVE_FLUSH_END === */

