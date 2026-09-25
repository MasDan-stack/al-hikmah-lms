# Standing Rule: Security, Per-Record Authorization & Mass Assignment Guard

## 1. Anti-IDOR & Per-Record Authorization (Mandatory)

In a multi-role educational LMS (Admin, Mentor/Teacher, Parent, Student):
- **Never rely exclusively on role middleware**: Checking `auth` or `role:parent` / `role:mentor` only verifies *who* the user is, not *which records* they are permitted to access.
- **Model Policies are required for all core domain models**:
  - `Student`: Access must be restricted to Admin, the Parent linked to `student.parent_id`, the assigned Mentor(s), or the Student user account itself.
  - `Mentor` & Salary/Honorarium Slips: Access to view, download, or print salary slips must be restricted to Admin and the specific Mentor instance linked to `auth()->user()->mentor->id`.
  - `Payment` / Invoices: Must only be viewed or processed by Admin or the Parent linked to the associated student.
- **Enforce Route Model Binding**:
  - Avoid un-scoped routes with raw `{id}` (e.g., `GET /children/{id}`).
  - Use typed Route Model Binding (e.g., `GET /children/{student}`) and apply `$this->authorize('view', $student)` in controllers, or route middleware `can:view,student`.
- **Zero Raw Object Exposure**:
  - If a Mentor accesses `/mentor/students/{student}`, the policy must check that the mentor has an active assignment or enrollment with that student before rendering data.

## 2. Mass Assignment Prevention & FormRequests (Mandatory)

- **Avoid `$request->all()` on mutating operations**:
  - Never pass `$request->all()` into `Model::create()`, `Model::update()`, or `Model::fill()`.
  - Always use dedicated FormRequests or `$request->validated()`.
- **Attribute Segregation in Models**:
  - System-calculated or sensitive columns (such as `role_id`, `parent_id`, `total_points`, `current_streak`, `dropout_risk_score`, `dropout_risk_level`, `hourly_rate`) must not be unguarded or easily accessible via mass input.
  - Sensitive modifications must be executed through dedicated service methods or explicit attribute assignment.
