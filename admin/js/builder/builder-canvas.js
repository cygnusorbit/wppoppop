/**
 * WpPopPop Visual Builder: Canvas Stage Engine
 * Direct-Pointer Corner Drag Resizing, 26 Elements, SVG Vector Shapes & Adaptive Element Padding
 */
(function($) {
    'use strict';

    window.WpPopPopBuilderCanvas = {
        parseVideoEmbed: function(url, autoplay, mute, controls) {
            if (!url) return '';
            var ap = autoplay ? '1' : '0';
            var mu = mute ? '1' : '0';
            var ct = (controls !== false && controls !== '0' && controls !== 0) ? '1' : '0';
            var ytMatch = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/);
            if (ytMatch) {
                return 'https://www.youtube.com/embed/' + ytMatch[1] + '?autoplay=' + ap + '&mute=' + mu + '&controls=' + ct;
            }
            var vimMatch = url.match(/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)/);
            if (vimMatch) {
                return 'https://player.vimeo.com/video/' + vimMatch[3] + '?autoplay=' + ap + '&muted=' + mu;
            }
            return url;
        },

        isPreview: false,

        setZoom: function(level) {
        if (window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.setZoom === "function") { return window.WpPopPopBuilderCore.setZoom(level); }
        var $box = $('#wppoppop-canvas-box');
        var $workspace = $('.wppoppop-builder-workspace, #wppoppop-canvas-workspace');
        if (!$box.length) return;

        if (level === 'fit') {
            if (!$workspace.length) {
                level = 1;
            } else {
                var availW = $workspace.width() - 80;
                var availH = $workspace.height() - 80;
                var boxW = $box.outerWidth() || 640;
                var boxH = $box.outerHeight() || 400;
                var scaleX = availW / boxW;
                var scaleY = availH / boxH;
                level = Math.min(scaleX, scaleY, 1);
                if (level < 0.25) level = 0.25;
            }
        } else {
            level = parseFloat(level) || 1;
        }

        $box.css({
            '-webkit-transform': 'scale(' + level + ')',
            'transform': 'scale(' + level + ')',
            'transform-origin': 'center center',
            'transition': 'transform 0.15s ease'
        });
        this.currentZoom = level;
    },

    bindZoomControls: function() {
        var self = this;
        $(document).off('change.wppoppop_zoom', '#wppoppop-zoom-select').on('change.wppoppop_zoom', '#wppoppop-zoom-select', function() {
            var val = $(this).val();
            self.setZoom(val);
        });
    },

    smartSnapGuides: function(ui, el, $box) {
        if (!ui || !ui.position || !$box || !$box.length) return;
        var boxW = $box.outerWidth() || 640;
        var boxH = $box.outerHeight() || 400;
        var $node = $('#wppoppop-el-' + el.id);
        var elW = $node.outerWidth() || el.width || 100;
        var elH = $node.outerHeight() || el.height || 40;

        var threshold = 6;
        var posX = ui.position.left;
        var posY = ui.position.top;
        var centerX = posX + elW / 2;
        var centerY = posY + elH / 2;
        var rightX = posX + elW;
        var bottomY = posY + elH;

        var curCanvas = (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state) ? window.WpPopPopBuilderCore.state.currentCanvas || 1 : 1;
        var elements = (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state && window.WpPopPopBuilderCore.state.canvases) ? window.WpPopPopBuilderCore.state.canvases[curCanvas] : [];

        var snapTargetsX = [0, Math.round(boxW / 2), boxW];
        var snapTargetsY = [0, Math.round(boxH / 2), boxH];

        elements.forEach(function(other) {
            if (other.id === el.id) return;
            var oL = parseInt(other.left, 10) || 0;
            var oT = parseInt(other.top, 10) || 0;
            var oW = parseInt(other.width, 10) || 100;
            var oH = parseInt(other.height, 10) || 40;
            snapTargetsX.push(oL, oL + Math.round(oW / 2), oL + oW);
            snapTargetsY.push(oT, oT + Math.round(oH / 2), oT + oH);
        });

        var snappedX = null;
        var snappedY = null;

        for (var i = 0; i < snapTargetsX.length; i++) {
            var tx = snapTargetsX[i];
            if (Math.abs(posX - tx) <= threshold) {
                ui.position.left = tx;
                snappedX = tx;
                break;
            } else if (Math.abs(centerX - tx) <= threshold) {
                ui.position.left = tx - Math.round(elW / 2);
                snappedX = tx;
                break;
            } else if (Math.abs(rightX - tx) <= threshold) {
                ui.position.left = tx - elW;
                snappedX = tx;
                break;
            }
        }

        for (var j = 0; j < snapTargetsY.length; j++) {
            var ty = snapTargetsY[j];
            if (Math.abs(posY - ty) <= threshold) {
                ui.position.top = ty;
                snappedY = ty;
                break;
            } else if (Math.abs(centerY - ty) <= threshold) {
                ui.position.top = ty - Math.round(elH / 2);
                snappedY = ty;
                break;
            } else if (Math.abs(bottomY - ty) <= threshold) {
                ui.position.top = ty - elH;
                snappedY = ty;
                break;
            }
        }

        var $guideV = $('#wppoppop-guide-v');
        var $guideH = $('#wppoppop-guide-h');
        if (snappedX !== null) {
            $guideV.css({ left: snappedX + 'px', display: 'block' });
        } else {
            $guideV.hide();
        }
        if (snappedY !== null) {
            $guideH.css({ top: snappedY + 'px', display: 'block' });
        } else {
            $guideH.hide();
        }
    },

    alignSelectedElements: function(type) {
        var self = this;
        var activeId = self.activeId || (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state ? window.WpPopPopBuilderCore.state.activeId : null);
        var curCanvas = (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state) ? window.WpPopPopBuilderCore.state.currentCanvas || 1 : 1;
        var elements = (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state && window.WpPopPopBuilderCore.state.canvases) ? window.WpPopPopBuilderCore.state.canvases[curCanvas] : [];
        if (!elements || !elements.length) return;

        var $box = $('#wppoppop-canvas-box');
        var boxW = $box.outerWidth() || 640;
        var boxH = $box.outerHeight() || 400;

        if (type === 'distribute_h' || type === 'distribute_v') {
            if (elements.length < 3) return;
            var sorted = elements.slice();
            if (type === 'distribute_h') {
                sorted.sort(function(a, b) { return (parseInt(a.left, 10) || 0) - (parseInt(b.left, 10) || 0); });
                var minL = parseInt(sorted[0].left, 10) || 0;
                var maxL = parseInt(sorted[sorted.length - 1].left, 10) || 0;
                var stepL = (maxL - minL) / (sorted.length - 1);
                sorted.forEach(function(el, idx) {
                    if (idx > 0 && idx < sorted.length - 1) {
                        el.left = Math.round(minL + idx * stepL);
                        $('#wppoppop-el-' + el.id).css('left', el.left + 'px');
                    }
                });
            } else {
                sorted.sort(function(a, b) { return (parseInt(a.top, 10) || 0) - (parseInt(b.top, 10) || 0); });
                var minT = parseInt(sorted[0].top, 10) || 0;
                var maxT = parseInt(sorted[sorted.length - 1].top, 10) || 0;
                var stepT = (maxT - minT) / (sorted.length - 1);
                sorted.forEach(function(el, idx) {
                    if (idx > 0 && idx < sorted.length - 1) {
                        el.top = Math.round(minT + idx * stepT);
                        $('#wppoppop-el-' + el.id).css('top', el.top + 'px');
                    }
                });
            }
            if (window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') {
                window.WpPopPopBuilderCore.pushHistory();
            }
            return;
        }

        if (!activeId) return;
        var activeEl = null;
        for (var eIdx = 0; eIdx < elements.length; eIdx++) {
            if (elements[eIdx].id === activeId) {
                activeEl = elements[eIdx];
                break;
            }
        }
        if (!activeEl) return;

        var $node = $('#wppoppop-el-' + activeId);
        var elW = $node.outerWidth() || activeEl.width || 100;
        var elH = $node.outerHeight() || activeEl.height || 40;

        switch (type) {
            case 'left':
                activeEl.left = 0;
                break;
            case 'center':
                activeEl.left = Math.round((boxW - elW) / 2);
                break;
            case 'right':
                activeEl.left = Math.max(0, boxW - elW);
                break;
            case 'top':
                activeEl.top = 0;
                break;
            case 'middle':
                activeEl.top = Math.round((boxH - elH) / 2);
                break;
            case 'bottom':
                activeEl.top = Math.max(0, boxH - elH);
                break;
        }

        $node.css({ left: activeEl.left + 'px', top: activeEl.top + 'px' });
        $('#prop-left').val(activeEl.left);
        $('#prop-top').val(activeEl.top);

        if (window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') {
            window.WpPopPopBuilderCore.pushHistory();
        }
    },

    bindKeyboardNudge: function() {
        var self = this;
        $(document).off('keydown.wppoppop_nudge').on('keydown.wppoppop_nudge', function(e) {
            var tag = (e.target.tagName || '').toLowerCase();
            if (tag === 'input' || tag === 'textarea' || tag === 'select' || e.target.isContentEditable) {
                return;
            }

            var arrowKeys = ['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'];
            if (arrowKeys.indexOf(e.key) === -1) return;

            var activeId = self.activeId || (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state ? window.WpPopPopBuilderCore.state.activeId : null);
            if (!activeId) return;

            var curCanvas = (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state) ? window.WpPopPopBuilderCore.state.currentCanvas || 1 : 1;
            var elements = (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state && window.WpPopPopBuilderCore.state.canvases) ? window.WpPopPopBuilderCore.state.canvases[curCanvas] : [];
            var activeEl = null;
            for (var idx = 0; idx < elements.length; idx++) {
                if (elements[idx].id === activeId) {
                    activeEl = elements[idx];
                    break;
                }
            }
            if (!activeEl) return;

            e.preventDefault();
            var step = e.shiftKey ? 10 : 1;
            var $node = $('#wppoppop-el-' + activeId);
            var left = parseInt(activeEl.left, 10) || 0;
            var top = parseInt(activeEl.top, 10) || 0;

            if (e.key === 'ArrowUp') top -= step;
            if (e.key === 'ArrowDown') top += step;
            if (e.key === 'ArrowLeft') left -= step;
            if (e.key === 'ArrowRight') left += step;

            activeEl.left = left;
            activeEl.top = top;
            $node.css({ left: left + 'px', top: top + 'px' });

            $('#prop-left').val(left);
            $('#prop-top').val(top);

            clearTimeout(self._nudgeHistTimer);
            self._nudgeHistTimer = setTimeout(function() {
                if (window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') {
                    window.WpPopPopBuilderCore.pushHistory();
                }
            }, 300);
        });
    },

    bindAlignmentToolbar: function() {
        var self = this;
        $(document).off('click.wppoppop_align', '.wppoppop-align-btn').on('click.wppoppop_align', '.wppoppop-align-btn', function(e) {
            e.preventDefault();
            var alignType = $(this).data('align');
            self.alignSelectedElements(alignType);
        });
    },

    playAnimation: function(id, effect, duration, delay) {
        if (!effect || effect === 'none') return;
        if (window.WpPopPopBuilderAnimation && typeof window.WpPopPopBuilderAnimation.playElement === 'function') {
            return window.WpPopPopBuilderAnimation.playElement(id, effect, duration, delay);
        }
        duration = parseInt(duration, 10) || 1000;
        delay = parseInt(delay, 10) || 0;

        var $node = $('#wppoppop-el-' + id + ', #el-' + id + ', [data-id="' + id + '"]');
        if (!$node.length) return;

        var cleanEffect = effect.replace(/^animate__/, '').replace(/^wppoppop-anim-/, '').replace(/^anim-/, '');

        // 1. Remove existing animation classes
        $node.removeClass(function(index, className) {
            return (className.match(/\b(anim-\S+|wppoppop-anim-\S+|animate__\S+)/g) || []).join(' ');
        });

        // 2. Clear inline animation styles
        $node.css({
            '-webkit-animation-duration': '',
            'animation-duration': '',
            '-webkit-animation-delay': '',
            'animation-delay': '',
            '-webkit-animation-fill-mode': '',
            'animation-fill-mode': ''
        });

        // 3. Force browser layout reflow
        if ($node[0]) {
            void $node[0].offsetWidth;
        }

        // 4. Re-apply hardware-accelerated timing styles
        $node.css({
            '-webkit-animation-duration': duration + 'ms',
            'animation-duration': duration + 'ms',
            '-webkit-animation-delay': delay + 'ms',
            'animation-delay': delay + 'ms',
            '-webkit-animation-fill-mode': 'both',
            'animation-fill-mode': 'both'
        });

        // 5. Add dual-contract keyframe classes
        $node.addClass('animate__animated animate__' + cleanEffect);

        // 6. Reset styles after playback to keep element interactive
        clearTimeout($node.data('wppoppop_anim_cleanup_timer'));
        var timer = setTimeout(function() {
            $node.removeClass(function(index, className) {
                return (className.match(/\b(anim-\S+|wppoppop-anim-\S+|animate__\S+)/g) || []).join(' ');
            });
            $node.css({
                '-webkit-animation-duration': '',
                'animation-duration': '',
                '-webkit-animation-delay': '',
                'animation-delay': '',
                '-webkit-animation-fill-mode': '',
                'animation-fill-mode': ''
            });
        }, duration + delay + 100);
        $node.data('wppoppop_anim_cleanup_timer', timer);
    },

    alignActiveElement: function(alignment) {
        var self = this;
        var selId = self.selectedId || (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state ? window.WpPopPopBuilderCore.state.activeId : null);
        if (!selId) return;
        self.selectedId = selId;

        var curCanvas = (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state) ? (window.WpPopPopBuilderCore.state.currentCanvas || 1) : 1;
        var elements = window.WpPopPopBuilderCore.state.canvases[curCanvas] || [];
        var el = elements.find(function(item) { return item.id === self.selectedId; });
        if (!el) return;

        var $box = $('#wppoppop-canvas-box');
        var $node = $('#' + self.selectedId);
        if (!$node.length) return;

        var boxW = $box.outerWidth() || 640;
        var boxH = $box.outerHeight() || 400;
        var elW = $node.outerWidth() || parseInt(el.width, 10) || 120;
        var elH = $node.outerHeight() || parseInt(el.height, 10) || 40;

        var newX = parseInt(el.x !== undefined ? el.x : el.left, 10) || 0;
        var newY = parseInt(el.y !== undefined ? el.y : el.top, 10) || 0;

        switch (alignment) {
            case 'left':
                newX = 0;
                break;
            case 'center-h':
                newX = Math.round((boxW - elW) / 2);
                break;
            case 'right':
                newX = Math.max(0, boxW - elW);
                break;
            case 'top':
                newY = 0;
                break;
            case 'middle-v':
                newY = Math.round((boxH - elH) / 2);
                break;
            case 'bottom':
                newY = Math.max(0, boxH - elH);
                break;
        }

        // Commit coordinates
        if (el.x !== undefined) el.x = newX;
        if (el.left !== undefined) el.left = newX;
        if (el.y !== undefined) el.y = newY;
        if (el.top !== undefined) el.top = newY;

        $node.css({ left: newX + 'px', top: newY + 'px' });

        // Sync Inspector inputs if open
        $('#prop-x, #prop-left').val(newX);
        $('#prop-y, #prop-top').val(newY);

        if (window.WpPopPopBuilderCore && typeof window.WpPopPopBuilderCore.pushHistory === 'function') {
            window.WpPopPopBuilderCore.pushHistory('Align Element ' + alignment);
        }
    },

    smartSnapGuides: function(ui, $node) {
        var $box = $('#wppoppop-canvas-box');
        var $guideV = $('#wppoppop-guide-v');
        var $guideH = $('#wppoppop-guide-h');
        if (!$box.length) return;

        var boxW = $box.outerWidth() || 640;
        var boxH = $box.outerHeight() || 400;
        var elW = $node.outerWidth() || 100;
        var elH = $node.outerHeight() || 40;

        var threshold = 7;
        var centerX = Math.round(boxW / 2);
        var centerY = Math.round(boxH / 2);

        var curCenterX = ui.position.left + Math.round(elW / 2);
        var curCenterY = ui.position.top + Math.round(elH / 2);

        // Vertical Center Snap (X-axis)
        if (Math.abs(curCenterX - centerX) <= threshold) {
            ui.position.left = centerX - Math.round(elW / 2);
            if ($guideV.length) {
                $guideV.css({ left: centerX + 'px', display: 'block' });
            }
        } else {
            if ($guideV.length) $guideV.hide();
        }

        // Horizontal Center Snap (Y-axis)
        if (Math.abs(curCenterY - centerY) <= threshold) {
            ui.position.top = centerY - Math.round(elH / 2);
            if ($guideH.length) {
                $guideH.css({ top: centerY + 'px', display: 'block' });
            }
        } else {
            if ($guideH.length) $guideH.hide();
        }
    },

    bindKeyboardNudge: function() {
        var self = this;
        $(document).off('keydown.wppoppop_nudge').on('keydown.wppoppop_nudge', function(e) {
            if (!self.selectedId) return;
            if ($(e.target).is('input, textarea, select')) return;

            var key = e.which;
            if (key < 37 || key > 40) return;

            e.preventDefault();
            var step = e.shiftKey ? 10 : 1;

            var curCanvas = (window.WpPopPopBuilderCore && window.WpPopPopBuilderCore.state) ? (window.WpPopPopBuilderCore.state.currentCanvas || 1) : 1;
            var elements = window.WpPopPopBuilderCore.state.canvases[curCanvas] || [];
            var el = elements.find(function(item) { return item.id === self.selectedId; });
            if (!el) return;

            var $node = $('#' + self.selectedId);
            var x = parseInt(el.x !== undefined ? el.x : el.left, 10) || 0;
            var y = parseInt(el.y !== undefined ? el.y : el.top, 10) || 0;

            if (key === 37) x -= step; // Left
            if (key === 38) y -= step; // Up
            if (key === 39) x += step; // Right
            if (key === 40) y += step; // Down

            if (el.x !== undefined) el.x = x;
            if (el.left !== undefined) el.left = x;
            if (el.y !== undefined) el.y = y;
            if (el.top !== undefined) el.top = y;

            $node.css({ left: x + 'px', top: y + 'px' });
            $('#prop-x, #prop-left').val(x);
            $('#prop-y, #prop-top').val(y);
        });
    },

    bindAlignmentToolbar: function() {
        var self = this;
        $('#wppoppop-alignment-toolbar').off('click.wppoppop_align', '.wppoppop-align-btn').on('click.wppoppop_align', '.wppoppop-align-btn', function(e) {
            e.preventDefault();
            var alignMode = $(this).data('align');
            if (alignMode) {
                self.alignActiveElement(alignMode);
            }
        });
    },

    init: function() {
        this.bindKeyboardNudge();
        this.bindAlignmentToolbar();
            this.bindCanvasCornerResize();
            this.bindRibbonTools();
            this.bindCanvasSelection();
            this.bindSnapControls();
            this.bindPreviewModeControls();
        },

        bindPreviewModeControls: function() {
            var self = this;

            $('#wppoppop-btn-preview').off('click.wppoppop_preview_toggle').on('click.wppoppop_preview_toggle', function(e) {
                e.preventDefault();
                if (self.isPreview) {
                    self.exitPreviewMode();
                } else {
                    self.enterPreviewMode();
                }
            });

            $(document).on('click', '#wppoppop-preview-exit-btn', function(e) {
                e.preventDefault();
                self.exitPreviewMode();
            });

            $(document).on('click', '#wppoppop-preview-replay-btn', function(e) {
                e.preventDefault();
                self.playCanvasEntranceAnimation();
            });
        },

        enterPreviewMode: function() {
            this.isPreview = true;
            $('.wppoppop-builder-workspace').addClass('is-preview-mode');
            $('#wppoppop-btn-preview').addClass('active').html('<span class="dashicons dashicons-no" style="font-size:16px;width:16px;height:16px;margin-top:2px;"></span> Exit Preview');

            if (window.WpPopPopBuilderInspector) window.WpPopPopBuilderInspector.close();
            if (window.WpPopPopBuilderSettings) window.WpPopPopBuilderSettings.closeDrawer();

            if ($('#wppoppop-workspace-preview-bar').length === 0) {
                var $bar = $('<div id="wppoppop-workspace-preview-bar" style="position:fixed;top:60px;left:50%;transform:translateX(-50%);background:#0f172a;border:1px solid #334155;border-radius:24px;padding:6px 14px;display:flex;align-items:center;gap:10px;z-index:999999;box-shadow:0 10px 25px rgba(0,0,0,0.5);color:#f8fafc;font-size:12px;font-weight:700;">' +
                    '<span style="color:#10b981;display:flex;align-items:center;gap:4px;">● LIVE PREVIEW</span>' +
                    '<button type="button" id="wppoppop-preview-replay-btn" style="background:#1e293b;border:1px solid #475569;color:#e2e8f0;padding:3px 10px;border-radius:12px;font-size:11px;cursor:pointer;">Replay Animation</button>' +
                    '<button type="button" id="wppoppop-preview-exit-btn" style="background:#dc2626;border:none;color:#ffffff;padding:3px 10px;border-radius:12px;font-size:11px;cursor:pointer;">Exit Preview</button>' +
                    '</div>');
                $('body').append($bar);
            } else {
                $('#wppoppop-workspace-preview-bar').show();
            }

            this.playCanvasEntranceAnimation();
            this.playAllElementsEntranceAnimation();
            this.initActivePreviewWidgets();
        },

        exitPreviewMode: function() {
            this.isPreview = false;
            $('.wppoppop-builder-workspace').removeClass('is-preview-mode');
            $('#wppoppop-btn-preview').removeClass('active').html('<span class="dashicons dashicons-visibility" style="font-size:16px;width:16px;height:16px;margin-top:2px;"></span> Preview');
            $('#wppoppop-workspace-preview-bar').hide();

            var $box = $('#wppoppop-canvas-box');
            $box.removeClass('anim-fade anim-slideDown anim-slideUp anim-slideLeft anim-slideRight anim-zoomIn anim-bounceIn');

            if (window.WpPopPopBuilderSettings) {
                window.WpPopPopBuilderSettings.applyActiveCanvasBackground();
            }
            this.renderCanvas();
        },

        playCanvasEntranceAnimation: function() {
            if (window.WpPopPopBuilderAnimation) { return window.WpPopPopBuilderAnimation.playCanvas(); }
            var core = window.WpPopPopBuilderCore;
            if (!core) return;
            var cur = core.state.currentCanvas || 1;
            var meta = core.state.canvasMeta[cur] || {};
            var $box = $('#wppoppop-canvas-box');

            var anim = meta.anim_appearance || 'fade';
            var dur = (meta.anim_duration !== undefined ? meta.anim_duration : 1000) / 1000;
            var del = (meta.anim_delay !== undefined ? meta.anim_delay : 0) / 1000;

            $box.removeClass('anim-fade anim-slideDown anim-slideUp anim-slideLeft anim-slideRight anim-zoomIn anim-bounceIn');

            if (anim !== 'none') {
                $box.css({
                    animationDuration: dur + 's',
                    animationDelay: del + 's',
                    animationFillMode: 'both'
                }).addClass('anim-' + anim);
            }
        },

        playElementAnimation: function(id, anim) {
            if (window.WpPopPopBuilderAnimation) { return window.WpPopPopBuilderAnimation.playElement(id, anim); }
            if (!anim || anim === 'none') return;
            var $el = $('#el-' + id);
            $el.removeClass('anim-fade anim-bounce anim-tada anim-rubberBand anim-slideDown anim-slideUp anim-zoomIn');
            setTimeout(function() {
                $el.addClass('anim-' + anim);
            }, 15);
        },

        initActivePreviewWidgets: function() {
            var self = this;
            var core = window.WpPopPopBuilderCore;

            $('#wppoppop-canvas-elements-root').off('click.previewStep').on('click.previewStep', '.wppoppop-next-canvas-btn, .wppoppop-next-screen-btn, .wppoppop-next-step, [data-type="step_btn"]', function(e) {
                if (!self.isPreview) return;
                e.preventDefault();
                e.stopPropagation();

                var target = parseInt($(this).data('goto-canvas') || $(this).data('goto-screen') || $(this).data('goto'), 10) || 2;
                if (core) {
                    core.switchCanvas(target);
                    self.playCanvasEntranceAnimation();
                }
            });
        },

        bindCanvasCornerResize: function() {
            var self = this;
            var $box = $('#wppoppop-canvas-box');
            var $handle = $('#wppoppop-canvas-corner-handle');

            if (!$handle.length || !$box.length) return;
            $handle.off('mousedown.canvasCorner pointerdown.canvasCorner').on('mousedown.canvasCorner pointerdown.canvasCorner', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var startX = e.clientX;
                var startY = e.clientY;
                var startW = $box.outerWidth();
                var startH = $box.outerHeight();
                var core = window.WpPopPopBuilderCore;
                var cur = core ? (core.state.currentCanvas || 1) : 1;

                $('body').addClass('wppoppop-resizing-canvas');
                self.updateSizeBadge(startW, startH);

                $(document).on('mousemove.canvasCorner pointermove.canvasCorner', function(moveEvent) {
                    moveEvent.preventDefault();
                    var dx = moveEvent.clientX - startX;
                    var dy = moveEvent.clientY - startY;

                    var newW = Math.max(200, Math.min(1600, Math.round(startW + dx)));
                    var newH = Math.max(150, Math.min(1200, Math.round(startH + dy)));

                    $box.css({ width: newW + 'px', height: newH + 'px' });
                    self.updateSizeBadge(newW, newH);

                    if (core && core.state.canvasMeta && core.state.canvasMeta[cur]) {
                        core.state.canvasMeta[cur].width = newW;
                        core.state.canvasMeta[cur].height = newH;
                    }

                    $('#set-canvas-width, #quick-box-width').val(newW);
                    $('#set-canvas-height, #quick-box-height').val(newH);
                });

                $(document).one('mouseup.canvasCorner pointerup.canvasCorner', function() {
                    $(document).off('mousemove.canvasCorner pointermove.canvasCorner');
                    $('body').removeClass('wppoppop-resizing-canvas');
                    $('#wppoppop-canvas-size-badge').fadeOut(200);

                    if (core) {
                        core.pushHistory();
                    }
                });
            });
        },

        updateSizeBadge: function(w, h) {
            $('#wppoppop-canvas-size-badge').text(w + ' × ' + h + ' px').show();
        },

        getActiveElements: function() {
            var core = window.WpPopPopBuilderCore;
            if (!core || !core.state.canvases) return [];
            var cur = core.state.currentCanvas || 1;
            if (!core.state.canvases[cur]) {
                core.state.canvases[cur] = [];
            }
            return core.state.canvases[cur];
        },

        renderCanvas: function() {
            var self = this;
            var $root = $('#wppoppop-canvas-elements-root');
            $root.empty();

            var elements = this.getActiveElements();
            elements.forEach(function(el) {
                var $node = self.buildElementNode(el);
                $root.append($node);
                if (!self.isPreview) {
                    self.attachInteractions($node, el);
                }
            });
        },

        buildElementNode: function(el) {

            var pad = el.padding !== undefined ? parseInt(el.padding, 10) : 0;
            var isFormOrBtn = ['shape', 'step_btn', 'submit', 'pay', 'link_btn', 'textfield', 'email', 'number', 'select', 'date'].indexOf(el.type) !== -1;

            var $div = $('<div>')
                .addClass('wppoppop-canvas-item')
                .attr('id', 'el-' + el.id)
                .attr('data-id', el.id)
                .css({
                    top: ((el.top === undefined || el.top === null || el.top === '' || isNaN(parseFloat(el.top))) ? 20 : parseFloat(el.top)) + 'px',
                    left: ((el.left === undefined || el.left === null || el.left === '' || isNaN(parseFloat(el.left))) ? 20 : parseFloat(el.left)) + 'px',
                    width: (el.width || 180) + 'px',
                    height: (el.height || 42) + 'px',
                    zIndex: el.zIndex || 10,
                    borderRadius: (el.borderRadius || 4) + 'px',
                    opacity: el.opacity || 1,
                    display: el.hidden ? 'none' : 'block',
                    boxSizing: 'border-box'
                });

            // Container elements receive wrapper padding; form/button controls maintain 0 outer padding
            if (!isFormOrBtn && pad > 0) {
                $div.css('padding', pad + 'px');
            } else {
                $div.css('padding', '0px');
            }

            if (el.fontFamily && el.fontFamily !== 'inherit') $div.css('fontFamily', el.fontFamily);
            if (el.fontSize) $div.css('fontSize', el.fontSize + 'px');
            if (el.fontWeight) $div.css('fontWeight', el.fontWeight);
            if (el.lineHeight) $div.css('lineHeight', el.lineHeight);
            if (el.letterSpacing) $div.css('letterSpacing', el.letterSpacing + 'px');
            if (el.textAlign) $div.css('textAlign', el.textAlign);
            if (el.color) $div.css('color', el.color);
            if (el.bgColor && !el.transparentFill) $div.css('backgroundColor', el.bgColor);

            $div.html(this.getInnerMarkup(el));

            if (!this.isPreview && window.WpPopPopBuilderCore.state.activeId === el.id) {
                $div.addClass('wppoppop-selected');
            }

            return $div;
        },

        renderShapeSvg: function(preset, fill, stroke, strokeWidth, rotate) {
            preset = preset || 'square';
            fill = fill || '#3b82f6';
            stroke = stroke || '#1d4ed8';
            var sw = (strokeWidth !== undefined) ? parseInt(strokeWidth, 10) : 2;
            var rot = parseInt(rotate, 10) || 0;

            var sAttr = (sw > 0 && stroke !== 'transparent') ? 'stroke="' + stroke + '" stroke-width="' + sw + '" vector-effect="non-scaling-stroke"' : 'stroke="none"';
            var fAttr = (fill !== 'transparent') ? 'fill="' + fill + '"' : 'fill="transparent"';
            var transformAttr = rot ? ' transform="rotate(' + rot + ' 50 50)"' : '';

            var geom = '';
            switch(preset) {
                case 'square':
                    geom = '<rect x="0" y="0" width="100" height="100" ' + fAttr + ' ' + sAttr + transformAttr + ' />';
                    break;
                case 'rounded_rect':
                case 'rounded_rectangle':
                    geom = '<rect x="0" y="0" width="100" height="100" rx="15" ry="15" ' + fAttr + ' ' + sAttr + transformAttr + ' />';
                    break;
                case 'star':
                    geom = '<polygon points="50,5 64,36 98,36 70,57 81,91 50,70 19,91 30,57 2,36 36,36" ' + fAttr + ' ' + sAttr + transformAttr + ' stroke-linejoin="round" />';
                    break;
                case 'triangle':
                    geom = '<polygon points="50,8 92,90 8,90" ' + fAttr + ' ' + sAttr + transformAttr + ' stroke-linejoin="round" />';
                    break;
                case 'diamond':
                    geom = '<polygon points="50,5 92,50 50,95 8,50" ' + fAttr + ' ' + sAttr + transformAttr + ' stroke-linejoin="round" />';
                    break;
                case 'heart':
                    geom = '<path d="M50 82 C50 82 12 58 12 33 C12 18 24 10 36 10 C44 10 48 15 50 18 C52 15 56 10 64 10 C76 10 88 18 88 33 C88 58 50 82 50 82 Z" ' + fAttr + ' ' + sAttr + transformAttr + ' stroke-linejoin="round" />';
                    break;
                case 'hexagon':
                    geom = '<polygon points="50,5 89,27 89,73 50,95 11,73 11,27" ' + fAttr + ' ' + sAttr + transformAttr + ' stroke-linejoin="round" />';
                    break;
                case 'octagon':
                    geom = '<polygon points="30,5 70,5 95,30 95,70 70,95 30,95 5,70 5,30" ' + fAttr + ' ' + sAttr + transformAttr + ' stroke-linejoin="round" />';
                    break;
                case 'shield':
                    geom = '<path d="M50 5 L88 18 V50 C88 72 70 88 50 95 C30 88 12 72 12 50 V18 Z" ' + fAttr + ' ' + sAttr + transformAttr + ' stroke-linejoin="round" />';
                    break;
                case 'cross':
                    geom = '<polygon points="35,5 65,5 65,35 95,35 95,65 65,65 65,95 35,95 35,65 5,65 5,35 35,35" ' + fAttr + ' ' + sAttr + transformAttr + ' stroke-linejoin="round" />';
                    break;
                case 'circle':
                default:
                    geom = '<circle cx="50" cy="50" r="45" ' + fAttr + ' ' + sAttr + transformAttr + ' />';
                    break;
            }

            return '<svg viewBox="0 0 100 100" preserveAspectRatio="none" style="width:100%;height:100%;display:block;overflow:visible;">' + geom + '</svg>';
        },

        getInnerMarkup: function(el) {
            var label = el.content || el.name || 'Element';
            var align = el.textAlign || 'left';
            var jAlign = (align === 'center' ? 'center' : (align === 'right' ? 'flex-end' : 'flex-start'));
            var padVal = (el.padding !== undefined && el.padding !== null) ? parseInt(el.padding, 10) : 0;

            switch (el.type) {
                case 'shape':
                    return this.renderShapeSvg(el.shapePreset, el.shapeFill, el.shapeStroke, el.shapeStrokeWidth, el.shapeRotate);
                case 'title':
            case 'heading':
                var tTag = el.titleTag || el.htmlTag || 'h2';
                var tText = el.text !== undefined ? el.text : (el.content || 'Headline Title');
                return '<' + tTag + ' class="wppoppop-stage-title" style="margin:0;padding:0;width:100%;height:100%;font-family:inherit;font-size:inherit;font-weight:inherit;color:inherit;text-align:inherit;line-height:inherit;letter-spacing:inherit;display:flex;align-items:center;word-break:break-word;">' + tText + '</' + tTag + '>';
            case 'paragraph':
                case 'text':
                    return '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:' + jAlign + ';text-align:' + align + ';font-size:inherit;color:inherit;line-height:inherit;">' + (el.content || 'Headline or Text') + '</div>';
                case 'textfield':
                    return '<input type="text" placeholder="' + (el.content || 'Enter text...') + '" style="width:100%;height:100%;padding:0 ' + (padVal > 0 ? padVal : 10) + 'px;border:1px solid #cbd5e1;border-radius:inherit;text-align:' + align + ';box-sizing:border-box;">';
                case 'image':
                    var imgSrc = el.imageUrl || el.src || el.url || (el.content && String(el.content).indexOf('http') === 0 ? el.content : '');
                    var imgFit = el.objectFit || el.imageFit || 'cover';
                    var imgAlt = el.imageAlt || el.alt || 'Popup Image';
                    if (imgSrc) {
                        return '<img src="' + imgSrc + '" alt="' + imgAlt.replace(/"/g, '&quot;') + '" style="width:100%;height:100%;object-fit:' + imgFit + ';display:block;border-radius:inherit;pointer-events:none;" />';
                    }
                    return '<div style="width:100%;height:100%;background:#e2e8f0;border:1px dashed #94a3b8;display:flex;align-items:center;justify-content:center;color:#475569;font-size:12px;font-weight:600;border-radius:inherit;"><span class="dashicons dashicons-format-image" style="font-size:20px;width:20px;height:20px;margin-right:6px;"></span>Image</div>';
                case 'video':
                var vUrl = el.videoUrl || el.url || '';
                var embedSrc = self.parseVideoEmbed ? self.parseVideoEmbed(vUrl, el.videoAutoplay, el.videoMute, el.videoControls) : vUrl;
                if (embedSrc) {
                    return '<iframe src="' + embedSrc + '" style="width:100%;height:100%;border:0;pointer-events:none;border-radius:inherit;" allow="autoplay; encrypted-media"></iframe>';
                }
                return '<div class="wppoppop-video-placeholder" style="width:100%;height:100%;background:#1e293b;color:#94a3b8;display:flex;flex-direction:column;align-items:center;justify-content:center;font-size:12px;border-radius:inherit;"><span class="dashicons dashicons-video-alt3" style="font-size:32px;width:32px;height:32px;margin-bottom:8px;"></span><span>Video Embed</span></div>';
                            case 'html':
                    var rawHtml = (el.content !== undefined && el.content !== '') ? el.content : (el.htmlCode || '');
                    if (!rawHtml || !rawHtml.trim()) {
                        return '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#1e293b;border:1px dashed #475569;color:#94a3b8;font-size:11px;font-family:monospace;padding:8px;box-sizing:border-box;text-align:center;">&lt;/&gt; Empty HTML Element (Click to Edit)</div>';
                    }
                    return '<div class="wppoppop-custom-html-stage" style="width:100%;height:100%;overflow:auto;box-sizing:border-box;">' + rawHtml + '</div>';

                case 'button':
                case 'link':
                case 'link_btn':
                    var btLabel = (el.content !== undefined && el.content !== null && String(el.content).replace(/\s+/g, '') !== '') ? el.content : 'Click Here';
                    return '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;text-align:center;font:inherit;color:inherit;letter-spacing:inherit;pointer-events:none;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;padding:0 12px;box-sizing:border-box;">' + String(btLabel).replace(/</g, '&lt;') + '</div>';

                case 'divider':
                    var dStyle = el.dividerStyle || 'solid';
                    var dWeight = parseInt(el.dividerWeight || 2, 10);
                    if (dStyle === 'double' && dWeight < 3) dWeight = 4;
                    var dColor = el.dividerColor || el.color || '#94a3b8';
                    var dPct = parseInt(el.dividerWidthPct || 100, 10);
                    var dAlign = el.dividerAlign || 'center';
                    var marginStyle = 'margin: 0 auto;';
                    if (dAlign === 'left') marginStyle = 'margin: 0 auto 0 0;';
                    if (dAlign === 'right') marginStyle = 'margin: 0 0 0 auto;';
                    return '<div style="width:100%;height:100%;display:flex;align-items:center;box-sizing:border-box;padding:0 4px;">' +
                           '<hr style="width:' + dPct + '%;border:none;border-top:' + dWeight + 'px ' + dStyle + ' ' + dColor + ';' + marginStyle + 'padding:0;" />' +
                           '</div>';

                case 'icon':
                    var iClass = el.iconClass || el.content || 'fas fa-star';
                    if (iClass.indexOf('fa') === 0 && iClass.indexOf(' ') === -1) {
                        iClass = 'fas ' + iClass;
                    }
                    var iColor = el.iconColor || el.color || '#38bdf8';
                    var iSize = parseInt(el.iconSize || 36, 10);
                    return '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:' + iColor + ';font-size:' + iSize + 'px;"><i class="' + iClass + '"></i></div>';

                default:
                    return '<div style="padding:6px;font-size:12px;">' + label + '</div>';
            }
        },

        // WPPOPPOP_OVERFLOW_CONTAINMENT: per-canvas overflow aware containment
        isOverflowAllowed: function() {
            var core = window.WpPopPopBuilderCore;
            if (!core || !core.state) return false;
            var cur = core.state.currentCanvas || 1;
            var m = (core.state.canvasMeta && core.state.canvasMeta[cur]) || {};
            return (m.allow_overflow === true || m.allow_overflow === 1 || m.allow_overflow === '1' || m.allow_overflow === 'true');
        },

        getContainment: function() {
            return this.isOverflowAllowed() ? false : '#wppoppop-canvas-elements-root';
        },

        refreshInteractionContainment: function() {
            var c = this.getContainment();
            $('#wppoppop-canvas-elements-root').children().each(function() {
                var $n = $(this);
                if ($n.data('ui-draggable') || $n.data('uiDraggable')) { try { $n.draggable('option', 'containment', c); } catch (e) {} }
                if ($n.data('ui-resizable') || $n.data('uiResizable')) { try { $n.resizable('option', 'containment', c); } catch (e) {} }
            });
        },

        // WPPOPPOP_EDGE_SNAP: magnetic snap to the canvas edges (hold Alt to disable)
        snapToCanvasEdges: function(event, ui, $node) {
            var $box = $('#wppoppop-canvas-box');
            var boxEl = $box[0];
            if (!boxEl || !ui || !ui.position || !$node || !$node[0]) return;
            var $gv = $('#wppoppop-guide-v');
            var $gh = $('#wppoppop-guide-h');
            if (event && event.altKey) { $gv.hide(); $gh.hide(); return; }

            var boxW = boxEl.clientWidth;
            var boxH = boxEl.clientHeight;
            var elW = $node[0].offsetWidth;
            var elH = $node[0].offsetHeight;
            var T = 10;

            var x = Math.round(ui.position.left);
            var y = Math.round(ui.position.top);
            var gx = null, gy = null;

            if (Math.abs(x) <= T) { x = 0; gx = 0; }
            else if (Math.abs((x + elW) - boxW) <= T) { x = boxW - elW; gx = boxW; }

            if (Math.abs(y) <= T) { y = 0; gy = 0; }
            else if (Math.abs((y + elH) - boxH) <= T) { y = boxH - elH; gy = boxH; }

            ui.position.left = x;
            ui.position.top = y;

            if (gx !== null) { $gv.css({ left: gx + 'px', display: 'block' }); } else { $gv.hide(); }
            if (gy !== null) { $gh.css({ top: gy + 'px', display: 'block' }); } else { $gh.hide(); }
        },

        // WPPOPPOP_SNAP_OBJECTS: snap-to-object toggle (hold Alt to bypass)
        snapObjects: false,

        setSnapObjects: function(on) {
            this.snapObjects = !!on;
            try { window.localStorage.setItem('wppoppop_snap_objects', on ? '1' : '0'); } catch (e) {}
            this.syncSnapUI();
        },

        syncSnapUI: function() {
            $('#wppoppop-snap-object').prop('checked', !!this.snapObjects);
        },

        bindSnapControls: function() {
            var self = this;
            var o = '0';
            try { o = window.localStorage.getItem('wppoppop_snap_objects'); } catch (e) {}
            self.snapObjects = (o === '1');
            self.syncSnapUI();
            $(document).off('change.wppoppop_snap', '.wppoppop-snap-toggle')
                .on('change.wppoppop_snap', '.wppoppop-snap-toggle', function() {
                    self.setSnapObjects(this.checked);
                });
        },

        snapToObjects: function(event, ui, $node, el) {
            if (!this.snapObjects || !ui || !ui.position || !$node || !$node[0]) return;
            if (event && event.altKey) return;
            var boxEl = $('#wppoppop-canvas-box')[0];
            if (!boxEl) return;
            var $gv = $('#wppoppop-guide-v');
            var $gh = $('#wppoppop-guide-h');
            var boxW = boxEl.clientWidth;
            var boxH = boxEl.clientHeight;
            var elW = $node[0].offsetWidth;
            var elH = $node[0].offsetHeight;
            var T = 6;
            var x = Math.round(ui.position.left);
            var y = Math.round(ui.position.top);

            // canvas-edge snap has priority: leave those axes alone
            var lockX = (x === 0) || (x + elW === boxW);
            var lockY = (y === 0) || (y + elH === boxH);

            var tx = [], ty = [];
            var others = this.getActiveElements();
            for (var i = 0; i < others.length; i++) {
                var o = others[i];
                if (!o || o.id === el.id || o.hidden) continue;
                var n = document.getElementById('el-' + o.id);
                var oL = parseFloat(o.left); if (isNaN(oL)) oL = 0;
                var oT = parseFloat(o.top);  if (isNaN(oT)) oT = 0;
                var oW = n ? n.offsetWidth : (parseFloat(o.width) || 0);
                var oH = n ? n.offsetHeight : (parseFloat(o.height) || 0);
                tx.push(oL, Math.round(oL + oW / 2), oL + oW);
                ty.push(oT, Math.round(oT + oH / 2), oT + oH);
            }

            var best = function(pos, size, targets) {
                var out = null, bestD = T + 1;
                var pts = [[pos, 0], [pos + Math.round(size / 2), Math.round(size / 2)], [pos + size, size]];
                for (var a = 0; a < pts.length; a++) {
                    for (var b = 0; b < targets.length; b++) {
                        var d = Math.abs(pts[a][0] - targets[b]);
                        if (d < bestD) { bestD = d; out = { pos: targets[b] - pts[a][1], guide: targets[b] }; }
                    }
                }
                return out;
            };

            var gx = null, gy = null;
            if (!lockX) {
                var rx = best(x, elW, tx);
                if (rx) { ui.position.left = rx.pos; gx = rx.guide; }
            }
            if (!lockY) {
                var ry = best(y, elH, ty);
                if (ry) { ui.position.top = ry.pos; gy = ry.guide; }
            }
            if (gx !== null) { $gv.css({ left: gx + 'px', display: 'block' }); }
            if (gy !== null) { $gh.css({ top: gy + 'px', display: 'block' }); }
        },

        attachInteractions: function($node, el) {
            var self = this;
            if (el.locked) {
                $node.addClass('wppoppop-locked');
                return;
            }

            $node.draggable({
                containment: self.getContainment(),
                drag: function(event, ui) {
                    self.snapToCanvasEdges(event, ui, $node);
                    self.snapToObjects(event, ui, $node, el);
                    el.top = ui.position.top;
                    el.left = ui.position.left;
                    if (window.WpPopPopBuilderInspector) {
                        window.WpPopPopBuilderInspector.syncCoordinates(el);
                    }
                },
                stop: function(event, ui) {
                    el.top = ui.position.top;
                    el.left = ui.position.left;
                    if (window.WpPopPopBuilderInspector) {
                        window.WpPopPopBuilderInspector.syncCoordinates(el);
                    }
                    window.WpPopPopBuilderCore.pushHistory();
                }
            });

            $node.on('dragstop.wppoppopSnap', function() {
                $('#wppoppop-guide-v, #wppoppop-guide-h').hide();
            });

            $node.resizable({
                containment: self.getContainment(),
                handles: 'se',
                resize: function(event, ui) {
                    el.width = ui.size.width;
                    el.height = ui.size.height;
                    if (window.WpPopPopBuilderInspector) {
                        window.WpPopPopBuilderInspector.syncCoordinates(el);
                    }
                },
                stop: function(event, ui) {
                    el.width = ui.size.width;
                    el.height = ui.size.height;
                    if (window.WpPopPopBuilderInspector) {
                        window.WpPopPopBuilderInspector.syncCoordinates(el);
                    }
                    window.WpPopPopBuilderCore.pushHistory();
                }
            });
        },

        bindRibbonTools: function() {
        var self = this;
        var $ribbonBtns = $('.wppoppop-ribbon-btn, .wppoppop-ribbon-tool, .wppoppop-tool-btn');

        var normalizeType = function(rawType) {
            var map = {
                'step': 'step_btn',
                'link': 'link_btn',
                'close': 'close_icon',
                'dropdown': 'select',
                'radio': 'radios',
                'checkbox': 'checkboxes',
                'text': 'text'
            };
            return map[rawType] || rawType;
        };

        // 1-Click Spawning directly onto canvas center
        $ribbonBtns.off('click.wppoppop_add').on('click.wppoppop_add', function(e) {
            e.preventDefault();
            var rawType = $(this).data('type');
            if (!rawType) return;
            var type = normalizeType(rawType);

            var $box = $('#wppoppop-canvas-box');
            var boxW = $box.outerWidth() || 640;
            var boxH = $box.outerHeight() || 400;

            var spawnX = Math.max(20, Math.round((boxW / 2) - 100));
            var spawnY = Math.max(20, Math.round((boxH / 2) - 30));

            self.addElement(type, spawnX, spawnY);
        });

        // Tactile Drag Ghost & Droppable
        if ($.fn.draggable) {
            $ribbonBtns.draggable({
                helper: function() {
                    var label = $(this).find('.btn-label').text() || $(this).data('type') || 'Element';
                    var iconHtml = $(this).find('.dashicons').prop('outerHTML') || '<span class="dashicons dashicons-plus"></span>';
                    return $('<div class="wppoppop-drag-ghost">' + iconHtml + '<span>' + label + '</span></div>');
                },
                appendTo: 'body',
                cursor: 'grabbing',
                cursorAt: { left: 40, top: 18 },
                zIndex: 999999,
                revert: 'invalid'
            });
        }

        // Droppable on Canvas Box with Zoom Normalization
        var $box = $('#wppoppop-canvas-box');
        if ($.fn.droppable && $box.length) {
            $box.droppable({
                accept: '.wppoppop-ribbon-btn, .wppoppop-ribbon-tool, .wppoppop-tool-btn',
                tolerance: 'pointer',
                drop: function(e, ui) {
                    var rawType = ui.draggable.data('type');
                    if (!rawType) return;
                    var type = normalizeType(rawType);

                    var boxOffset = $box.offset();
                    var scale = self.currentZoom || 1.0;

                    var dropX = Math.round((e.pageX - boxOffset.left) / scale);
                    var dropY = Math.round((e.pageY - boxOffset.top) / scale);

                    if (!self.isOverflowAllowed()) {
                        dropX = Math.max(0, Math.min(dropX, $box.outerWidth() - 50));
                        dropY = Math.max(0, Math.min(dropY, $box.outerHeight() - 30));
                    }

                    self.addElement(type, dropX, dropY);
                }
            });
        }
    },

    addElement: function(type, spawnX, spawnY) {
            var elements = this.getActiveElements();
            var id = 'layer_' + Date.now().toString(36) + '_' + Math.random().toString(36).substr(2, 4);
            var nextZ = elements.length ? Math.max.apply(null, elements.map(function(e) { return e.zIndex || 10; })) + 1 : 10;

            var newEl = {
                id: id,
                type: type,
                name: type.toUpperCase() + ' ' + (elements.length + 1),
                top: (typeof spawnY === 'number' && !isNaN(spawnY)) ? spawnY : (50 + (elements.length * 15) % 150),
                left: (typeof spawnX === 'number' && !isNaN(spawnX)) ? spawnX : (50 + (elements.length * 15) % 200),
                width: (type === 'close' || type === 'close_icon') ? 35 : (type === 'shape') ? 120 : ((type === 'text' || type === 'textfield') ? 200 : (type === 'html') ? 240 : (type === 'rating' ? 140 : 200)),
                height: (type === 'progress') ? 20 /* WPPOPPOP_PROGRESS_SPAWN_20 */ : (type === 'close' || type === 'close_icon') ? 35 : (type === 'shape') ? 120 : ((type === 'signature') ? 60 : 42),
                zIndex: nextZ, /* WPPOPPOP_CLOSE_SPAWN_35 */
                sizePresetApplied: (type === 'close' || type === 'close_icon'),
                borderRadius: 4,
                opacity: 1,
                fontSize: 14,
                fontWeight: '400',
                lineHeight: 1.4,
                letterSpacing: 0,
                textAlign: 'left',
                padding: (type === 'text' || type === 'paragraph' || type === 'title') ? 8 : 0,
                animation: 'none',
                content: this.getDefaultContent(type),
                goto_canvas: 2,
                goto_screen: 2,
                locked: false,
                hidden: false,
                custom_settings: {}
            };

            if (type === 'button' || type === 'link' || type === 'link_btn') { /* WPPOPPOP_BUTTON_DEFAULTS */
                newEl.bgColor = '#2563eb';
                newEl.color = '#ffffff';
                newEl.fontWeight = '600';
                newEl.fontSize = 14;
                newEl.borderRadius = 6;
                newEl.textAlign = 'center';
                newEl.buttonAction = 'link';
                newEl.linkBlank = false;
                newEl.nofollow = false;
                newEl.hoverBgColor = '#1d4ed8';
                newEl.hoverTextColor = '#ffffff';
                newEl.hoverBorderColor = '#1d4ed8';
            }

            if (type === 'phone' || type === 'tel') { newEl.autocomplete = 'tel'; } /* WPPOPPOP_PHONE_AC_DEFAULT */
            if (type === 'shape') {
                newEl.shapePreset = 'square';
                newEl.shapeFill = '#3b82f6';
                newEl.shapeStroke = '#1d4ed8';
                newEl.shapeStrokeWidth = 2;
                newEl.shapeRotate = 0;
            }

            if (type === 'textarea') { newEl.height = 100; newEl.h = 100; if (!newEl.rows) { newEl.rows = 4; } } /* WPPOPPOP_TEXTAREA_H100 */
            if (type === 'checkbox' || type === 'checkboxes') { newEl.width = 300; newEl.w = 300; newEl.fontSize = 12; newEl.bgColor = 'transparent'; newEl.transparentFill = true; } /* WPPOPPOP_CHECKBOX_DEFAULTS */
            if (type === 'radio' || type === 'radios') { newEl.width = 100; newEl.w = 100; newEl.height = 100; newEl.h = 100; newEl.fontSize = 12; newEl.bgColor = 'transparent'; newEl.transparentFill = true; } /* WPPOPPOP_RADIO_DEFAULTS */
            if (type === 'multiselect') { newEl.width = 125; newEl.w = 125; newEl.height = 125; newEl.h = 125; } /* WPPOPPOP_MULTISELECT_SIZE */
            if (type === 'tile') { newEl.textAlign = 'center'; newEl.hoverBgColor = '#0284c7'; newEl.hoverTextColor = '#ffffff'; newEl.hoverBorderColor = '#0284c7'; } /* WPPOPPOP_TILE_DEFAULTS */
            elements.push(newEl);
            window.WpPopPopBuilderCore.state.activeId = id;
            this.renderCanvas();

            if (window.WpPopPopBuilderLayers) {
                window.WpPopPopBuilderLayers.renderLayers();
            }
            if (window.WpPopPopBuilderInspector) {
                window.WpPopPopBuilderInspector.open(id);
            }

            window.WpPopPopBuilderCore.pushHistory();
        },

        getDefaultContent: function(type) {
            switch(type) {
                case 'label': return 'Hello World';
                case 'title': return 'Headline Title';
                case 'paragraph':
                case 'text': return 'Click to edit your text headline...';
                case 'textfield': return 'Enter your text...';
                case 'email': return 'Enter your best email...';
                case 'number': return '1';
                case 'dropdown':
                case 'select': return 'First Option, Second Option';
                case 'radio':
                case 'radios': return 'Option A, Option B';
                case 'checkbox':
                case 'checkboxes': return 'I agree to the terms';
                case 'date': return '2026-10-10';
                case 'step':
                case 'step_btn': return 'Next Canvas &rarr;';
                case 'button':
                case 'link':
                case 'link_btn': return 'Click Here';
                case 'submit': return 'Claim Offer Now';
                case 'pay': return 'Complete Checkout ($19.99)';
                case 'html': return '<p>Custom <strong>HTML block</strong></p>';
                default: return '';
            }
        },

        bindCanvasSelection: function() {

            var self = this;

            // Primary click handler (keeps existing click behavior)
            $(document).off('click.wppoppop_canvas_select', '.wppoppop-canvas-item')
                .on('click.wppoppop_canvas_select', '.wppoppop-canvas-item', function(e) {
                    if (self.isPreview) return;
                    e.stopPropagation();
                    var id = $(this).data('id') || $(this).attr('id') || '';
                    if (id && String(id).indexOf('el-') === 0) id = String(id).replace(/^el-/, '');
                    self.selectElement(id);
                });

            // Pointer / touch / mousedown for improved cross-browser selection and to work around draggable click suppression
            $(document).off('pointerdown.wppoppop_canvas_select touchstart.wppoppop_canvas_select mousedown.wppoppop_canvas_select', '.wppoppop-canvas-item')
                .on('pointerdown.wppoppop_canvas_select touchstart.wppoppop_canvas_select mousedown.wppoppop_canvas_select', '.wppoppop-canvas-item', function(e) {
                    if (self.isPreview) return;
                    e.stopPropagation();
                    var $el = $(this);
                    var id = $el.data('id') || $el.attr('id') || '';
                    if (id && String(id).indexOf('el-') === 0) id = String(id).replace(/^el-/, '');
                    // allow UI events (drag) to settle; immediate selection still occurs
                    setTimeout(function() { self.selectElement(id); }, 0);
                });

            // Deselect handlers (preserve original behavior)
            $('#wppoppop-canvas-box').off('click.wppoppop_deselect').on('click.wppoppop_deselect', function(e) {
                if (self.isPreview) return;
                if ($(e.target).closest('.wppoppop-canvas-item, #wppoppop-canvas-corner-handle, #wppoppop-canvas-stage-bar').length === 0) {
                    self.deselect();
                    if (window.WpPopPopBuilderSettings) {
                        window.WpPopPopBuilderSettings.openDrawer();
                    }
                }
            });

            $('.wppoppop-builder-workspace').off('click.wppoppop_deselect').on('click.wppoppop_deselect', function(e) {
                if (self.isPreview) return;
                if ($(e.target).closest('.wppoppop-canvas-item, #wppoppop-inspector-drawer, #wppoppop-floating-layers-panel, #wppoppop-canvas-corner-handle, .wppoppop-ribbon-bar, .wppoppop-builder-header, #wppoppop-settings-drawer, #wppoppop-canvas-box').length === 0) {
                    self.deselect();
                }
            });

        },

        selectElement: function(id) {

            if (!id) return;
            // Normalize many possible id shapes: '#el-xxx', 'el-xxx', 'wppoppop-el-xxx', or raw id
            var raw = String(id || '');
            raw = raw.replace(/^#/, '').replace(/^wppoppop-el-/, '').replace(/^el-/, '');
            this.selectedId = raw;
            if ($("#wppoppop-alignment-toolbar").length) {
                $("#wppoppop-alignment-toolbar").fadeIn(120);
            }
            if (window.WpPopPopBuilderCore) {
                window.WpPopPopBuilderCore.state.activeId = raw;
            }
            $('.wppoppop-canvas-item').removeClass('wppoppop-selected');
            $('#el-' + raw).addClass('wppoppop-selected');

            if (window.WpPopPopBuilderSettings && typeof window.WpPopPopBuilderSettings.closeDrawer === 'function') {
                window.WpPopPopBuilderSettings.closeDrawer();
            }
            if (window.WpPopPopBuilderLayers && typeof window.WpPopPopBuilderLayers.highlightLayer === 'function') {
                window.WpPopPopBuilderLayers.highlightLayer(raw);
            }
            if (window.WpPopPopBuilderInspector && typeof window.WpPopPopBuilderInspector.open === 'function') {
                window.WpPopPopBuilderInspector.open(raw);
            }

    },

    deselect: function() {
        this.selectedId = null;
        if ($("#wppoppop-alignment-toolbar").length) {
            $("#wppoppop-alignment-toolbar").fadeOut(120);
        }
        $("#wppoppop-guide-v, #wppoppop-guide-h").hide();
        if (window.WpPopPopBuilderCore) {
            window.WpPopPopBuilderCore.state.activeId = null;
        }
        $('.wppoppop-canvas-item').removeClass('wppoppop-selected');
        if (window.WpPopPopBuilderInspector && typeof window.WpPopPopBuilderInspector.close === 'function') {
            window.WpPopPopBuilderInspector.close();
        }
        if (window.WpPopPopBuilderLayers) {
            $('.wppoppop-layer-item').removeClass('active');
        }
    },

    deleteActiveElement: function() {
        var core = window.WpPopPopBuilderCore;
        if (!core || !core.state || !core.state.activeId) return;
        if (typeof this.deleteElement === 'function') {
            this.deleteElement(core.state.activeId);
        }
    }
};

// Global namespace aliases
window.WpPopPopCanvas = window.WpPopPopBuilderCanvas;
window.WpPopPopBuilder = window.WpPopPopBuilder || {};
window.WpPopPopBuilder.Canvas = window.WpPopPopBuilderCanvas;
})(jQuery);

