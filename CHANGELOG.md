# Changelog

## Version 2.0 - JSON Storage (Current)

**Released:** October 20, 2026

### 🎉 Major Changes

**Removed Redis Dependency**
- Replaced Redis with local JSON file storage
- No external database required
- Zero configuration deployment
- No subscription costs

### ✨ New Features

**JSON File Storage:**
- `data/visits.json` - All visit logs and analytics
- `data/campaigns.json` - Campaign configurations
- Automatic file creation on first use
- Atomic write operations for data safety
- File locking to prevent corruption

**Updated API Endpoints:**
- `api/log-visit.php` - Now uses JSON storage
- `api/get-visits.php` - Reads from JSON files
- `api/storage.php` - Campaign management with JSON

**Documentation:**
- `DATA-STORAGE.md` - Complete guide to JSON storage system
- Updated all deployment guides
- Removed Redis references
- Simplified quick start (5 minutes instead of 10)

### 🔧 Technical Changes

**Files Modified:**
- `api/log-visit.php` - Rewritten for JSON
- `api/get-visits.php` - Rewritten for JSON
- `api/storage.php` - Rewritten for JSON
- `vercel.json` - Updated to include data directory
- `.gitignore` - Excludes JSON data files

**Files Added:**
- `data/.gitkeep` - Ensures data directory in git
- `data/visits.json` - Visit data storage
- `data/campaigns.json` - Campaign storage
- `DATA-STORAGE.md` - JSON storage documentation

**Files Removed:**
- Dependency on `predis/predis` in composer.json
- Redis connection logic
- Environment variable requirements

### ⬆️ Upgrading from v1.0 (Redis)

If you were using the Redis version:

1. **Export your data:**
   ```bash
   # Get all visits from Redis
   curl "https://your-domain.vercel.app/api/get-visits.php" > backup.json
   
   # Get all campaigns
   curl "https://your-domain.vercel.app/api/storage.php?action=list" > campaigns-backup.json
   ```

2. **Deploy new version:**
   ```bash
   git pull
   vercel --prod
   ```

3. **Import campaigns (if needed):**
   ```bash
   # Manually recreate campaigns via API
   curl "https://your-domain.vercel.app/api/storage.php?action=set&key=campaign1&sender=..."
   ```

4. **Remove Redis:**
   - Delete `REDIS_URL` environment variable from Vercel
   - Cancel Redis subscription
   - Remove `composer.json` Redis dependency

### 📊 Benefits

**Simplicity:**
- ✅ No database setup
- ✅ No environment variables
- ✅ No connection strings
- ✅ No authentication

**Cost:**
- ✅ Zero database costs
- ✅ No Redis subscription
- ✅ Free tier friendly

**Performance:**
- ✅ Fast local file access
- ✅ No network latency
- ✅ Efficient for small-medium deployments

**Maintenance:**
- ✅ Easy backups (download JSON)
- ✅ Human-readable data
- ✅ Simple debugging

### ⚠️ Limitations

**Scalability:**
- Best for <50,000 visits
- File locking limits concurrent writes
- Consider database for high traffic

**Backup:**
- Manual backups recommended
- No automatic point-in-time recovery
- File-based disaster recovery

### 🔄 Migration Notes

**Data Compatibility:**
- JSON structure matches Redis data model
- Same API response format
- No frontend changes needed
- Dashboard works unchanged

**Deployment:**
- Vercel deployment unchanged
- No environment variables needed
- Data persists across deployments
- Functions include `data/**` directory

---

## Version 1.0 - Redis Storage (Original)

**Initial Release:** October 2026

### Features

**Core Functionality:**
- Adobe Document Cloud phishing simulator
- Email provider detection via MX records
- Device fingerprinting
- Redis-based data storage
- Admin dashboard
- Educational reveal page

**Technology Stack:**
- Frontend: HTML, CSS, JavaScript
- Backend: PHP
- Database: Redis (Upstash/Redis Labs)
- Hosting: Vercel

**Known Issues:**
- Required paid Redis subscription
- Complex setup with environment variables
- Redis connection failures on free tier
- Business card payment issues with Upstash

---

## Upcoming Features

### v2.1 (Planned)
- [ ] Automatic data archiving
- [ ] Export to CSV from dashboard
- [ ] Data retention policies
- [ ] Bulk campaign creation
- [ ] Email templates in UI

### v2.2 (Planned)
- [ ] SQLite migration option
- [ ] Scheduled campaigns
- [ ] A/B testing variants
- [ ] Employee reporting mechanism
- [ ] Gamification features

### v3.0 (Future)
- [ ] Multi-tenant support
- [ ] SAML/SSO integration
- [ ] Advanced analytics
- [ ] Custom phishing templates
- [ ] API webhooks

---

## Support

**Questions about upgrading?**
- Check [DATA-STORAGE.md](./DATA-STORAGE.md) for JSON details
- Review [DEPLOYMENT.md](./DEPLOYMENT.md) for setup
- See [README.md](./README.md) for complete docs

**Found a bug?**
- Check Vercel logs
- Verify JSON file integrity
- Review troubleshooting guides

---

*Last Updated: October 20, 2026*
