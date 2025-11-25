#!/usr/bin/env python3
"""Test testimonial positions at different viewport sizes."""

from playwright.sync_api import sync_playwright
import json

def test_viewport_positions():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)

        viewports = [
            {'width': 1920, 'height': 1080, 'name': 'Desktop 1920'},
            {'width': 1440, 'height': 900, 'name': 'Desktop 1440'},
            {'width': 1200, 'height': 800, 'name': 'Desktop 1200'},
            {'width': 768, 'height': 1024, 'name': 'Tablet'},
            {'width': 375, 'height': 812, 'name': 'Mobile'},
        ]

        for vp in viewports:
            page = browser.new_page(viewport={'width': vp['width'], 'height': vp['height']})
            page.goto('http://192.168.1.99:8088/')
            page.wait_for_load_state('networkidle')

            print(f"\n=== {vp['name']} ({vp['width']}x{vp['height']}) ===")

            # Scroll to testimonials
            testimonial = page.locator('.testimonial-slider')
            if testimonial.count() > 0:
                testimonial.first.scroll_into_view_if_needed()
                page.wait_for_timeout(500)

                # Take screenshot
                screenshot_name = f"/tmp/testimonial_{vp['width']}x{vp['height']}.png"
                testimonial.first.screenshot(path=screenshot_name)
                print(f"Screenshot: {screenshot_name}")

                # Get positions
                positions = page.evaluate('''() => {
                    const activeSlide = document.querySelector('.testimonial-slide.active');
                    const stars = activeSlide ? activeSlide.querySelector('.stars') : null;
                    const controls = document.querySelector('.testimonial-controls');
                    const dots = document.querySelector('.testimonial-dots');
                    const slider = document.querySelector('.testimonial-slider');

                    const getRect = (el) => {
                        if (!el) return null;
                        const rect = el.getBoundingClientRect();
                        return {
                            top: rect.top,
                            bottom: rect.bottom,
                            left: rect.left,
                            right: rect.right,
                            height: rect.height,
                            width: rect.width
                        };
                    };

                    const getStyles = (el) => {
                        if (!el) return null;
                        const styles = window.getComputedStyle(el);
                        return {
                            position: styles.position,
                            bottom: styles.bottom,
                            marginTop: styles.marginTop,
                            paddingBottom: styles.paddingBottom
                        };
                    };

                    return {
                        slider: getRect(slider),
                        stars: getRect(stars),
                        controls: getRect(controls),
                        dots: getRect(dots),
                        sliderStyles: getStyles(slider),
                        controlsStyles: getStyles(controls),
                        dotsStyles: getStyles(dots)
                    };
                }''')

                if positions['stars'] and positions['dots']:
                    stars_bottom = positions['stars']['bottom']
                    dots_top = positions['dots']['top']
                    gap = dots_top - stars_bottom
                    print(f"Stars bottom: {stars_bottom:.1f}px")
                    print(f"Dots top: {dots_top:.1f}px")
                    print(f"Gap: {gap:.1f}px")
                    print(f"Controls styles: {positions['controlsStyles']}")

                    if gap < 15:
                        print("!!! WARNING: Gap is too small !!!")
                else:
                    print("Could not measure positions")

            page.close()

        browser.close()

if __name__ == '__main__':
    test_viewport_positions()
