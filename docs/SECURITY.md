# MotoShowroom SaaS - Security Architecture & Threat Mitigation

## 1. Security Overview
MotoShowroom SaaS enforces multi-layered tenant isolation and security safeguards across every request lifecycle:
* **Identification & Context**: Dynamic tenant resolution through route domain/slug or authenticated session.
* **Server-Side Enforcement**: Zero trust for client-provided `tenant_id` or query string parameters.
* **Database Boundaries**: Strict Eloquent Global Scopes, foreign keys, unique composite indexes, and explicit tenant scoping.
* **Authorization Policies**: Laravel Policies on all resources validating both role and showroom tenancy.

---

## 2. Tenant Isolation & Defense-in-Depth

### 2.1 The 5 Rings of Tenancy Defense
1. **Network / Route Layer**: Route middleware (`EnsureTenantContext`) resolves the showroom and validates that it is active and paid (or in valid trial).
2. **Controller / Request Layer**: Form Requests validate inputs against database tables scoping strictly to the active showroom ID (`Rule::unique('bikes')->where('tenant_id', $currentShowroom->id)`).
3. **Policy / Gate Layer**: Laravel Policies verify `$user->isMemberOf($showroom)` and `$user->hasPermission($module)`.
4. **ORM / Model Layer**: `BelongsToTenant` trait applies `TenantScope` to automatically inject `WHERE tenant_id = ?` into all select, update, and delete queries.
5. **Database Constraint Layer**: Foreign key constraints and unique composite indexes (`UNIQUE KEY tenant_bike_sku (tenant_id, sku)`) prevent cross-tenant duplication or orphan manipulation.

---

## 3. Financial & Payment Security (Razorpay)

* **HMAC-SHA256 Signature Verification**: No payment or subscription status is ever updated based on client-side success callbacks. All transactions must be validated against Razorpay's generated signature:
  ```php
  hash_hmac('sha256', $orderId . '|' . $paymentId, config('services.razorpay.key_secret')) === $signature
  ```
* **Idempotent Webhooks**: All webhook events store their unique Razorpay Event ID (`event_id`) in `webhook_events`. If an event ID already exists, it is acknowledged with HTTP 200 without duplicate execution.
* **Monetary Data Safety**: All financial values are stored using `DECIMAL(12, 2)` to eliminate floating-point calculation errors.
* **Zero PCI Exposure**: No raw credit card numbers or CVVs ever touch our servers or database.

---

## 4. Authentication, Session & Access Control

* **Google OAuth Verification**: Laravel Socialite handles Google authentication with strict callback domain validation and state token checks preventing CSRF / OAuth interception.
* **Privilege Escalation Prevention**: Public registration can only create a showroom owner account with trial status. The `is_super_admin` flag is guarded against mass assignment and can only be assigned via database seeding or an existing Super Admin.
* **Rate Limiting**:
  * Authentication: 5 attempts per minute per IP.
  * Password Reset: 3 attempts per minute per IP.
  * Public Scratch Card participations: 10 attempts per minute per IP with CAPTCHA/session checks.
  * Booking Submissions: 10 attempts per minute per IP.
  * AI Virtual Assistant: 15 queries per minute per session to prevent token exhaustion.

---

## 5. Storage Security & Document Privacy

* **Candidate Resumes & Sensitive Docs**: Stored exclusively on the `private` storage disk (`storage/app/private/resumes`).
* **Direct File Access Prohibited**: Web server blocks direct file access to private directories.
* **Authorized Streaming**: Documents are served exclusively through authenticated controllers with policy checks (`JobApplicationPolicy::viewDocument`) using temporary signed URLs or streamed responses.
* **MIME-Type & Extension Validation**: Uploaded files are strictly validated against allowed binary signatures (`pdf`, `docx`, `jpg`, `png`, `webp`) up to 10MB maximum.

---

## 6. Audit Logging & Compliance

All critical state transitions are recorded in `audit_logs`:
* Super Admin subscription changes (expiry extension, manual plan overrides, showroom suspensions).
* Showroom staff creation and permission alterations.
* Payment failures, refunds, and chargebacks.
* Deletion or unpublishing of motorcycle inventory and job vacancies.
