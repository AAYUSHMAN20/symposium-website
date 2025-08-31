/**
 * Contact Form JavaScript Handler
 * Handles form validation and AJAX submission
 */

class ContactForm {
    constructor() {
        this.form = document.getElementById('contactForm');
        this.submitBtn = document.querySelector('#contactForm button[type="submit"]');
        this.originalBtnText = this.submitBtn.textContent;
        
        this.init();
    }
    
    init() {
        if (this.form) {
            this.form.addEventListener('submit', this.handleSubmit.bind(this));
            this.addRealTimeValidation();
        }
    }
    
    // Add real-time validation to form fields
    addRealTimeValidation() {
        const fields = {
            firstName: {
                element: document.getElementById('firstName'),
                validate: (value) => value.length >= 2 && value.length <= 50,
                message: 'First name must be between 2 and 50 characters'
            },
            lastName: {
                element: document.getElementById('lastName'),
                validate: (value) => value.length >= 2 && value.length <= 50,
                message: 'Last name must be between 2 and 50 characters'
            },
            email: {
                element: document.getElementById('email'),
                validate: (value) => this.validateEmail(value),
                message: 'Please enter a valid email address'
            },
            phone: {
                element: document.getElementById('phone'),
                validate: (value) => value === '' || this.validatePhone(value),
                message: 'Please enter a valid phone number'
            },
            subject: {
                element: document.getElementById('subject'),
                validate: (value) => value.length >= 5 && value.length <= 200,
                message: 'Subject must be between 5 and 200 characters'
            },
            message: {
                element: document.getElementById('message'),
                validate: (value) => value.length >= 10 && value.length <= 1000,
                message: 'Message must be between 10 and 1000 characters'
            }
        };
        
        Object.keys(fields).forEach(fieldName => {
            const field = fields[fieldName];
            if (field.element) {
                field.element.addEventListener('blur', () => {
                    this.validateField(field.element, field.validate, field.message);
                });
                
                field.element.addEventListener('input', () => {
                    this.clearFieldError(field.element);
                });
            }
        });
    }
    
    // Validate individual field
    validateField(element, validateFn, message) {
        const value = element.value.trim();
        const isValid = validateFn(value);
        
        if (!isValid && value !== '') {
            this.showFieldError(element, message);
            return false;
        } else {
            this.clearFieldError(element);
            return true;
        }
    }
    
    // Show field error
    showFieldError(element, message) {
        this.clearFieldError(element);
        element.classList.add('is-invalid');
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        element.parentNode.appendChild(errorDiv);
    }
    
    // Clear field error
    clearFieldError(element) {
        element.classList.remove('is-invalid');
        const errorDiv = element.parentNode.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.remove();
        }
    }
    
    // Email validation
    validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Phone validation
    validatePhone(phone) {
        const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,15}$/;
        return phoneRegex.test(phone);
    }
    
    // Validate entire form
    validateForm() {
        let isValid = true;
        const formData = new FormData(this.form);
        
        // Required fields validation
        const requiredFields = ['firstName', 'lastName', 'email', 'subject', 'message'];
        requiredFields.forEach(fieldName => {
            const element = document.getElementById(fieldName);
            const value = formData.get(fieldName);
            
            if (!value || value.trim() === '') {
                this.showFieldError(element, 'This field is required');
                isValid = false;
            }
        });
        
        // Specific validation for each field
        const firstName = formData.get('firstName');
        if (firstName && (firstName.length < 2 || firstName.length > 50)) {
            this.showFieldError(document.getElementById('firstName'), 'First name must be between 2 and 50 characters');
            isValid = false;
        }
        
        const lastName = formData.get('lastName');
        if (lastName && (lastName.length < 2 || lastName.length > 50)) {
            this.showFieldError(document.getElementById('lastName'), 'Last name must be between 2 and 50 characters');
            isValid = false;
        }
        
        const email = formData.get('email');
        if (email && !this.validateEmail(email)) {
            this.showFieldError(document.getElementById('email'), 'Please enter a valid email address');
            isValid = false;
        }
        
        const phone = formData.get('phone');
        if (phone && !this.validatePhone(phone)) {
            this.showFieldError(document.getElementById('phone'), 'Please enter a valid phone number');
            isValid = false;
        }
        
        const subject = formData.get('subject');
        if (subject && (subject.length < 5 || subject.length > 200)) {
            this.showFieldError(document.getElementById('subject'), 'Subject must be between 5 and 200 characters');
            isValid = false;
        }
        
        const message = formData.get('message');
        if (message && (message.length < 10 || message.length > 1000)) {
            this.showFieldError(document.getElementById('message'), 'Message must be between 10 and 1000 characters');
            isValid = false;
        }
        
        return isValid;
    }
    
    // Handle form submission
    async handleSubmit(e) {
        e.preventDefault();
        
        // Clear any existing alerts
        this.clearAlerts();
        
        // Validate form
        if (!this.validateForm()) {
            this.showAlert('Please correct the errors above before submitting.', 'danger');
            return;
        }
        
        // Disable submit button and show loading state
        this.setLoadingState(true);
        
        try {
            // Prepare form data
            const formData = new FormData(this.form);
            const data = Object.fromEntries(formData);
            
            // Submit form data
            const response = await fetch('backend/submit_contact.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showAlert(result.message, 'success');
                this.form.reset();
                this.clearAllFieldErrors();
            } else {
                this.showAlert(result.message, 'danger');
            }
            
        } catch (error) {
            console.error('Form submission error:', error);
            this.showAlert('An error occurred while submitting the form. Please try again.', 'danger');
        } finally {
            this.setLoadingState(false);
        }
    }
    
    // Set loading state
    setLoadingState(isLoading) {
        if (isLoading) {
            this.submitBtn.disabled = true;
            this.submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Sending...';
        } else {
            this.submitBtn.disabled = false;
            this.submitBtn.textContent = this.originalBtnText;
        }
    }
    
    // Show alert message
    showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Insert alert before the form
        this.form.parentNode.insertBefore(alertDiv, this.form);
        
        // Auto-hide success alerts after 5 seconds
        if (type === 'success') {
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
    }
    
    // Clear all alerts
    clearAlerts() {
        const alerts = this.form.parentNode.querySelectorAll('.alert');
        alerts.forEach(alert => alert.remove());
    }
    
    // Clear all field errors
    clearAllFieldErrors() {
        const invalidFields = this.form.querySelectorAll('.is-invalid');
        invalidFields.forEach(field => this.clearFieldError(field));
    }
}

// Initialize contact form when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new ContactForm();
});

// Export for potential external use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ContactForm;
}
