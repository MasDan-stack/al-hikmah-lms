# AI Rules Index

This file maps file paths and globs to the governing standing rules for the Al-Hikmah LMS codebase.

| File Pattern | Rule File | Description |
|---|---|---|
| `app/Http/Controllers/**` | [.ai/rules/security-authorization.md](file:///c:/xampp/htdocs/al-hikmah-lms/.ai/rules/security-authorization.md) | Per-record authorization, Anti-IDOR, FormRequest usage |
| `app/Models/**` | [.ai/rules/security-authorization.md](file:///c:/xampp/htdocs/al-hikmah-lms/.ai/rules/security-authorization.md) | Mass assignment prevention, Model Policies |
| `app/Policies/**` | [.ai/rules/security-authorization.md](file:///c:/xampp/htdocs/al-hikmah-lms/.ai/rules/security-authorization.md) | Model Policy conventions & authorization matrices |
| `routes/**` | [.ai/rules/security-authorization.md](file:///c:/xampp/htdocs/al-hikmah-lms/.ai/rules/security-authorization.md) | Route Model Binding over raw `{id}` parameters |
| `*` | [.ai/rules/lifecycle-maintenance.md](file:///c:/xampp/htdocs/al-hikmah-lms/.ai/rules/lifecycle-maintenance.md) | Framework lifecycle, major upgrade SLA & contract standards |
