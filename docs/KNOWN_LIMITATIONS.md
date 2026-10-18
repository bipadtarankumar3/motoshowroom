# MotoShowroom SaaS - Known Limitations & Architectural Boundaries

## 1. 3D Model Availability
* Real motorcycle manufacturers do not publicly supply standardized GLTF/GLB CAD files for all production models due to proprietary IP.
* **System Design Solution**: The platform supports both 3D GLTF models and 36-frame turntable photo sequences. When interactive 3D/360 assets are not uploaded by the showroom, the UI gracefully falls back to a high-resolution photo gallery carousel.

## 2. Shared Database Multi-Tenancy
* Because all tenants share a single MySQL database instance with `tenant_id` columns, intense database load from one extremely high-volume showroom can theoretically impact other showrooms (noisy neighbor effect).
* **System Design Solution**: Eloquent queries are strictly indexed with composite keys (`tenant_id, is_published`, `tenant_id, created_at`). Read queries on high-traffic public catalog pages can be cached with tenant-keyed cache tags (`cache()->tags(["tenant_{$id}"])`).

## 3. Subdomain vs Path Slug on Localhost
* On local development without local wildcard DNS (`*.localhost`), tenant URLs can be tested seamlessly via path routing (`/showrooms/{slug}`) or local `/etc/hosts` aliases (`apex-moto.localhost:8000`). Both routing methods are first-class citizens in MotoShowroom SaaS.

## 4. Payment Gateway Webhook Delivery Guarantees
* Payment webhooks from third-party processors like Razorpay may experience network delays or duplicate deliveries during internet spikes.
* **System Design Solution**: The platform enforces an idempotent `webhook_events` ledger and double-checks transaction state directly via server-to-server signature verification.
