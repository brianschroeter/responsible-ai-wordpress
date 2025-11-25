#!/usr/bin/env python3
"""Debug slide visibility - check opacity and z-index of each slide."""

from playwright.sync_api import sync_playwright
import json

def debug_slide_visibility():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page(viewport={'width': 1920, 'height': 1080})

        page.goto('http://192.168.1.99:8088/')
        page.wait_for_load_state('networkidle')

        print("=" * 70)
        print("SLIDE VISIBILITY DEBUG")
        print("=" * 70)

        def get_slide_styles():
            return page.evaluate('''() => {
                const slides = document.querySelectorAll('.testimonial-slide');
                return Array.from(slides).map((slide, index) => {
                    const computed = window.getComputedStyle(slide);
                    return {
                        index: index,
                        hasActiveClass: slide.classList.contains('active'),
                        classList: slide.className,
                        opacity: computed.opacity,
                        zIndex: computed.zIndex,
                        position: computed.position,
                        visibility: computed.visibility,
                        display: computed.display,
                        pointerEvents: computed.pointerEvents,
                        inlineOpacity: slide.style.opacity,
                        inlinePosition: slide.style.position
                    };
                });
            }''')

        print("\n=== INITIAL STATE ===")
        styles = get_slide_styles()
        for s in styles:
            active = "ACTIVE" if s['hasActiveClass'] else "inactive"
            print(f"\nSlide {s['index']} [{active}]:")
            print(f"  opacity: {s['opacity']} (inline: {s['inlineOpacity'] or 'none'})")
            print(f"  z-index: {s['zIndex']}")
            print(f"  position: {s['position']} (inline: {s['inlinePosition'] or 'none'})")
            print(f"  visibility: {s['visibility']}")
            print(f"  pointer-events: {s['pointerEvents']}")

        # Click to slide 1
        print("\n\n=== AFTER CLICKING DOT 1 ===")
        page.locator('.testimonial-dots .dot').nth(1).click()
        page.wait_for_timeout(700)

        styles = get_slide_styles()
        for s in styles:
            active = "ACTIVE" if s['hasActiveClass'] else "inactive"
            print(f"\nSlide {s['index']} [{active}]:")
            print(f"  opacity: {s['opacity']} (inline: {s['inlineOpacity'] or 'none'})")
            print(f"  z-index: {s['zIndex']}")
            print(f"  position: {s['position']} (inline: {s['inlinePosition'] or 'none'})")
            print(f"  visibility: {s['visibility']}")
            print(f"  pointer-events: {s['pointerEvents']}")

        # Check what content is actually visible
        print("\n\n=== VISIBILITY CHECK ===")
        visibility_check = page.evaluate('''() => {
            const slides = document.querySelectorAll('.testimonial-slide');
            const results = [];

            slides.forEach((slide, index) => {
                const rect = slide.getBoundingClientRect();
                const computed = window.getComputedStyle(slide);
                const cite = slide.querySelector('cite');

                // Check if element is truly visible
                const isVisible = (
                    computed.opacity !== '0' &&
                    computed.visibility !== 'hidden' &&
                    computed.display !== 'none' &&
                    rect.width > 0 &&
                    rect.height > 0
                );

                results.push({
                    index: index,
                    author: cite ? cite.textContent.trim() : 'N/A',
                    isVisuallyVisible: isVisible,
                    computedOpacity: computed.opacity,
                    hasActiveClass: slide.classList.contains('active')
                });
            });

            return results;
        }''')

        print("\nWhich slides are visually visible right now?")
        for v in visibility_check:
            status = "VISIBLE" if v['isVisuallyVisible'] else "HIDDEN"
            active = "(active)" if v['hasActiveClass'] else ""
            print(f"  Slide {v['index']}: {status} {active} - opacity={v['computedOpacity']} - {v['author']}")

        browser.close()

if __name__ == '__main__':
    debug_slide_visibility()
