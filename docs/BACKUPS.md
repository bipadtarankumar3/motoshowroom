# MotoShowroom SaaS - Backup & Disaster Recovery Architecture

## 1. Backup Strategy
Data integrity is protected through automated, redundant backups of both MySQL databases and tenant-uploaded media assets.

---

## 2. Automated Database Dumps
* Daily full database dumps using `mysqldump` with `--single-transaction` to prevent table locking during execution.
* Compressed using `gzip` and stored with timestamped filenames: `backup_motoshowroom_YYYY-MM-DD_HHMMSS.sql.gz`.
* Stored on local backup partitions and synchronized offsite to an encrypted S3-compatible cloud storage bucket.

---

## 3. Media & Document Backups
* `storage/app/public` (motorcycle images, branding banners).
* `storage/app/private` (candidate resumes, customer documents).
* Incremental daily sync via `rclone` or AWS CLI.

---

## 4. Retention Policy
* **Daily Backups**: Retained for 14 days.
* **Weekly Backups**: Retained for 8 weeks.
* **Monthly Backups**: Retained for 12 months.

---

## 5. Restoration Runbook
```bash
# 1. Stop queue workers and put application in maintenance mode
php artisan down --message="System maintenance in progress"

# 2. Extract database dump
gunzip < backup_motoshowroom_2026-10-18.sql.gz > restore.sql

# 3. Restore to MySQL
mysql -u root -p motoshowroom < restore.sql

# 4. Run any pending migrations
php artisan migrate --force

# 5. Bring application back online
php artisan up
```
