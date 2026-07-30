/**
 * Universal Form Validation - Ninh Bình Travel Hub
 * Validates all forms with [novalidate] or data-validate attribute.
 * Shows red error text below invalid fields on submit.
 * Clears errors on input.
 */
(function () {
    'use strict';

    // Vietnamese error messages
    const MESSAGES = {
        required: 'Vui lòng điền vào trường này.',
        email: 'Vui lòng nhập đúng định dạng email.',
        minlength: 'Vui lòng nhập tối thiểu {0} ký tự.',
        maxlength: 'Vui lòng nhập tối đa {0} ký tự.',
        pattern: 'Vui lòng nhập đúng định dạng yêu cầu.',
        number: 'Vui lòng nhập một số hợp lệ.',
        url: 'Vui lòng nhập đúng định dạng URL.',
        match: 'Giá trị xác nhận không khớp.',
        fileRequired: 'Vui lòng chọn tệp.',
        selectRequired: 'Vui lòng chọn một tùy chọn.',
    };

    // Field name translations for better messages
    const FIELD_NAMES = {
        'username': 'tên đăng nhập',
        'email': 'email',
        'password': 'mật khẩu',
        'password_confirmation': 'xác nhận mật khẩu',
        'display_name': 'tên hiển thị',
        'name': 'tên',
        'title': 'tiêu đề',
        'content': 'nội dung',
        'description': 'mô tả',
        'category_id': 'danh mục',
        'lat': 'vĩ độ',
        'lng': 'kinh độ',
        'start_time': 'thời gian bắt đầu',
        'end_time': 'thời gian kết thúc',
        'business_name': 'tên doanh nghiệp',
        'address_street': 'địa chỉ',
        'phone': 'số điện thoại',
        'current_password': 'mật khẩu hiện tại',
        'confirm_password': 'mật khẩu xác nhận',
    };

    /**
     * Get a friendly field name from the input's name or label.
     */
    function getFieldName(input) {
        // Try label first
        if (input.id) {
            const label = document.querySelector('label[for="' + input.id + '"]');
            if (label) {
                let text = label.textContent.replace(/\*/g, '').trim();
                if (text) return text.toLowerCase();
            }
        }
        // Try field name map
        const name = input.name || input.id || '';
        if (FIELD_NAMES[name]) return FIELD_NAMES[name];
        return name.replace(/_/g, ' ');
    }

    /**
     * Validate a single input field and return error message or null.
     */
    function validateField(input) {
        const value = input.value.trim();
        const tagName = input.tagName.toLowerCase();
        const type = (input.type || '').toLowerCase();

        // Skip hidden, disabled, or submit/button fields
        if (input.type === 'hidden' || input.type === 'submit' || input.type === 'button' ||
            input.type === 'reset' || input.disabled || input.readOnly) {
            return null;
        }

        // Skip checkboxes and radio (unless required)
        if (type === 'checkbox' || type === 'radio') {
            if (input.required && !input.checked) {
                return MESSAGES.required;
            }
            return null;
        }

        // Required check
        const isRequired = input.required || input.hasAttribute('required') ||
            input.closest('.form-group, .mb-3')?.querySelector('.text-danger, .required-star');

        if (isRequired) {
            if (tagName === 'select') {
                if (!value || value === '' || value === '-- Chọn --') {
                    return MESSAGES.selectRequired;
                }
            } else if (type === 'file') {
                if (!input.files || input.files.length === 0) {
                    // Only require file on create forms, not edit
                    const form = input.closest('form');
                    if (form && form.querySelector('input[name="_method"]')) {
                        return null; // Edit form, file is optional
                    }
                    return null; // Files are generally optional unless explicitly required
                }
            } else if (!value) {
                const fieldName = getFieldName(input);
                return 'Vui lòng nhập ' + fieldName + '.';
            }
        }

        // Email format check
        if (type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                return MESSAGES.email;
            }
        }

        // Minlength check
        if (input.minLength > 0 && value && value.length < input.minLength) {
            return MESSAGES.minlength.replace('{0}', input.minLength);
        }

        // Min attribute for number
        if (type === 'number' && value) {
            const num = parseFloat(value);
            if (isNaN(num)) return MESSAGES.number;
            if (input.min !== '' && num < parseFloat(input.min)) {
                return 'Giá trị tối thiểu là ' + input.min + '.';
            }
        }

        // Password confirmation match
        if (input.name === 'password_confirmation' && value) {
            const form = input.closest('form');
            const passwordField = form?.querySelector('input[name="password"]');
            if (passwordField && passwordField.value !== value) {
                return 'Mật khẩu xác nhận không khớp.';
            }
        }

        return null;
    }

    /**
     * Show error on a field.
     */
    function showFieldError(input, message) {
        // Add invalid class
        input.classList.add('is-invalid');

        // Find or create error element
        const wrapper = input.closest('.input-wrapper') || input.parentElement;
        const group = input.closest('.form-group, .mb-3, .col-md-6, .col-md-9, .col-6, .col-half, .col-12');
        const container = group || wrapper;

        // Check for existing error element (from Blade @error or previous validation)
        let errorEl = null;

        // For client auth pages — look for .field-error-msg
        if (input.id) {
            errorEl = container?.querySelector('#' + input.id + 'Error');
        }
        if (!errorEl) {
            errorEl = container?.querySelector('.field-error-msg');
        }

        // For Bootstrap admin pages — look for .invalid-feedback
        if (!errorEl) {
            errorEl = container?.querySelector('.invalid-feedback');
        }

        // If no error element exists, create one
        if (!errorEl) {
            errorEl = document.createElement('div');
            // Detect which style to use
            const isBootstrap = !!input.closest('[class*="form-control-sm"], .card-minimal');
            if (isBootstrap) {
                errorEl.className = 'invalid-feedback';
                errorEl.style.fontSize = '0.75rem';
                errorEl.style.display = 'block';
            } else {
                errorEl.className = 'field-error-msg';
            }
            errorEl.setAttribute('data-validation-error', 'true');

            // Insert after input wrapper or input itself
            if (wrapper && wrapper.classList.contains('input-wrapper')) {
                wrapper.parentNode.insertBefore(errorEl, wrapper.nextSibling);
            } else {
                input.parentNode.insertBefore(errorEl, input.nextSibling);
            }
        }

        errorEl.textContent = message;
        errorEl.style.display = 'block';
    }

    /**
     * Clear error on a field.
     */
    function clearFieldError(input) {
        input.classList.remove('is-invalid');

        const group = input.closest('.form-group, .mb-3, .col-md-6, .col-md-9, .col-6, .col-half, .col-12');
        const wrapper = input.closest('.input-wrapper') || input.parentElement;
        const container = group || wrapper;

        // Clear all error elements in the group
        if (container) {
            const errorEls = container.querySelectorAll('.field-error-msg, .invalid-feedback, [data-validation-error]');
            errorEls.forEach(el => {
                el.textContent = '';
                // Only hide dynamically created ones
                if (el.hasAttribute('data-validation-error')) {
                    el.style.display = 'none';
                }
            });
        }
    }

    /**
     * Initialize validation on a form.
     */
    function initFormValidation(form) {
        // Skip forms that are already initialized
        if (form.dataset.validationInit === 'true') return;
        form.dataset.validationInit = 'true';

        form.addEventListener('submit', function (e) {
            let firstInvalid = null;
            let isValid = true;

            // Get all form fields
            const inputs = form.querySelectorAll('input, textarea, select');

            inputs.forEach(function (input) {
                const error = validateField(input);
                if (error) {
                    showFieldError(input, error);
                    isValid = false;
                    if (!firstInvalid) firstInvalid = input;
                } else {
                    clearFieldError(input);
                }
            });

            if (!isValid) {
                e.preventDefault();
                e.stopPropagation();
                // Scroll to first invalid field
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });

        // Clear error on input
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(function (input) {
            const events = ['input', 'change'];
            events.forEach(function (eventName) {
                input.addEventListener(eventName, function () {
                    clearFieldError(this);
                });
            });
        });
    }

    /**
     * On DOM ready, init all forms.
     */
    document.addEventListener('DOMContentLoaded', function () {
        // Select forms with novalidate attribute or data-validate
        const forms = document.querySelectorAll('form[novalidate], form[data-validate]');
        forms.forEach(initFormValidation);

        // Also initialize forms in admin that don't have novalidate but have required fields
        const allForms = document.querySelectorAll('form');
        allForms.forEach(function (form) {
            if (form.dataset.validationInit === 'true') return;
            // Check if form has required fields
            const hasRequired = form.querySelector('[required], .text-danger');
            if (hasRequired) {
                form.setAttribute('novalidate', '');
                initFormValidation(form);
            }
        });
    });
})();
