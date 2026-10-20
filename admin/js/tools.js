/**
 * WpPopPop Tools: Master Coordinator
 */
(function($) {
    'use strict';

    /* form.submit() drops the clicked button's name, so the PHP handler never ran. */
    window.WpPopPopToolsFallback = {
        submit: function(form, key) {
            if (!form) { return; }
            if (key && !form.querySelector('input[type="hidden"][name="' + key + '"]')) {
                var i = document.createElement('input');
                i.type = 'hidden'; i.name = key; i.value = '1';
                form.appendChild(i);
            }
            HTMLFormElement.prototype.submit.call(form);
        }
    };

    /* Destructive actions require typing RESET. */
    window.WpPopPopTypedConfirm = function(message) {
        var v = window.prompt(message + '\n\nType RESET to confirm.');
        return v !== null && v.replace(/^\s+|\s+$/g, '') === 'RESET';
    };

    window.WpPopPopTools = {
        init: function() {
            if (window.WpPopPopToolsSystem) {
                window.WpPopPopToolsSystem.init();
            }
            if (window.WpPopPopToolsDatabase) {
                window.WpPopPopToolsDatabase.init();
            }
            if (window.WpPopPopToolsPortability) {
                window.WpPopPopToolsPortability.init();
            }
        }
    };

    $(document).ready(function() {
        window.WpPopPopTools.init();
    });
})(jQuery);
