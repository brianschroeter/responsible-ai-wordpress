/**
 * Playwright Test: Compare Testimonial Slider Positioning
 * Compares local (http://localhost:8088) vs production (https://mydefenselaw.com/index.php)
 */

const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext({ viewport: { width: 1920, height: 1080 } });

    console.log('\n🔍 Testimonial Slider Comparison Test\n');
    console.log('=' .repeat(80));

    // Test Production Site
    console.log('\n📍 Testing PRODUCTION: https://mydefenselaw.com/index.php');
    console.log('-'.repeat(80));
    const prodPage = await context.newPage();
    await prodPage.goto('https://mydefenselaw.com/index.php', { waitUntil: 'networkidle' });
    await prodPage.waitForTimeout(2000); // Let page fully render

    // Scroll to testimonial section
    await prodPage.evaluate(() => {
        const section = document.querySelector('.why-choose-us');
        if (section) section.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
    await prodPage.waitForTimeout(1000);

    // Get production testimonial data
    const prodData = await prodPage.evaluate(() => {
        // Production uses .testimonial-highlight (static), not .testimonial-slider
        const testimonial = document.querySelector('.testimonial-highlight, .testimonial-slider');
        if (!testimonial) return { error: 'Testimonial section not found' };

        const isSlider = testimonial.classList.contains('testimonial-slider');
        const container = testimonial.getBoundingClientRect();

        if (!isSlider) {
            // Static testimonial - just get container info
            return {
                type: 'static',
                container: {
                    width: container.width,
                    height: container.height,
                    position: window.getComputedStyle(testimonial).position,
                    overflow: window.getComputedStyle(testimonial).overflow,
                    padding: window.getComputedStyle(testimonial).padding
                },
                slideCount: 1
            };
        }

        const slides = testimonial.querySelectorAll('.testimonial-slide');
        const activeSlide = testimonial.querySelector('.testimonial-slide.active');

        const slidesData = Array.from(slides).map((slide, index) => {
            const rect = slide.getBoundingClientRect();
            const computed = window.getComputedStyle(slide);
            return {
                index,
                isActive: slide.classList.contains('active'),
                position: computed.position,
                top: computed.top,
                left: computed.left,
                opacity: computed.opacity,
                width: rect.width,
                height: rect.height,
                offsetTop: slide.offsetTop,
                offsetLeft: slide.offsetLeft,
                zIndex: computed.zIndex
            };
        });

        return {
            type: 'slider',
            container: {
                width: container.width,
                height: container.height,
                position: window.getComputedStyle(testimonial).position,
                overflow: window.getComputedStyle(testimonial).overflow,
                padding: window.getComputedStyle(testimonial).padding
            },
            slides: slidesData,
            slideCount: slides.length
        };
    });

    console.log(`\nProduction Type: ${prodData.type.toUpperCase()}`);
    console.log('\nProduction Container:');
    console.log(JSON.stringify(prodData.container, null, 2));
    if (prodData.slides) {
        console.log('\nProduction Slides:');
        prodData.slides.forEach(slide => {
            console.log(`  Slide ${slide.index} ${slide.isActive ? '(ACTIVE)' : ''}:`);
            console.log(`    Position: ${slide.position}, Top: ${slide.top}, Left: ${slide.left}`);
            console.log(`    Size: ${slide.width}x${slide.height}, Opacity: ${slide.opacity}, Z-Index: ${slide.zIndex}`);
        });
    }

    // Take screenshot
    await prodPage.screenshot({ path: 'tests/screenshots/production-testimonials.png', fullPage: false });

    // Test Local Site
    console.log('\n\n📍 Testing LOCAL: http://localhost:8088');
    console.log('-'.repeat(80));
    const localPage = await context.newPage();
    await localPage.goto('http://localhost:8088', { waitUntil: 'networkidle' });
    await localPage.waitForTimeout(2000);

    // Scroll to testimonial section
    await localPage.evaluate(() => {
        const section = document.querySelector('.why-choose-us');
        if (section) section.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
    await localPage.waitForTimeout(1000);

    // Get local testimonial data
    const localData = await localPage.evaluate(() => {
        const slider = document.querySelector('.testimonial-slider');
        if (!slider) return { error: 'Testimonial slider not found' };

        const slides = slider.querySelectorAll('.testimonial-slide');
        const activeSlide = slider.querySelector('.testimonial-slide.active');
        const container = slider.getBoundingClientRect();

        const slidesData = Array.from(slides).map((slide, index) => {
            const rect = slide.getBoundingClientRect();
            const computed = window.getComputedStyle(slide);
            return {
                index,
                isActive: slide.classList.contains('active'),
                position: computed.position,
                top: computed.top,
                left: computed.left,
                opacity: computed.opacity,
                width: rect.width,
                height: rect.height,
                offsetTop: slide.offsetTop,
                offsetLeft: slide.offsetLeft,
                zIndex: computed.zIndex
            };
        });

        return {
            container: {
                width: container.width,
                height: container.height,
                position: window.getComputedStyle(slider).position,
                overflow: window.getComputedStyle(slider).overflow,
                padding: window.getComputedStyle(slider).padding
            },
            slides: slidesData,
            slideCount: slides.length
        };
    });

    console.log('\nLocal Slider Container:');
    console.log(JSON.stringify(localData.container, null, 2));
    console.log('\nLocal Slides:');
    localData.slides.forEach(slide => {
        console.log(`  Slide ${slide.index} ${slide.isActive ? '(ACTIVE)' : ''}:`);
        console.log(`    Position: ${slide.position}, Top: ${slide.top}, Left: ${slide.left}`);
        console.log(`    Size: ${slide.width}x${slide.height}, Opacity: ${slide.opacity}, Z-Index: ${slide.zIndex}`);
    });

    // Take screenshot
    await localPage.screenshot({ path: 'tests/screenshots/local-testimonials.png', fullPage: false });

    // Compare and identify differences
    console.log('\n\n🔎 ANALYSIS:\n');
    console.log('=' .repeat(80));

    console.log(`\nProduction: ${prodData.type} testimonial`);
    console.log(`Local: ${localData.type} testimonial`);

    const differences = [];

    // Compare containers
    console.log('\n📊 Container Comparison:');
    if (Math.abs(prodData.container.height - localData.container.height) > 10) {
        const diff = `Container Height: Production=${prodData.container.height.toFixed(1)}px vs Local=${localData.container.height.toFixed(1)}px`;
        differences.push(diff);
        console.log('  ⚠️  ' + diff);
    } else {
        console.log(`  ✅ Height similar: ${prodData.container.height.toFixed(1)}px vs ${localData.container.height.toFixed(1)}px`);
    }

    if (prodData.container.padding !== localData.container.padding) {
        const diff = `Container Padding: Production="${prodData.container.padding}" vs Local="${localData.container.padding}"`;
        differences.push(diff);
        console.log('  ⚠️  ' + diff);
    } else {
        console.log(`  ✅ Padding matches: ${prodData.container.padding}`);
    }

    if (prodData.container.overflow !== localData.container.overflow) {
        const diff = `Container Overflow: Production="${prodData.container.overflow}" vs Local="${localData.container.overflow}"`;
        differences.push(diff);
        console.log('  ⚠️  ' + diff);
    } else {
        console.log(`  ✅ Overflow matches: ${prodData.container.overflow}`);
    }

    // Compare slides if both have them
    if (prodData.slides && localData.slides) {
        console.log('\n📝 Slides Comparison:');
        prodData.slides.forEach((prodSlide, index) => {
            const localSlide = localData.slides[index];
            if (!localSlide) return;

            if (prodSlide.position !== localSlide.position) {
                const diff = `Slide ${index} Position: Production="${prodSlide.position}" vs Local="${localSlide.position}"`;
                differences.push(diff);
                console.log('  ⚠️  ' + diff);
            }
            if (prodSlide.top !== localSlide.top) {
                const diff = `Slide ${index} Top: Production="${prodSlide.top}" vs Local="${localSlide.top}"`;
                differences.push(diff);
                console.log('  ⚠️  ' + diff);
            }
            if (Math.abs(prodSlide.height - localSlide.height) > 5) {
                const diff = `Slide ${index} Height: Production=${prodSlide.height}px vs Local=${localSlide.height}px`;
                differences.push(diff);
                console.log('  ⚠️  ' + diff);
            }
        });
    }

    console.log('\n📋 SUMMARY:');
    if (differences.length === 0) {
        console.log('✅ Positioning appears correct! No significant differences found.');
    } else {
        console.log(`⚠️  Found ${differences.length} difference(s) - may be expected due to slider vs static:`);
        differences.forEach(diff => console.log('  - ' + diff));
    }

    console.log('\n📸 Screenshots saved:');
    console.log('  - tests/screenshots/production-testimonials.png');
    console.log('  - tests/screenshots/local-testimonials.png');
    console.log('\n' + '='.repeat(80));

    // Keep browser open for 10 seconds to inspect
    console.log('\n⏳ Keeping browser open for 10 seconds for manual inspection...\n');
    await new Promise(resolve => setTimeout(resolve, 10000));

    await browser.close();
})();
