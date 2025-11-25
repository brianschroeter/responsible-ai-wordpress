/**
 * MyDefenseLaw Client Portal JavaScript
 * Comprehensive interactive functionality for the enhanced modern client portal
 * Version: 1.0.0
 */
(function() {
    'use strict';

    // Portal namespace
    const Portal = {
        // Configuration
        config: {
            ajaxUrl: typeof portalData !== 'undefined' ? portalData.ajaxUrl : '/wp-admin/admin-ajax.php',
            nonces: typeof portalData !== 'undefined' ? portalData.nonces : {},
            pollInterval: 30000, // 30 seconds
            sessionTimeout: 30 * 60 * 1000, // 30 minutes
            sessionWarning: 25 * 60 * 1000, // 25 minutes
        },

        // State management
        state: {
            sessionTimers: {
                timeout: null,
                warning: null
            },
            activeModal: null,
            uploadQueue: [],
            currentThread: null
        },

        /**
         * Initialize all portal modules
         */
        init: function() {
            console.log('Portal: Initializing...');

            this.initNavigation();
            this.initUploadZone();
            this.initDocuments();
            this.initMessages();
            this.initProfile();
            this.initModals();
            this.initToasts();
            this.initSessionTimeout();
            this.initUnreadPolling();
            this.initFormValidation();

            console.log('Portal: Initialized successfully');
        },

        /**
         * Navigation Management
         */
        initNavigation: function() {
            // Mobile menu toggle
            const mobileToggle = document.querySelector('.portal-nav-toggle');
            const navMenu = document.querySelector('.portal-nav-menu');

            if (mobileToggle && navMenu) {
                mobileToggle.addEventListener('click', () => {
                    navMenu.classList.toggle('active');
                });
            }

            // Active state management
            const navLinks = document.querySelectorAll('.portal-nav-link');
            const currentPath = window.location.pathname;

            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.parentElement.classList.add('active');
                }
            });
        },

        /**
         * Drag & Drop Upload Zone
         */
        initUploadZone: function() {
            const zone = document.querySelector('.upload-zone');
            if (!zone) return;

            const input = zone.querySelector('input[type="file"]');
            const browseBtn = zone.querySelector('.upload-zone-browse');

            // Prevent defaults
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(event => {
                zone.addEventListener(event, this.preventDefaults.bind(this));
            });

            // Highlight on drag
            ['dragenter', 'dragover'].forEach(event => {
                zone.addEventListener(event, () => zone.classList.add('drag-over'));
            });

            // Remove highlight
            ['dragleave', 'drop'].forEach(event => {
                zone.addEventListener(event, () => zone.classList.remove('drag-over'));
            });

            // Handle drop
            zone.addEventListener('drop', this.handleDrop.bind(this));

            // Handle browse click
            if (browseBtn) {
                browseBtn.addEventListener('click', () => input?.click());
            }

            // Handle file input change
            if (input) {
                input.addEventListener('change', this.handleFileSelect.bind(this));
            }
        },

        preventDefaults: function(e) {
            e.preventDefault();
            e.stopPropagation();
        },

        handleDrop: function(e) {
            const files = e.dataTransfer.files;
            this.uploadFiles(files);
        },

        handleFileSelect: function(e) {
            const files = e.target.files;
            this.uploadFiles(files);
        },

        uploadFiles: function(files) {
            if (!files || files.length === 0) return;

            const progressBar = document.querySelector('.upload-progress');
            const progressFill = document.querySelector('.upload-progress-fill');
            const progressText = document.querySelector('.upload-progress-text');

            if (progressBar) {
                progressBar.classList.add('active');
            }

            // Upload each file
            Array.from(files).forEach((file, index) => {
                const formData = new FormData();
                formData.append('action', 'portal_upload_document');
                formData.append('nonce', this.config.nonces.upload_document || '');
                formData.append('file', file);

                // Get folder from dropdown if exists
                const folderSelect = document.querySelector('#document-folder');
                if (folderSelect) {
                    formData.append('folder', folderSelect.value);
                }

                const xhr = new XMLHttpRequest();

                // Progress tracking
                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        const percent = (e.loaded / e.total) * 100;
                        if (progressFill) {
                            progressFill.style.width = percent + '%';
                        }
                        if (progressText) {
                            progressText.textContent = `Uploading ${file.name}: ${Math.round(percent)}%`;
                        }
                    }
                });

                // Success/Error handling
                xhr.addEventListener('load', () => {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                this.showToast(`${file.name} uploaded successfully`, 'success');

                                // Reload documents list
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            } else {
                                this.showToast(response.data?.message || 'Upload failed', 'error');
                            }
                        } catch (error) {
                            this.showToast('Upload error: Invalid response', 'error');
                        }
                    } else {
                        this.showToast('Upload failed: Server error', 'error');
                    }

                    // Reset progress
                    if (index === files.length - 1) {
                        setTimeout(() => {
                            if (progressBar) progressBar.classList.remove('active');
                            if (progressFill) progressFill.style.width = '0%';
                            if (progressText) progressText.textContent = '';
                        }, 1000);
                    }
                });

                xhr.addEventListener('error', () => {
                    this.showToast(`Failed to upload ${file.name}`, 'error');
                });

                xhr.open('POST', this.config.ajaxUrl);
                xhr.send(formData);
            });
        },

        /**
         * Document Operations
         */
        initDocuments: function() {
            // View toggle (grid/list)
            const gridBtn = document.querySelector('[data-view="grid"]');
            const listBtn = document.querySelector('[data-view="list"]');
            const documentsContainer = document.querySelector('.documents-container');

            if (gridBtn && listBtn && documentsContainer) {
                gridBtn.addEventListener('click', () => {
                    gridBtn.classList.add('active');
                    listBtn.classList.remove('active');
                    documentsContainer.className = 'documents-container documents-grid';
                });

                listBtn.addEventListener('click', () => {
                    listBtn.classList.add('active');
                    gridBtn.classList.remove('active');
                    documentsContainer.className = 'documents-container documents-list';
                });
            }

            // Delete document handlers
            const deleteButtons = document.querySelectorAll('.document-delete-btn');
            deleteButtons.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    const documentId = btn.dataset.documentId;
                    const documentName = btn.dataset.documentName;

                    if (confirm(`Are you sure you want to delete "${documentName}"?`)) {
                        this.deleteDocument(documentId);
                    }
                });
            });

            // Document preview handlers
            const documentCards = document.querySelectorAll('.document-card, .document-list-item');
            documentCards.forEach(card => {
                card.addEventListener('click', (e) => {
                    // Don't trigger if clicking action buttons
                    if (e.target.closest('.document-action-btn')) return;

                    const documentUrl = card.dataset.documentUrl;
                    if (documentUrl) {
                        window.open(documentUrl, '_blank');
                    }
                });
            });

            // Folder navigation
            const folderItems = document.querySelectorAll('.folder-item');
            folderItems.forEach(item => {
                item.addEventListener('click', () => {
                    // Remove active from all
                    folderItems.forEach(f => f.classList.remove('active'));
                    // Add active to clicked
                    item.classList.add('active');

                    // Filter documents by folder
                    const folderId = item.dataset.folderId;
                    this.filterDocumentsByFolder(folderId);
                });
            });
        },

        deleteDocument: function(documentId) {
            this.ajax('portal_delete_document', { document_id: documentId })
                .then(response => {
                    if (response.success) {
                        this.showToast('Document deleted successfully', 'success');

                        // Remove document from DOM
                        const documentElement = document.querySelector(`[data-document-id="${documentId}"]`);
                        if (documentElement) {
                            documentElement.closest('.document-card, .document-list-item').remove();
                        }
                    } else {
                        this.showToast(response.data?.message || 'Failed to delete document', 'error');
                    }
                })
                .catch(error => {
                    console.error('Delete error:', error);
                    this.showToast('Error deleting document', 'error');
                });
        },

        filterDocumentsByFolder: function(folderId) {
            const documents = document.querySelectorAll('.document-card, .document-list-item');

            documents.forEach(doc => {
                const docFolder = doc.dataset.folderId;

                if (folderId === 'all' || docFolder === folderId) {
                    doc.style.display = '';
                } else {
                    doc.style.display = 'none';
                }
            });
        },

        /**
         * Message Operations
         */
        initMessages: function() {
            // Thread click handlers
            const threadItems = document.querySelectorAll('.thread-item');

            threadItems.forEach(item => {
                item.addEventListener('click', () => {
                    // Mark as active
                    threadItems.forEach(t => t.classList.remove('active'));
                    item.classList.add('active');

                    // Remove unread state
                    item.classList.remove('unread');

                    // Load conversation
                    const threadId = item.dataset.threadId;
                    this.loadConversation(threadId);
                });
            });

            // Reply form submission
            const replyForm = document.querySelector('.reply-form');
            if (replyForm) {
                replyForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.sendReply();
                });
            }

            // Compose button
            const composeBtn = document.querySelector('.compose-btn');
            if (composeBtn) {
                composeBtn.addEventListener('click', () => {
                    this.openModal('compose-modal');
                });
            }

            // Compose form submission
            const composeForm = document.querySelector('#compose-form');
            if (composeForm) {
                composeForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.sendNewMessage();
                });
            }
        },

        loadConversation: function(threadId) {
            this.state.currentThread = threadId;

            this.ajax('portal_get_conversation', { thread_id: threadId })
                .then(response => {
                    if (response.success) {
                        this.renderConversation(response.data);

                        // Mark as read
                        this.markThreadAsRead(threadId);
                    } else {
                        this.showToast('Failed to load conversation', 'error');
                    }
                })
                .catch(error => {
                    console.error('Load conversation error:', error);
                    this.showToast('Error loading conversation', 'error');
                });
        },

        renderConversation: function(data) {
            const conversationHeader = document.querySelector('.conversation-header');
            const conversationMessages = document.querySelector('.conversation-messages');

            if (!conversationHeader || !conversationMessages) return;

            // Update header
            conversationHeader.querySelector('.conversation-subject').textContent = data.subject;
            conversationHeader.querySelector('.conversation-meta').textContent =
                `${data.participants.join(', ')} • ${data.message_count} messages`;

            // Render messages
            conversationMessages.innerHTML = data.messages.map(msg => `
                <div class="message-bubble ${msg.sender_type === 'client' ? 'from-client' : 'from-staff'}">
                    <div class="message-header">
                        <span class="message-sender">${msg.sender_name}</span>
                        <span class="message-time">${msg.time}</span>
                    </div>
                    <div class="message-content">${msg.content}</div>
                </div>
            `).join('');

            // Scroll to bottom
            conversationMessages.scrollTop = conversationMessages.scrollHeight;
        },

        sendReply: function() {
            const textarea = document.querySelector('.reply-form textarea');
            const submitBtn = document.querySelector('.reply-submit');

            if (!textarea || !this.state.currentThread) return;

            const message = textarea.value.trim();
            if (!message) {
                this.showToast('Please enter a message', 'warning');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';

            this.ajax('portal_send_reply', {
                thread_id: this.state.currentThread,
                message: message
            })
                .then(response => {
                    if (response.success) {
                        this.showToast('Reply sent successfully', 'success');
                        textarea.value = '';

                        // Reload conversation
                        this.loadConversation(this.state.currentThread);
                    } else {
                        this.showToast(response.data?.message || 'Failed to send reply', 'error');
                    }
                })
                .catch(error => {
                    console.error('Send reply error:', error);
                    this.showToast('Error sending reply', 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Send Reply';
                });
        },

        sendNewMessage: function() {
            const form = document.querySelector('#compose-form');
            const submitBtn = form.querySelector('[type="submit"]');

            const formData = new FormData(form);

            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';

            this.ajax('portal_send_message', {
                subject: formData.get('subject'),
                message: formData.get('message')
            })
                .then(response => {
                    if (response.success) {
                        this.showToast('Message sent successfully', 'success');
                        this.closeModal('compose-modal');
                        form.reset();

                        // Reload page to show new message
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        this.showToast(response.data?.message || 'Failed to send message', 'error');
                    }
                })
                .catch(error => {
                    console.error('Send message error:', error);
                    this.showToast('Error sending message', 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Send Message';
                });
        },

        markThreadAsRead: function(threadId) {
            this.ajax('portal_mark_read', { thread_id: threadId })
                .then(() => {
                    this.updateUnreadCount();
                })
                .catch(error => {
                    console.error('Mark read error:', error);
                });
        },

        /**
         * Profile Operations
         */
        initProfile: function() {
            // Profile form submission
            const profileForm = document.querySelector('#profile-form');
            if (profileForm) {
                profileForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.updateProfile();
                });
            }

            // Password change form
            const passwordForm = document.querySelector('#password-form');
            if (passwordForm) {
                passwordForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.changePassword();
                });
            }

            // Password visibility toggles
            const passwordToggles = document.querySelectorAll('.password-toggle');
            passwordToggles.forEach(toggle => {
                toggle.addEventListener('click', () => {
                    const input = toggle.previousElementSibling;
                    if (input.type === 'password') {
                        input.type = 'text';
                        toggle.innerHTML = '<i class="fas fa-eye-slash"></i>';
                    } else {
                        input.type = 'password';
                        toggle.innerHTML = '<i class="fas fa-eye"></i>';
                    }
                });
            });

            // Password strength indicator
            const newPassword = document.querySelector('#new-password');
            if (newPassword) {
                newPassword.addEventListener('input', (e) => {
                    this.updatePasswordStrength(e.target.value);
                });
            }

            // Notification preferences
            const notificationCheckboxes = document.querySelectorAll('.notification-checkbox input');
            notificationCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    this.updateNotificationPrefs();
                });
            });
        },

        updateProfile: function() {
            const form = document.querySelector('#profile-form');
            const submitBtn = form.querySelector('[type="submit"]');
            const formData = new FormData(form);

            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';

            this.ajax('portal_update_profile', {
                first_name: formData.get('first_name'),
                last_name: formData.get('last_name'),
                email: formData.get('email'),
                phone: formData.get('phone')
            })
                .then(response => {
                    if (response.success) {
                        this.showToast('Profile updated successfully', 'success');
                    } else {
                        this.showToast(response.data?.message || 'Failed to update profile', 'error');
                    }
                })
                .catch(error => {
                    console.error('Update profile error:', error);
                    this.showToast('Error updating profile', 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Save Changes';
                });
        },

        changePassword: function() {
            const form = document.querySelector('#password-form');
            const submitBtn = form.querySelector('[type="submit"]');
            const formData = new FormData(form);

            const currentPassword = formData.get('current_password');
            const newPassword = formData.get('new_password');
            const confirmPassword = formData.get('confirm_password');

            // Validation
            if (!currentPassword || !newPassword || !confirmPassword) {
                this.showToast('Please fill in all password fields', 'warning');
                return;
            }

            if (newPassword !== confirmPassword) {
                this.showToast('New passwords do not match', 'error');
                return;
            }

            if (newPassword.length < 8) {
                this.showToast('Password must be at least 8 characters', 'error');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.textContent = 'Changing...';

            this.ajax('portal_change_password', {
                current_password: currentPassword,
                new_password: newPassword
            })
                .then(response => {
                    if (response.success) {
                        this.showToast('Password changed successfully', 'success');
                        form.reset();
                    } else {
                        this.showToast(response.data?.message || 'Failed to change password', 'error');
                    }
                })
                .catch(error => {
                    console.error('Change password error:', error);
                    this.showToast('Error changing password', 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Change Password';
                });
        },

        updatePasswordStrength: function(password) {
            const strengthBar = document.querySelector('.password-strength-bar');
            if (!strengthBar) return;

            let strength = 0;

            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            strengthBar.className = 'password-strength-bar';

            if (strength <= 2) {
                strengthBar.classList.add('weak');
            } else if (strength <= 4) {
                strengthBar.classList.add('medium');
            } else {
                strengthBar.classList.add('strong');
            }
        },

        updateNotificationPrefs: function() {
            const checkboxes = document.querySelectorAll('.notification-checkbox input');
            const prefs = {};

            checkboxes.forEach(checkbox => {
                prefs[checkbox.name] = checkbox.checked;
            });

            this.ajax('portal_update_notifications', { preferences: prefs })
                .then(response => {
                    if (response.success) {
                        this.showToast('Notification preferences updated', 'success');
                    }
                })
                .catch(error => {
                    console.error('Update notifications error:', error);
                });
        },

        /**
         * Modal Management
         */
        initModals: function() {
            // Close on overlay click
            document.addEventListener('click', (e) => {
                if (e.target.classList.contains('portal-modal-overlay')) {
                    this.closeModal(this.state.activeModal);
                }
            });

            // Close on X button click
            const closeButtons = document.querySelectorAll('.portal-modal-close, [data-modal-close]');
            closeButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const modal = btn.closest('.portal-modal-overlay');
                    if (modal) {
                        this.closeModal(modal.id);
                    }
                });
            });

            // Escape key to close
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.state.activeModal) {
                    this.closeModal(this.state.activeModal);
                }
            });

            // Open modal triggers
            const modalTriggers = document.querySelectorAll('[data-modal]');
            modalTriggers.forEach(trigger => {
                trigger.addEventListener('click', () => {
                    const modalId = trigger.dataset.modal;
                    this.openModal(modalId);
                });
            });
        },

        openModal: function(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;

            modal.classList.add('active');
            document.body.classList.add('modal-open');
            this.state.activeModal = modalId;

            // Focus trap
            const focusableElements = modal.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );

            if (focusableElements.length > 0) {
                focusableElements[0].focus();
            }
        },

        closeModal: function(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;

            modal.classList.remove('active');
            document.body.classList.remove('modal-open');
            this.state.activeModal = null;
        },

        /**
         * Toast Notifications
         */
        initToasts: function() {
            // Create toast container if it doesn't exist
            if (!document.querySelector('.portal-toast-container')) {
                const container = document.createElement('div');
                container.className = 'portal-toast-container';
                document.body.appendChild(container);
            }
        },

        showToast: function(message, type = 'success') {
            const container = document.querySelector('.portal-toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `portal-toast ${type}`;

            const iconMap = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };

            toast.innerHTML = `
                <span class="toast-icon"><i class="fas ${iconMap[type] || iconMap.info}"></i></span>
                <span class="toast-message">${message}</span>
                <button class="toast-close">&times;</button>
            `;

            container.appendChild(toast);

            // Auto remove after 5 seconds
            const autoRemoveTimeout = setTimeout(() => {
                toast.remove();
            }, 5000);

            // Manual close
            const closeBtn = toast.querySelector('.toast-close');
            closeBtn.addEventListener('click', () => {
                clearTimeout(autoRemoveTimeout);
                toast.remove();
            });
        },

        /**
         * Session Timeout Warning
         */
        initSessionTimeout: function() {
            const resetTimer = () => {
                // Clear existing timers
                clearTimeout(this.state.sessionTimers.timeout);
                clearTimeout(this.state.sessionTimers.warning);

                // Warning at 25 minutes
                this.state.sessionTimers.warning = setTimeout(() => {
                    this.showToast(
                        'Your session will expire in 5 minutes. Please save your work.',
                        'warning'
                    );
                }, this.config.sessionWarning);

                // Logout at 30 minutes
                this.state.sessionTimers.timeout = setTimeout(() => {
                    window.location.href = window.location.origin + '/portal/login/?expired=1';
                }, this.config.sessionTimeout);
            };

            // Reset on user activity
            ['mousemove', 'keypress', 'click', 'scroll', 'touchstart'].forEach(event => {
                document.addEventListener(event, resetTimer, { passive: true });
            });

            resetTimer();
        },

        /**
         * Poll for Unread Messages
         */
        initUnreadPolling: function() {
            // Initial check
            this.updateUnreadCount();

            // Poll every 30 seconds
            setInterval(() => {
                this.updateUnreadCount();
            }, this.config.pollInterval);
        },

        updateUnreadCount: function() {
            this.ajax('portal_get_unread_count')
                .then(response => {
                    if (response.success && response.data) {
                        const badge = document.querySelector('.portal-nav-badge');
                        if (badge) {
                            badge.textContent = response.data.count;
                            badge.style.display = response.data.count > 0 ? 'inline' : 'none';
                        }
                    }
                })
                .catch(error => {
                    console.error('Unread count error:', error);
                });
        },

        /**
         * Form Validation
         */
        initFormValidation: function() {
            const forms = document.querySelectorAll('form[data-validate]');

            forms.forEach(form => {
                form.addEventListener('submit', (e) => {
                    if (!this.validateForm(form)) {
                        e.preventDefault();
                    }
                });

                // Real-time validation
                const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
                inputs.forEach(input => {
                    input.addEventListener('blur', () => {
                        this.validateField(input);
                    });
                });
            });
        },

        validateForm: function(form) {
            let isValid = true;
            const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');

            inputs.forEach(input => {
                if (!this.validateField(input)) {
                    isValid = false;
                }
            });

            return isValid;
        },

        validateField: function(field) {
            const value = field.value.trim();
            let isValid = true;
            let errorMessage = '';

            // Required check
            if (field.hasAttribute('required') && !value) {
                isValid = false;
                errorMessage = 'This field is required';
            }

            // Email validation
            if (field.type === 'email' && value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    isValid = false;
                    errorMessage = 'Please enter a valid email address';
                }
            }

            // Min length
            if (field.hasAttribute('minlength')) {
                const minLength = parseInt(field.getAttribute('minlength'));
                if (value.length < minLength) {
                    isValid = false;
                    errorMessage = `Must be at least ${minLength} characters`;
                }
            }

            // Update UI
            if (isValid) {
                field.classList.remove('field-error');
                const errorEl = field.parentElement.querySelector('.field-error-message');
                if (errorEl) errorEl.remove();
            } else {
                field.classList.add('field-error');

                // Remove existing error message
                const existingError = field.parentElement.querySelector('.field-error-message');
                if (existingError) existingError.remove();

                // Add new error message
                const errorEl = document.createElement('span');
                errorEl.className = 'field-error-message';
                errorEl.textContent = errorMessage;
                field.parentElement.appendChild(errorEl);
            }

            return isValid;
        },

        /**
         * AJAX Helper
         */
        ajax: function(action, data = {}) {
            const formData = new FormData();
            formData.append('action', action);

            // Add nonce if available
            const nonceKey = action.replace('portal_', '');
            if (this.config.nonces[nonceKey]) {
                formData.append('nonce', this.config.nonces[nonceKey]);
            }

            // Add data
            for (const key in data) {
                if (typeof data[key] === 'object') {
                    formData.append(key, JSON.stringify(data[key]));
                } else {
                    formData.append(key, data[key]);
                }
            }

            return fetch(this.config.ajaxUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            });
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => Portal.init());
    } else {
        Portal.init();
    }

    // Expose Portal globally for debugging
    window.Portal = Portal;
})();
