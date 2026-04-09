{{-- Scroll-reveal for sections using `scroll_reveal` setting (Renderer wrap). Loaded once per rendered page. --}}
<style>
    .pb-reveal {
        --pb-reveal-duration: 0.65s;
        --pb-reveal-ease: cubic-bezier(0.22, 1, 0.36, 1);
    }

    .pb-reveal:not(.pb-reveal--visible) {
        opacity: 0;
        transition: opacity var(--pb-reveal-duration) var(--pb-reveal-ease),
            transform var(--pb-reveal-duration) var(--pb-reveal-ease);
    }

    .pb-reveal.pb-reveal--visible {
        opacity: 1;
        transform: none !important;
    }

    .pb-reveal--fade-up:not(.pb-reveal--visible) {
        transform: translate3d(0, 1.5rem, 0);
    }

    .pb-reveal--fade:not(.pb-reveal--visible) {
        transform: none;
    }

    .pb-reveal--slide-left:not(.pb-reveal--visible) {
        transform: translate3d(-1.5rem, 0, 0);
    }

    .pb-reveal--slide-right:not(.pb-reveal--visible) {
        transform: translate3d(1.5rem, 0, 0);
    }

    .pb-reveal--zoom:not(.pb-reveal--visible) {
        transform: scale(0.96);
    }

    @media (prefers-reduced-motion: reduce) {
        .pb-reveal {
            opacity: 1 !important;
            transform: none !important;
            transition: none !important;
        }
    }
</style>
<script>
    (function () {
        if (typeof window === "undefined") return;
        var reduce =
            window.matchMedia &&
            window.matchMedia("(prefers-reduced-motion: reduce)").matches;

        function revealAll() {
            document.querySelectorAll("[data-pb-reveal]").forEach(function (el) {
                el.classList.add("pb-reveal--visible");
            });
        }

        if (reduce) {
            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", revealAll);
            } else {
                revealAll();
            }
            return;
        }

        var io =
            "IntersectionObserver" in window
                ? new IntersectionObserver(
                      function (entries) {
                          entries.forEach(function (entry) {
                              if (!entry.isIntersecting) return;
                              entry.target.classList.add("pb-reveal--visible");
                              io.unobserve(entry.target);
                          });
                      },
                      { root: null, rootMargin: "0px 0px -8% 0px", threshold: 0.08 }
                  )
                : null;

        function boot() {
            document.querySelectorAll("[data-pb-reveal]").forEach(function (el) {
                if (io) io.observe(el);
                else el.classList.add("pb-reveal--visible");
            });
        }

        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", boot);
        } else {
            boot();
        }
    })();
</script>
