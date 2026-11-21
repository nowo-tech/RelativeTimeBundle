import { test, expect } from '@playwright/test';

/** REQ-DEMO-013 — happy path of the Relative Time demo: the page renders inside the demo platform shell with the examples table and the Twig helpers. */
test.describe('Relative Time demo', () => {
  test('the page renders inside the demo platform shell', async ({ page }) => {
    const response = await page.goto('/');
    expect(response?.ok()).toBeTruthy();
    await expect(page.getByTestId('demo-platform')).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Relative Time Bundle – Demo', level: 1 })).toBeVisible();
  });

  test('the table shows every example in four locales', async ({ page }) => {
    await page.goto('/');
    const rows = page.getByTestId('demo-table').locator('tbody tr');
    await expect(rows).toHaveCount(10);
    await expect(rows.first()).toContainText(/ago|now|just/i);
  });

  test('the Twig helpers render', async ({ page }) => {
    await page.goto('/');
    await expect(page.getByTestId('demo-helpers')).toContainText('relative_time()');
  });
});
