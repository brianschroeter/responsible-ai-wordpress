/**
 * Responsible AI Theme - Main JavaScript
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

(function() {
    'use strict';

    // ==========================================================================
    // Configuration
    // ==========================================================================

    const config = {
        selectors: {
            header: '#site-header',
            nav: '#site-nav',
            mobileToggle: '#mobile-menu-toggle',
            heroCarousel: '.hero-carousel',
            logoWall: '.logo-wall',
            testimonials: '.testimonials-carousel',
            flipCards: '.flip-card',
            tabs: '.tabs',
            animateOnScroll: '[data-animate]',
            contactForm: '.contact-form',
            newsletterForm: '.newsletter-form',
        },
        classes: {
            scrolled: 'is-scrolled',
            navOpen: 'nav-open',
            active: 'active',
            visible: 'is-visible',
            flipped: 'is-flipped',
        },
        breakpoints: {
            mobile: 640,
            tablet: 768,
            desktop: 1024,
        }
    };

    // ==========================================================================
    // Navigation
    // ==========================================================================

    const Navigation = {
        header: null,
        nav: null,
        toggle: null,
        isOpen: false,

        init() {
            this.header = document.querySelector(config.selectors.header);
            this.nav = document.querySelector(config.selectors.nav);
            this.toggle = document.querySelector(config.selectors.mobileToggle);

            if (!this.header) return;

            this.bindEvents();
            this.handleScroll();
        },

        bindEvents() {
            // Scroll handling for sticky header
            window.addEventListener('scroll', () => this.handleScroll(), { passive: true });

            // Mobile menu toggle
            if (this.toggle) {
                this.toggle.addEventListener('click', () => this.toggleMobileMenu());
            }

            // Close mobile menu on link click
            if (this.nav) {
                this.nav.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', () => this.closeMobileMenu());
                });
            }

            // Close mobile menu on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.isOpen) {
                    this.closeMobileMenu();
                }
            });

            // Close mobile menu on outside click
            document.addEventListener('click', (e) => {
                if (this.isOpen && !this.nav.contains(e.target) && !this.toggle.contains(e.target)) {
                    this.closeMobileMenu();
                }
            });
        },

        handleScroll() {
            const scrollY = window.scrollY;
            const threshold = 50;

            if (scrollY > threshold) {
                this.header.classList.add(config.classes.scrolled);
            } else {
                this.header.classList.remove(config.classes.scrolled);
            }
        },

        toggleMobileMenu() {
            this.isOpen = !this.isOpen;
            document.body.classList.toggle(config.classes.navOpen, this.isOpen);
            this.toggle.setAttribute('aria-expanded', this.isOpen);
        },

        closeMobileMenu() {
            this.isOpen = false;
            document.body.classList.remove(config.classes.navOpen);
            if (this.toggle) {
                this.toggle.setAttribute('aria-expanded', 'false');
            }
        }
    };

    // ==========================================================================
    // Hero Carousel
    // ==========================================================================

    const HeroCarousel = {
        init() {
            const carousel = document.querySelector(config.selectors.heroCarousel);
            if (!carousel || typeof Swiper === 'undefined') return;

            new Swiper(carousel, {
                loop: true,
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
        }
    };

    // ==========================================================================
    // Logo Wall Carousel
    // ==========================================================================

    const LogoWall = {
        init() {
            const logoWall = document.querySelector(config.selectors.logoWall);
            if (!logoWall || typeof Swiper === 'undefined') return;

            new Swiper(logoWall, {
                loop: true,
                slidesPerView: 2,
                spaceBetween: 30,
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                },
                breakpoints: {
                    640: { slidesPerView: 3 },
                    768: { slidesPerView: 4 },
                    1024: { slidesPerView: 5 },
                    1280: { slidesPerView: 6 },
                },
            });
        }
    };

    // ==========================================================================
    // Testimonials Carousel
    // ==========================================================================

    const Testimonials = {
        init() {
            const testimonials = document.querySelector(config.selectors.testimonials);
            if (!testimonials || typeof Swiper === 'undefined') return;

            new Swiper(testimonials, {
                loop: true,
                slidesPerView: 1,
                spaceBetween: 30,
                autoplay: {
                    delay: 6000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                breakpoints: {
                    768: { slidesPerView: 2 },
                    1024: { slidesPerView: 3 },
                },
            });
        }
    };

    // ==========================================================================
    // Flip Cards
    // ==========================================================================

    const FlipCards = {
        init() {
            const cards = document.querySelectorAll(config.selectors.flipCards);
            if (!cards.length) return;

            cards.forEach(card => {
                // Touch support - tap to flip
                card.addEventListener('click', (e) => {
                    if (window.innerWidth <= config.breakpoints.tablet) {
                        card.classList.toggle(config.classes.flipped);
                    }
                });

                // Keyboard support
                card.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        card.classList.toggle(config.classes.flipped);
                    }
                });
            });
        }
    };

    // ==========================================================================
    // Tabs
    // ==========================================================================

    const Tabs = {
        init() {
            const tabContainers = document.querySelectorAll(config.selectors.tabs);
            if (!tabContainers.length) return;

            tabContainers.forEach(container => {
                const buttons = container.querySelectorAll('[role="tab"]');
                const panels = container.querySelectorAll('[role="tabpanel"]');

                buttons.forEach(button => {
                    button.addEventListener('click', () => {
                        this.switchTab(button, buttons, panels);
                    });

                    button.addEventListener('keydown', (e) => {
                        this.handleKeyboard(e, button, buttons, panels);
                    });
                });
            });
        },

        switchTab(button, buttons, panels) {
            // Deactivate all
            buttons.forEach(btn => {
                btn.setAttribute('aria-selected', 'false');
                btn.classList.remove(config.classes.active);
            });
            panels.forEach(panel => panel.hidden = true);

            // Activate selected
            button.setAttribute('aria-selected', 'true');
            button.classList.add(config.classes.active);

            const panelId = button.getAttribute('aria-controls');
            const panel = document.getElementById(panelId);
            if (panel) {
                panel.hidden = false;
            }
        },

        handleKeyboard(e, currentButton, buttons, panels) {
            const buttonArray = Array.from(buttons);
            const currentIndex = buttonArray.indexOf(currentButton);
            let newIndex;

            switch (e.key) {
                case 'ArrowLeft':
                case 'ArrowUp':
                    newIndex = currentIndex === 0 ? buttonArray.length - 1 : currentIndex - 1;
                    break;
                case 'ArrowRight':
                case 'ArrowDown':
                    newIndex = currentIndex === buttonArray.length - 1 ? 0 : currentIndex + 1;
                    break;
                case 'Home':
                    newIndex = 0;
                    break;
                case 'End':
                    newIndex = buttonArray.length - 1;
                    break;
                default:
                    return;
            }

            e.preventDefault();
            buttonArray[newIndex].focus();
            this.switchTab(buttonArray[newIndex], buttons, panels);
        }
    };

    // ==========================================================================
    // Scroll Animations (Intersection Observer)
    // ==========================================================================

    const ScrollAnimations = {
        init() {
            const elements = document.querySelectorAll(config.selectors.animateOnScroll);
            if (!elements.length) return;

            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add(config.classes.visible);
                            observer.unobserve(entry.target);
                        }
                    });
                },
                {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                }
            );

            elements.forEach(el => observer.observe(el));
        }
    };

    // ==========================================================================
    // Forms
    // ==========================================================================

    const Forms = {
        init() {
            this.initContactForm();
            this.initNewsletterForm();
        },

        initContactForm() {
            const form = document.querySelector(config.selectors.contactForm);
            if (!form) return;

            form.addEventListener('submit', (e) => this.handleContactSubmit(e, form));
        },

        initNewsletterForm() {
            const form = document.querySelector(config.selectors.newsletterForm);
            if (!form) return;

            form.addEventListener('submit', (e) => this.handleNewsletterSubmit(e, form));
        },

        async handleContactSubmit(e, form) {
            e.preventDefault();

            const submitBtn = form.querySelector('[type="submit"]');
            const originalText = submitBtn.textContent;
            const strings = window.responsibleaiStrings || {};

            // Validate
            if (!this.validateForm(form)) {
                this.showMessage(form, strings.fieldsRequired || 'Please fill in all required fields.', 'error');
                return;
            }

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.textContent = strings.sending || 'Sending...';

            try {
                const formData = new FormData(form);
                formData.append('action', 'responsibleai_contact_form');
                formData.append('contact_nonce', window.responsibleaiData?.nonce || '');

                const response = await fetch(window.responsibleaiData?.ajaxUrl || '/wp-admin/admin-ajax.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    this.showMessage(form, data.data.message || strings.thankYou, 'success');
                    form.reset();
                } else {
                    this.showMessage(form, data.data.message || strings.errorOccurred, 'error');
                }
            } catch (error) {
                console.error('Form submission error:', error);
                this.showMessage(form, strings.errorOccurred || 'An error occurred.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        },

        async handleNewsletterSubmit(e, form) {
            e.preventDefault();

            const emailInput = form.querySelector('input[type="email"]');
            const strings = window.responsibleaiStrings || {};

            if (!emailInput || !this.isValidEmail(emailInput.value)) {
                this.showMessage(form, strings.emailInvalid || 'Please enter a valid email.', 'error');
                return;
            }

            try {
                const formData = new FormData(form);
                formData.append('action', 'responsibleai_newsletter');
                formData.append('newsletter_nonce', window.responsibleaiData?.nonce || '');

                const response = await fetch(window.responsibleaiData?.ajaxUrl || '/wp-admin/admin-ajax.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    this.showMessage(form, data.data.message || strings.subscribeSuccess, 'success');
                    form.reset();
                } else {
                    this.showMessage(form, data.data.message || strings.errorOccurred, 'error');
                }
            } catch (error) {
                console.error('Newsletter error:', error);
                this.showMessage(form, strings.errorOccurred || 'An error occurred.', 'error');
            }
        },

        validateForm(form) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                } else {
                    field.classList.remove('error');
                }

                if (field.type === 'email' && !this.isValidEmail(field.value)) {
                    isValid = false;
                    field.classList.add('error');
                }
            });

            return isValid;
        },

        isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        },

        showMessage(form, message, type) {
            // Remove existing message
            const existing = form.querySelector('.form-message');
            if (existing) existing.remove();

            const messageEl = document.createElement('div');
            messageEl.className = `form-message form-message--${type}`;
            messageEl.textContent = message;

            form.insertBefore(messageEl, form.firstChild);

            // Auto-remove after 5 seconds
            setTimeout(() => messageEl.remove(), 5000);
        }
    };

    // ==========================================================================
    // Initialize
    // ==========================================================================

    document.addEventListener('DOMContentLoaded', () => {
        Navigation.init();
        HeroCarousel.init();
        LogoWall.init();
        Testimonials.init();
        FlipCards.init();
        Tabs.init();
        ScrollAnimations.init();
        Forms.init();
    });

})();
