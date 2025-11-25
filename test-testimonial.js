const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({headless: 'new'});
  const page = await browser.newPage();
  await page.setViewport({width: 1920, height: 1080});

  console.log('Navigating to site...');
  await page.goto('http://192.168.1.99:8088', {waitUntil: 'networkidle2', timeout: 30000});

  // Scroll to testimonial section
  await page.evaluate(() => {
    const slider = document.querySelector('.testimonial-slider');
    if (slider) {
      slider.scrollIntoView({behavior: 'smooth', block: 'center'});
    }
  });

  await page.waitForTimeout(1000);

  // Get container and content heights
  const dimensions = await page.evaluate(() => {
    const slider = document.querySelector('.testimonial-slider');
    const activeSlide = document.querySelector('.testimonial-slide.active');
    if (!slider || !activeSlide) return null;

    const sliderRect = slider.getBoundingClientRect();
    const slideRect = activeSlide.getBoundingClientRect();

    return {
      containerHeight: slider.offsetHeight,
      contentHeight: activeSlide.offsetHeight,
      containerComputedHeight: getComputedStyle(slider).height,
      containerMinHeight: getComputedStyle(slider).minHeight,
      containerPadding: getComputedStyle(slider).padding,
      slideTop: slideRect.top,
      slideBottom: slideRect.bottom,
      containerTop: sliderRect.top,
      containerBottom: sliderRect.bottom,
      isOverflowing: slideRect.bottom > sliderRect.bottom
    };
  });

  console.log('Dimensions:', JSON.stringify(dimensions, null, 2));

  // Take screenshot of the whole section
  const element = await page.$('.why-choose-us');
  if (element) {
    await element.screenshot({path: '/Volumes/Data/projects/mydefenselaw-wordpress/testimonial-issue.png'});
    console.log('Screenshot saved to testimonial-issue.png');
  }

  await browser.close();
})();
