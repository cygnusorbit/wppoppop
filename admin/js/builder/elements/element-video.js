/**
 * WpPopPop Element: Responsive Video Embed
 * Cross-browser Stage Renderer & Inspector Settings
 */
(function($) {
    'use strict';

    if (!window.WpPopPopElements) return;

    window.WpPopPopElements.register('video', {
        label: 'Video',
        icon: 'dashicons-video-alt3',

        getDefaults: function() {
            return {
                width: 320,
                height: 180,
                content: 'https://www.youtube.com/watch?v=ScMzIvxBSi4',
                videoUrl: 'https://www.youtube.com/watch?v=ScMzIvxBSi4',
                aspectRatio: '16:9',
                videoAutoplay: false,
                videoMute: true,
                videoLoop: false,
                borderRadius: 4
            };
        },

        renderStage: function(el) {
            var url = el.videoUrl || el.content || 'https://www.youtube.com/watch?v=ScMzIvxBSi4';
            var ratio = el.aspectRatio || '16:9';

            return '<div style="width:100%;height:100%;border-radius:inherit;background:#0f172a;color:#ffffff;' +
                'display:flex;flex-direction:column;align-items:center;justify-content:center;position:relative;' +
                'overflow:hidden;box-shadow:inset 0 0 20px rgba(0,0,0,0.5);user-select:none;pointer-events:none;">' +
                '<span class="dashicons dashicons-controls-play" style="font-size:48px;width:48px;height:48px;color:#ef4444;"></span>' +
                '<span style="font-size:11px;font-weight:600;margin-top:6px;max-width:80%;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;">' + url + '</span>' +
                '<span style="position:absolute;bottom:6px;right:8px;font-size:10px;opacity:0.65;">' + ratio + '</span>' +
                '</div>';
        },

        renderSettings: function(el, $container) {
            var url = el.videoUrl || el.content || 'https://www.youtube.com/watch?v=ScMzIvxBSi4';
            var ratio = el.aspectRatio || '16:9';

            var html = '<div class="element-panel">' +
                '<h4 style="margin:0 0 10px 0;font-size:12px;color:#1e293b;text-transform:uppercase;font-weight:700;">Video Settings</h4>' +
                '<div style="margin-bottom:8px;">' +
                    '<label>Video URL (YouTube, Vimeo, MP4)</label>' +
                    '<input type="url" id="insp-video-url" value="' + url + '" placeholder="https://..." class="wppoppop-insp-input">' +
                '</div>' +
                '<div style="margin-bottom:8px;">' +
                    '<label>Aspect Ratio</label>' +
                    '<select id="insp-video-ratio" class="wppoppop-insp-input">' +
                        '<option value="16:9"' + (ratio === '16:9' ? ' selected' : '') + '>16:9 (Standard Widescreen)</option>' +
                        '<option value="4:3"' + (ratio === '4:3' ? ' selected' : '') + '>4:3 (Classic Television)</option>' +
                        '<option value="1:1"' + (ratio === '1:1' ? ' selected' : '') + '>1:1 (Square)</option>' +
                        '<option value="9:16"' + (ratio === '9:16' ? ' selected' : '') + '>9:16 (Vertical / Stories)</option>' +
                    '</select>' +
                '</div>' +
                '<div style="display:flex;flex-direction:column;gap:6px;margin-top:8px;">' +
                    '<div style="display:flex;align-items:center;gap:6px;">' +
                        '<input type="checkbox" id="insp-video-autoplay"' + (el.videoAutoplay ? ' checked' : '') + '>' +
                        '<label for="insp-video-autoplay" style="margin:0;cursor:pointer;">Autoplay Video</label>' +
                    '</div>' +
                    '<div style="display:flex;align-items:center;gap:6px;">' +
                        '<input type="checkbox" id="insp-video-mute"' + (el.videoMute ? ' checked' : '') + '>' +
                        '<label for="insp-video-mute" style="margin:0;cursor:pointer;">Mute Sound (Required for Autoplay)</label>' +
                    '</div>' +
                    '<div style="display:flex;align-items:center;gap:6px;">' +
                        '<input type="checkbox" id="insp-video-loop"' + (el.videoLoop ? ' checked' : '') + '>' +
                        '<label for="insp-video-loop" style="margin:0;cursor:pointer;">Loop Playback</label>' +
                    '</div>' +
                '</div>' +
            '</div>';

            $container.append(html);

            $('#insp-video-url').on('input change', function() {
                el.videoUrl = $(this).val().trim();
                el.content = el.videoUrl;
                window.WpPopPopBuilderCanvas.renderCanvas();
            }).on('change', function() {
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-video-ratio').on('change', function() {
                el.aspectRatio = $(this).val();
                window.WpPopPopBuilderCanvas.renderCanvas();
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-video-autoplay').on('change', function() {
                el.videoAutoplay = $(this).is(':checked');
                if (el.videoAutoplay) {
                    el.videoMute = true;
                    $('#insp-video-mute').prop('checked', true);
                }
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-video-mute').on('change', function() {
                el.videoMute = $(this).is(':checked');
                window.WpPopPopBuilderCore.pushHistory();
            });

            $('#insp-video-loop').on('change', function() {
                el.videoLoop = $(this).is(':checked');
                window.WpPopPopBuilderCore.pushHistory();
            });
        }
    });
})(jQuery);
