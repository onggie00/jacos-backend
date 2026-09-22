/* =====================================================
   labs-ui-toggle.js
   Show/hide chart toggle dengan localStorage persistence
   Usage: <button class="labs-toggle-trigger" data-labs-toggle="chart-section">
              <span class="labs-toggle-trigger__icon"><i class="fa fa-chevron-right"></i></span>
              ...
          </button>
          <div class="labs-toggle-content" id="chart-section">...</div>
   ===================================================== */

(function($) {
    'use strict';

    var LabsToggle = {
        storagePrefix: 'labs-ui-toggle-',

        init: function() {
            var self = this;
            // Restore state dari localStorage
            $('.labs-toggle-trigger').each(function() {
                var target = $(this).data('labs-toggle');
                if (!target) return;
                var $content = $('#' + target);
                if (!$content.length) return;

                var saved = localStorage.getItem(self.storagePrefix + target);
                if (saved === 'open') {
                    self.expand($(this), $content, false); // false = no animation
                }
            });

            // Click handler
            $(document).on('click', '.labs-toggle-trigger', function(e) {
                e.preventDefault();
                var target = $(this).data('labs-toggle');
                if (!target) return;
                var $content = $('#' + target);
                if (!$content.length) return;

                var isExpanded = $(this).hasClass('is-expanded');
                if (isExpanded) {
                    self.collapse($(this), $content, true);
                    localStorage.setItem(self.storagePrefix + target, 'closed');
                } else {
                    self.expand($(this), $content, true);
                    localStorage.setItem(self.storagePrefix + target, 'open');
                }
            });
        },

        expand: function($trigger, $content, animate) {
            $trigger.addClass('is-expanded');
            $content.addClass('is-expanded');

            if (animate) {
                // Hitung natural height untuk animasi smooth
                $content.css('max-height', 'none');
                var h = $content.outerHeight();
                $content.css('max-height', '0');
                // Force reflow
                $content[0].offsetHeight;
                $content.css({
                    'max-height': h + 'px',
                    'opacity': '1'
                });
                setTimeout(function() {
                    $content.css('max-height', '');
                }, 450);
            }
        },

        collapse: function($trigger, $content, animate) {
            if (animate) {
                var h = $content.outerHeight();
                $content.css('max-height', h + 'px');
                $content[0].offsetHeight;
                $content.css({
                    'max-height': '0',
                    'opacity': '0'
                });
                setTimeout(function() {
                    $content.removeClass('is-expanded');
                }, 400);
            } else {
                $content.removeClass('is-expanded');
            }
            $trigger.removeClass('is-expanded');
        }
    };

    $(document).ready(function() {
        LabsToggle.init();
    });

    // Expose ke global
    window.LabsToggle = LabsToggle;

})(jQuery);