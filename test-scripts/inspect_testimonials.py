#!/usr/bin/env python3
"""Inspect the testimonial section on the homepage to identify issues."""

from playwright.sync_api import sync_playwright
import json

def inspect_testimonials():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page(viewport={'width': 1920, 'height': 1080})

        print("Navigating to homepage...")
        page.goto('http://192.168.1.99:8088/')
        page.wait_for_load_state('networkidle')

        # Take full page screenshot
        page.screenshot(path='/tmp/homepage_full.png', full_page=True)
        print("Full page screenshot saved to /tmp/homepage_full.png")

        # Find testimonial section and take focused screenshot
        testimonial_section = page.locator('.testimonials-section, .testimonial-section, [class*="testimonial"], #testimonials')
        if testimonial_section.count() > 0:
            # Scroll to testimonial section
            testimonial_section.first.scroll_into_view_if_needed()
            page.wait_for_timeout(500)

            # Take screenshot of the testimonial area
            testimonial_section.first.screenshot(path='/tmp/testimonial_section.png')
            print("Testimonial section screenshot saved to /tmp/testimonial_section.png")

            # Get the bounding boxes of key elements
            testimonial_box = testimonial_section.first.bounding_box()
            print(f"\nTestimonial section bounding box: {testimonial_box}")

        # Look for navigation dots/bullets
        nav_dots = page.locator('.slick-dots, .swiper-pagination, .carousel-indicators, [class*="dots"], [class*="bullet"], [class*="nav-dot"]')
        print(f"\nNavigation dots found: {nav_dots.count()}")
        if nav_dots.count() > 0:
            dots_box = nav_dots.first.bounding_box()
            print(f"Nav dots bounding box: {dots_box}")

            # Get computed styles
            dots_styles = page.evaluate('''(selector) => {
                const el = document.querySelector(selector);
                if (!el) return null;
                const styles = window.getComputedStyle(el);
                return {
                    position: styles.position,
                    bottom: styles.bottom,
                    top: styles.top,
                    marginTop: styles.marginTop,
                    marginBottom: styles.marginBottom,
                    paddingTop: styles.paddingTop,
                    paddingBottom: styles.paddingBottom
                };
            }''', '.slick-dots, .swiper-pagination, [class*="dots"]')
            print(f"Nav dots computed styles: {dots_styles}")

        # Look for star ratings
        stars = page.locator('.star-rating, [class*="stars"], .fa-star, [class*="rating"]')
        print(f"\nStar ratings found: {stars.count()}")
        if stars.count() > 0:
            stars_box = stars.first.bounding_box()
            print(f"Stars bounding box: {stars_box}")

        # Check for slider/carousel
        slider = page.locator('.slick-slider, .swiper, .carousel, [class*="slider"]')
        print(f"\nSlider elements found: {slider.count()}")

        # Get all testimonial-related HTML
        testimonial_html = page.evaluate('''() => {
            const section = document.querySelector('.testimonials-section, .testimonial-section, [class*="testimonial"], #testimonials');
            return section ? section.outerHTML : 'No testimonial section found';
        }''')

        # Save HTML to file for analysis
        with open('/tmp/testimonial_html.html', 'w') as f:
            f.write(testimonial_html)
        print("\nTestimonial HTML saved to /tmp/testimonial_html.html")

        # Check if testimonials are rotating (wait and check for class changes)
        print("\nChecking if testimonials rotate...")
        initial_active = page.evaluate('''() => {
            const active = document.querySelector('.slick-active, .swiper-slide-active, .active, [class*="active"]');
            return active ? active.className : null;
        }''')
        print(f"Initial active slide class: {initial_active}")

        # Wait 5 seconds to see if auto-rotation happens
        page.wait_for_timeout(5000)

        after_wait_active = page.evaluate('''() => {
            const active = document.querySelector('.slick-active, .swiper-slide-active, .active, [class*="active"]');
            return active ? active.className : null;
        }''')
        print(f"After 5s active slide class: {after_wait_active}")

        # Take another screenshot after waiting
        if testimonial_section.count() > 0:
            testimonial_section.first.screenshot(path='/tmp/testimonial_section_after_wait.png')
            print("Post-wait screenshot saved to /tmp/testimonial_section_after_wait.png")

        # Get the slick settings if it's a slick slider
        slick_settings = page.evaluate('''() => {
            const slider = document.querySelector('.slick-slider');
            if (slider && window.jQuery) {
                try {
                    const $slider = jQuery(slider);
                    if ($slider.slick) {
                        return $slider.slick('getSlick').options;
                    }
                } catch(e) {
                    return 'Error getting slick settings: ' + e.message;
                }
            }
            return 'No slick slider found or jQuery not available';
        }''')
        print(f"\nSlick slider settings: {slick_settings}")

        browser.close()
        print("\n=== Inspection Complete ===")

if __name__ == '__main__':
    inspect_testimonials()
