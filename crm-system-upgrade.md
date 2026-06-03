# PMDCRM System Upgrade Plan

Comprehensive plan to address all security vulnerabilities, UI/UX issues, architectural bottlenecks, and performance challenges detected during the system audit.

## Overview
This project refactors the existing custom PHP CRM system to transition it from a 4.5/10 audit rating to near-perfect. The upgrade fixes security vulnerabilities (CSRF, XSS, Role Escalation, multi-tenant leaks), standardizes layout templates to eliminate duplicate HTML, and optimizes frontend delivery and PWA configuration.

---

## Project Type: WEB
- **Primary Agent**: `frontend-specialist` (for UI/UX, Toast component, layout reorganization, PWA)
- **Secondary Agent**: `backend-specialist` (for security API endpoints, CSRF validation, tenant isolation, DB helpers)
- **Support Agent**: `security-auditor` (for validating security vulnerability fixes)

---

## Tech Stack & Architecture
- **Backend**: PHP 7.4+ (No framework, using PDO for Tidb MySQL database connection)
- **Frontend**: Tailwind CSS (moving towards PostCSS compilation or optimized loading), Chart.js (lazy loaded), Custom HTML/JS components
- **Architecture**: Multi-tenant database architecture with session-based tenant context isolation

---

## File Structure

```
pmdcrm/
├── api/
│   ├── register.php (Modified)
│   ├── financeiro.php (Modified)
│   ├── notifications.php (Modified)
│   ├── kanban.php (Modified)
│   ├── agenda.php (Modified)
│   ├── admin_impersonate.php (Modified)
│   └── ... (Other API endpoints modified to validate CSRF)
├── src/
│   ├── auth.php (Modified)
│   ├── db.php (Modified)
│   ├── utils.php (Modified)
│   └── helpers.php (New)
├── includes/
│   ├── header.php (New)
│   └── footer.php (New)
├── components/
│   └── toast.php (New)
├── service-worker.js (Modified)
├── manifest.json (Modified)
├── .htaccess (Modified)
├── .gitignore (Modified)
└── crm-system-upgrade.md (New - This Plan)
```

---

## Task Breakdown

### Phase 1: Foundation & P0 Security Fixes
| Task ID | Task Name | Assigned Agent | Required Skills | Priority | Dependencies | INPUT → OUTPUT → VERIFY |
|---|---|---|---|---|---|---|
| T1.1 | Implement Register Role Lock | `backend-specialist` | `api-patterns` | P0 | None | **In**: `api/register.php`<br>**Out**: Hardcoded `role = 'gestor'` in register logic<br>**Verify**: Attempt sending `{"role": "admin"}` to register endpoint and check if the database records role as `gestor`. |
| T1.2 | Protect Config Page Access | `backend-specialist` | `api-patterns` | P0 | None | **In**: `configuracoes.php`<br>**Out**: Top of file contains `require_once 'src/auth.php'; require_login();`<br>**Verify**: Try accessing `configuracoes.php` in a logged-out session; it must redirect to `index.php`. |
| T1.3 | Clean Finance Debug Leaks | `backend-specialist` | `api-patterns` | P0 | None | **In**: `api/financeiro.php`<br>**Out**: Removal of 6 `file_put_contents` debugging lines<br>**Verify**: Trigger payments/transactions and ensure no new `.txt` debug files are generated in root. |
| T1.4 | Tenant Isolation in Notifications & Kanban | `backend-specialist` | `database-design` | P0 | None | **In**: `api/notifications.php`, `api/kanban.php`<br>**Out**: Addition of tenant validation (`get_tenant_condition()`) to `move_lead` and notification queries<br>**Verify**: Try updating a lead of a different tenant using another user session; check API rejection or constraint. |
| T1.5 | Fix Agenda Database Initialization | `backend-specialist` | `api-patterns` | P0 | None | **In**: `api/agenda.php`<br>**Out**: Import of `../src/db.php` in file header<br>**Verify**: Verify that the agenda endpoints return correctly without throwing `$pdo is undefined` errors. |
| T1.6 | Impersonation Protection | `backend-specialist` | `api-patterns` | P0 | None | **In**: `api/admin_impersonate.php`<br>**Out**: Restricted targets (admins cannot impersonate admins) and addition of `session_regenerate_id(true)`<br>**Verify**: Test that impersonation of another administrator is blocked. |

### Phase 2: Core Infrastructure (CSRF, Helpers, Templates)
| Task ID | Task Name | Assigned Agent | Required Skills | Priority | Dependencies | INPUT → OUTPUT → VERIFY |
|---|---|---|---|---|---|---|
| T2.1 | Create Global Helper File | `backend-specialist` | `api-patterns` | P1 | Phase 1 | **In**: New `src/helpers.php`<br>**Out**: File with `escapeHtml()` script builder, CSRF generation/validation, security headers loader<br>**Verify**: Run a syntax test `php -l src/helpers.php` to ensure zero compilation issues. |
| T2.2 | Session Cookie Hardening | `backend-specialist` | `api-patterns` | P1 | Phase 1 | **In**: `src/auth.php`<br>**Out**: Configured session options (`httponly`, `secure`, `samesite`) during session init<br>**Verify**: Check cookie parameters in browser storage tool; verification flag must show `HttpOnly` and `Secure`. |
| T2.3 | Layout Extraction (Header/Footer) | `frontend-specialist` | `frontend-design` | P1 | T2.1 | **In**: New `includes/header.php`, `includes/footer.php`<br>**Out**: Reusable templates containing HTML setup, styles, script loadings, fonts, CSRF injection, and PWA registration<br>**Verify**: Confirm templates match all required scripts and styles without duplicates. |
| T2.4 | Page Refactoring & DRY Execution | `frontend-specialist` | `frontend-design` | P1 | T2.3 | **In**: Page `.php` files<br>**Out**: Templates cleaned of boilerplate, importing global headers and footers instead<br>**Verify**: Inspect site pages visually and check Chrome DevTools console for layout or JavaScript issues. |
| T2.5 | CSRF Integration & XSS Escaping | `backend-specialist` | `api-patterns` | P1 | T2.4 | **In**: Frontend Forms & APIs<br>**Out**: Forms injecting CSRF token; APIs checking CSRF validity; all outputs formatted with `escapeHtml` or `htmlspecialchars`<br>**Verify**: Submit form requests without token to ensure HTTP 403 response code, and confirm standard scripts are sanitized. |
| T2.6 | Toast Alert Component | `frontend-specialist` | `frontend-design` | P1 | T2.4 | **In**: `components/toast.php` + JS scripts<br>**Out**: A sleek Toast notification replacing raw `alert()` browser boxes<br>**Verify**: Trigger errors/success flows and inspect modern Toast rendering. |

### Phase 3: Performance, PWA & Polish
| Task ID | Task Name | Assigned Agent | Required Skills | Priority | Dependencies | INPUT → OUTPUT → VERIFY |
|---|---|---|---|---|---|---|
| T3.1 | PWA Configuration Rewrite | `frontend-specialist` | `frontend-design` | P2 | Phase 2 | **In**: `service-worker.js`, `manifest.json`<br>**Out**: Modern service worker with offline page support and stale-while-revalidate strategy; fully structured manifest<br>**Verify**: Check Application PWA rating and test offline mode loading in Chrome DevTools. |
| T3.2 | Security Headers & .htaccess Caching | `backend-specialist` | `api-patterns` | P2 | Phase 2 | **In**: `.htaccess`<br>**Out**: Directives configuration for CSP, HSTS, X-Frame-Options, and static cache times<br>**Verify**: Run headers check tool or check response headers on dashboard page. |
| T3.3 | Database Query Selection Refactoring | `backend-specialist` | `database-design` | P2 | Phase 1 | **In**: API files and Database queries<br>**Out**: Replaced `SELECT *` commands with explicit columns selection (excluding photo profile payloads)<br>**Verify**: Profile API response sizes before and after changes. |
| T3.4 | Dark Mode Corrections & Styling Polish | `frontend-specialist` | `frontend-design` | P2 | Phase 2 | **In**: `configuracoes.php`, pages<br>**Out**: Synchronized dark/light mode toggle; verified contrast on configurations panel<br>**Verify**: Toggle dark theme manually and look for contrast/readability bugs. |

---

## Phase X: Verification Checklist

### Pre-Flight Script Scans
- Run security checker: `python .agent/skills/vulnerability-scanner/scripts/security_scan.py .`
- Run UX audit script: `python .agent/skills/frontend-design/scripts/ux_audit.py .`
- Run checklist script: `python .agent/scripts/checklist.py .`

### Manual Quality Checks
- [ ] No purple/violet color codes are present in the new elements.
- [ ] Toast notification works correctly across all user operations.
- [ ] Privilege escalation through register endpoint is blocked.
- [ ] Cross-Site Request Forgery attempt triggers HTTP 403.
- [ ] Pages display properly under dark mode setup.
- [ ] PWA installation banner is functional and offline page displays.

---

## ✅ PHASE X COMPLETE
- Lint: [ ]
- Security: [ ]
- Build: [ ]
- Date:
