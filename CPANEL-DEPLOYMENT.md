# cPanel Hosting Deployment Guide

## ✅ Perfect for Shared Hosting!

This version works on **any cPanel shared hosting** with:
- PHP 7.4 or higher
- MySQL database
- Standard file permissions

**No Vercel, no MongoDB, no complications!**

---

## 📦 Step 1: Upload Files

### Option A: File Manager
1. Log into cPanel
2. Open **File Manager**
3. Navigate to `public_html` (or your domain folder)
4. Upload all files from the `b2b` folder
5. Extract if uploaded as ZIP

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

## 🗄️ Step 2: Create MySQL Database

### In cPanel:
1. Go to **MySQL Databases**
2. Click **"Create New Database"**
3. Database name: `phishing_sim` (or your choice)
4. Click **"Create Database"**

### Create Database User:
1. Scroll down to **"MySQL Users"**
2. Username: `phishing_admin` (or your choice)
3. Password: Click **"Generate Password"** → **Save it!**
4. Click **"Create User"**

### Add User to Database:
1. Scroll to **"Add User to Database"**
2. Select your user and database
3. Click **"Add"**
4. Check **"ALL PRIVILEGES"**
5. Click **"Make Changes"**

**Save these details:**
- Database name: `username_phishing_sim`
- Username: `username_phishing_admin`
- Password: (the one you saved)

---

## ⚙️ Step 3: Run Setup

1. Open your browser
2. Go to: `https://yourdomain.com/setup.php`
3. Enter your database details:
   - **Host:** `localhost`
   - **Database:** `username_phishing_sim`
   - **Username:** `username_phishing_admin`
   - **Password:** Your saved password
4. Click **"Install & Configure"**

The setup will:
- ✅ Test database connection
- ✅ Update `config.php` with your details
- ✅ Create required tables (`campaigns` and `visits`)

---

## 🔒 Step 4: Secure Setup File

After setup completes:

```bash
# Option 1: Delete (recommended)
rm setup.php

# Option 2: Rename
mv setup.php setup-backup.php
```

Or in cPanel File Manager:
- Right-click `setup.php` → Delete

---

## ✅ Step 5: Test Everything

### Test Admin Dashboard:
```
https://yourdomain.com/admin
```

Should load without errors (will be empty until you create campaigns)

### Test API:
Create a test campaign:
```bash
curl "https://yourdomain.com/api/storage.php?action=set&key=test&sender=Test%20User&title=Test%20Campaign&url=https://yourdomain.com/training"
```

Expected response:
```json
{"success":true,"message":"Data stored"}
```

### View Campaigns:
```bash
curl "https://yourdomain.com/api/storage.php?action=list"
```

### Test Main Page:
```
https://yourdomain.com/adb_token.html#test
```

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
```

---

## 🔧 Troubleshooting

### "500 Internal Server Error"
**Check:**
1. PHP version is 7.4+ (cPanel → Select PHP Version)
2. File permissions: folders 755, files 644
3. `.htaccess` file uploaded correctly
4. Check error logs in cPanel

### "Database connection failed"
**Check:**
1. Database name, username, password in `config.php`
2. Database user has ALL PRIVILEGES
3. Database exists and is accessible

### "Page not found"
**Check:**
1. `.htaccess` file is in the root directory
2. mod_rewrite is enabled (usually is on cPanel)
3. URL rewriting works on your host

### Admin dashboard blank
**Check:**
1. Create at least one campaign first
2. Test campaign creation via curl
3. Check browser console for errors
4. Verify API endpoints work

---

## 📁 File Permissions

Set correct permissions in cPanel File Manager:

**Folders:** 755
```
public_html/
api/
```

**Files:** 644
```
*.html
*.php
.htaccess
```

**config.php:** 640 (more secure)
```
chmod 640 config.php
```

---

## 🔐 Security Hardening

### 1. Protect config.php
Add to `.htaccess`:
```apache
<Files "config.php">
    Order Allow,Deny
    Deny from all
</Files>
```

### 2. Delete setup.php
```bash
rm setup.php
```

### 3. Enable HTTPS
In cPanel → SSL/TLS → Install free Let's Encrypt certificate

### 4. Regular Backups
Use cPanel → Backup to backup:
- Database
- Files

---

## 🚀 Advantages Over Vercel

✅ **Full PHP support** - All extensions available  
✅ **MySQL included** - No external database needed  
✅ **File persistence** - Can write files if needed  
✅ **Simpler setup** - No build process  
✅ **Standard hosting** - Works everywhere  
✅ **Lower cost** - Often cheaper than managed services  

---

## 📊 Database Structure

### campaigns table:
```sql
- id (VARCHAR 255, PRIMARY KEY)
- sender (VARCHAR 255)
- title (VARCHAR 255)
- url (TEXT)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

### visits table:
```sql
- id (INT, AUTO_INCREMENT)
- ip (VARCHAR 45)
- email (VARCHAR 255)
- device (VARCHAR 500)
- mx_type (VARCHAR 50)
- document_key (VARCHAR 255)
- timestamp (TIMESTAMP)
```

---

## 💾 Backup & Export

### Export Database:
cPanel → phpMyAdmin → Export → Go

### Export Visits (JSON):
```bash
curl "https://yourdomain.com/api/get-visits.php" > backup.json
```

### Restore Database:
cPanel → phpMyAdmin → Import → Choose file

---

## 🎓 Email Templates

See [EMAIL-TEMPLATES.md](./EMAIL-TEMPLATES.md) for 8 ready-to-use phishing scenarios.

---

## ✅ Quick Setup Checklist

- [ ] Files uploaded to cPanel
- [ ] MySQL database created
- [ ] Database user created and assigned
- [ ] Ran setup.php successfully
- [ ] Deleted/secured setup.php
- [ ] Tested admin dashboard
- [ ] Created test campaign
- [ ] Tested campaign link
- [ ] Verified visit logging works
- [ ] Set up SSL certificate

**All checked?** You're ready to start training! 🎉

---

## 🔗 Useful Links

- **Admin Dashboard:** https://yourdomain.com/admin
- **Training Page:** https://yourdomain.com/training
- **API Docs:** [README.md](./README.md)
- **Email Templates:** [EMAIL-TEMPLATES.md](./EMAIL-TEMPLATES.md)

---

**Need help?** Check cPanel error logs or contact your hosting support.
