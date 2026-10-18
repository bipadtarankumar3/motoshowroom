# MotoShowroom SaaS - Email Notification Architecture

## 1. Notification Strategy
Email communications are managed via **Laravel Mail & Notifications** with database queued delivery (`QUEUE_CONNECTION=database`). All transactional emails support showroom branding (custom logo, brand colors, showroom address/contact footer).

---

## 2. Dual Notification Pattern
Critical events trigger simultaneous paired notifications to ensure both sides of the transaction stay informed:

| Event | Customer / Recipient | Showroom / Admin Recipient |
|---|---|---|
| **Showroom Provisioning** | Showroom Owner Invitation & Password Setup | Super Admin Account Creation Confirmation |
| **Service Booking** | Customer Booking Confirmation & Details | Service Manager New Booking Alert |
| **Purchase Enquiry** | Customer Enquiry Acknowledgment | Sales Manager Lead Alert |
| **Job Application** | Applicant Receipt & Next Steps | HR Manager New Application Notice |
| **Scratch Card Prize** | Customer Coupon Details & Redemption Guide | Showroom Marketing Prize Claim Alert |
| **Subscription Payment** | Showroom Owner Invoice & Renewal Receipt | Super Admin Revenue Alert |

---

## 3. Notification Audit Ledger (`notifications`)
Every email dispatch records an entry in the `notifications` table:
* `recipient_type`: `customer`, `showroom_owner`, `super_admin`, `staff`
* `recipient_email`: Target address
* `purpose`: Specific transaction event
* `status`: `queued`, `sent`, `delivered`, `failed`
* `attempt_count`: Delivery retries
* `error_message`: Error detail if delivery failed
* `sent_at`: UTC timestamp of successful delivery

---

## 4. Queue Processing & Retries
Notifications implement `ShouldQueue` with backoff retry policy:
```php
public $tries = 3;
public $backoff = [60, 300, 900]; // 1m, 5m, 15m
```
Failed jobs land in Laravel's `failed_jobs` table and alert administrators on the Super Admin dashboard.
