# cPanel Hosting Deployment Guide

## ✅ Super Simple - No Database Required!

This version uses **JSON file storage** - no MySQL, no MongoDB, no database setup!

**Perfect for:**
- Any cPanel shared hosting
- PHP 7.4 or higher
- Standard file permissions
- Zero configuration

---

## 📦 Step 1: Upload Files (2 minutes)

### Option A: File Manager
1. Log into cPanel
2. Open **File Manager**
3. Navigate to `public_html` (or your domain folder)
4. Click **"Upload"**
5. Upload all files from the `b2b` folder
6. Extract if uploaded as ZIP

### Option B: FTP
1. Use FileZilla or your FTP client
2. Connect to your host
3. Upload all files to `public_html`

**Files to upload:**
```
public_html/
├── index.html
├── adb_token.html
├── admin-dashboard.html
├── training-reveal.html
├── config.php
├── setup.php
├── .htaccess
├── api/
│   ├── log-visit.php
│   ├── get-visits.php
│   └── storage.php
└── (all other HTML/CSS/JS files)
```

---

## ⚙️ Step 2: Run Setup (1 minute)

1. Open your browser
2. Go to: `https://yourdomain.com/setup.php`
3. Click **"Initialize Data Storage"**
4. Wait for confirmation

The setup will:
- ✅ Create `data` directory
- ✅ Create `campaigns.json` file
- ✅ Create `visits.json` file
- ✅ Set proper permissions

**That's it!** No database configuration needed!

---

## 🔒 Step 3: Secure Setup File (30 seconds)

After setup completes, delete the setup file:

**In cPanel File Manager:**
- Right-click `setup.php` → Delete

**Or via FTP:**
- Delete `setup.php`

---

## ✅ Step 4: Test Everything (2 minutes)

### Test Admin Dashboard:
```
https://yourdomain.com/admin
```

Should load without errors (will be empty until you create campaigns)

### Create Test Campaign:
Visit this URL (replace yourdomain.com):
```
https://yourdomain.com/api/storage.php?action=set&key=test&sender=Test%20User&title=Test%20Campaign&url=https://yourdomain.com/training
```

Expected response:
```json
{"success":true,"message":"Data stored"}
```

### View Test Campaign:
```
https://yourdomain.com/adb_token.html#test
```

### Check Dashboard:
```
https://yourdomain.com/admin
```

You should see your test campaign!

---

## 🎯 Creating Campaigns

### Via URL (Easiest):
```
https://yourdomain.com/api/storage.php?action=set&key=CAMPAIGN_KEY&sender=SENDER_NAME&title=DOCUMENT_TITLE&url=https://yourdomain.com/training
```

### Example:
```
https://yourdomain.com/api/storage.php?action=set&key=rfp2024&sender=John%20Smith%20-%20ABC%20Corp&title=RFP%20%234523%20Office%20Project&url=https://yourdomain.com/training
```

### Share Link:
```
https://yourdomain.com/adb_token.html#rfp2024
```

---

## 📊 Viewing Results

### Admin Dashboard:
```
https://yourdomain.com/admin
```

Shows:
- Total visits
- Campaign statistics
- Email addresses captured
- Device/browser breakdown
- Export options

### API Access:
```bash
# All visits
curl "https://yourdomain.com/api/get-visits.php"

# Specific campaign
curl "https://yourdomain.com/api/get-visits.php?key=rfp2024"

# All campaigns
curl "https://yourdomain.com/api/storage.php?action=list"
```

---

## 🔧 Troubleshooting

### "500 Internal Server Error"
**Check:**
1. PHP version is 7.4+ (cPanel → Select PHP Version)
2. File permissions: folders 755, files 644
3. `.htaccess` file uploaded correctly
4. `data` directory has 755 permissions

### "Cannot write to data directory"
**Fix permissions in cPanel File Manager:**
1. Right-click `data` folder
2. Select "Change Permissions"
3. Set to `755`
4. Check "Recurse into subdirectories"
5. Click "Change Permissions"

### "Page not found"
**Check:**
1. `.htaccess` file is in the root directory
2. mod_rewrite is enabled (usually is on cPanel)
3. Files are in correct location

### Admin dashboard blank
**Check:**
1. Create at least one campaign first
2. Verify setup.php completed successfully
3. Check browser console for errors
4. Ensure `data/campaigns.json` and `data/visits.json` exist

### "Permission denied" errors
**Set correct permissions:**
```
Folders: 755
Files: 644
data/ directory: 755
data/*.json files: 644
```

---

## 📁 File Permissions

Set in cPanel File Manager (right-click → Change Permissions):

**Folders:** 755
- `public_html/`
- `api/`
- `data/` ← Important!

**Files:** 644
- `*.html`
- `*.php`
- `.htaccess`
- `data/*.json` ← Important!

**config.php:** 640 (more secure)

---

## 🔐 Security Hardening

### 1. Protect Data Directory
The `data/.htaccess` file (auto-created) protects JSON files from direct access:
```apache
Deny from all
```

### 2. Delete setup.php
```bash
rm setup.php
```

### 3. Protect config.php
Add to main `.htaccess`:
```apache
<Files "config.php">
    Order Allow,Deny
    Deny from all
</Files>
```

### 4. Enable HTTPS
In cPanel:
- Go to **SSL/TLS**
- Install free Let's Encrypt certificate
- Force HTTPS redirect

### 5. Regular Backups
Use cPanel **Backup** to backup:
- All files (especially `data/` directory)
- Run weekly backups

---

## 💾 Data Storage

### Files:
- `data/campaigns.json` - All campaign configurations
- `data/visits.json` - All visit logs and analytics

### Structure:

**campaigns.json:**
```json
{
  "campaign1": {
    "sender": "John Smith",
    "title": "RFP Project",
    "url": "https://yourdomain.com/training",
    "created_at": "2024-10-20T14:30:00+00:00",
    "updated_at": "2024-10-20T14:30:00+00:00"
  }
}
```

**visits.json:**
```json
{
  "visits": {
    "visit_123": {
      "ip": "192.168.1.1",
      "email": "user@example.com",
      "device": "Windows 10 - Chrome",
      "mx_type": "office365",
      "document_key": "campaign1",
      "timestamp": "2024-10-20T14:30:00+00:00"
    }
  },
  "stats": { ... },
  "by_key": { ... }
}
```

---

## 💾 Backup & Export

### Manual Backup:
In cPanel File Manager:
1. Right-click `data` folder
2. Select **"Compress"**
3. Choose ZIP format
4. Download the archive

### Export Data (JSON):
```bash
# Export visits
curl "https://yourdomain.com/api/get-visits.php" > backup-visits.json

# Export campaigns
curl "https://yourdomain.com/api/storage.php?action=list" > backup-campaigns.json
```

### Automated Backup:
Create cron job in cPanel:
```bash
# Daily at 2 AM
0 2 * * * cd /home/username/public_html/data && tar -czf backup-$(date +\%Y\%m\%d).tar.gz *.json
```

---

## 🚀 Advantages

### vs. MySQL:
✅ **No database setup** - Just upload files  
✅ **No credentials** - Nothing to configure  
✅ **Easy backups** - Just copy files  
✅ **Portable** - Works everywhere  
✅ **Simple** - View data with any text editor  

### vs. MongoDB:
✅ **No external service** - Everything local  
✅ **No dependencies** - Just PHP  
✅ **Fast** - No network latency  
✅ **Free** - No subscription fees  

### vs. Vercel:
✅ **File write works** - No read-only issues  
✅ **All PHP extensions** - Full compatibility  
✅ **Standard hosting** - Familiar environment  
✅ **Easier debugging** - Direct file access  

---

## 📊 Performance

**Expected Performance:**
- Handles 10,000+ visits easily
- Fast read/write operations
- No database overhead
- Suitable for most organizations

**Scalability:**
- **< 50,000 visits:** Perfect
- **50,000 - 100,000:** Works well
- **> 100,000:** Consider migrating to MySQL

**File Sizes:**
- 1,000 visits ≈ 250 KB
- 10,000 visits ≈ 2.5 MB
- 50,000 visits ≈ 12 MB

---

## 🔄 Migrating to MySQL (If Needed)

If your JSON files get too large:

1. Create MySQL database in cPanel
2. Export JSON data
3. Import into MySQL using provided migration script
4. Update `config.php` to use MySQL
5. Done!

(Migration script available on request)

---

## 📋 Quick Checklist

- [ ] Files uploaded to cPanel
- [ ] Ran setup.php successfully
- [ ] Deleted/secured setup.php
- [ ] Data directory has 755 permissions
- [ ] JSON files created and writable
- [ ] Tested admin dashboard
- [ ] Created test campaign
- [ ] Tested campaign link
- [ ] Verified visit logging works
- [ ] Set up SSL certificate
- [ ] Configured regular backups

**All checked?** You're ready to train! 🎉

---

## 🎓 Email Templates

See [EMAIL-TEMPLATES.md](./EMAIL-TEMPLATES.md) for 8 ready-to-use phishing scenarios.

---

## 🔗 Useful Links

- **Admin Dashboard:** https://yourdomain.com/admin
- **Training Page:** https://yourdomain.com/training  
- **API Docs:** [README.md](./README.md)
- **Email Templates:** [EMAIL-TEMPLATES.md](./EMAIL-TEMPLATES.md)

---

## 💡 Pro Tips

1. **Backup Often:** JSON files are easy to backup - do it weekly
2. **Monitor Size:** Check file sizes monthly, migrate to MySQL if > 10 MB
3. **Test Permissions:** After server updates, verify `data/` is still writable
4. **Keep Simple:** Don't over-engineer - JSON files work great for most use cases

---

**Total Setup Time:** 5 minutes  
**Technical Difficulty:** Easy  
**Cost:** $0 (just hosting)  

**This is the simplest possible deployment!** 🚀
