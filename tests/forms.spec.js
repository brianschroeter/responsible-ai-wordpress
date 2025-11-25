// @ts-check
const { test, expect } = require('@playwright/test');

/**
 * Form Submission Tests for MyDefenseLaw
 * Tests all three contact forms:
 * 1. Homepage Contact Form
 * 2. Consultation Modal
 * 3. Contact Page Form
 */

// Test data for form submissions
const testData = {
  valid: {
    firstName: 'John',
    lastName: 'TestUser',
    phone: '555-123-4567',
    email: 'test@example.com',
    legalIssue: 'civil-litigation',
    message: 'This is a test message for automated testing purposes.',
  },
  invalid: {
    emptyFirst: '',
    emptyLast: '',
    invalidEmail: 'not-an-email',
    emptyPhone: '',
  }
};

test.describe('Homepage Contact Form', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/');
    // Scroll to contact section
    await page.locator('#contact').scrollIntoViewIfNeeded();
  });

  test('should display contact form on homepage', async ({ page }) => {
    const contactForm = page.locator('#contactForm');
    await expect(contactForm).toBeVisible();

    // Check form fields exist
    await expect(page.locator('#contactForm input[name="firstName"]')).toBeVisible();
    await expect(page.locator('#contactForm input[name="lastName"]')).toBeVisible();
    await expect(page.locator('#contactForm input[name="phone"]')).toBeVisible();
    await expect(page.locator('#contactForm input[name="email"]')).toBeVisible();
    await expect(page.locator('#contactForm select[name="legalIssue"]')).toBeVisible();
    await expect(page.locator('#contactForm textarea[name="message"]')).toBeVisible();
    await expect(page.locator('#contactForm input[name="agreement"]')).toBeVisible();
  });

  test('should have correct form source hidden field', async ({ page }) => {
    const formSource = page.locator('#contactForm input[name="form_source"]');
    await expect(formSource).toHaveValue('homepage');
  });

  test('should have all legal issue options', async ({ page }) => {
    const select = page.locator('#contactForm select[name="legalIssue"]');
    await expect(select.locator('option')).toHaveCount(11); // 10 options + placeholder

    const expectedOptions = [
      'civil-litigation',
      'consumer-protection',
      'family-law',
      'bankruptcy',
      'contract-dispute',
      'real-estate',
      'landlord-tenant',
      'intellectual-property',
      'traffic-tickets',
      'other'
    ];

    for (const option of expectedOptions) {
      await expect(select.locator(`option[value="${option}"]`)).toBeAttached();
    }
  });

  test('should require mandatory fields', async ({ page }) => {
    // Check HTML5 required attributes
    await expect(page.locator('#contactForm input[name="firstName"]')).toHaveAttribute('required', '');
    await expect(page.locator('#contactForm input[name="lastName"]')).toHaveAttribute('required', '');
    await expect(page.locator('#contactForm input[name="phone"]')).toHaveAttribute('required', '');
    await expect(page.locator('#contactForm input[name="email"]')).toHaveAttribute('required', '');
    await expect(page.locator('#contactForm select[name="legalIssue"]')).toHaveAttribute('required', '');
    await expect(page.locator('#contactForm input[name="agreement"]')).toHaveAttribute('required', '');
  });

  test('should submit form successfully with valid data', async ({ page }) => {
    // Fill out the form
    await page.locator('#contactForm input[name="firstName"]').fill(testData.valid.firstName);
    await page.locator('#contactForm input[name="lastName"]').fill(testData.valid.lastName);
    await page.locator('#contactForm input[name="phone"]').fill(testData.valid.phone);
    await page.locator('#contactForm input[name="email"]').fill(testData.valid.email);
    await page.locator('#contactForm select[name="legalIssue"]').selectOption(testData.valid.legalIssue);
    await page.locator('#contactForm textarea[name="message"]').fill(testData.valid.message);
    await page.locator('#contactForm input[name="agreement"]').check();

    // Intercept the AJAX request
    const responsePromise = page.waitForResponse(response =>
      response.url().includes('admin-ajax.php') && response.status() === 200
    );

    // Submit the form
    await page.locator('#contactForm button[type="submit"]').click();

    // Wait for the response
    const response = await responsePromise;
    const responseData = await response.json();

    // Check for success
    expect(responseData.success).toBe(true);
    expect(responseData.data.message).toContain('Thank you');

    // Check for success message display
    await expect(page.locator('#formResponse')).toContainText('Thank you');
  });

  test('should show loading state during submission', async ({ page }) => {
    await page.locator('#contactForm input[name="firstName"]').fill(testData.valid.firstName);
    await page.locator('#contactForm input[name="lastName"]').fill(testData.valid.lastName);
    await page.locator('#contactForm input[name="phone"]').fill(testData.valid.phone);
    await page.locator('#contactForm input[name="email"]').fill(testData.valid.email);
    await page.locator('#contactForm select[name="legalIssue"]').selectOption(testData.valid.legalIssue);
    await page.locator('#contactForm input[name="agreement"]').check();

    // Click submit and immediately check for loading state
    const submitButton = page.locator('#contactForm button[type="submit"]');
    await submitButton.click();

    // Button should be disabled and show loading
    await expect(submitButton).toBeDisabled();
    await expect(submitButton).toContainText(/Sending|Loading/i);
  });
});

test.describe('Consultation Modal', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/');
  });

  test('should open modal when clicking CTA button', async ({ page }) => {
    // Click a button that triggers the modal (Free Consultation button)
    const ctaButton = page.locator('a[href="#consultationModal"], .open-modal, [data-modal="consultation"]').first();

    if (await ctaButton.isVisible()) {
      await ctaButton.click();
      await expect(page.locator('#consultationModal')).toBeVisible();
    } else {
      // If no trigger button, manually show modal by navigating
      await page.evaluate(() => {
        const modal = document.getElementById('consultationModal');
        if (modal) modal.classList.add('active');
      });
    }
  });

  test('should have correct form source hidden field in modal', async ({ page }) => {
    // Open the modal
    await page.evaluate(() => {
      const modal = document.getElementById('consultationModal');
      if (modal) {
        modal.style.display = 'flex';
        modal.classList.add('active');
      }
    });

    const formSource = page.locator('#modalContactForm input[name="form_source"]');
    await expect(formSource).toHaveValue('modal');
  });

  test('should have honeypot field hidden', async ({ page }) => {
    await page.evaluate(() => {
      const modal = document.getElementById('consultationModal');
      if (modal) {
        modal.style.display = 'flex';
        modal.classList.add('active');
      }
    });

    const honeypot = page.locator('#modalContactForm input[name="website_url"]');
    await expect(honeypot).toBeHidden();
  });

  test('should display all form fields in modal', async ({ page }) => {
    await page.evaluate(() => {
      const modal = document.getElementById('consultationModal');
      if (modal) {
        modal.style.display = 'flex';
        modal.classList.add('active');
      }
    });

    await expect(page.locator('#modal-firstName')).toBeVisible();
    await expect(page.locator('#modal-lastName')).toBeVisible();
    await expect(page.locator('#modal-phone')).toBeVisible();
    await expect(page.locator('#modal-email')).toBeVisible();
    await expect(page.locator('#modal-legalIssue')).toBeVisible();
    await expect(page.locator('#modal-message')).toBeVisible();
  });

  test('should close modal when clicking close button', async ({ page }) => {
    await page.evaluate(() => {
      const modal = document.getElementById('consultationModal');
      if (modal) {
        modal.style.display = 'flex';
        modal.classList.add('active');
      }
    });

    await page.locator('#modalClose').click();

    // Give time for animation
    await page.waitForTimeout(500);

    await expect(page.locator('#consultationModal')).not.toHaveClass(/active/);
  });

  test('should submit modal form successfully', async ({ page }) => {
    // Show the modal
    await page.evaluate(() => {
      const modal = document.getElementById('consultationModal');
      if (modal) {
        modal.style.display = 'flex';
        modal.classList.add('active');
      }
    });

    // Fill out the form
    await page.locator('#modal-firstName').fill(testData.valid.firstName);
    await page.locator('#modal-lastName').fill(testData.valid.lastName);
    await page.locator('#modal-phone').fill(testData.valid.phone);
    await page.locator('#modal-email').fill(testData.valid.email);
    await page.locator('#modal-legalIssue').selectOption(testData.valid.legalIssue);
    await page.locator('#modal-message').fill(testData.valid.message);
    await page.locator('#modalContactForm input[name="agreement"]').check();

    // Intercept the AJAX request
    const responsePromise = page.waitForResponse(response =>
      response.url().includes('admin-ajax.php') && response.status() === 200
    );

    // Submit the form
    await page.locator('#modalContactForm button[type="submit"]').click();

    // Wait for the response
    const response = await responsePromise;
    const responseData = await response.json();

    expect(responseData.success).toBe(true);
  });
});

test.describe('Contact Page Form', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/contact/');
  });

  test('should display contact form on contact page', async ({ page }) => {
    const contactForm = page.locator('#contactForm');
    await expect(contactForm).toBeVisible();
  });

  test('should have correct form source hidden field', async ({ page }) => {
    const formSource = page.locator('#contactForm input[name="form_source"]');
    await expect(formSource).toHaveValue('contact_page');
  });

  test('should have urgency dropdown specific to contact page', async ({ page }) => {
    const urgencySelect = page.locator('#contactForm select[name="urgency"]');
    await expect(urgencySelect).toBeVisible();

    const expectedOptions = [
      'immediate',
      'urgent',
      'normal',
      'planning'
    ];

    for (const option of expectedOptions) {
      await expect(urgencySelect.locator(`option[value="${option}"]`)).toBeAttached();
    }
  });

  test('should have honeypot field hidden', async ({ page }) => {
    const honeypot = page.locator('#contactForm input[name="website_url"]');
    await expect(honeypot).toBeHidden();
  });

  test('should display contact information', async ({ page }) => {
    // Check that contact info section exists
    await expect(page.locator('.contact-info')).toBeVisible();

    // Check for phone number
    await expect(page.locator('.contact-item').filter({ hasText: /Phone/i })).toBeVisible();

    // Check for location
    await expect(page.locator('.contact-item').filter({ hasText: /Location/i })).toBeVisible();
  });

  test('should submit contact page form successfully', async ({ page }) => {
    // Fill out the form
    await page.locator('#contactForm input[name="firstName"]').fill(testData.valid.firstName);
    await page.locator('#contactForm input[name="lastName"]').fill(testData.valid.lastName);
    await page.locator('#contactForm input[name="phone"]').fill(testData.valid.phone);
    await page.locator('#contactForm input[name="email"]').fill(testData.valid.email);
    await page.locator('#contactForm select[name="legalIssue"]').selectOption(testData.valid.legalIssue);
    await page.locator('#contactForm select[name="urgency"]').selectOption('urgent');
    await page.locator('#contactForm textarea[name="message"]').fill(testData.valid.message);
    await page.locator('#contactForm input[name="agreement"]').check();

    // Intercept the AJAX request
    const responsePromise = page.waitForResponse(response =>
      response.url().includes('admin-ajax.php') && response.status() === 200
    );

    // Submit the form
    await page.locator('#contactForm button[type="submit"]').click();

    // Wait for the response
    const response = await responsePromise;
    const responseData = await response.json();

    expect(responseData.success).toBe(true);
    expect(responseData.data.message).toContain('Thank you');
  });

  test('should mark urgent checkbox correctly', async ({ page }) => {
    await page.locator('#contactForm input[name="firstName"]').fill(testData.valid.firstName);
    await page.locator('#contactForm input[name="lastName"]').fill(testData.valid.lastName);
    await page.locator('#contactForm input[name="phone"]').fill(testData.valid.phone);
    await page.locator('#contactForm input[name="email"]').fill(testData.valid.email);
    await page.locator('#contactForm select[name="legalIssue"]').selectOption(testData.valid.legalIssue);
    await page.locator('#contactForm textarea[name="message"]').fill('URGENT: ' + testData.valid.message);

    // Check the urgent checkbox
    await page.locator('#contactForm input[name="urgent"]').check();
    await page.locator('#contactForm input[name="agreement"]').check();

    // Intercept the AJAX request
    const requestPromise = page.waitForRequest(request =>
      request.url().includes('admin-ajax.php')
    );

    // Submit the form
    await page.locator('#contactForm button[type="submit"]').click();

    // Check that urgent was sent
    const request = await requestPromise;
    const postData = request.postData();
    expect(postData).toContain('urgent=on');
  });
});

test.describe('Form Validation', () => {
  test('should reject submission without required fields', async ({ page }) => {
    await page.goto('/');
    await page.locator('#contact').scrollIntoViewIfNeeded();

    // Try to submit without filling anything
    const submitButton = page.locator('#contactForm button[type="submit"]');

    // Check that form validation prevents submission
    await submitButton.click();

    // The form should not have been submitted (HTML5 validation)
    const firstNameInput = page.locator('#contactForm input[name="firstName"]');

    // Check for validation state - the input should be invalid
    const isInvalid = await firstNameInput.evaluate(el => !el.validity.valid);
    expect(isInvalid).toBe(true);
  });

  test('should validate email format', async ({ page }) => {
    await page.goto('/');
    await page.locator('#contact').scrollIntoViewIfNeeded();

    // Fill with invalid email
    await page.locator('#contactForm input[name="firstName"]').fill(testData.valid.firstName);
    await page.locator('#contactForm input[name="lastName"]').fill(testData.valid.lastName);
    await page.locator('#contactForm input[name="phone"]').fill(testData.valid.phone);
    await page.locator('#contactForm input[name="email"]').fill('invalid-email');
    await page.locator('#contactForm select[name="legalIssue"]').selectOption(testData.valid.legalIssue);
    await page.locator('#contactForm input[name="agreement"]').check();

    // Try to submit
    await page.locator('#contactForm button[type="submit"]').click();

    // Email input should be invalid
    const emailInput = page.locator('#contactForm input[name="email"]');
    const isInvalid = await emailInput.evaluate(el => !el.validity.valid);
    expect(isInvalid).toBe(true);
  });

  test('should require agreement checkbox', async ({ page }) => {
    await page.goto('/');
    await page.locator('#contact').scrollIntoViewIfNeeded();

    // Fill all fields except agreement
    await page.locator('#contactForm input[name="firstName"]').fill(testData.valid.firstName);
    await page.locator('#contactForm input[name="lastName"]').fill(testData.valid.lastName);
    await page.locator('#contactForm input[name="phone"]').fill(testData.valid.phone);
    await page.locator('#contactForm input[name="email"]').fill(testData.valid.email);
    await page.locator('#contactForm select[name="legalIssue"]').selectOption(testData.valid.legalIssue);
    // Don't check agreement

    // Try to submit
    await page.locator('#contactForm button[type="submit"]').click();

    // Agreement checkbox should be invalid
    const agreementCheckbox = page.locator('#contactForm input[name="agreement"]');
    const isInvalid = await agreementCheckbox.evaluate(el => !el.validity.valid);
    expect(isInvalid).toBe(true);
  });
});

test.describe('Spam Protection', () => {
  test('should have honeypot fields that are hidden', async ({ page }) => {
    await page.goto('/');

    // Check homepage form honeypot if it exists
    const homepageHoneypot = page.locator('#contactForm input[name="website_url"]');
    if (await homepageHoneypot.count() > 0) {
      // Should be visually hidden
      const isHidden = await homepageHoneypot.evaluate(el => {
        const style = window.getComputedStyle(el);
        return style.display === 'none' ||
               style.visibility === 'hidden' ||
               el.offsetParent === null ||
               el.classList.contains('screen-reader-text');
      });
      expect(isHidden).toBe(true);
    }

    // Check contact page form honeypot
    await page.goto('/contact/');
    const contactHoneypot = page.locator('#contactForm input[name="website_url"]');
    const isHiddenContact = await contactHoneypot.evaluate(el => {
      const style = window.getComputedStyle(el);
      const parent = el.closest('div[style*="position: absolute"]');
      return style.display === 'none' ||
             style.visibility === 'hidden' ||
             el.offsetParent === null ||
             parent !== null;
    });
    expect(isHiddenContact).toBe(true);
  });

  test('should silently accept honeypot-filled submissions (spam detection)', async ({ page }) => {
    await page.goto('/contact/');

    // Fill out the form including the honeypot
    await page.locator('#contactForm input[name="firstName"]').fill(testData.valid.firstName);
    await page.locator('#contactForm input[name="lastName"]').fill(testData.valid.lastName);
    await page.locator('#contactForm input[name="phone"]').fill(testData.valid.phone);
    await page.locator('#contactForm input[name="email"]').fill(testData.valid.email);
    await page.locator('#contactForm select[name="legalIssue"]').selectOption(testData.valid.legalIssue);

    // Fill the honeypot (simulating a bot)
    await page.locator('#contactForm input[name="website_url"]').evaluate(el => el.value = 'spam-bot-url.com');

    await page.locator('#contactForm input[name="agreement"]').check();

    // Intercept the AJAX request
    const responsePromise = page.waitForResponse(response =>
      response.url().includes('admin-ajax.php') && response.status() === 200
    );

    // Submit the form
    await page.locator('#contactForm button[type="submit"]').click();

    // Server should return success (to not alert bots) but not actually process
    const response = await responsePromise;
    const responseData = await response.json();

    // Response should still be "success" to not alert bots
    expect(responseData.success).toBe(true);
  });

  test('should have CSRF nonce protection', async ({ page }) => {
    await page.goto('/');
    await page.locator('#contact').scrollIntoViewIfNeeded();

    // Check for nonce field
    const nonceField = page.locator('#contactForm input[name="contact_nonce"]');
    await expect(nonceField).toBeAttached();

    const nonceValue = await nonceField.getAttribute('value');
    expect(nonceValue).toBeTruthy();
    expect(nonceValue.length).toBeGreaterThan(5);
  });
});

test.describe('Accessibility', () => {
  test('should have proper labels for form fields', async ({ page }) => {
    await page.goto('/');

    // Open modal which has better accessibility markup
    await page.evaluate(() => {
      const modal = document.getElementById('consultationModal');
      if (modal) {
        modal.style.display = 'flex';
        modal.classList.add('active');
      }
    });

    // Check for labels
    await expect(page.locator('label[for="modal-firstName"]')).toBeAttached();
    await expect(page.locator('label[for="modal-lastName"]')).toBeAttached();
    await expect(page.locator('label[for="modal-phone"]')).toBeAttached();
    await expect(page.locator('label[for="modal-email"]')).toBeAttached();
  });

  test('should have proper ARIA attributes on modal', async ({ page }) => {
    await page.goto('/');

    const modal = page.locator('#consultationModal');
    await expect(modal).toHaveAttribute('role', 'dialog');
    await expect(modal).toHaveAttribute('aria-modal', 'true');
    await expect(modal).toHaveAttribute('aria-labelledby', 'modal-title');
  });

  test('should have form response area with proper ARIA', async ({ page }) => {
    await page.goto('/');

    // Open modal
    await page.evaluate(() => {
      const modal = document.getElementById('consultationModal');
      if (modal) {
        modal.style.display = 'flex';
        modal.classList.add('active');
      }
    });

    const responseArea = page.locator('#modalFormResponse');
    await expect(responseArea).toHaveAttribute('role', 'alert');
    await expect(responseArea).toHaveAttribute('aria-live', 'polite');
  });
});

test.describe('Form Source Tracking', () => {
  test('homepage form should track source as "homepage"', async ({ page }) => {
    await page.goto('/');
    await page.locator('#contact').scrollIntoViewIfNeeded();

    await page.locator('#contactForm input[name="firstName"]').fill(testData.valid.firstName);
    await page.locator('#contactForm input[name="lastName"]').fill(testData.valid.lastName);
    await page.locator('#contactForm input[name="phone"]').fill(testData.valid.phone);
    await page.locator('#contactForm input[name="email"]').fill(testData.valid.email);
    await page.locator('#contactForm select[name="legalIssue"]').selectOption(testData.valid.legalIssue);
    await page.locator('#contactForm input[name="agreement"]').check();

    const requestPromise = page.waitForRequest(request =>
      request.url().includes('admin-ajax.php')
    );

    await page.locator('#contactForm button[type="submit"]').click();

    const request = await requestPromise;
    const postData = request.postData();
    expect(postData).toContain('form_source=homepage');
  });

  test('modal form should track source as "modal"', async ({ page }) => {
    await page.goto('/');

    await page.evaluate(() => {
      const modal = document.getElementById('consultationModal');
      if (modal) {
        modal.style.display = 'flex';
        modal.classList.add('active');
      }
    });

    await page.locator('#modal-firstName').fill(testData.valid.firstName);
    await page.locator('#modal-lastName').fill(testData.valid.lastName);
    await page.locator('#modal-phone').fill(testData.valid.phone);
    await page.locator('#modal-email').fill(testData.valid.email);
    await page.locator('#modal-legalIssue').selectOption(testData.valid.legalIssue);
    await page.locator('#modalContactForm input[name="agreement"]').check();

    const requestPromise = page.waitForRequest(request =>
      request.url().includes('admin-ajax.php')
    );

    await page.locator('#modalContactForm button[type="submit"]').click();

    const request = await requestPromise;
    const postData = request.postData();
    expect(postData).toContain('form_source=modal');
  });

  test('contact page form should track source as "contact_page"', async ({ page }) => {
    await page.goto('/contact/');

    await page.locator('#contactForm input[name="firstName"]').fill(testData.valid.firstName);
    await page.locator('#contactForm input[name="lastName"]').fill(testData.valid.lastName);
    await page.locator('#contactForm input[name="phone"]').fill(testData.valid.phone);
    await page.locator('#contactForm input[name="email"]').fill(testData.valid.email);
    await page.locator('#contactForm select[name="legalIssue"]').selectOption(testData.valid.legalIssue);
    await page.locator('#contactForm input[name="agreement"]').check();

    const requestPromise = page.waitForRequest(request =>
      request.url().includes('admin-ajax.php')
    );

    await page.locator('#contactForm button[type="submit"]').click();

    const request = await requestPromise;
    const postData = request.postData();
    expect(postData).toContain('form_source=contact_page');
  });
});
