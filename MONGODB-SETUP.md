# MongoDB Atlas Setup Guide

## Why MongoDB Atlas?

MongoDB Atlas has a **generous free tier** and **easier sign-up** than Redis (no payment card issues).

✅ **512 MB storage free**  
✅ **No credit card required for free tier**  
✅ **Easy email sign-up**  
✅ **Perfect for this use case**  

## Step-by-Step Setup (5 minutes)

### 1. Create MongoDB Atlas Account

1. Go to [https://www.mongodb.com/cloud/atlas/register](https://www.mongodb.com/cloud/atlas/register)
2. Sign up with your email (or Google/GitHub)
3. **No credit card required!**

### 2. Create Free Cluster

1. Click "Build a Database"
2. Choose **"M0 FREE"** tier
3. Select a region close to you (or keep default)
4. Cluster name: keep default or name it "phishing-sim"
5. Click **"Create"**

Wait 1-3 minutes for cluster to deploy.

### 3. Create Database User

1. Click "Database Access" in left sidebar
2. Click **"Add New Database User"**
3. Authentication Method: **Password**
4. Username: `phishing-admin` (or your choice)
5. Password: Click **"Autogenerate Secure Password"** - **SAVE THIS!**
6. Database User Privileges: **"Read and write to any database"**
7. Click **"Add User"**

### 4. Allow Network Access

1. Click "Network Access" in left sidebar
2. Click **"Add IP Address"**
3. Click **"Allow Access from Anywhere"** (0.0.0.0/0)
   - This is safe for read-only phishing data
   - Vercel functions come from multiple IPs
4. Click **"Confirm"**

### 5. Get Connection String

1. Click "Database" in left sidebar
2. Click **"Connect"** button on your cluster
3. Choose **"Connect your application"**
4. Driver: **PHP**, Version: **1.13 or later**
5. Copy the connection string - looks like:
   ```
   mongodb+srv://phishing-admin:<password>@cluster0.xxxxx.mongodb.net/?retryWrites=true&w=majority
   ```
6. **Replace `<password>`** with the password you saved in Step 3

### 6. Add to Vercel

1. Go to your Vercel project dashboard
2. Click **Settings** → **Environment Variables**
3. Add new variable:
   - **Name:** `MONGODB_URI`
   - **Value:** Your connection string from Step 5
   - **Environments:** Check all (Production, Preview, Development)
4. Click **"Save"**

### 7. Install Dependencies & Deploy

```bash
# Install MongoDB PHP library
composer install

# Deploy to Vercel
vercel --prod
```

Done! Your phishing simulator now uses MongoDB Atlas.

---

## Connection String Format

```
mongodb+srv://USERNAME:PASSWORD@CLUSTER.xxxxx.mongodb.net/?retryWrites=true&w=majority
```

**Example:**
```
mongodb+srv://phishing-admin:MySecureP@ssw0rd@cluster0.abc123.mongodb.net/?retryWrites=true&w=majority
```

---

## Database Structure

MongoDB will automatically create these collections:

### `phishing.visits`
```javascript
{
  "_id": ObjectId("..."),
  "ip": "192.168.1.1",
  "email": "user@example.com",
  "device": "Windows 10/11 - Google Chrome",
  "mx_type": "office365",
  "document_key": "campaign1",
  "timestamp": ISODate("2024-10-20T14:30:00Z")
}
```

### `phishing.campaigns`
```javascript
{
  "_id": "campaign1",
  "sender": "John Smith - ABC Construction",
  "title": "RFP #4523 - Office Building Project",
  "url": "https://your-domain.vercel.app/training-reveal.html",
  "updated_at": ISODate("2024-10-20T14:30:00Z")
}
```

---

## Testing Your Connection

### Method 1: Create Test Campaign

```bash
curl "https://your-domain.vercel.app/api/storage.php?action=set&key=test&sender=Test%20Sender&title=Test%20Campaign&url=https://example.com"
```

Expected response:
```json
{"success":true,"message":"Data stored"}
```

### Method 2: List Campaigns

```bash
curl "https://your-domain.vercel.app/api/storage.php?action=list"
```

Expected response:
```json
{
  "success": true,
  "docs": {
    "test": {
      "sender": "Test Sender",
      "title": "Test Campaign",
      "url": "https://example.com"
    }
  }
}
```

### Method 3: Check Dashboard

Visit: `https://your-domain.vercel.app/admin`

Should load without errors.

---

## Troubleshooting

### "MONGODB_URI not configured"

**Solution:**
1. Verify environment variable is set in Vercel
2. Redeploy after adding variable: `vercel --prod`
3. Check spelling: `MONGODB_URI` (all caps, underscore)

### "Authentication failed"

**Solution:**
1. Verify password in connection string is correct
2. Check for special characters - URL encode them:
   - `@` → `%40`
   - `:` → `%3A`
   - `/` → `%2F`
3. Example: `p@ssw0rd:123` → `p%40ssw0rd%3A123`

### "Connection timeout"

**Solution:**
1. Check Network Access allows 0.0.0.0/0
2. Wait a few minutes for IP allowlist to propagate
3. Verify cluster is running (green status in Atlas)

### "Composer install failed"

**Solution:**
```bash
# Remove old dependencies
rm -rf vendor/ composer.lock

# Install fresh
composer require mongodb/mongodb

# Deploy
vercel --prod
```

---

## MongoDB Atlas Free Tier Limits

✅ **512 MB storage** - Stores ~500,000 visit records  
✅ **Shared RAM** - More than enough for this use  
✅ **100 connections** - Plenty for serverless functions  
✅ **No time limit** - Free forever  

Perfect for phishing simulations!

---

## Viewing Your Data

### Option 1: MongoDB Atlas Dashboard

1. Go to Atlas dashboard
2. Click **"Browse Collections"**
3. View `phishing.visits` and `phishing.campaigns`
4. Search, filter, and export data

### Option 2: Your Admin Dashboard

Visit: `https://your-domain.vercel.app/admin`

### Option 3: API

```bash
# Get all visits
curl "https://your-domain.vercel.app/api/get-visits.php"

# Get visits for campaign
curl "https://your-domain.vercel.app/api/get-visits.php?key=campaign1"
```

---

## Backup & Export

### Export from Atlas Dashboard

1. Go to Collections browser
2. Click collection name
3. Click **"Export Collection"**
4. Choose JSON or CSV
5. Download

### Export via API

```bash
# Export all data as JSON
curl "https://your-domain.vercel.app/api/get-visits.php" > backup.json

# Export campaigns
curl "https://your-domain.vercel.app/api/storage.php?action=list" > campaigns.json
```

---

## Cost

**Free Tier:** $0/month forever  
**Next Tier (M10):** $9/month (if you outgrow free tier)

For typical phishing simulations, **free tier is plenty!**

---

## Upgrading from JSON (if you tried that first)

If you tried the JSON version:

1. Set up MongoDB Atlas (above steps)
2. Add `MONGODB_URI` to Vercel
3. Run `composer install`
4. Deploy: `vercel --prod`
5. Recreate campaigns via API
6. JSON data won't auto-migrate (but you have backups)

---

## Support

**MongoDB Atlas Issues:**
- [Atlas Documentation](https://www.mongodb.com/docs/atlas/)
- [Atlas Support](https://www.mongodb.com/cloud/atlas/support)

**Vercel Deployment:**
- Check deployment logs in Vercel dashboard
- Verify `MONGODB_URI` environment variable
- Ensure `composer install` ran successfully

---

## Quick Reference

```bash
# Install dependencies
composer install

# Deploy
vercel --prod

# Create campaign
curl "https://DOMAIN/api/storage.php?action=set&key=KEY&sender=SENDER&title=TITLE&url=URL"

# View campaigns
curl "https://DOMAIN/api/storage.php?action=list"

# View visits
curl "https://DOMAIN/api/get-visits.php"

# Admin dashboard
https://DOMAIN/admin
```

---

**That's it!** MongoDB Atlas is free, easy, and works perfectly with Vercel. 🎉
