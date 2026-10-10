import assert from 'node:assert/strict';
import test from 'node:test';
import { shouldLoadProfileForSession } from './authState.js';

test('does not restart profile loading when an existing session refreshes its token', () => {
  const refreshedSession = { user: { id: 'user-1' }, access_token: 'refreshed-token' };

  assert.equal(shouldLoadProfileForSession('user-1', refreshedSession), false);
});

test('loads a profile when a different user signs in', () => {
  assert.equal(shouldLoadProfileForSession('user-1', { user: { id: 'user-2' } }), true);
});

test('does not load a profile after sign out', () => {
  assert.equal(shouldLoadProfileForSession('user-1', null), false);
});
