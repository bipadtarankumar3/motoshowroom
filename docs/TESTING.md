# MotoShowroom SaaS - Testing Strategy & Quality Assurance

## 1. Testing Philosophy
MotoShowroom SaaS requires rigorous automated testing covering:
1. **Multi-Tenant Isolation**: Ensuring zero cross-tenant record leakage.
2. **Entitlement & Subscription Enforcement**: Verifying feature gates and limits.
3. **Financial Security**: Testing signature validation and idempotent webhooks.
4. **Booking Concurrency**: Preventing overlapping service bookings.
5. **Scratch Card Allocation**: Ensuring server-side prize integrity and zero overselling.

---

## 2. Test Suites
* **Unit Tests (`tests/Unit`)**: Isolated domain logic (e.g., entitlement calculations, monetary conversions, prize probability distribution algorithms).
* **Feature Tests (`tests/Feature`)**: End-to-end HTTP requests, authorization policies, middleware verification, Livewire components, and database transactions.

---

## 3. Running Automated Tests
```bash
# Run all tests using compact output
php artisan test --compact

# Run a specific test suite
php artisan test --filter=TenantIsolationTest

# Run with PHPUnit directly
vendor/bin/phpunit
```

---

## 4. Key Security Test Cases
* `ShowroomATenantCannotAccessShowroomBDataTest`: Asserts 403 / 404 when querying another showroom's bikes, bookings, customers, or job applications.
* `SubscriptionExpiryRestrictsBackOfficeTest`: Asserts redirection to renewal page when subscription expires.
* `RazorpaySignatureTamperingTest`: Asserts 400 Bad Request when signature is modified.
* `ScratchCardSingleParticipationTest`: Asserts that concurrent requests cannot generate multiple prizes for the same booking.
