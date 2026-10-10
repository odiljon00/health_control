import test from 'node:test';
import assert from 'node:assert/strict';
import { attendanceSmsAudience } from './attendanceSmsRules.js';
import { normalizeUzbekPhone } from './uzbekPhone.js';

test('sends the third-day absence alert to the parent', () => {
  assert.equal(attendanceSmsAudience(3), 'parent');
});

test('sends fourth- and fifth-day absence alerts to IIO', () => {
  assert.equal(attendanceSmsAudience(4), 'iio');
  assert.equal(attendanceSmsAudience(5), 'iio');
});

test('does not send absence SMS outside the configured days', () => {
  assert.equal(attendanceSmsAudience(0), null);
  assert.equal(attendanceSmsAudience(2), null);
  assert.equal(attendanceSmsAudience(6), null);
});

test('normalizes parent phone numbers to Eskiz Uzbekistan format', () => {
  assert.equal(normalizeUzbekPhone('+998 90 123 45 67'), '998901234567');
  assert.equal(normalizeUzbekPhone('90 123 45 67'), '998901234567');
  assert.equal(normalizeUzbekPhone('00998901234567'), '998901234567');
  assert.equal(normalizeUzbekPhone('12345'), null);
  assert.equal(normalizeUzbekPhone(''), null);
});
