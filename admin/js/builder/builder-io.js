/**
 * WpPopPop Visual Builder: I/O Persistence Engine
 * Handles Save/Load Routines, Per-Canvas Metadata (Name, Size, Color, Logic) & URL Syncing
 */
(function($) {
    'use strict';

    window.WpPopPopBuilderIO = {
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
            $('#wppoppop-btn-save').on('click', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var origText = $btn.html();

                $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Saving...');

                var core = window.WpPopPopBuilderCore;
                var settings = window.WpPopPopBuilderSettings ? window.WpPopPopBuilderSettings.getSettings() : {};

                var payload = {
                    canvases: (core && core.state && core.state.canvases) ? core.state.canvases : { 1: [], 2: [] },
                    screens: (core && core.state && core.state.canvases) ? core.state.canvases : { 1: [], 2: [] },
                    canvasMeta: (core && core.state && core.state.canvasMeta) ? core.state.canvasMeta : {},
                    canvas_meta: (core && core.state && core.state.canvasMeta) ? core.state.canvasMeta : {},
                    settings: settings,
                    title: $('#wppoppop-builder-title').val() || 'Untitled Campaign'
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
                        1: { name: 'Canvas 1', width: 640, height: 400, bg_mode: 'solid', bg_color: '#ffffff', grad_color1: '#3b82f6', grad_color2: '#1d4ed8', grad_angle: 135, logic_enabled: false },
                        2: { name: 'Canvas 2', width: 640, height: 400, bg_mode: 'solid', bg_color: '#ffffff', grad_color1: '#3b82f6', grad_color2: '#1d4ed8', grad_angle: 135, logic_enabled: false }
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
                                        logic_enabled: false
                                    };
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
