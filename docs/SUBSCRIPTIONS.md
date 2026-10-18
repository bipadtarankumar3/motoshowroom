# MotoShowroom SaaS - Subscription & Entitlement Architecture

## 1. Subscription Overview
MotoShowroom SaaS implements dynamic, database-driven subscription tiers. Plan features and limits are never hardcoded; they are defined via `subscription_plans` and validated through `SubscriptionService` and `EnsureSubscriptionActive` middleware.

---

## 2. Subscription Lifecycle & Statuses

```
       [Registration / Creation]
                  │
                  ▼
              [Trial] ───────────────► [Pending Payment]
                  │                            │
                  ▼                            ▼
               [Active] ◄────────────── [Payment Verified]
                  │
                  ├── Expiry approaching (7 days, 3 days, 1 day warnings)
                  ▼
              [Past Due] (Grace period: 3 days)
                  │
                  ▼
              [Expired]
                  │
                  ├── Read-Only Public Website / Renewal Screen
                  ▼
             [Suspended] (Administrative action)
```

### Supported Statuses:
* `trial`: New showroom evaluation period (configurable, default 14 days).
* `pending_payment`: Subscription purchase initiated, awaiting Razorpay capture.
* `active`: Paid or active trial with full entitlement access.
* `past_due`: Period expired but within grace period (3 days).
* `expired`: Subscription expired; back-office restricted to billing/renewals.
* `suspended`: Showroom blocked by Super Admin due to terms violation or manual hold.
* `cancelled`: Owner or Admin initiated termination at end of billing cycle.

---

## 3. Dynamic Plan Entitlements

Each plan in `subscription_plans` configures:
* `max_staff_accounts`: Numeric limit for showroom team members.
* `max_bike_listings`: Maximum active motorcycle listings.
* `max_monthly_bookings`: Limit for monthly service appointments and test rides.
* `media_storage_mb`: Storage quota for vehicle photos and 360-degree assets.
* `has_careers_module`: Boolean enabling job posting and applicant tracking.
* `has_scratch_cards`: Boolean enabling promotional scratch card campaigns.
* `has_360_viewer`: Boolean enabling interactive 360-degree viewer.
* `has_ai_assistant`: Boolean enabling AI showroom virtual assistant.
* `has_custom_domain`: Boolean enabling showroom-branded custom domain mapping.
* `has_advanced_analytics`: Boolean enabling advanced revenue and conversion reporting.

---

## 4. Expiry Enforcement Rules

1. **Back-Office Enforcement**: When status is `expired` or `suspended`, all back-office routes redirect to `/showroom/billing/renew`, with flash messages explaining the status and renewal options.
2. **Public Website Behavior**: The public website remains in a configurable **Read-Only Mode** (customers can view existing bikes and contact info, but new bookings or enquiries are politely paused with a notification).
3. **Data Preservation Guarantee**: Expired showrooms **never** suffer automatic record deletion. Data remains intact for renewal.
4. **Super Admin Overrides**: Super Admins can manually extend expiry dates, adjust status, and grant promotional access with mandatory audit reason logging.
