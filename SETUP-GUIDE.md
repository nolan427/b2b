# Complete Setup Guide - Phishing Simulator

## 🚀 Quick Overview

**Total Time:** 10 minutes  
**Cost:** $0 (100% free)  
**Required:** MongoDB Atlas account (no credit card!)

---

## Step 1: MongoDB Atlas Setup (5 min)

### Why MongoDB Atlas?
- ✅ **512 MB free storage** (enough for 500,000 visits)
- ✅ **No credit card required**
- ✅ **Easier sign-up than Redis**
- ✅ **Works perfectly with Vercel**

### Create Account

1. Go to: [https://www.mongodb.com/cloud/atlas/register](https://www.mongodb.com/cloud/atlas/register)
2. Sign up with email (or Google/GitHub)
3. Verify your email

### Create Database

1. Click **"Build a Database"**
2. Choose **"M0 FREE"** tier ← Important!
3. Keep default settings
4. Click **"Create"**
5. Wait 1-2 minutes for deployment

### Create User

1. **Security Quickstart** will appear
2. **Authentication:** Choose "Username and Password"
3. Username: `admin` (or your choice)
4. Password: Click **"Autogenerate"** - SAVE THIS PASSWORD!
5. Click **"Create User"**

### Allow Network Access

1. **Where would you like to connect from?**
2. Choose **"Cloud Environment"**
3. Add: `0.0.0.0/0` (allows Vercel)
4. Click **"Finish and Close"**

### Get Connection String

1. Click **"Connect"** on your cluster
2. Choose **"Connect your application"**
3. Copy the connection string:
   ```
   mongodb+srv://admin:<password>@cluster0.xxxxx.mongodb.net/?retryWrites=true&w=majority
   ```
4. **Replace `<password>`** with your saved password

**Example:**
```
mongodb+srv://admin:MyP@ssw0rd123@cluster0.abc123.mongodb.net/?retryWrites=true&w=majority
```

✅ **MongoDB setup complete!**

---

## Step 2: Deploy to Vercel (5 min)

### Install Dependencies

```bash
# Navigate to project directory
cd b2b

# Install MongoDB library
composer install
```

### Deploy

```bash
# Install Vercel CLI (if needed)
npm i -g vercel

# Login
vercel login

# Deploy
vercel --prod
```

### Add Environment Variable

1. Go to Vercel dashboard → Your project
2. Click **Settings** → **Environment Variables**
3. Add variable:
   - Name: `MONGODB_URI`
   - Value: Your connection string from Step 1
   - Environments: Check all three boxes
4. Click **"Save"**
5. Redeploy: `vercel --prod`

✅ **Deployment complete!**

---

## Step 3: Create First Campaign (1 min)

```bash
curl "https://your-domain.vercel.app/api/storage.php?action=set&key=campaign1&sender=John%20Smith%20-%20ABC%20Corp&title=RFP%20%234523%20Project&url=https://your-domain.vercel.app/training-reveal.html"
```

Replace `your-domain` with your actual Vercel URL.

Expected response:
```json
{"success":true,"message":"Data stored"}
```

---

## Step 4: Test It! (1 min)

### Share Campaign Link

```
https://your-domain.vercel.app/adb_token.html#campaign1
```

1. Open this link
2. Enter a test email
3. You'll be redirected to training page

### Check Dashboard

```
https://your-domain.vercel.app/admin
```

You should see your test visit!

✅ **Everything working!**

---

## Common Issues & Solutions

### "MONGODB_URI not configured"

**Fix:**
1. Check environment variable in Vercel settings
2. Spelling must be exact: `MONGODB_URI`
3. Redeploy after adding: `vercel --prod`

### "Authentication failed"

**Fix:**
1. Password might have special characters
2. URL-encode special characters in connection string:
   - `@` becomes `%40`
   - `:` becomes `%3A`
   - `#` becomes `%23`

**Example:**
```
# Original password: p@ss:123
# Connection string: mongodb+srv://admin:p%40ss%3A123@cluster0...
```

### "Connection timeout"

**Fix:**
1. Verify Atlas Network Access allows `0.0.0.0/0`
2. Wait 5 minutes for changes to propagate
3. Check cluster status in Atlas (should be green)

### "composer: command not found"

**Fix:**
```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
php composer.phar install

# Or install globally: https://getcomposer.org/download/
```

---

## Next Steps

### Create More Campaigns

See [EMAIL-TEMPLATES.md](./EMAIL-TEMPLATES.md) for 8 ready-to-use scenarios:
- RFP/Bid invitations
- Invoice reviews
- HR benefits
- IT security alerts
- Adobe license renewals
- Contract signatures
- Payroll updates
- CEO requests

### Monitor Results

**Admin Dashboard:**
```
https://your-domain.vercel.app/admin
```

**API Access:**
```bash
# All visits
curl "https://your-domain.vercel.app/api/get-visits.php"

# Specific campaign
curl "https://your-domain.vercel.app/api/get-visits.php?key=campaign1"

# All campaigns
curl "https://your-domain.vercel.app/api/storage.php?action=list"
```

### Send to Employees

1. Pick appropriate template from [EMAIL-TEMPLATES.md](./EMAIL-TEMPLATES.md)
2. Customize for your organization
3. Send to test group first
4. Monitor dashboard
5. Send follow-up training email

---

## MongoDB Free Tier Limits

| Metric | Limit | Enough For |
|--------|-------|------------|
| Storage | 512 MB | ~500,000 visits |
| RAM | Shared | Normal traffic |
| Connections | 100 | Serverless functions |
| Duration | Forever | ∞ |

Perfect for training simulations!

---

## Cost Breakdown

| Service | Cost |
|---------|------|
| MongoDB Atlas | $0/month (free tier) |
| Vercel | $0/month (hobby tier) |
| Domain | $10/year (optional) |
| **Total** | **$0/month** |

---

## Managing Your Data

### View in Atlas

1. Go to MongoDB Atlas dashboard
2. Click **"Browse Collections"**
3. See `phishing.visits` and `phishing.campaigns`
4. Search, filter, export

### Export Backups

```bash
# Backup visits
curl "https://your-domain.vercel.app/api/get-visits.php" > backup-visits-$(date +%Y%m%d).json

# Backup campaigns
curl "https://your-domain.vercel.app/api/storage.php?action=list" > backup-campaigns-$(date +%Y%m%d).json
```

Schedule weekly backups!

---

## Architecture

```
Frontend (HTML/JS)
├── adb_token.html - Main phishing page
├── training-reveal.html - Educational reveal
└── admin-dashboard.html - Analytics

Backend (PHP)
├── api/log-visit.php - Log visits to MongoDB
├── api/get-visits.php - Retrieve analytics
└── api/storage.php - Campaign management

Database (MongoDB Atlas)
├── phishing.visits - Visit logs
└── phishing.campaigns - Campaign configs

Hosting
└── Vercel (serverless PHP functions)
```

---

## Security Notes

### What's Logged
- ✅ Email addresses (as entered by user)
- ✅ IP addresses
- ✅ Device/browser info
- ✅ Timestamps

### What's NOT Logged
- ❌ Passwords
- ❌ Personal identification
- ❌ Payment information
- ❌ Session cookies

### Database Security
- ✅ Encrypted at rest (MongoDB)
- ✅ Encrypted in transit (TLS)
- ✅ Authentication required
- ✅ HTTPS only (Vercel)

---

## Troubleshooting Checklist

- [ ] MongoDB cluster is running (green in Atlas)
- [ ] `MONGODB_URI` environment variable is set in Vercel
- [ ] Connection string has correct password
- [ ] Network Access allows `0.0.0.0/0`
- [ ] `composer install` completed successfully
- [ ] Vercel deployment succeeded
- [ ] Test campaign created successfully
- [ ] Admin dashboard loads without errors

---

## Getting Help

**MongoDB Issues:**
- [Atlas Documentation](https://www.mongodb.com/docs/atlas/)
- [Connection String Format](https://www.mongodb.com/docs/manual/reference/connection-string/)

**Vercel Issues:**
- [Vercel PHP Runtime](https://vercel.com/docs/runtimes#official-runtimes/php)
- [Environment Variables](https://vercel.com/docs/concepts/projects/environment-variables)

**This Project:**
- [Full README](./README.md)
- [Email Templates](./EMAIL-TEMPLATES.md)
- [Improvements Guide](./IMPROVEMENTS.md)

---

## ✅ Checklist Summary

- [ ] MongoDB Atlas account created
- [ ] Free M0 cluster deployed
- [ ] Database user created
- [ ] Network access configured (0.0.0.0/0)
- [ ] Connection string saved
- [ ] `composer install` run
- [ ] Deployed to Vercel
- [ ] `MONGODB_URI` environment variable set
- [ ] Test campaign created
- [ ] Test email submitted
- [ ] Dashboard showing data

**All done?** You're ready to train employees! 🎉

---

**Questions?** See [MONGODB-SETUP.md](./MONGODB-SETUP.md) for detailed MongoDB guide.
