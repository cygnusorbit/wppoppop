<?php
namespace WPPopPop\Core;

use WPPopPop\Targeting\PopupPostType;

class RemoteEmbedHandler {
    public static function init(): void {
        add_action('init', [__CLASS__, 'handle_remote_embed_request']);
        add_action('rest_api_init', [__CLASS__, 'enable_cors_headers']);
    }

    public static function enable_cors_headers(): void {
        add_filter('rest_pre_serve_request', function ($value) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, X-WP-Nonce');
            return $value;
        });
    }

    public static function handle_remote_embed_request(): void {
        if (!isset($_GET['wppoppop_embed'])) {
            return;
        }

        $popup_id = absint($_GET['wppoppop_embed']);
        if (!$popup_id) {
            status_header(404);
            exit;
        }

        $popup = get_post($popup_id);
        if (!$popup || $popup->post_type !== PopupPostType::POST_TYPE || $popup->post_status !== 'publish') {
            status_header(404);
            exit;
        }

        $html_content = LayerRenderer::render_layers($popup_id, apply_filters('the_content', $popup->post_content));
        
        $css_file = WPPOPPOP_PATH . 'assets/css/wppoppop.css';
        $css_content = file_exists($css_file) ? file_get_contents($css_file) : '';

        $escaped_html = wp_json_encode(
            '<div id="wppoppop-modal-' . $popup_id . '" class="wppoppop-overlay wppoppop-remote-modal" data-popup-id="' . $popup_id . '" aria-hidden="true" style="display:none;">' .
            '<div class="wppoppop-container" role="dialog" aria-modal="true">' .
            '<button type="button" class="wppoppop-close" onclick="this.closest(\'.wppoppop-overlay\').style.display=\'none\';" aria-label="Close">&times;</button>' .
            '<div class="wppoppop-content">' .
            $html_content .
            '</div></div></div>'
        );

        $escaped_css   = wp_json_encode($css_content);
        $submit_url    = wp_json_encode(rest_url('wppoppop/v1/submit'));
        $impression_url = wp_json_encode(rest_url('wppoppop/v1/impression'));

        header('Content-Type: application/javascript; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Cache-Control: public, max-age=300');

        echo "(function() {\n";
        echo "  'use strict';\n";
        echo "  if (document.getElementById('wppoppop-modal-" . $popup_id . "')) return;\n";
        echo "  var css = " . $escaped_css . ";\n";
        echo "  var style = document.createElement('style');\n";
        echo "  style.type = 'text/css';\n";
        echo "  style.appendChild(document.createTextNode(css));\n";
        echo "  document.head.appendChild(style);\n";
        echo "  var div = document.createElement('div');\n";
        echo "  div.innerHTML = " . $escaped_html . ";\n";
        echo "  document.body.appendChild(div.firstElementChild);\n";
        echo "  var modal = document.getElementById('wppoppop-modal-" . $popup_id . "');\n";
        echo "  var recordImpression = function() {\n";
        echo "    fetch(" . $impression_url . ", { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({popup_id: " . $popup_id . "}) }).catch(function(){});\n";
        echo "  };\n";
        echo "  var show = function() {\n";
        echo "    if (modal && modal.style.display !== 'flex') { modal.style.display = 'flex'; modal.setAttribute('aria-hidden', 'false'); recordImpression(); }\n";
        echo "  };\n";
        echo "  window.addEventListener('DOMContentLoaded', function() { setTimeout(show, 1500); });\n";
        echo "  document.addEventListener('mouseleave', function(e) { if (e.clientY <= 0) show(); });\n";
        echo "  modal.addEventListener('submit', function(e) {\n";
        echo "    var form = e.target.closest('.wppoppop-form');\n";
        echo "    if (!form) return;\n";
        echo "    e.preventDefault();\n";
        echo "    var emailInput = form.querySelector('input[name=\"email\"]');\n";
        echo "    var nameInput = form.querySelector('input[name=\"name\"]');\n";
        echo "    var btn = form.querySelector('button[type=\"submit\"]');\n";
        echo "    if (btn) btn.disabled = true;\n";
        echo "    fetch(" . $submit_url . ", {\n";
        echo "      method: 'POST',\n";
        echo "      headers: {'Content-Type': 'application/json'},\n";
        echo "      body: JSON.stringify({ popup_id: " . $popup_id . ", email: emailInput ? emailInput.value : '', name: nameInput ? nameInput.value : '' })\n";
        echo "    }).then(function(r){ return r.json(); }).then(function(data){\n";
        echo "      if (data.success) {\n";
        echo "        form.innerHTML = '<div style=\"color:#10b981;font-weight:600;padding:20px;text-align:center;\">' + (data.message || 'Thank you for subscribing!') + '</div>';\n";
        echo "        setTimeout(function(){ modal.style.display = 'none'; }, 2000);\n";
        echo "      } else {\n";
        echo "        alert(data.message || 'Submission failed.');\n";
        echo "        if (btn) btn.disabled = false;\n";
        echo "      }\n";
        echo "    }).catch(function(){\n";
        echo "      alert('A network error occurred.');\n";
        echo "      if (btn) btn.disabled = false;\n";
        echo "    });\n";
        echo "  });\n";
        echo "})();\n";
        exit;
    }
}
