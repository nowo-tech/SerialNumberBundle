import { test, expect } from '@playwright/test';

/** REQ-DEMO-013: the demo page renders on the UiKit shell and shows generated serial numbers. */
test.describe('Serial Number demo', () => {
  test('examples render with a result and a masked value, without a console error', async ({ page }) => {
    const errors: string[] = [];
    page.on('console', (message) => {
      if (message.type() === 'error') {
        errors.push(message.text());
      }
    });
    page.on('pageerror', (error) => errors.push(error.message));

    const response = await page.goto('/');
    expect(response?.status()).toBe(200);
    await expect(page.getByTestId('demo-platform')).toBeVisible();
    await expect(page.getByRole('heading', { level: 1 })).toContainText('Serial Number Bundle');
    await expect(page.getByText('Masked (last 4)').first()).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Generate in Twig with the same pattern' })).toBeVisible();
    await page.waitForLoadState('networkidle');
    expect(errors).toEqual([]);
  });
});
