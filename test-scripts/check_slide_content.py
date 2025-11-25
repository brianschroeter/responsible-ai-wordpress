#!/usr/bin/env python3
"""Check actual content on each testimonial slide."""

from playwright.sync_api import sync_playwright
import json

def check_slide_content():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page(viewport={'width': 1920, 'height': 1080})

        page.goto('http://192.168.1.99:8088/')
        page.wait_for_load_state('networkidle')

        print("=" * 60)
        print("TESTIMONIAL SLIDE CONTENT CHECK")
        print("=" * 60)

        # Get all slide content
        slides_content = page.evaluate('''() => {
            const slides = document.querySelectorAll('.testimonial-slide');
            return Array.from(slides).map((slide, index) => {
                const blockquote = slide.querySelector('blockquote p');
                const cite = slide.querySelector('cite');
                return {
                    index: index,
                    text: blockquote ? blockquote.textContent.trim() : 'NO TEXT FOUND',
                    author: cite ? cite.textContent.trim() : 'NO AUTHOR FOUND',
                    isActive: slide.classList.contains('active'),
                    ariaLabel: slide.getAttribute('aria-label')
                };
            });
        }''')

        print(f"\nFound {len(slides_content)} slides:\n")

        for slide in slides_content:
            status = " [ACTIVE]" if slide['isActive'] else ""
            print(f"--- Slide {slide['index']}{status} ---")
            print(f"Aria Label: {slide['ariaLabel']}")
            print(f"Author: {slide['author']}")
            print(f"Text: {slide['text'][:100]}...")
            print()

        # Check if content is identical
        if len(slides_content) >= 2:
            if slides_content[0]['text'] == slides_content[1]['text']:
                print("!!! WARNING: Slide 0 and Slide 1 have IDENTICAL text !!!")
            else:
                print("OK: Slides have different content")

            if slides_content[0]['author'] == slides_content[1]['author']:
                print("!!! WARNING: Slide 0 and Slide 1 have IDENTICAL author !!!")
            else:
                print("OK: Slides have different authors")

        # Now click through slides and take screenshots
        print("\n" + "=" * 60)
        print("CLICKING THROUGH SLIDES")
        print("=" * 60)

        dots = page.locator('.testimonial-dots .dot')
        for i in range(dots.count()):
            dots.nth(i).click()
            page.wait_for_timeout(700)

            # Get current visible content
            visible_content = page.evaluate('''() => {
                const activeSlide = document.querySelector('.testimonial-slide.active');
                if (!activeSlide) return null;
                const blockquote = activeSlide.querySelector('blockquote p');
                const cite = activeSlide.querySelector('cite');
                return {
                    text: blockquote ? blockquote.textContent.trim().substring(0, 80) : 'NO TEXT',
                    author: cite ? cite.textContent.trim() : 'NO AUTHOR'
                };
            }''')

            print(f"\nAfter clicking dot {i}:")
            print(f"  Author: {visible_content['author']}")
            print(f"  Text: {visible_content['text']}...")

            # Screenshot
            testimonial = page.locator('.testimonial-slider')
            testimonial.first.screenshot(path=f'/tmp/slide_{i}.png')
            print(f"  Screenshot: /tmp/slide_{i}.png")

        browser.close()
        print("\n" + "=" * 60)

if __name__ == '__main__':
    check_slide_content()
