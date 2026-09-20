# Standing Rule: Framework Lifecycle, Upgrades & Contract Standards

## 1. Framework Lifecycle Alignment

- **Laravel Release Cadence**:
  - Laravel publishes one major release annually in Q1.
  - Active bug fixes are maintained for ~18 months; security fixes for ~24 months.
  - Laravel 13 release target: March 17, 2026 (requires PHP 8.3+).
  - Bug fixes until Q3 2027; security patches until Q1 2028.
- **PHP Runtime Requirements**:
  - Keep PHP dependencies checked regularly via `composer show --direct`.
  - Maintain compatibility checks across Livewire, Inertia, and third-party packages before planning major bumps.

## 2. Long-term LMS Contract Architecture

- **Separate Build from Lifecycle Retainers**:
  - Software development contracts for multi-year LMS deployments must include explicit Maintenance & Upgrade Retainers (SLA).
  - Never quote/deliver multi-year LMS software solely on initial "build" cost.
  - Scope major version migrations (e.g., Laravel 12 -> 13, PHP 8.2 -> 8.3+) under planned upgrade allocations.
