# 🎉 cPanel Version - Simple & Ready!

## What Changed

Your phishing simulator now works on **any cPanel shared hosting**!

### ✅ Major Improvements:

**No More Complex Setup:**
- ❌ No Vercel needed
- ❌ No MongoDB complications
- ❌ No build process
- ❌ No serverless issues

**Simple MySQL Database:**
- ✅ Standard MySQL (included with cPanel)
- ✅ Easy to set up
- ✅ PHPMyAdmin access
- ✅ Works everywhere

**Easy Installation:**
- ✅ Upload files
- ✅ Create MySQL database
- ✅ Run setup.php
- ✅ Done!

---

## 🚀 Quick Start (10 Minutes)

### 1. Upload Files to cPanel
- Use File Manager or FTP
- Upload to `public_html`

### 2. Create MySQL Database
- cPanel → MySQL Databases
- Create database and user
- Note credentials

### 3. Run Setup
- Visit: `https://yourdomain.com/setup.php`
- Enter database details
- Click "Install"

### 4. Start Using!
- Dashboard: `https://yourdomain.com/admin`
- Create campaigns via API
- Send phishing emails
- Monitor results

---

## 📖 Full Documentation

**For cPanel Deployment:**
- [CPANEL-DEPLOYMENT.md](./CPANEL-DEPLOYMENT.md) - Complete setup guide

**For Email Campaigns:**
- [EMAIL-TEMPLATES.md](./EMAIL-TEMPLATES.md) - 8 ready-to-use templates

**General Info:**
- [README.md](./README.md) - Full system documentation
- [IMPROVEMENTS.md](./IMPROVEMENTS.md) - Enhancement ideas

---

## 🎯 What You Get

**All Features Working:**
- ✅ Adobe Document Cloud simulator
- ✅ Email provider detection
- ✅ Device fingerprinting
- ✅ Training reveal page
- ✅ Admin analytics dashboard
- ✅ Campaign management
- ✅ Visit logging
- ✅ Export data

**Database:**
- MySQL (standard on all cPanel)
- Two tables: `campaigns` and `visits`
- Auto-created by setup script

---

## 💰 Cost

**Typical cPanel Hosting:**
- $3-10/month (shared hosting)
- MySQL included
- No additional costs
- Often unlimited storage

**vs. Vercel + MongoDB Atlas:**
- Simpler
- More reliable
- Better PHP support
- Standard hosting everyone knows

---

## 🔧 Files Structure

```
public_html/
├── config.php          # Database configuration
├── setup.php           # One-time setup script
├── .htaccess           # URL routing
├── index.html          # Main phishing page
├── adb_token.html      # Adobe portal (main)
├── admin-dashboard.html # Analytics dashboard
├── training-reveal.html # Educational page
└── api/
    ├── storage.php     # Campaign management
    ├── log-visit.php   # Visit logging
    └── get-visits.php  # Analytics API
```

---

## ⚙️ Configuration

Edit `config.php` after setup:

```php
// Database (auto-configured by setup.php)
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database');
define('DB_USER', 'your_user');
define('DB_PASS', 'your_password');

// Application settings
define('APP_NAME', 'Adobe Document Cloud');
define('TIMEZONE', 'America/New_York');
```

---

## 🎓 Creating Your First Campaign

```bash
curl "https://yourdomain.com/api/storage.php?action=set&key=campaign1&sender=John%20Smith&title=RFP%20Project&url=https://yourdomain.com/training"
```

**Share this link:**
```
https://yourdomain.com/adb_token.html#campaign1
```

---

## 📊 Monitoring Results

**Admin Dashboard:**
```
https://yourdomain.com/admin
```

**API Access:**
```bash
# All visits
curl "https://yourdomain.com/api/get-visits.php"

# Specific campaign
curl "https://yourdomain.com/api/get-visits.php?key=campaign1"

# All campaigns
curl "https://yourdomain.com/api/storage.php?action=list"
```

---

## 🔒 Security

**After Setup:**
1. Delete `setup.php`
2. Set `config.php` to 640 permissions
3. Enable SSL in cPanel (free Let's Encrypt)
4. Regular backups

**Database Security:**
- Strong password
- Localhost only access
- Regular backups via cPanel

---

## 🆚 Why cPanel Instead of Vercel?

| Feature | cPanel | Vercel |
|---------|--------|--------|
| Setup | ✅ Easy | ❌ Complex |
| PHP Extensions | ✅ All available | ❌ Limited |
| Database | ✅ MySQL included | ❌ External needed |
| File Write | ✅ Yes | ❌ Read-only |
| Cost | ✅ $3-10/mo | ✅ Free (but complex) |
| Debugging | ✅ Easy | ❌ Harder |
| Standard | ✅ Yes | ❌ Serverless-specific |

---

## 🔄 Migrating from Vercel?

If you were trying to use Vercel:

1. ✅ **Forget about Vercel** - Use cPanel instead
2. ✅ **No MongoDB needed** - MySQL works better
3. ✅ **No build process** - Just upload files
4. ✅ **Simpler everything** - Standard PHP hosting

---

## 📞 Support

**cPanel Issues:**
- Contact your hosting provider
- Check cPanel documentation
- PHPMyAdmin for database

**Application Issues:**
- Check error logs in cPanel
- Verify setup.php completed
- Test API endpoints

**Questions:**
- See [CPANEL-DEPLOYMENT.md](./CPANEL-DEPLOYMENT.md)
- Check [EMAIL-TEMPLATES.md](./EMAIL-TEMPLATES.md)

---

## ✅ Quick Checklist

- [ ] Files uploaded to cPanel
- [ ] MySQL database created
- [ ] Setup completed successfully
- [ ] setup.php deleted/secured
- [ ] Admin dashboard accessible
- [ ] Test campaign created
- [ ] Campaign link working
- [ ] Visit logging confirmed
- [ ] SSL certificate installed

**All done?** Start training your employees! 🎉

---

**This version is production-ready and works on ANY cPanel hosting!** 🚀
