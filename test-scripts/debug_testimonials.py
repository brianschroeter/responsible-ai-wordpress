#!/usr/bin/env python3
"""Debug testimonial slider - check for console errors and autoplay behavior."""

from playwright.sync_api import sync_playwright
import json
import time

def debug_testimonials():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page(viewport={'width': 1920, 'height': 1080})

        # Capture console messages
        console_messages = []
        page.on('console', lambda msg: console_messages.append({
            'type': msg.type,
            'text': msg.text,
            'location': str(msg.location)
        }))

        # Capture page errors
        page_errors = []
        page.on('pageerror', lambda error: page_errors.append(str(error)))

        print("Navigating to homepage...")
        page.goto('http://192.168.1.99:8088/')
        page.wait_for_load_state('networkidle')

        print("\n=== Console Messages ===")
        for msg in console_messages:
            print(f"[{msg['type']}] {msg['text']}")

        print("\n=== Page Errors ===")
        for error in page_errors:
            print(f"ERROR: {error}")

        # Check slider state over time
        print("\n=== Checking Slider Autoplay ===")

        # Get initial state
        initial_state = page.evaluate('''() => {
            const slider = document.querySelector('.testimonial-slider');
            const activeSlide = slider ? slider.querySelector('.testimonial-slide.active') : null;
            const activeDot = slider ? slider.querySelector('.dot.active') : null;
            return {
                sliderFound: !!slider,
                activeSlideIndex: activeSlide ? Array.from(slider.querySelectorAll('.testimonial-slide')).indexOf(activeSlide) : -1,
                activeDotIndex: activeDot ? parseInt(activeDot.dataset.slide) : -1,
                totalSlides: slider ? slider.querySelectorAll('.testimonial-slide').length : 0
            };
        }''')
        print(f"Initial state: {json.dumps(initial_state, indent=2)}")

        # Wait 6 seconds (slightly more than the 5s autoplay interval)
        print("\nWaiting 6 seconds for autoplay...")
        page.wait_for_timeout(6000)

        # Get state after waiting
        after_state = page.evaluate('''() => {
            const slider = document.querySelector('.testimonial-slider');
            const activeSlide = slider ? slider.querySelector('.testimonial-slide.active') : null;
            const activeDot = slider ? slider.querySelector('.dot.active') : null;
            return {
                sliderFound: !!slider,
                activeSlideIndex: activeSlide ? Array.from(slider.querySelectorAll('.testimonial-slide')).indexOf(activeSlide) : -1,
                activeDotIndex: activeDot ? parseInt(activeDot.dataset.slide) : -1,
                totalSlides: slider ? slider.querySelectorAll('.testimonial-slide').length : 0
            };
        }''')
        print(f"After 6s state: {json.dumps(after_state, indent=2)}")

        # Check if slide changed
        if initial_state['activeSlideIndex'] == after_state['activeSlideIndex']:
            print("\n!!! PROBLEM: Slide did NOT change - autoplay is NOT working !!!")
        else:
            print("\nAutoplay is working correctly!")

        # Check positions of dots and stars
        print("\n=== Checking Element Positions ===")
        positions = page.evaluate('''() => {
            const stars = document.querySelector('.testimonial-slide.active .stars');
            const controls = document.querySelector('.testimonial-controls');
            const dots = document.querySelector('.testimonial-dots');

            const getRect = (el) => el ? el.getBoundingClientRect() : null;

            return {
                stars: getRect(stars),
                controls: getRect(controls),
                dots: getRect(dots),
                gap: controls && stars ? (controls.top - (stars.top + stars.height)) : null
            };
        }''')

        if positions['stars'] and positions['dots']:
            stars_bottom = positions['stars']['top'] + positions['stars']['height']
            dots_top = positions['dots']['top']
            gap = dots_top - stars_bottom
            print(f"Stars bottom: {stars_bottom:.1f}px")
            print(f"Dots top: {dots_top:.1f}px")
            print(f"Gap between stars and dots: {gap:.1f}px")

            if gap < 10:
                print("\n!!! PROBLEM: Dots are too close to stars (gap < 10px) !!!")
        else:
            print("Could not measure element positions")

        # Try clicking a dot to test manual navigation
        print("\n=== Testing Manual Navigation ===")
        dots = page.locator('.testimonial-dots .dot')
        if dots.count() > 1:
            # Click second dot
            dots.nth(1).click()
            page.wait_for_timeout(600)  # Wait for transition

            after_click = page.evaluate('''() => {
                const slider = document.querySelector('.testimonial-slider');
                const activeSlide = slider ? slider.querySelector('.testimonial-slide.active') : null;
                return {
                    activeSlideIndex: activeSlide ? Array.from(slider.querySelectorAll('.testimonial-slide')).indexOf(activeSlide) : -1
                };
            }''')
            print(f"After clicking dot 1: active slide index = {after_click['activeSlideIndex']}")

            if after_click['activeSlideIndex'] == 1:
                print("Manual navigation works!")
            else:
                print("!!! PROBLEM: Manual navigation is broken !!!")

        browser.close()
        print("\n=== Debug Complete ===")

if __name__ == '__main__':
    debug_testimonials()
