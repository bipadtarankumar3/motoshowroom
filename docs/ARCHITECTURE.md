# MotoShowroom SaaS - Architecture Overview

## 1. System Vision
MotoShowroom SaaS is a high-performance, multi-tenant motorcycle showroom management platform designed for dealership chains, independent bike showrooms, and modern franchise networks. It provides:
1. **Central SaaS Platform**: Managed by Super Admins for tenant provisioning, subscription tier enforcement, platform analytics, and billing oversight.
2. **Showroom Business Operations**: An authenticated back-office for showroom owners and department staff (Sales, Service, HR, Marketing) to handle inventory, appointments, enquiries, job listings, and marketing campaigns.
3. **Branded Public Showroom Websites**: Dedicated customer-facing storefronts with custom branding, motorcycle catalog, test ride booking, online service booking, interactive 360-degree bike viewers, careers portal, promotional scratch cards, and an AI virtual assistant.

---

## 2. Multi-Tenant Architecture Pattern

### 2.1 Shared Database, Column-Based Tenant Isolation
The platform employs a **Shared MySQL Database** with `tenant_id` (foreign key pointing to `showrooms.id`) on all tenant-owned models.

#### Key Principles:
* **Explicit Context Resolution**: The active tenant is resolved from the subdomain or path slug (`{tenant}.motoshowroom.test` or `/showrooms/{tenant_slug}`), or from the authenticated user's active showroom membership.
* **Tenant Isolation Trait (`BelongsToTenant`)**:
  * Applies an Eloquent Global Scope (`TenantScope`) ensuring queries automatically filter by `tenant_id` in tenant-aware contexts.
  * Automatically injects `tenant_id` on model creation.
  * Provides bypass methods only for explicit Super Admin platform-wide operations (`withoutTenantScope()`).
* **Multi-Layered Enforcement (Defense-in-Depth)**:
  * Route Middleware (`EnsureTenantContext`, `VerifyShowroomMembership`).
  * Database composite foreign keys and unique constraints: `UNIQUE(tenant_id, slug)`, `UNIQUE(tenant_id, sku)`.
  * Form Requests with tenant-scoped validation rules (`Rule::unique('bikes')->where('tenant_id', $tenantId)`).
  * Explicit Authorization Policies for every model action verifying `$user->canAccessTenant($record->tenant_id)`.
  * Background Queue Jobs serializing and re-hydrating the `tenant_id` context.

---

## 3. High-Level Modular Structure

```
app/
├── Actions/                  # Single-purpose invokable domain actions
├── Enums/                    # Typed PHP 8.5 Enums (UserRole, SubscriptionStatus, EnquiryStatus, etc.)
├── Events/                   # Domain events (e.g. BookingCreated, PaymentVerified, ScratchCardRevealed)
├── Http/
│   ├── Controllers/          # Thin controllers delegating to Services/Actions
│   ├── Middleware/           # Tenant resolution, subscription check, RBAC
│   └── Requests/             # Form Requests with tenant-aware validation
├── Jobs/                     # Queued tasks (Emails, Razorpay webhooks, AI processing)
├── Listeners/                # Event handlers
├── Livewire/                 # Reactive UI components (Inventory table, 360 viewer, scratch card)
├── Models/                   # Eloquent Models with explicit relationships & casts
├── Notifications/            # Mailable notifications (Customer & Showroom dual delivery)
├── Policies/                 # Strict granular authorization policies
├── Scopes/                   # Eloquent Scopes (TenantScope, PublishedScope)
├── Services/                 # Heavy business logic & third-party integrations
│   ├── Ai/                   # Virtual Assistant adapter & prompt management
│   ├── Billing/              # Subscription & entitlement evaluator
│   ├── Booking/              # Service appointment concurrency engine
│   ├── Payment/              # Razorpay order generation & signature verification
│   └── ScratchCard/          # Cryptographically safe prize allocation engine
└── Traits/                   # Reusable model capabilities (BelongsToTenant, HasAuditTrail)
```

---

## 4. Tenant Resolution Strategy

```
                          Incoming HTTP Request
                                    │
                                    ▼
                     [Identify Host & Route Slug]
                                    │
           ┌────────────────────────┴────────────────────────┐
           ▼                                                 ▼
[Platform Domain / Central Routes]          [Showroom Domain / Subdomain / Slug]
* Super Admin Dashboard                     * Public Website: /showrooms/{slug}
* Global Authentication                     * Customer Booking & Enquiries
* SaaS Marketing & Pricing                  * 360 Viewer & Scratch Cards
                                            * Showroom Staff Management Dashboard
                                                             │
                                                             ▼
                                                [Resolve Active Showroom]
                                                             │
                                                             ▼
                                                [Verify Subscription Status]
                                                             │
                                                             ▼
                                                [Bind Showroom to App Container]
```

---

## 5. Technology Integration Architecture

* **Database Engine**: MySQL 8.0 InnoDB with strict mode, foreign keys, and decimal monetary columns (`DECIMAL(12, 2)`).
* **Frontend**: Blade components + Livewire 4 for high-reactivity without SPA bloat.
* **Styling**: Tailwind CSS 4 with custom showroom CSS variable injections (`--brand-primary`, `--brand-accent`).
* **Interactive 360 Viewer**: Three.js WebGL canvas rendering GLTF/GLB models or 36-frame turntable image sequences.
* **Payments**: Razorpay API with server-to-server HMAC-SHA256 signature verification and idempotent webhook ledger.
* **AI Assistant**: Abstracted provider interface (`AiProviderInterface`) with OpenAI / Anthropic / local mock drivers and tenant-scoped retrieval groundings.
