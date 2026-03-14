<style id="pb-preview-shell-style">
    html.pb-preview-mode,
    body.pb-preview-mode {
        background: transparent !important;
    }

    #pb-preview-editor {
        display: block;
        width: 100%;
    }
</style>

<script id="pb-preview-shell-script">
    (function() {
        if (window.__pbPreviewShellBooted) return;
        window.__pbPreviewShellBooted = true;

        function byId(id) {
            return document.getElementById(id);
        }

        function stripBodyForPreview() {
            if (!document.body) return;

            var children = Array.from(document.body.children);
            children.forEach(function(node) {
                var tag = node.tagName;
                if (tag !== 'STYLE' && tag !== 'SCRIPT') {
                    node.remove();
                }
            });
        }

        function ensurePreviewRoot() {
            var editor = byId('pb-preview-editor');
            if (editor) return editor;

            editor = document.createElement('pb-editor');
            editor.id = 'pb-preview-editor';
            document.body.appendChild(editor);
            return editor;
        }

        function reportHeight() {
            var editor = byId('pb-preview-editor');
            if (!editor) return;

            var height = Math.max(
                editor.scrollHeight,
                editor.offsetHeight,
                document.documentElement.scrollHeight
            );

            window.parent.postMessage({
                type: 'preview-resize',
                height: height
            }, '*');
        }

        function boot() {
            document.documentElement.classList.add('pb-preview-mode');
            document.body.classList.add('pb-preview-mode');
            document.body.setAttribute('data-pb-preview', '1');

            stripBodyForPreview();
            var editor = ensurePreviewRoot();

            window.addEventListener('message', function(e) {
                var msg = e.data || {};
                if (msg.type !== 'set-preview-html') return;

                editor.innerHTML = msg.html || '';
                reportHeight();
                setTimeout(reportHeight, 50);
                setTimeout(reportHeight, 300);
            });

            if (window.ResizeObserver) {
                var heightObserver = new ResizeObserver(reportHeight);
                heightObserver.observe(editor);
            }

            reportHeight();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', boot, {
                once: true
            });
        } else {
            boot();
        }
    })();
</script>
