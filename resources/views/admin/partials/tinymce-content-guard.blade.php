<script>
(function () {
    const editorId = @json($editorId ?? 'content');
    const form = document.querySelector('form[action*="news"]');

    function getEditorPlainText() {
        if (typeof tinymce !== 'undefined') {
            tinymce.triggerSave();
            const ed = tinymce.get(editorId);
            if (ed) {
                return ed.getContent({ format: 'text' }).replace(/\u00a0/g, ' ').trim();
            }
        }
        const el = document.getElementById(editorId);
        if (!el) return '';
        const tmp = document.createElement('div');
        tmp.innerHTML = el.value;
        return (tmp.textContent || tmp.innerText || '').replace(/\u00a0/g, ' ').trim();
    }

    window.clearNewsContentError = function () {
        const errEl = document.getElementById('contentClientError');
        const tox = document.querySelector('.tox-tinymce');
        if (errEl) errEl.classList.add('d-none');
        if (tox) {
            tox.style.border = '';
            tox.style.borderRadius = '';
        }
    };

    function setEditorError() {
        const errEl = document.getElementById('contentClientError');
        const tox = document.querySelector('.tox-tinymce');
        if (errEl) errEl.classList.remove('d-none');
        if (tox) {
            tox.style.border = '1px solid #dc3545';
            tox.style.borderRadius = '6px';
            tox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            if (!getEditorPlainText()) {
                e.preventDefault();
                setEditorError();
                return false;
            }
            window.clearNewsContentError();
        });
    }

    if (@json($errors->has('content'))) {
        setEditorError();
    }
})();
</script>
