#!/usr/bin/env python3
"""
Comprehensive test for testimonial slider fixes.
Tests:
1. Nav dots have proper spacing from stars (gap > 15px)
2. Autoplay rotation works (slide changes within 6 seconds)
3. Manual navigation works (clicking dots changes slides)
"""

from playwright.sync_api import sync_playwright
import sys

def test_testimonial_fixes():
    """Run all testimonial tests and return pass/fail status."""
    results = {
        'positioning': False,
        'autoplay': False,
        'manual_nav': False
    }

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page(viewport={'width': 1920, 'height': 1080})

        print("=" * 60)
        print("TESTIMONIAL SLIDER TEST SUITE")
        print("=" * 60)

        page.goto('http://192.168.1.99:8088/')
        page.wait_for_load_state('networkidle')

        # Test 1: Positioning
        print("\n[TEST 1] Nav Dots Positioning")
        print("-" * 40)

        positions = page.evaluate('''() => {
            const activeSlide = document.querySelector('.testimonial-slide.active');
            const stars = activeSlide ? activeSlide.querySelector('.stars') : null;
            const dots = document.querySelector('.testimonial-dots');

            if (!stars || !dots) return null;

            const starsRect = stars.getBoundingClientRect();
            const dotsRect = dots.getBoundingClientRect();

            return {
                starsBottom: starsRect.bottom,
                dotsTop: dotsRect.top,
                gap: dotsRect.top - starsRect.bottom
            };
        }''')

        if positions and positions['gap'] >= 15:
            print(f"  Stars bottom: {positions['starsBottom']:.1f}px")
            print(f"  Dots top: {positions['dotsTop']:.1f}px")
            print(f"  Gap: {positions['gap']:.1f}px")
            print("  ✓ PASS: Nav dots have proper spacing from stars")
            results['positioning'] = True
        else:
            gap = positions['gap'] if positions else 'N/A'
            print(f"  ✗ FAIL: Gap is {gap}px (expected >= 15px)")

        # Test 2: Autoplay
        print("\n[TEST 2] Autoplay Rotation")
        print("-" * 40)

        initial_slide = page.evaluate('''() => {
            const slider = document.querySelector('.testimonial-slider');
            const active = slider.querySelector('.testimonial-slide.active');
            return Array.from(slider.querySelectorAll('.testimonial-slide')).indexOf(active);
        }''')
        print(f"  Initial active slide: {initial_slide}")

        page.wait_for_timeout(6000)  # Wait for autoplay (5s interval + buffer)

        after_slide = page.evaluate('''() => {
            const slider = document.querySelector('.testimonial-slider');
            const active = slider.querySelector('.testimonial-slide.active');
            return Array.from(slider.querySelectorAll('.testimonial-slide')).indexOf(active);
        }''')
        print(f"  After 6s active slide: {after_slide}")

        if initial_slide != after_slide:
            print("  ✓ PASS: Autoplay rotation is working")
            results['autoplay'] = True
        else:
            print("  ✗ FAIL: Slide did not change after 6 seconds")

        # Test 3: Manual Navigation
        print("\n[TEST 3] Manual Navigation")
        print("-" * 40)

        # Click on the first dot to reset
        page.locator('.testimonial-dots .dot').first.click()
        page.wait_for_timeout(600)

        before_click = page.evaluate('''() => {
            const active = document.querySelector('.testimonial-dots .dot.active');
            return active ? parseInt(active.dataset.slide) : -1;
        }''')
        print(f"  Before click, active dot: {before_click}")

        # Click second dot
        page.locator('.testimonial-dots .dot').nth(1).click()
        page.wait_for_timeout(600)

        after_click = page.evaluate('''() => {
            const active = document.querySelector('.testimonial-dots .dot.active');
            return active ? parseInt(active.dataset.slide) : -1;
        }''')
        print(f"  After clicking dot 1, active dot: {after_click}")

        if after_click == 1:
            print("  ✓ PASS: Manual navigation works correctly")
            results['manual_nav'] = True
        else:
            print("  ✗ FAIL: Manual navigation not working")

        # Take final screenshot
        testimonial = page.locator('.testimonial-slider')
        if testimonial.count() > 0:
            testimonial.first.scroll_into_view_if_needed()
            page.wait_for_timeout(300)
            testimonial.first.screenshot(path='/tmp/testimonial_test_final.png')
            print("\n  Screenshot saved: /tmp/testimonial_test_final.png")

        browser.close()

    # Summary
    print("\n" + "=" * 60)
    print("TEST SUMMARY")
    print("=" * 60)

    all_passed = all(results.values())
    for test, passed in results.items():
        status = "✓ PASS" if passed else "✗ FAIL"
        print(f"  {test}: {status}")

    print("\n" + ("ALL TESTS PASSED!" if all_passed else "SOME TESTS FAILED"))
    print("=" * 60)

    return 0 if all_passed else 1

if __name__ == '__main__':
    sys.exit(test_testimonial_fixes())
