/* =====================================================
   labs-ui-init.js
   Auto-init semua komponen labs-ui pada page load
   Komponen:
   - labs-toggle (chart show/hide)
   - labs-counter (stat card animation)
   - labs-modal-tabs (tab switching di modal)
   - labs-avatar (generate inisial dari nama)
   ===================================================== */

(function($) {
    'use strict';

    var LabsUI = {
        init: function() {
            this.initTabs();
            this.initAvatars();
        },

        // Tab switching untuk modal
        initTabs: function() {
            $(document).on('click', '.labs-modal-tabs__item', function() {
                var $tab = $(this);
                var target = $tab.data('tab');

                // Deactivate semua
                $tab.closest('.labs-modal-tabs').find('.labs-modal-tabs__item').removeClass('active');
                $tab.closest('.labs-modal').find('.labs-tab-content').removeClass('active');

                // Activate target
                $tab.addClass('active');
                $('#' + target).addClass('active');
            });
        },

        // Auto-generate avatar inisial dari nama
        initAvatars: function() {
            $('.labs-avatar[data-name]').each(function() {
                var $el = $(this);
                if ($el.text().trim() !== '') return; // sudah ada content
                var name = $el.data('name') || '';
                var parts = name.trim().split(/\s+/);
                var initials = '';
                if (parts.length >= 2) {
                    initials = (parts[0][0] || '') + (parts[parts.length - 1][0] || '');
                } else if (parts.length === 1) {
                    initials = parts[0].substring(0, 2);
                }
                $el.text(initials.toUpperCase());
            });
        }
    };

    $(document).ready(function() {
        LabsUI.init();
    });

    window.LabsUI = LabsUI;

})(jQuery);