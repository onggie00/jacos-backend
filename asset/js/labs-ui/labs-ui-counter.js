/* =====================================================
   labs-ui-counter.js
   Animated count-up untuk stat card numbers
   Usage: <span class="labs-stat-card__value" data-counter data-target="123">0</span>
   ===================================================== */

(function($) {
    'use strict';

    var LabsCounter = {
        duration: 1200, // ms
        easing: 'easeOutCubic',

        init: function() {
            var self = this;
            var $counters = $('[data-counter]');

            if (!$counters.length) return;

            // Pakai IntersectionObserver kalau ada, fallback ke immediate
            if ('IntersectionObserver' in window) {
                var observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            self.animate($(entry.target));
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.3 });

                $counters.each(function() {
                    observer.observe(this);
                });
            } else {
                $counters.each(function() {
                    self.animate($(this));
                });
            }
        },

        animate: function($el) {
            var self = this;
            var target = parseInt($el.data('target'), 10) || 0;
            var start = 0;
            var startTime = null;

            $el.text('0');

            function step(timestamp) {
                if (!startTime) startTime = timestamp;
                var progress = Math.min((timestamp - startTime) / self.duration, 1);
                var eased = self.easeOutCubic(progress);
                var current = Math.floor(eased * target);
                $el.text(current.toLocaleString('id-ID'));
                if (progress < 1) {
                    requestAnimationFrame(step);
                } else {
                    $el.text(target.toLocaleString('id-ID'));
                }
            }

            requestAnimationFrame(step);
        },

        easeOutCubic: function(t) {
            return 1 - Math.pow(1 - t, 3);
        }
    };

    $(document).ready(function() {
        LabsCounter.init();
    });

    window.LabsCounter = LabsCounter;

})(jQuery);