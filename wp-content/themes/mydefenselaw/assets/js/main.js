/**
 * My Defense Law - Main JavaScript
 * WordPress-compatible version with proper AJAX integration
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

(function() {
    'use strict';

    // DOM Elements - declared at top level for access across functions
    let mobileMenuBtn, navigation, navMenu, modal, modalClose, modalForm, contactForm, header;

    /**
     * Initialize all functionality when DOM is ready
     */
    document.addEventListener('DOMContentLoaded', function() {
        // Cache DOM elements
        mobileMenuBtn = document.getElementById('mobileMenuBtn');
        navigation = document.getElementById('navigation');
        navMenu = navigation ? navigation.querySelector('.nav-menu') : null;
        modal = document.getElementById('consultationModal');
        modalClose = document.getElementById('modalClose');
        modalForm = document.getElementById('modalContactForm');
        contactForm = document.getElementById('contactForm');
        header = document.querySelector('.header');

        // Initialize all modules
        initMobileMenu();
        initSmoothScroll();
        initModal();
        initContactForm();
        initModalForm();
        initAnimations();
        initHeaderScroll();
        initFormValidation();
        initTestimonialSlider();

        // Set initial active nav link for home page
        const homeLink = document.querySelector('.nav-link[href="#home"]');
        if (homeLink && window.location.pathname === '/') {
            homeLink.classList.add('active');
        }

        console.log('Defense Lawyers website loaded successfully');
    });

    /**
     * Mobile Menu Toggle
     */
    function initMobileMenu() {
        if (!mobileMenuBtn || !navMenu) return;

        mobileMenuBtn.addEventListener('click', function() {
            var isExpanded = navMenu.classList.contains('active');
            navMenu.classList.toggle('active');
            mobileMenuBtn.classList.toggle('active');

            // Update ARIA state for accessibility
            mobileMenuBtn.setAttribute('aria-expanded', !isExpanded);

            // Animate hamburger menu
            var spans = mobileMenuBtn.querySelectorAll('span');
            if (mobileMenuBtn.classList.contains('active')) {
                spans[0].style.transform = 'rotate(45deg) translate(6px, 6px)';
                spans[1].style.opacity = '0';
                spans[2].style.transform = 'rotate(-45deg) translate(6px, -6px)';
            } else {
                resetMobileMenu(spans);
            }
        });

        // Handle resize events
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                // Close mobile menu on resize to desktop
                if (window.innerWidth > 768 && navMenu && mobileMenuBtn) {
                    navMenu.classList.remove('active');
                    mobileMenuBtn.classList.remove('active');
                    mobileMenuBtn.setAttribute('aria-expanded', 'false');
                    var spans = mobileMenuBtn.querySelectorAll('span');
                    resetMobileMenu(spans);
                }
            }, 250);
        });
    }

    /**
     * Reset mobile menu hamburger animation
     */
    function resetMobileMenu(spans) {
        if (spans && spans.length >= 3) {
            spans[0].style.transform = 'none';
            spans[1].style.opacity = '1';
            spans[2].style.transform = 'none';
        }
    }

    /**
     * Smooth scrolling for anchor links
     */
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');

                // Ignore empty hash links
                if (!href || href === '#') return;

                e.preventDefault();
                const target = document.querySelector(href);

                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });

                    // Close mobile menu if open
                    if (navMenu && navMenu.classList.contains('active')) {
                        navMenu.classList.remove('active');
                        if (mobileMenuBtn) {
                            mobileMenuBtn.classList.remove('active');
                            const spans = mobileMenuBtn.querySelectorAll('span');
                            resetMobileMenu(spans);
                        }
                    }
                }
            });
        });

        // Active navigation link highlighting on scroll
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.nav-link');

            let current = '';

            sections.forEach(function(section) {
                const sectionTop = section.offsetTop - 150;
                const sectionHeight = section.clientHeight;

                if (window.pageYOffset >= sectionTop && window.pageYOffset < sectionTop + sectionHeight) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(function(link) {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });
    }

    /**
     * Modal functionality
     */
    function initModal() {
        if (!modal) return;

        // All modal triggers - multiple selectors for different trigger elements
        const modalTriggers = document.querySelectorAll(
            '#modalTriggerHeader, #modalTriggerSticky, #modalTriggerHero, ' +
            '.btn-consultation, .consultation-trigger, .consult-btn'
        );

        // Open modal
        modalTriggers.forEach(function(trigger) {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                openModal();
            });
        });

        // Close modal handlers
        if (modalClose) {
            modalClose.addEventListener('click', closeModal);
        }

        // Close modal when clicking overlay
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.style.display === 'block') {
                closeModal();
            }
        });
    }

    // Store last focused element for focus restoration
    var lastFocusedElement = null;
    var focusableElements = null;

    /**
     * Open modal
     */
    function openModal() {
        if (!modal) return;

        // Store the element that had focus before opening
        lastFocusedElement = document.activeElement;

        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        modal.setAttribute('aria-hidden', 'false');

        // Get all focusable elements in modal for focus trap
        focusableElements = modal.querySelectorAll(
            'button, [href], input:not([type="hidden"]), select, textarea, [tabindex]:not([tabindex="-1"])'
        );

        // Focus on first input
        setTimeout(function() {
            var firstInput = modalForm ? modalForm.querySelector('input[name="firstName"]') : null;
            if (firstInput) {
                firstInput.focus();
            } else if (focusableElements.length > 0) {
                focusableElements[0].focus();
            }
        }, 300);

        // Add focus trap event listener
        document.addEventListener('keydown', trapFocus);
    }

    /**
     * Trap focus within modal (accessibility)
     */
    function trapFocus(e) {
        if (e.key !== 'Tab' || !focusableElements || focusableElements.length === 0) return;

        var firstElement = focusableElements[0];
        var lastElement = focusableElements[focusableElements.length - 1];

        if (e.shiftKey) {
            // Shift + Tab: if on first element, go to last
            if (document.activeElement === firstElement) {
                e.preventDefault();
                lastElement.focus();
            }
        } else {
            // Tab: if on last element, go to first
            if (document.activeElement === lastElement) {
                e.preventDefault();
                firstElement.focus();
            }
        }
    }

    /**
     * Close modal
     */
    function closeModal() {
        if (!modal) return;

        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        modal.setAttribute('aria-hidden', 'true');

        // Remove focus trap event listener
        document.removeEventListener('keydown', trapFocus);

        // Restore focus to the element that had it before modal opened
        if (lastFocusedElement) {
            lastFocusedElement.focus();
            lastFocusedElement = null;
        }
    }

    /**
     * Contact Form Handling with WordPress AJAX
     */
    function initContactForm() {
        if (!contactForm) return;

        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm(contactForm, document.getElementById('formResponse'));
        });
    }

    /**
     * Modal Form Handling with WordPress AJAX
     */
    function initModalForm() {
        if (!modalForm) return;

        modalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm(modalForm, document.getElementById('modalFormResponse'), function() {
                closeModal();
            });
        });
    }

    /**
     * Submit form via WordPress AJAX
     */
    function submitForm(form, responseEl, onSuccess) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        submitBtn.disabled = true;

        // Validate all required fields first
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(function(field) {
            if (!validateField(field)) {
                isValid = false;
            }
        });

        if (!isValid) {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            showNotification('Please fill in all required fields correctly.', 'error');
            return;
        }

        // Collect form data
        const formData = new FormData(form);

        // Use WordPress AJAX if available
        const ajaxUrl = (typeof mydefenselawData !== 'undefined')
            ? mydefenselawData.ajaxUrl
            : form.action;

        fetch(ajaxUrl, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                showNotification(data.data.message || 'Thank you! We\'ll contact you as soon as possible.', 'success');
                form.reset();

                if (responseEl) {
                    responseEl.innerHTML = '<div class="success-message">' + (data.data.message || 'Success!') + '</div>';
                    responseEl.style.display = 'block';
                }

                if (typeof onSuccess === 'function') {
                    onSuccess();
                }
            } else {
                throw new Error(data.data.message || 'Form submission failed');
            }
        })
        .catch(function(error) {
            console.error('Form submission error:', error);
            showNotification(error.message || 'Something went wrong. Please call us at 888.444.0253', 'error');

            if (responseEl) {
                responseEl.innerHTML = '<div class="error-message">' + (error.message || 'Error submitting form') + '</div>';
                responseEl.style.display = 'block';
            }
        })
        .finally(function() {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    }

    /**
     * Initialize animations with Intersection Observer
     */
    function initAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    entry.target.classList.add('animated');
                }
            });
        }, observerOptions);

        // Observe elements for animation
        document.querySelectorAll('.practice-area-card, .feature-item, .result-item').forEach(function(el) {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(el);
        });
    }

    /**
     * Header scroll effect
     */
    function initHeaderScroll() {
        if (!header) return;

        window.addEventListener('scroll', function() {
            const currentScrollY = window.scrollY;

            if (currentScrollY > 100) {
                header.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.15)';
                header.style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
                header.style.backdropFilter = 'blur(10px)';
            } else {
                header.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
                header.style.backgroundColor = 'white';
                header.style.backdropFilter = 'none';
            }
        });

        // Phone number click tracking
        document.querySelectorAll('a[href^="tel:"]').forEach(function(phoneLink) {
            phoneLink.addEventListener('click', function() {
                console.log('Phone number clicked:', phoneLink.href);
                // Add analytics tracking here
            });
        });
    }

    /**
     * Form validation initialization
     */
    function initFormValidation() {
        // Add real-time validation to both forms
        [contactForm, modalForm].forEach(function(form) {
            if (!form) return;

            const inputs = form.querySelectorAll('input, select, textarea');

            inputs.forEach(function(input) {
                input.addEventListener('blur', function() {
                    validateField(input);
                });

                input.addEventListener('input', function() {
                    clearFieldError(input);
                });
            });
        });
    }

    /**
     * Validate a single field
     */
    function validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';

        if (field.hasAttribute('required') && !value) {
            isValid = false;
            errorMessage = 'This field is required';
        } else if (field.type === 'email' && value && !validateEmail(value)) {
            isValid = false;
            errorMessage = 'Please enter a valid email address';
        } else if (field.type === 'tel' && value && !validatePhone(value)) {
            isValid = false;
            errorMessage = 'Please enter a valid phone number';
        } else if (field.type === 'checkbox' && field.hasAttribute('required') && !field.checked) {
            isValid = false;
            errorMessage = 'This checkbox is required';
        }

        if (!isValid) {
            showFieldError(field, errorMessage);
        } else {
            clearFieldError(field);
        }

        return isValid;
    }

    /**
     * Validate email format
     */
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    /**
     * Validate phone format - must contain at least 10 digits
     */
    function validatePhone(phone) {
        // Remove all non-digit characters and count digits
        var digitsOnly = phone.replace(/\D/g, '');
        // Must have 10-15 digits (valid phone number range)
        return digitsOnly.length >= 10 && digitsOnly.length <= 15;
    }

    /**
     * Show field error
     */
    function showFieldError(field, message) {
        clearFieldError(field);

        field.style.borderColor = '#dc2626';

        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.textContent = message;
        errorDiv.style.cssText = 'color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;';

        field.parentNode.appendChild(errorDiv);
    }

    /**
     * Clear field error
     */
    function clearFieldError(field) {
        field.style.borderColor = '#e2e8f0';
        const existingError = field.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
    }

    /**
     * Show notification toast
     */
    function showNotification(message, type) {
        type = type || 'info';

        const notification = document.createElement('div');
        notification.className = 'notification notification-' + type;
        notification.innerHTML =
            '<div class="notification-content">' +
                '<i class="fas fa-' + (type === 'success' ? 'check-circle' : 'exclamation-triangle') + '"></i>' +
                '<span>' + message + '</span>' +
                '<button class="notification-close">&times;</button>' +
            '</div>';

        // Add styles
        notification.style.cssText =
            'position: fixed; top: 20px; right: 20px; ' +
            'background: ' + (type === 'success' ? '#10b981' : '#dc2626') + '; ' +
            'color: white; padding: 1rem 1.5rem; border-radius: 8px; ' +
            'box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); z-index: 10000; ' +
            'max-width: 400px; transform: translateX(100%); transition: transform 0.3s ease;';

        notification.querySelector('.notification-content').style.cssText =
            'display: flex; align-items: center; gap: 0.75rem;';

        notification.querySelector('.notification-close').style.cssText =
            'background: none; border: none; color: white; font-size: 1.2rem; cursor: pointer; margin-left: auto;';

        document.body.appendChild(notification);

        // Animate in
        setTimeout(function() {
            notification.style.transform = 'translateX(0)';
        }, 100);

        // Handle close button
        notification.querySelector('.notification-close').addEventListener('click', function() {
            notification.style.transform = 'translateX(100%)';
            setTimeout(function() {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        });

        // Auto remove after 5 seconds
        setTimeout(function() {
            if (document.body.contains(notification)) {
                notification.style.transform = 'translateX(100%)';
                setTimeout(function() {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }
        }, 5000);
    }

    /**
     * Initialize testimonial slider
     */
    function initTestimonialSlider() {
        const slider = document.querySelector('.testimonial-slider');
        if (!slider) return;

        const slides = slider.querySelectorAll('.testimonial-slide');
        const dots = slider.querySelectorAll('.dot');
        const prevBtn = slider.querySelector('.testimonial-prev');
        const nextBtn = slider.querySelector('.testimonial-next');

        if (slides.length <= 1) return; // No need for slider with single testimonial

        let currentSlide = 0;
        let autoplayInterval;

        // Show specific slide
        function showSlide(index) {
            // Hide all slides
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));

            // Show target slide
            if (index >= slides.length) {
                currentSlide = 0;
            } else if (index < 0) {
                currentSlide = slides.length - 1;
            } else {
                currentSlide = index;
            }

            slides[currentSlide].classList.add('active');
            dots[currentSlide].classList.add('active');
        }

        // Next slide
        function nextSlide() {
            showSlide(currentSlide + 1);
        }

        // Previous slide
        function prevSlide() {
            showSlide(currentSlide - 1);
        }

        // Start autoplay
        function startAutoplay() {
            autoplayInterval = setInterval(nextSlide, 5000); // Change slide every 5 seconds
        }

        // Stop autoplay
        function stopAutoplay() {
            if (autoplayInterval) {
                clearInterval(autoplayInterval);
            }
        }

        // Event listeners
        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                stopAutoplay();
                prevSlide();
                startAutoplay();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                stopAutoplay();
                nextSlide();
                startAutoplay();
            });
        }

        // Dot navigation
        dots.forEach((dot, index) => {
            dot.addEventListener('click', function() {
                stopAutoplay();
                showSlide(index);
                startAutoplay();
            });
        });

        // Pause on hover
        slider.addEventListener('mouseenter', stopAutoplay);
        slider.addEventListener('mouseleave', startAutoplay);

        // Start autoplay
        startAutoplay();
    }

})();
