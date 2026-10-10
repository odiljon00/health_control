import assert from 'node:assert/strict';
import test from 'node:test';
import { resolveStudentSchool } from './studentSchoolAssignment.js';

const schools = [{ id: 4, name: 'Maktab 4' }, { id: 9, name: 'Maktab 9' }];

test('uses the existing student school when editing', () => {
  assert.deepEqual(resolveStudentSchool({
    profile: { role: 'admin', school_id: null },
    student: { school_id: 9 },
    schools,
  }), { schoolId: 9, schoolName: 'Maktab 9', canAssignSchool: true });
});

test('uses the signed-in profile school without asking the user to choose it', () => {
  assert.deepEqual(resolveStudentSchool({
    profile: { role: 'teacher', school_id: 4 },
    student: null,
    schools: [],
  }), { schoolId: 4, schoolName: null, canAssignSchool: true });
});

test('uses the administrator default school when several schools exist', () => {
  assert.deepEqual(resolveStudentSchool({
    profile: { role: 'admin', school_id: 9 },
    student: null,
    schools,
  }), { schoolId: 9, schoolName: 'Maktab 9', canAssignSchool: true });
});

test('automatically assigns the only school to an unassigned administrator', () => {
  assert.deepEqual(resolveStudentSchool({
    profile: { role: 'admin', school_id: null },
    student: null,
    schools: [{ id: 4, name: 'Maktab 4' }],
  }), { schoolId: 4, schoolName: 'Maktab 4', canAssignSchool: true });
});

test('does not guess a school when an administrator has multiple schools', () => {
  assert.deepEqual(resolveStudentSchool({
    profile: { role: 'admin', school_id: null },
    student: null,
    schools,
  }), { schoolId: null, schoolName: null, canAssignSchool: false });
});
