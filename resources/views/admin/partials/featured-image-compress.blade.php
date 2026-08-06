@once
@push('scripts')
<script>
(function () {
    function compressImageFile(inputFile, options, callback) {
        options = options || {};
        const maxDim = options.maxDim || 1920;
        const quality = options.quality || 0.82;
        const minSize = options.minSize || 250000;

        if (!inputFile || inputFile.size <= minSize) {
            callback(inputFile);
            return;
        }

        const reader = new FileReader();
        reader.readAsDataURL(inputFile);
        reader.onload = function (event) {
            const img = new Image();
            img.src = event.target.result;
            img.onload = function () {
                let width = img.width;
                let height = img.height;

                if (width > maxDim || height > maxDim) {
                    if (width > height) {
                        height = Math.round((height * maxDim) / width);
                        width = maxDim;
                    } else {
                        width = Math.round((width * maxDim) / height);
                        height = maxDim;
                    }
                }

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                canvas.toBlob(function (blob) {
                    if (!blob) {
                        callback(inputFile);
                        return;
                    }

                    const baseName = inputFile.name.includes('.')
                        ? inputFile.name.substring(0, inputFile.name.lastIndexOf('.'))
                        : 'image';

                    const compressedFile = new File([blob], baseName + '.jpg', {
                        type: 'image/jpeg',
                        lastModified: Date.now(),
                    });

                    callback(compressedFile);
                }, 'image/jpeg', quality);
            };
            img.onerror = function () {
                callback(inputFile);
            };
        };
        reader.onerror = function () {
            callback(inputFile);
        };
    }

    window.bindFeaturedImageCompress = function (inputId) {
        const input = document.getElementById(inputId);
        const form = input && input.closest('form');
        if (!form || form.dataset.compressBound === '1') {
            return;
        }

        form.dataset.compressBound = '1';

        form.addEventListener('submit', function (e) {
            if (form.dataset.imageCompressed === '1') {
                return;
            }

            if (!input.files || !input.files[0]) {
                return;
            }

            const file = input.files[0];
            if (file.size <= 250000) {
                return;
            }

            e.preventDefault();

            const submitBtn = form.querySelector('[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.dataset.originalText = submitBtn.textContent;
                submitBtn.textContent = 'Đang nén ảnh...';
            }

            compressImageFile(file, { maxDim: 1920, quality: 0.82 }, function (compressed) {
                const dt = new DataTransfer();
                dt.items.add(compressed);
                input.files = dt.files;
                form.dataset.imageCompressed = '1';

                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = submitBtn.dataset.originalText || 'Lưu';
                }

                form.submit();
            });
        });
    };

    document.addEventListener('DOMContentLoaded', function () {
        if (document.getElementById('featured_image')) {
            window.bindFeaturedImageCompress('featured_image');
        }
    });
})();
</script>
@endpush
@endonce
