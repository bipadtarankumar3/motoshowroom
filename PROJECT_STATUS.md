# MotoShowroom SaaS - Project Status

## 1. Project Overview
* **Application**: MotoShowroom SaaS (Multi-Tenant Bike Showroom Management Platform)
* **Backend**: Laravel 13.37.0 on PHP 8.5.10
* **Database**: MySQL 8.0 on 127.0.0.1:3306 (database: `motoshowroom`)
* **Frontend**: Blade + Livewire 4.4 + Tailwind CSS 4.0 + Vanilla JavaScript + Three.js
* **Payments**: Razorpay
* **Authentication**: Laravel Session Auth, Google OAuth (Socialite), Role/Permission Engine

---

## 2. Current Status
* **Current Phase**: **Phase 6: Motorcycle Inventory Management** (COMPLETED)
* **Completed Features**:
  * [x] Project workspace initialized with Laravel 13 & PHP 8.5
  * [x] MySQL 8.0 database created and migrations applied on port 3306
  * [x] Full documentation suite delivered in `docs/` and `README.md`
  * [x] Two User Types (`admin` & `user`), user self-registration, and dedicated user details pages
  * [x] Multi-tenancy Core Engine:
    * `showrooms` table and model with dynamic brand colors (`brand_color_primary`, `brand_color_accent`), slugs, custom domains, and status.
    * `showroom_memberships` table and model linking users to showrooms with departmental roles (`owner`, `sales_manager`, etc.) and granular permissions.
    * `social_accounts` table and model for Google OAuth identity mapping.
  * [x] Tenancy Middleware & Security:
    * `EnsureTenantContext` middleware resolving active showroom via route slug, domain, or custom domain.
    * `VerifyShowroomMembership` middleware enforcing strict cross-tenant isolation on showroom back-office management.
    * Super Admin universal platform oversight.
  * [x] Google OAuth Integration with privilege escalation protection.
  * [x] Password Reset Lifecycle with secure token verification.
  * [x] Showroom Storefront & Back-Office Shells.
  * [x] Super Admin Master Control Engine (Phase 3):
    * Central dashboard with key platform metrics: total showrooms (active, trial, suspended), total users (admin vs customer), audit events counter, and live multi-tenant health indicators.
    * Showroom tenant management: directory with search and status filtering, provisioning wizard with automatic slug generation and owner assignment (existing user or inline new owner registration).
    * Showroom detail view: profile metadata, brand color preview swatches, staff membership roster, inline status modification (active, trial, suspended, cancelled) with audit reasoning.
    * Showroom edit view: brand color pickers, custom domain setting, public website toggling, and read-only mode controls.
    * Staff membership management: add staff with departmental roles (`owner`, `sales_manager`, `service_manager`, `staff`) or revoke access.
    * System-wide Audit Trail Ledger: filterable by action, tenant, user, or search query; inspects previous and updated JSON payloads with diff inspection.
    * Platform Configuration & Settings: global brand metadata, central support email, default ISO currency (`INR`, `₹`), trial duration defaults, feature toggles (custom domains, 360 viewer, AI assistant), and registration control backed by cached `PlatformSetting` model.
  * [x] Subscription Management & Dynamic Entitlements (Phase 4):
    * Database-driven subscription tiers (`subscription_plans`, `subscriptions`, `subscription_histories`).
    * Full subscription lifecycle engine (`SubscriptionService`): Subscribe, Start Trial, Renew, Switch Plan, Extend Expiry, Cancel.
    * Super Admin Plan Management: Create, edit, toggle, and view commercial pricing (Monthly & Annual in INR) and resource caps (Staff accounts, Bike listings, Monthly bookings, Cloud storage MB).
    * Dynamic Feature Flags: Careers portal, Promotional scratch cards, Interactive 360° turntable viewer, AI virtual showroom advisor, Custom domain brand mapping, Advanced analytics.
    * Super Admin Subscriptions Oversight: Platform-wide subscriptions ledger with status badges, days remaining counters, inline manual expiry extensions with audit reasons, and plan switching.
    * Showroom Tenant Billing Hub (`/showrooms/{showroom}/billing`): Live consumption progress meters (Staff, Bikes, Bookings, Storage) and plan upgrade comparison matrix with monthly/annual interval toggling.
    * Subscription Gating Middleware (`EnsureSubscriptionActive`): Protects showroom back-office operations from expired/suspended tenants with automatic redirection to renewal hub while preserving public storefronts.
  * [x] Showroom Dashboard & Storefront Customizer (Phase 5):
    * Dealership Back-Office Dashboard (`/showrooms/{showroom}/manage`): Real-time metrics grid for inventory, active enquiries, today's service appointments, open career postings, promotional scratch cards, and staff accounts.
    * Departmental Operations Hub: Quick-launch cards into Inventory, Enquiries, Bookings, Careers, Campaigns, Billing, and Website Builder.
    * Storefront Customizer & Website Builder (`/showrooms/{showroom}/manage/website`): Hero headline and sub-headline editor, background banner URL, brand color hex pickers, About Us narrative story, WhatsApp 1-click hotline, Google Maps link, weekly operating hours matrix (Monday to Sunday with open/close times and closed toggle), social media handles (Instagram, YouTube, Facebook, Twitter), and live storefront publishing toggle.
    * Dynamic Public Storefront Rendering (`/showrooms/{showroom}`): Live "Open Now" / "Closed" schedule computation (`$showroom->isOpenNow()`), branded hero backdrop, 1-click WhatsApp customer chat button, story narrative, and contact card.
    * Showroom Business Settings & Personnel (`/showrooms/{showroom}/manage/settings`): Physical address, official business email and phone updater, and staff roster view.
  * [x] Motorcycle Inventory Management (Phase 6):
    * Database tables: `bikes`, `bike_images`, `bike_variants` with foreign keys, composite unique constraints (`[tenant_id, slug]`, `[tenant_id, sku]`), soft deletes, and index optimizations.
    * Domain Enums: `BikeCategory` (Sports, Cruiser, Adventure, Commuter, Touring, Scooter, Electric EV) and `BikeStatus` (In Stock, Out of Stock, Incoming, Discontinued) with human-friendly labels and Tailwind badge styles.
    * Models & Scopes: `Bike`, `BikeImage`, `BikeVariant` with `BelongsToTenant` isolation, automatic tenant ID injection, casting, image galleries, color/trim variants, and dynamic query scopes (`published`, `featured`, `inStock`, `search`, `filterCategory`, `filterStatus`).
    * Back-Office Inventory Management (`/showrooms/{showroom}/manage/bikes`): Interactive inventory data table with stock badges, specs summary, quick featured and published status toggle buttons, search & category filters, pagination, and plan quota usage meter.
    * Add/Edit Motorcycle Wizards (`/showrooms/{showroom}/manage/bikes/create` & `edit`): Multi-section forms for mechanical specs (cc, power bhp, torque nm, transmission, mileage, top speed, curb weight), JSON specifications, photo galleries, and color/trim variants.
    * Subscription Quota Enforcement: Evaluates `$showroom->hasReachedLimit('max_bike_listings', $currentCount)` and blocks listings exceeding plan thresholds with upgrade call-to-actions.
    * Public Motorcycle Catalog (`/showrooms/{showroom}/bikes`): Customer-facing inventory catalog with category pills, brand dropdown, price sorting, search, and responsive cards.
    * Public Motorcycle Detail Profile (`/showrooms/{showroom}/bikes/{bike}`): Interactive image gallery, color/trim swatches, comprehensive powertrain/dimensions/equipment specs grid, WhatsApp dealer contact button, and related motorcycles showcase.
    * Dealership Homepage Showcase: Featured motorcycles automatically highlighted on the public showroom storefront.
  * [x] Purchase Enquiries & Test Rides (Phase 7):
    * Customer purchase enquiry and test-ride request booking with automated reference tracking (`ENQ-YYYYMMDD-XXXX`).
    * Back-office enquiry hub with status workflows, appointment scheduling, staff assignment, and audit trails.
  * [x] Service Booking Management & Bay Concurrency (Phase 8):
    * Multi-tier service package selection, physical bay slot concurrency control (`service_bays_count`), and live repair tracking (`/services/track`).
    * Back-office workshop manager with status progression, mechanic assignment, diagnostic notes, and invoice costing.
  * [x] Razorpay Payments & Webhooks (Phase 9):
    * Server-side cryptographic HMAC-SHA256 signature verification (`RazorpayService`, `PaymentProcessingService`).
    * Dealership SaaS subscription checkout (`/showrooms/{showroom}/billing/checkout`) with automatic plan activation.
    * Workshop service invoice checkout (`/showrooms/{showroom}/services/{booking}/checkout`) with live status settlement.
    * Idempotent asynchronous Razorpay webhook processing (`POST /api/webhooks/razorpay`) with idempotency ledger (`webhook_events`).
    * Back-office financial ledger (`/showrooms/{showroom}/manage/payments`) with KPI revenue volume metrics and printable digital tax receipts.
  * [x] Email Notification System (Phase 10):
    * High-conversion dark/rose responsive HTML email master layout (`emails.layouts.master`).
    * 6 dedicated transaction email templates: `EnquiryReceivedMail`, `NewEnquiryAlertMail`, `ServiceBookingConfirmedMail`, `ServiceStatusUpdatedMail`, `PaymentReceiptMail`, and `SubscriptionExpiringMail`.
    * Centralized `ShowroomNotificationService` with clean error handling and logging.
    * Integrated event triggers for customer enquiry submission, workshop booking confirmation, service status progression, payment capture, and webhook settlement.
    * Scheduled Artisan command: `php artisan subscriptions:send-expiry-reminders {--days=7}` to alert dealership owners before plan expiration.
    * Back-office visual email template previewer (`/showrooms/{showroom}/manage/email-previews`) for showroom staff and admins.
  * [x] Automated PHPUnit test suite passing (131 tests, 576 assertions, 0 failures)
  * [x] Code formatted with Laravel Pint

---

## 3. Implementation Phases Roadmap

| Phase | Description | Status |
|---|---|---|
| **Phase 1** | Laravel Foundation & Core Structure | ✅ COMPLETED |
| **Phase 1.5** | User Roles (`admin` & `user`), Admin Creation & Personal Details Page | ✅ COMPLETED |
| **Phase 2** | Authentication & Multi-Tenant Isolation | ✅ COMPLETED |
| **Phase 3** | Super Admin Dashboard | ✅ COMPLETED |
| **Phase 4** | Subscription Management & Entitlements | ✅ COMPLETED |
| **Phase 5** | Showroom Dashboard & Website Builder | ✅ COMPLETED |
| **Phase 6** | Motorcycle Inventory Management | ✅ COMPLETED |
| **Phase 7** | Purchase Enquiries & Test Rides | ✅ COMPLETED |
| **Phase 8** | Service Booking Management | ✅ COMPLETED |
| **Phase 9** | Razorpay Payments & Webhooks | ✅ COMPLETED |
| **Phase 10** | Email Notification System | ✅ COMPLETED |
| **Phase 11** | Careers & Job Applications | ✅ COMPLETED |
| **Phase 12** | Promotional Scratch Cards | ✅ COMPLETED |
| **Phase 13** | Interactive 360-Degree Motorcycle Viewer | ✅ COMPLETED |
| **Phase 14** | AI Virtual Showroom Assistant | ✅ COMPLETED |
| **Phase 15** | Analytics & Production Testing | 🔄 READY FOR IMPLEMENTATION |
| **Phase 16** | Deployment & Operational Readiness | ⏳ PENDING |

---

## 4. Test & Verification Status
* **PHPUnit Suite**: 193 tests, 888 assertions, 0 failures (100% passing).
* **Coverage**: 
  - AI Virtual Showroom Concierge with multi-intent heuristic NLP and contextual reasoning engine (`AiAssistantSetting`, `AiConversation`, `AiMessage`, `AiAssistantService`, `PublicAiAssistantController`, `AiAssistantManagementController`).
  - Context-aware motorcycle recommendation engine matching user queries on budget, category (sports, cruiser, adventure, EV, scooter), and brand against live dealership inventory, returning rich cards with prices, specs, and direct booking links.
  - Automated high-intent lead capture converting customer chat inquiries (with contact phone/name) directly into active `Enquiry` records (`type = 'test_ride'`).
  - Showroom operational answering accurately explaining current open/closed status, operating hours matrix, address, contact phone, and workshop service options.
  - Integration with promotional scratch card campaigns (`PromotionalCampaign`), linking active marketing rewards inside conversational replies.
  - Custom FAQs knowledge builder allowing dealership staff to add shop-specific Q&A pairs (financing partners, trade-in bonuses, delivery radius).
  - Floating responsive storefront chat widget (`ai-assistant-widget.blade.php`) with quick prompt chips, persistent local session tracking, live typing indicators, and message history.
  - Back-office AI management dashboard (`/showrooms/{showroom}/manage/ai`) with concierge personality customizer (bot name, tone, welcome message, prompt chips), KPI inquiry counters, and full customer chat transcript inspection.
  - Subscription entitlement gating (`has_ai_assistant`) restricting AI concierge to Pro/Enterprise tiers with upgrade prompts.
  - Session rate limiting (15 queries per minute) preventing abuse and spam.
  - Strict multi-tenant isolation guarding chat conversations, settings, and leads across showrooms. 
  - Interactive 360-degree motorcycle turntable viewer and 3D studio (`Bike`, `BikeImage`, `Bike360ViewerController`, `PublicBike360Controller`).
  - Dealership back-office 360 Studio workbench (`/showrooms/{showroom}/manage/bikes/{bike}/360`) with live turntable preview canvas, angle degrees indicator, auto-spin toggle, rotation speed configuration (1-10), starting angle, and bulk frame importer with automated equidistant angle distribution (0° to 359°).
  - Interactive feature Hotspots & POI callout builder with custom coordinate positioning (`x_pct`, `y_pct`, `frame_angle`) and animated pulsing targets.
  - Dedicated public full-screen 360 studio showcase (`/showrooms/{showroom}/bikes/{bike}/360`) with touch/pointer momentum dragging, angle scrubber, play/pause auto-spin, keyboard navigation (left/right arrows, spacebar), zoom inspection, and 1-click test ride booking.
  - Inline 360 Studio tab integration on public motorcycle profile (`/showrooms/{showroom}/bikes/{bike}`) with graceful fallback to standard photo gallery carousel when 360 assets are disabled.
  - Separation of 360 turntable frames from standard photo gallery relations (`images()` vs `frames360()` vs `allImages()`) preventing gallery pollution.
  - Subscription plan entitlement gating (`has_360_viewer`) restricting back-office studio access and public presentation to Pro/Enterprise plans with upgrade callouts.
  - Strict multi-tenant isolation guarding 360 configuration, frames, and hotspots across dealerships. 
  - Promotional scratch cards marketing engine with probability-weighted prize distribution engine (`PromotionalCampaign`, `CampaignPrize`, `ScratchCardParticipation`, `CampaignStatus`, `PrizeType`, `ScratchCardService`).
  - Interactive HTML5 Canvas scratch-off game with touch/pointer dragging, reveal percentage threshold detection (40%), instant reveal fallback, dynamic sound fx triggers, and coupon code clipboard copying.
  - Multi-tier campaign prize allocation engine with pessimistic locking (`lockForUpdate()`), inventory decrementing, and automatic fallback to non-exhaustible tiers (`better_luck`).
  - Anti-fraud single/repeat customer participation gating (`max_participations_per_customer`), preventing spam or repeated wins per email/phone.
  - Public promotions showcase (`/showrooms/{showroom}/promotions`) and campaign claim portal with real-time inventory counter.
  - Dealership back-office campaign builder (`/showrooms/{showroom}/manage/campaigns`) with dynamic multi-tier prize creation, activation toggling, and campaign analytics.
  - Scratched cards ledger with search, prize filtering, and cashier in-store coupon redemption desk (`/showrooms/{showroom}/manage/coupons/redeem`) verifying unique `MOTO-XXXX-XXXX` tokens.
  - Plan entitlement gating (`has_scratch_cards`) ensuring only subscribed tiers can configure and launch promotional games.
  - Strict multi-tenant isolation guarding campaigns, prizes, and claimed coupons from cross-dealership exposure. 
  - Dealership recruitment & job opening management (`JobOpening`, `JobApplication`, `JobDepartment`, `JobType`, `ApplicationStatus`).
  - Public careers portal (`/showrooms/{showroom}/careers`) with dynamic department and contract type filtering, keyword search, and responsive vacancies showcase.
  - Job vacancy profile and candidate application submission (`/showrooms/{showroom}/careers/{jobOpening}`) with automated reference number generation (`APP-YYYYMMDD-XXXX`) and PDF/DOC resume file uploads.
  - Subscription plan entitlement gating guarding back-office job posting (`has_careers_module`) with plan upgrade prompts.
  - Back-office recruitment dashboard (`/showrooms/{showroom}/manage/careers`) with KPI metric cards, vacancy publishing, editing, one-click active toggling, and soft deletion.
  - Full Applicant Tracking System (ATS) ledger (`/showrooms/{showroom}/manage/applicants`) with 7-stage candidate lifecycle progression, interview scheduling datetime integration, recruiter internal notes, and rejection tracking.
  - Multi-tenant data isolation guarding job vacancies and candidate resume profiles against cross-showroom exposure.
  - Transactional email dispatching and queued mailables (`EnquiryReceivedMail`, `NewEnquiryAlertMail`, `ServiceBookingConfirmedMail`, `ServiceStatusUpdatedMail`, `PaymentReceiptMail`, `SubscriptionExpiringMail`).
  - Artisan command `subscriptions:send-expiry-reminders` with days threshold filtering and multi-tenant owner dispatch.
  - Interactive email template browser previewer (`/showrooms/{showroom}/manage/email-previews`).
  - Razorpay payment gateway integration with server-side HMAC-SHA256 cryptographic verification (`PaymentProcessingService`, `RazorpayService`).
  - SaaS dealership subscription checkout (`/showrooms/{showroom}/billing/checkout`), automatic tier provisioning, and activation.
  - Workshop service booking invoice settlement (`/showrooms/{showroom}/services/{booking}/checkout`) with live cost updates.
  - Idempotent asynchronous Razorpay webhook handling (`POST /api/webhooks/razorpay`) with duplicate event suppression via `webhook_events` ledger and HMAC signature validation.
  - Back-office financial ledger (`/showrooms/{showroom}/manage/payments`) with volume processed, transaction counts, search, status filtering, and printable digital tax receipts.
  - Strict multi-tenant isolation guarding payment records and checkout flows against cross-tenant tampering.
  - Service booking appointment scheduling with physical bay slot concurrency control (`service_bays_count`), preventing overbooking when all bays are occupied.
  - Multi-tier service package catalog (`ServiceType`: general maintenance, oil & lube, brake overhaul, engine tuning, electrical, accident repair, custom upgrades).
  - Public customer booking form with dynamic slot availability computation, registration plate inputs, and odometer reading.
  - Live customer repair tracker (`/services/track`) with animated 6-stage lifecycle progression bar, technician diagnostic notes, bay assignment, and pickup notifications.
  - Dealership back-office workshop management hub with KPI metric cards, quick stage transition actions, bay assignments, mechanic/technician assignments, and invoiced cost tracking.
  - Customer purchase enquiries, test-ride requests, and general showroom inquiries with automated reference numbers (`ENQ-YYYYMMDD-XXXX`).
  - Pre-selected motorcycle links from inventory showcase to test-ride booking form.
  - Back-office enquiry management with KPI metric counters, real-time search, category/status filters, staff assignment, appointment scheduling, and internal notes.
  - Milestone timestamp tracking (`contacted_at`, `completed_at`, `confirmed_at`, `checked_in_at`, `in_service_at`, `ready_at`) and audit logging (`payment.captured`, `payment.failed`, `service_booking.created`, `service_booking.updated`, `service_booking.deleted`).
  - Motorcycle inventory CRUD operations, specifications, photo gallery, variant trims, subscription listing quota enforcement (`max_bike_listings`), multi-tenant cross-dealership isolation (404/403).
  - Public inventory catalog, public bike detail profiles with interactive gallery, featured showcase on storefront homepage, showroom dashboard, website builder, operating hours computation, showroom business settings.
  - Subscription lifecycle & plans CRUD, manual expiry extensions, plan upgrades, dynamic feature & limit entitlement checks, back-office subscription gating, showroom billing hub with usage meters.
  - Super Admin dashboard, tenant provisioning, showroom status toggles & audit logs, staff role assignments, platform settings with cache management, user roles & details pages, admin user provisioning, password reset lifecycle, Google OAuth integration, and base foundation.
* **Code Styling**: Formatted with `vendor/bin/pint --format agent`.
