/* public/js/script.js */

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {

    // ========== Sidebar Toggle ==========
    const sidebarToggleBtn = document.querySelector('.navbar-toggler');
    const sidebar = document.getElementById('sidebarMenu');
    
    if (sidebarToggleBtn && sidebar) {
        sidebarToggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        const isClickInsideSidebar = sidebar && sidebar.contains(event.target);
        const isClickOnToggleBtn = sidebarToggleBtn && (sidebarToggleBtn.contains(event.target) || sidebarToggleBtn === event.target);
        
        if (sidebar && sidebar.classList.contains('show') && !isClickInsideSidebar && !isClickOnToggleBtn) {
            sidebar.classList.remove('show');
        }
    });

    // ========== Tooltips Initialization ==========
    // Initialize all tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    if (tooltipTriggerList.length > 0) {
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    }

    // ========== Popovers Initialization ==========
    // Initialize all popovers
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    if (popoverTriggerList.length > 0) {
        const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
    }

    // ========== Auto Close Alerts ==========
    // Auto close alerts after 5 seconds
    const autoCloseAlerts = document.querySelectorAll('.alert-dismissible.fade.show');
    if (autoCloseAlerts.length > 0) {
        autoCloseAlerts.forEach(function(alert) {
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    }

    // ========== Form Validation ==========
    // Add validation styles to forms
    const forms = document.querySelectorAll('.needs-validation');
    if (forms.length > 0) {
        Array.from(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                form.classList.add('was-validated');
            }, false);
        });
    }

    // ========== Confirm Delete Modals ==========
    // Handle confirm delete modals
    const deleteButtons = document.querySelectorAll('[data-confirm="delete"]');
    if (deleteButtons.length > 0) {
        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                
                const targetForm = this.closest('form');
                const itemName = this.getAttribute('data-item-name') || 'cet élément';
                
                if (confirm(`Êtes-vous sûr de vouloir supprimer ${itemName} ? Cette action est irréversible.`)) {
                    targetForm.submit();
                }
            });
        });
    }

    // ========== Datepicker Initialization ==========
    // Initialize date pickers if any (assuming Bootstrap Datepicker is used)
    const datepickers = document.querySelectorAll('.datepicker');
    if (typeof $.fn.datepicker !== 'undefined' && datepickers.length > 0) {
        $(datepickers).datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'fr'
        });
    }

    // ========== Select2 Initialization ==========
    // Initialize Select2 dropdowns if any
    const select2Dropdowns = document.querySelectorAll('.select2');
    if (typeof $.fn.select2 !== 'undefined' && select2Dropdowns.length > 0) {
        $(select2Dropdowns).select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }

    // ========== Tables Sortable ==========
    // Make tables sortable if they have the class 'sortable'
    const sortableTables = document.querySelectorAll('table.sortable');
    if (typeof $.fn.DataTable !== 'undefined' && sortableTables.length > 0) {
        $(sortableTables).DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
            }
        });
    }

    // ========== Theme Switching ==========
    // Handle theme switching if theme toggler exists
    const themeToggler = document.getElementById('themeToggler');
    if (themeToggler) {
        themeToggler.addEventListener('click', function() {
            document.body.classList.toggle('dark-theme');
            
            // Save preference to localStorage
            const isDarkTheme = document.body.classList.contains('dark-theme');
            localStorage.setItem('darkTheme', isDarkTheme);
            
            // Update toggle icon
            const icon = this.querySelector('i');
            if (isDarkTheme) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        });
        
        // Check localStorage for theme preference
        const savedTheme = localStorage.getItem('darkTheme');
        if (savedTheme === 'true') {
            document.body.classList.add('dark-theme');
            const icon = themeToggler.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            }
        }
    }

    // ========== Dynamic Height Adjustment ==========
    // Adjust content height to fill the viewport
    function adjustContentHeight() {
        const content = document.querySelector('main');
        const navbar = document.querySelector('.navbar');
        const footer = document.querySelector('.footer');
        
        if (content && navbar) {
            let navbarHeight = navbar.offsetHeight;
            let footerHeight = footer ? footer.offsetHeight : 0;
            let windowHeight = window.innerHeight;
            
            content.style.minHeight = (windowHeight - navbarHeight - footerHeight) + 'px';
        }
    }
    
    // Call on load and on resize
    adjustContentHeight();
    window.addEventListener('resize', adjustContentHeight);

    // ========== Print Functionality ==========
    // Handle print buttons
    const printButtons = document.querySelectorAll('.print-btn');
    if (printButtons.length > 0) {
        printButtons.forEach(function(button) {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                window.print();
            });
        });
    }

    // ========== Handle Back Button ==========
    // Add back navigation to back buttons
    const backButtons = document.querySelectorAll('.back-btn');
    if (backButtons.length > 0) {
        backButtons.forEach(function(button) {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                window.history.back();
            });
        });
    }

    // ========== Password Toggle ==========
    // Toggle password visibility
    const passwordTogglers = document.querySelectorAll('.password-toggle');
    if (passwordTogglers.length > 0) {
        passwordTogglers.forEach(function(toggler) {
            toggler.addEventListener('click', function() {
                const passwordInput = document.getElementById(this.getAttribute('data-target'));
                const icon = this.querySelector('i');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    }

    // ========== Form Dirty Check ==========
    // Warn when leaving page with unsaved changes
    const formsWithDirtyCheck = document.querySelectorAll('form.dirty-check');
    if (formsWithDirtyCheck.length > 0) {
        let formIsDirty = false;
        
        formsWithDirtyCheck.forEach(function(form) {
            const initialFormData = new FormData(form);
            const initialFormDataObj = Object.fromEntries(initialFormData.entries());
            
            form.addEventListener('change', function() {
                const currentFormData = new FormData(form);
                const currentFormDataObj = Object.fromEntries(currentFormData.entries());
                
                formIsDirty = JSON.stringify(initialFormDataObj) !== JSON.stringify(currentFormDataObj);
            });
            
            form.addEventListener('submit', function() {
                formIsDirty = false;
            });
        });
        
        window.addEventListener('beforeunload', function(event) {
            if (formIsDirty) {
                event.preventDefault();
                event.returnValue = 'Vous avez des modifications non enregistrées. Êtes-vous sûr de vouloir quitter cette page ?';
                return event.returnValue;
            }
        });
    }

    // ========== Custom File Input ==========
    // Show file name in custom file input
    const customFileInputs = document.querySelectorAll('.custom-file-input');
    if (customFileInputs.length > 0) {
        customFileInputs.forEach(function(input) {
            input.addEventListener('change', function(event) {
                const fileName = event.target.files[0].name;
                const label = input.nextElementSibling;
                
                if (label && label.classList.contains('custom-file-label')) {
                    label.textContent = fileName;
                }
            });
        });
    }

    // ========== Dynamic Textarea Height ==========
    // Auto-resize textareas
    const autoResizeTextareas = document.querySelectorAll('textarea.auto-resize');
    if (autoResizeTextareas.length > 0) {
        function resizeTextarea(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }
        
        autoResizeTextareas.forEach(function(textarea) {
            resizeTextarea(textarea);
            
            textarea.addEventListener('input', function() {
                resizeTextarea(this);
            });
        });
    }

    // ========== Handle AJAX Forms ==========
    // Submit forms via AJAX
    const ajaxForms = document.querySelectorAll('form.ajax-form');
    if (ajaxForms.length > 0) {
        ajaxForms.forEach(function(form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                
                const formData = new FormData(form);
                const url = form.getAttribute('action') || window.location.href;
                const method = form.getAttribute('method') || 'POST';
                
                // Show loading state
                form.classList.add('loading');
                
                // Disable submit button
                const submitButton = form.querySelector('[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                }
                
                // Send AJAX request
                fetch(url, {
                    method: method,
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Handle response
                    if (data.status === 'success') {
                        // Show success message
                        if (data.message) {
                            showNotification(data.message, 'success');
                        }
                        
                        // Redirect if needed
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                        
                        // Reset form if needed
                        if (data.reset) {
                            form.reset();
                        }
                    } else {
                        // Show error message
                        if (data.message) {
                            showNotification(data.message, 'error');
                        }
                        
                        // Show validation errors
                        if (data.errors) {
                            for (const [field, error] of Object.entries(data.errors)) {
                                const input = form.querySelector(`[name="${field}"]`);
                                const feedbackEl = input?.nextElementSibling;
                                
                                if (input) {
                                    input.classList.add('is-invalid');
                                    
                                    if (feedbackEl && feedbackEl.classList.contains('invalid-feedback')) {
                                        feedbackEl.textContent = error;
                                    } else {
                                        // Create feedback element if it doesn't exist
                                        const newFeedbackEl = document.createElement('div');
                                        newFeedbackEl.classList.add('invalid-feedback');
                                        newFeedbackEl.textContent = error;
                                        input.parentNode.insertBefore(newFeedbackEl, input.nextSibling);
                                    }
                                }
                            }
                        }
                    }
                })
                .catch(error => {
                    // Handle error
                    showNotification('Une erreur s\'est produite. Veuillez réessayer.', 'error');
                    console.error('Error:', error);
                })
                .finally(() => {
                    // Remove loading state
                    form.classList.remove('loading');
                    
                    // Re-enable submit button
                    if (submitButton) {
                        submitButton.disabled = false;
                    }
                });
            });
        });
    }

    // ========== Notification Helper ==========
    // Show notification
    function showNotification(message, type = 'info') {
        // Check if the notification container exists
        let notificationContainer = document.getElementById('notification-container');
        
        // Create container if it doesn't exist
        if (!notificationContainer) {
            notificationContainer = document.createElement('div');
            notificationContainer.id = 'notification-container';
            notificationContainer.className = 'notification-container';
            document.body.appendChild(notificationContainer);
        }
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        
        // Create message element
        const messageEl = document.createElement('div');
        messageEl.className = 'notification-message';
        messageEl.textContent = message;
        notification.appendChild(messageEl);
        
        // Create close button
        const closeBtn = document.createElement('button');
        closeBtn.className = 'notification-close';
        closeBtn.innerHTML = '&times;';
        closeBtn.addEventListener('click', function() {
            notification.remove();
        });
        notification.appendChild(closeBtn);
        
        // Add notification to container
        notificationContainer.appendChild(notification);
        
        // Auto-remove notification after 5 seconds
        setTimeout(function() {
            notification.classList.add('notification-fade-out');
            
            // Remove from DOM after fade out animation
            setTimeout(function() {
                notification.remove();
            }, 300);
        }, 5000);
    }
});