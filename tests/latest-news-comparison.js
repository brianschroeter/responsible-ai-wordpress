/**
 * Playwright Test: Compare Latest Legal News Page
 * Compares local (http://localhost:8088/latest-legal-news/) vs production (https://mydefenselaw.com/latest_legal_news.php)
 */

const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext({ viewport: { width: 1920, height: 1080 } });

    console.log('\n🔍 Latest Legal News Page Comparison Test\n');
    console.log('='.repeat(80));

    // Test Production Site
    console.log('\n📍 Testing PRODUCTION: https://mydefenselaw.com/latest_legal_news.php');
    console.log('-'.repeat(80));
    const prodPage = await context.newPage();
    await prodPage.goto('https://mydefenselaw.com/latest_legal_news.php', { waitUntil: 'networkidle' });
    await prodPage.waitForTimeout(2000);

    // Get production page data
    const prodData = await prodPage.evaluate(() => {
        const newsSection = document.querySelector('.news-section');
        if (!newsSection) return { error: 'News section not found' };

        const newsItems = newsSection.querySelectorAll('.news-item');
        const h2 = newsSection.querySelector('h2');
        const introParagraph = newsSection.querySelector('p');
        const ctaBanner = newsSection.querySelector('[style*="background: #1a365d"], .news-cta-banner');

        const newsArticles = Array.from(newsItems).map(item => {
            const title = item.querySelector('h3')?.textContent?.trim();
            const meta = item.querySelector('.news-meta span')?.textContent?.trim();
            const content = item.querySelector('p')?.textContent?.trim();
            const styles = window.getComputedStyle(item);
            return {
                title,
                meta,
                content,
                background: styles.backgroundColor,
                borderLeftColor: styles.borderLeftColor,
                padding: styles.padding,
                borderRadius: styles.borderRadius
            };
        });

        return {
            pageTitle: document.title,
            h2Text: h2?.textContent?.trim(),
            introText: introParagraph?.textContent?.trim()?.substring(0, 100) + '...',
            articleCount: newsItems.length,
            articles: newsArticles,
            hasCTABanner: !!ctaBanner,
            colors: {
                navy: '#1a365d',
                red: '#dc2626',
                bgLight: '#f8fafc'
            }
        };
    });

    console.log(`\nProduction Page Title: ${prodData.pageTitle}`);
    console.log(`H2: ${prodData.h2Text}`);
    console.log(`Intro: ${prodData.introText}`);
    console.log(`Article Count: ${prodData.articleCount}`);
    console.log(`Has CTA Banner: ${prodData.hasCTABanner}`);
    console.log('\nArticle Titles:');
    prodData.articles.forEach((article, i) => {
        console.log(`  ${i + 1}. ${article.title}`);
    });

    // Take full page screenshot
    await prodPage.screenshot({ path: 'tests/screenshots/production-latest-news.png', fullPage: true });

    // Test Local Site
    console.log('\n\n📍 Testing LOCAL: http://localhost:8088/latest-legal-news/');
    console.log('-'.repeat(80));
    const localPage = await context.newPage();
    await localPage.goto('http://localhost:8088/latest-legal-news/', { waitUntil: 'networkidle' });
    await localPage.waitForTimeout(2000);

    // Get local page data
    const localData = await localPage.evaluate(() => {
        const newsSection = document.querySelector('.news-section');
        if (!newsSection) return { error: 'News section not found' };

        const newsItems = newsSection.querySelectorAll('.news-item');
        const h2 = newsSection.querySelector('h2');
        const introParagraph = newsSection.querySelector('h2 + p, .news-section > p');
        const ctaBanner = newsSection.querySelector('.news-cta-banner');

        const newsArticles = Array.from(newsItems).map(item => {
            const title = item.querySelector('h3')?.textContent?.trim();
            const meta = item.querySelector('.news-meta span')?.textContent?.trim();
            const content = item.querySelector('p')?.textContent?.trim();
            const styles = window.getComputedStyle(item);
            return {
                title,
                meta,
                content,
                background: styles.backgroundColor,
                borderLeftColor: styles.borderLeftColor,
                padding: styles.padding,
                borderRadius: styles.borderRadius
            };
        });

        return {
            pageTitle: document.title,
            h2Text: h2?.textContent?.trim(),
            introText: introParagraph?.textContent?.trim()?.substring(0, 100) + '...',
            articleCount: newsItems.length,
            articles: newsArticles,
            hasCTABanner: !!ctaBanner,
            colors: {
                navy: '#1a365d',
                red: '#dc2626',
                bgLight: '#f8fafc'
            }
        };
    });

    console.log(`\nLocal Page Title: ${localData.pageTitle}`);
    console.log(`H2: ${localData.h2Text}`);
    console.log(`Intro: ${localData.introText}`);
    console.log(`Article Count: ${localData.articleCount}`);
    console.log(`Has CTA Banner: ${localData.hasCTABanner}`);
    console.log('\nArticle Titles:');
    localData.articles.forEach((article, i) => {
        console.log(`  ${i + 1}. ${article.title}`);
    });

    // Take full page screenshot
    await localPage.screenshot({ path: 'tests/screenshots/local-latest-news.png', fullPage: true });

    // Compare and identify differences
    console.log('\n\n🔎 ANALYSIS:\n');
    console.log('='.repeat(80));

    const differences = [];
    const matches = [];

    // Compare H2
    if (prodData.h2Text === localData.h2Text) {
        matches.push(`H2 Title matches: "${prodData.h2Text}"`);
    } else {
        differences.push(`H2: Production="${prodData.h2Text}" vs Local="${localData.h2Text}"`);
    }

    // Compare article count
    if (prodData.articleCount === localData.articleCount) {
        matches.push(`Article count matches: ${prodData.articleCount}`);
    } else {
        differences.push(`Article Count: Production=${prodData.articleCount} vs Local=${localData.articleCount}`);
    }

    // Compare CTA banner
    if (prodData.hasCTABanner === localData.hasCTABanner) {
        matches.push(`CTA Banner present on both: ${prodData.hasCTABanner}`);
    } else {
        differences.push(`CTA Banner: Production=${prodData.hasCTABanner} vs Local=${localData.hasCTABanner}`);
    }

    // Compare articles
    console.log('\n📝 Article Comparison:');
    prodData.articles.forEach((prodArticle, i) => {
        const localArticle = localData.articles[i];
        if (!localArticle) {
            differences.push(`Article ${i + 1}: Missing in local`);
            return;
        }
        if (prodArticle.title === localArticle.title) {
            console.log(`  ✅ Article ${i + 1}: "${prodArticle.title}" - Title matches`);
        } else {
            differences.push(`Article ${i + 1} Title: Production="${prodArticle.title}" vs Local="${localArticle.title}"`);
            console.log(`  ⚠️  Article ${i + 1}: Title mismatch`);
        }
    });

    // Compare styling
    console.log('\n🎨 Style Comparison:');
    if (prodData.articles[0] && localData.articles[0]) {
        const prodStyle = prodData.articles[0];
        const localStyle = localData.articles[0];

        // Check border-left color (should be red)
        if (prodStyle.borderLeftColor === localStyle.borderLeftColor) {
            console.log(`  ✅ Border-left color matches: ${prodStyle.borderLeftColor}`);
            matches.push(`Border-left color matches`);
        } else {
            console.log(`  ⚠️  Border-left: Production="${prodStyle.borderLeftColor}" vs Local="${localStyle.borderLeftColor}"`);
        }

        // Check background color
        if (prodStyle.background === localStyle.background) {
            console.log(`  ✅ Background color matches: ${prodStyle.background}`);
            matches.push(`Background color matches`);
        } else {
            console.log(`  ⚠️  Background: Production="${prodStyle.background}" vs Local="${localStyle.background}"`);
        }

        // Check border radius
        if (prodStyle.borderRadius === localStyle.borderRadius) {
            console.log(`  ✅ Border radius matches: ${prodStyle.borderRadius}`);
            matches.push(`Border radius matches`);
        } else {
            console.log(`  ⚠️  Border-radius: Production="${prodStyle.borderRadius}" vs Local="${localStyle.borderRadius}"`);
        }
    }

    console.log('\n📋 SUMMARY:');
    console.log('-'.repeat(40));
    console.log(`\n✅ MATCHES (${matches.length}):`);
    matches.forEach(m => console.log('  - ' + m));

    if (differences.length > 0) {
        console.log(`\n⚠️  DIFFERENCES (${differences.length}):`);
        differences.forEach(d => console.log('  - ' + d));
    } else {
        console.log('\n🎉 No significant differences found! Pages match.');
    }

    console.log('\n📸 Screenshots saved:');
    console.log('  - tests/screenshots/production-latest-news.png');
    console.log('  - tests/screenshots/local-latest-news.png');
    console.log('\n' + '='.repeat(80));

    // Keep browser open for inspection
    console.log('\n⏳ Keeping browser open for 15 seconds for manual inspection...\n');
    await new Promise(resolve => setTimeout(resolve, 15000));

    await browser.close();

    // Exit with appropriate code
    process.exit(differences.length > 0 ? 1 : 0);
})();
