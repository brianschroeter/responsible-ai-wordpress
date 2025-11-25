/**
 * Playwright Test: Compare Civil Defense Litigation Page
 * Compares local (http://localhost:8088/practice-areas/civil-defense-litigation/)
 * vs production (https://mydefenselaw.com/civil_defense_litigation.php)
 */

const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext({ viewport: { width: 1920, height: 1080 } });

    console.log('\n🔍 Civil Defense Litigation Page Comparison Test\n');
    console.log('='.repeat(80));

    // Test Production Site
    console.log('\n📍 Testing PRODUCTION: https://mydefenselaw.com/civil_defense_litigation.php');
    console.log('-'.repeat(80));
    const prodPage = await context.newPage();
    await prodPage.goto('https://mydefenselaw.com/civil_defense_litigation.php', { waitUntil: 'networkidle' });
    await prodPage.waitForTimeout(2000);

    // Get production page data
    const prodData = await prodPage.evaluate(() => {
        // Find main content area
        const mainContent = document.querySelector('.content, .main-content, main, article');
        const title = document.querySelector('h1, .page-title')?.textContent?.trim();

        // Get all paragraphs in content
        const contentArea = document.querySelector('.content-columns, .content, main') || document.body;
        const paragraphs = Array.from(contentArea.querySelectorAll('p')).map(p => p.textContent?.trim()).filter(t => t && t.length > 50);

        // Get h3 headings
        const h3s = Array.from(contentArea.querySelectorAll('h3')).map(h => h.textContent?.trim());

        // Get list items
        const listItems = Array.from(contentArea.querySelectorAll('ul li, ol li')).map(li => li.textContent?.trim());

        // Get sidebar info
        const sidebar = document.querySelector('.sidebar, aside');

        // Check for key content keywords
        const pageText = document.body.textContent;
        const hasContractDisputes = pageText.includes('Contract dispute');
        const hasBusinessLitigation = pageText.includes('Business') && pageText.includes('litigation');
        const hasPersonalInjury = pageText.includes('Personal injury');
        const hasOurExpertise = pageText.toLowerCase().includes('our expertise');

        return {
            pageTitle: document.title,
            h1: title,
            h3Headings: h3s,
            paragraphCount: paragraphs.length,
            firstParagraphPreview: paragraphs[0]?.substring(0, 150) + '...',
            listItemCount: listItems.length,
            listItems: listItems.slice(0, 10),
            hasSidebar: !!sidebar,
            contentChecks: {
                hasContractDisputes,
                hasBusinessLitigation,
                hasPersonalInjury,
                hasOurExpertise
            }
        };
    });

    console.log(`\nProduction Page Title: ${prodData.pageTitle}`);
    console.log(`H1: ${prodData.h1}`);
    console.log(`H3 Headings: ${prodData.h3Headings.join(', ')}`);
    console.log(`Paragraph Count: ${prodData.paragraphCount}`);
    console.log(`First Paragraph: ${prodData.firstParagraphPreview}`);
    console.log(`List Items: ${prodData.listItemCount}`);
    console.log(`Has Sidebar: ${prodData.hasSidebar}`);
    console.log('\nList Items Preview:');
    prodData.listItems.forEach((item, i) => {
        console.log(`  ${i + 1}. ${item}`);
    });
    console.log('\nContent Checks:');
    Object.entries(prodData.contentChecks).forEach(([key, value]) => {
        console.log(`  ${value ? '✅' : '❌'} ${key}: ${value}`);
    });

    // Take full page screenshot
    await prodPage.screenshot({ path: 'tests/screenshots/production-civil-defense.png', fullPage: true });

    // Test Local Site
    console.log('\n\n📍 Testing LOCAL: http://localhost:8088/practice-areas/civil-defense-litigation/');
    console.log('-'.repeat(80));
    const localPage = await context.newPage();
    await localPage.goto('http://localhost:8088/practice-areas/civil-defense-litigation/', { waitUntil: 'networkidle' });
    await localPage.waitForTimeout(2000);

    // Get local page data
    const localData = await localPage.evaluate(() => {
        const mainContent = document.querySelector('.practice-area-main, .content, main, article');
        const title = document.querySelector('h1, .page-title')?.textContent?.trim();

        // Get all paragraphs in main content
        const contentArea = document.querySelector('.practice-area-content, .practice-area-main, main') || document.body;
        const paragraphs = Array.from(contentArea.querySelectorAll('p')).map(p => p.textContent?.trim()).filter(t => t && t.length > 50);

        // Get h3 headings
        const h3s = Array.from(contentArea.querySelectorAll('h3')).map(h => h.textContent?.trim());

        // Get list items
        const listItems = Array.from(contentArea.querySelectorAll('ul li, ol li')).map(li => li.textContent?.trim());

        // Get sidebar info
        const sidebar = document.querySelector('.practice-area-sidebar, .sidebar, aside');

        // Check styling
        const mainSection = document.querySelector('.practice-area-main');
        const mainStyles = mainSection ? window.getComputedStyle(mainSection) : null;

        // Check for key content keywords
        const pageText = document.body.textContent;
        const hasContractDisputes = pageText.includes('Contract dispute');
        const hasBusinessLitigation = pageText.includes('Business') && pageText.includes('litigation');
        const hasPersonalInjury = pageText.includes('Personal injury');
        const hasOurExpertise = pageText.toLowerCase().includes('our expertise');

        return {
            pageTitle: document.title,
            h1: title,
            h3Headings: h3s,
            paragraphCount: paragraphs.length,
            firstParagraphPreview: paragraphs[0]?.substring(0, 150) + '...',
            listItemCount: listItems.length,
            listItems: listItems.slice(0, 10),
            hasSidebar: !!sidebar,
            styles: {
                mainBackground: mainStyles?.backgroundColor,
                mainPadding: mainStyles?.padding,
                mainBorderRadius: mainStyles?.borderRadius
            },
            contentChecks: {
                hasContractDisputes,
                hasBusinessLitigation,
                hasPersonalInjury,
                hasOurExpertise
            }
        };
    });

    console.log(`\nLocal Page Title: ${localData.pageTitle}`);
    console.log(`H1: ${localData.h1}`);
    console.log(`H3 Headings: ${localData.h3Headings.join(', ')}`);
    console.log(`Paragraph Count: ${localData.paragraphCount}`);
    console.log(`First Paragraph: ${localData.firstParagraphPreview}`);
    console.log(`List Items: ${localData.listItemCount}`);
    console.log(`Has Sidebar: ${localData.hasSidebar}`);
    console.log('\nList Items Preview:');
    localData.listItems.forEach((item, i) => {
        console.log(`  ${i + 1}. ${item}`);
    });
    console.log('\nContent Checks:');
    Object.entries(localData.contentChecks).forEach(([key, value]) => {
        console.log(`  ${value ? '✅' : '❌'} ${key}: ${value}`);
    });
    console.log('\nStyles:');
    Object.entries(localData.styles).forEach(([key, value]) => {
        console.log(`  ${key}: ${value}`);
    });

    // Take full page screenshot
    await localPage.screenshot({ path: 'tests/screenshots/local-civil-defense.png', fullPage: true });

    // Compare and identify differences
    console.log('\n\n🔎 ANALYSIS:\n');
    console.log('='.repeat(80));

    const differences = [];
    const matches = [];

    // Compare H1 Title
    if (prodData.h1?.toLowerCase().includes('civil') && localData.h1?.toLowerCase().includes('civil')) {
        matches.push(`Page title contains "civil": Production="${prodData.h1}" Local="${localData.h1}"`);
    } else {
        differences.push(`H1: Production="${prodData.h1}" vs Local="${localData.h1}"`);
    }

    // Compare "Our Expertise" heading
    const prodHasExpertiseHeading = prodData.h3Headings.some(h => h?.toLowerCase().includes('expertise'));
    const localHasExpertiseHeading = localData.h3Headings.some(h => h?.toLowerCase().includes('expertise'));
    if (prodHasExpertiseHeading && localHasExpertiseHeading) {
        matches.push(`Both have "Our Expertise" heading`);
    } else if (prodHasExpertiseHeading !== localHasExpertiseHeading) {
        differences.push(`"Our Expertise" heading: Production=${prodHasExpertiseHeading} vs Local=${localHasExpertiseHeading}`);
    }

    // Compare sidebar
    if (prodData.hasSidebar && localData.hasSidebar) {
        matches.push(`Both have sidebar`);
    } else if (localData.hasSidebar && !prodData.hasSidebar) {
        matches.push(`Local has enhanced sidebar (improvement)`);
    }

    // Compare content coverage
    console.log('\n📝 Content Coverage Comparison:');
    Object.keys(prodData.contentChecks).forEach(key => {
        const prodHas = prodData.contentChecks[key];
        const localHas = localData.contentChecks[key];
        if (prodHas && localHas) {
            console.log(`  ✅ ${key}: Both have this content`);
            matches.push(`${key} content present in both`);
        } else if (prodHas && !localHas) {
            console.log(`  ⚠️  ${key}: Missing in local`);
            differences.push(`${key}: Missing in local`);
        } else if (!prodHas && localHas) {
            console.log(`  ℹ️  ${key}: Added in local (enhancement)`);
            matches.push(`${key} added in local`);
        }
    });

    // Compare list items count
    if (localData.listItemCount >= prodData.listItemCount) {
        matches.push(`List items: Local has ${localData.listItemCount} items (production has ${prodData.listItemCount})`);
    } else {
        differences.push(`List items: Production=${prodData.listItemCount} vs Local=${localData.listItemCount}`);
    }

    // Compare key list items
    console.log('\n📋 List Item Comparison:');
    const keyItems = ['Contract', 'Business', 'Personal injury', 'Real estate', 'Employment', 'Consumer'];
    keyItems.forEach(key => {
        const inProd = prodData.listItems.some(item => item?.toLowerCase().includes(key.toLowerCase()));
        const inLocal = localData.listItems.some(item => item?.toLowerCase().includes(key.toLowerCase()));
        if (inProd && inLocal) {
            console.log(`  ✅ "${key}" item present in both`);
        } else if (inProd && !inLocal) {
            console.log(`  ⚠️  "${key}" missing in local`);
            differences.push(`"${key}" list item missing in local`);
        }
    });

    console.log('\n📋 SUMMARY:');
    console.log('-'.repeat(40));
    console.log(`\n✅ MATCHES (${matches.length}):`);
    matches.forEach(m => console.log('  - ' + m));

    if (differences.length > 0) {
        console.log(`\n⚠️  DIFFERENCES (${differences.length}):`);
        differences.forEach(d => console.log('  - ' + d));
    } else {
        console.log('\n🎉 No significant differences found! Content matches.');
    }

    console.log('\n📸 Screenshots saved:');
    console.log('  - tests/screenshots/production-civil-defense.png');
    console.log('  - tests/screenshots/local-civil-defense.png');
    console.log('\n' + '='.repeat(80));

    // Calculate match percentage
    const totalChecks = matches.length + differences.length;
    const matchPercentage = Math.round((matches.length / totalChecks) * 100);
    console.log(`\n📊 Overall Match: ${matchPercentage}% (${matches.length}/${totalChecks} checks passed)`);

    if (matchPercentage >= 80) {
        console.log('✅ PASS: Page meets matching threshold\n');
    } else {
        console.log('⚠️  NEEDS WORK: Page below matching threshold\n');
    }

    // Keep browser open for inspection
    console.log('\n⏳ Keeping browser open for 20 seconds for manual inspection...\n');
    await new Promise(resolve => setTimeout(resolve, 20000));

    await browser.close();

    // Exit with appropriate code
    process.exit(differences.length > 2 ? 1 : 0);
})();
