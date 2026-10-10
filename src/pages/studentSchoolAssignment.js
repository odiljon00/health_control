export function resolveStudentSchool({ profile, student, schools }) {
  const schoolId = student?.school_id ?? profile?.school_id ?? (schools.length === 1 ? schools[0].id : null);
  const school = schools.find((item) => String(item.id) === String(schoolId));

  return {
    schoolId,
    schoolName: school?.name ?? null,
    canAssignSchool: schoolId !== null && schoolId !== undefined,
  };
}
