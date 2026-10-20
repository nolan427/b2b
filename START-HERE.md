# 🚀 START HERE - Phishing Simulator Setup

## ✅ Solution: MongoDB Atlas (Free!)

**You were right!** Vercel's serverless functions have **read-only filesystems**, so JSON files don't work.

Instead, we're using **MongoDB Atlas** which:
- ✅ Has a generous **free tier** (512 MB)
- ✅ **No credit card required** for free tier
- ✅ Easier sign-up than Redis
- ✅ Works perfectly with Vercel

---

## 📋 10-Minute Setup

### Step 1: MongoDB Atlas (5 minutes)

1. **Create account:** [https://www.mongodb.com/cloud/atlas/register](https://www.mongodb.com/cloud/atlas/register)
   - Sign up with email (no credit card!)

2. **Create database:**
   - Click "Build a Database"
   - Choose **"M0 FREE"** tier
   - Click "Create"
   
3. **Create user:**
   - Username: `admin` (or your choice)
   - Password: Click "Autogenerate" → **SAVE THIS!**
   - Click "Create User"
   
4. **Allow access:**
   - Click "Add IP Address"
   - Choose "Allow Access from Anywhere" (`0.0.0.0/0`)
   - Click "Confirm"
   
5. **Get connection string:**
   - Click "Connect" → "Connect your application"
   - Copy the string (looks like):
     ```
     mongodb+srv://admin:<password>@cluster0.xxxxx.mongodb.net/?retryWrites=true&w=majority
     ```
   - Replace `<password>` with your saved password

### Step 2: Deploy (5 minutes)

```bash
# Install dependencies
composer install

# Deploy to Vercel
npm i -g vercel
vercel login
vercel --prod
```

### Step 3: Add Connection String

1. Go to Vercel dashboard → Your project
2. Settings → Environment Variables
3. Add:
   - **Name:** `MONGODB_URI`
   - **Value:** Your connection string from Step 1
4. Click "Save"
5. Redeploy: `vercel --prod`

### Step 4: Test It!

```bash
# Create test campaign
curl "https://your-domain.vercel.app/api/storage.php?action=set&key=test&sender=Test%20User&title=Test%20Campaign&url=https://example.com"

# Should return:
# {"success":true,"message":"Data stored"}
```

Visit your dashboard:
```
https://your-domain.vercel.app/admin
```

✅ **Done!**

---

## 📖 Full Documentation

- **[SETUP-GUIDE.md](./SETUP-GUIDE.md)** - Complete step-by-step guide
- **[MONGODB-SETUP.md](./MONGODB-SETUP.md)** - Detailed MongoDB instructions
- **[EMAIL-TEMPLATES.md](./EMAIL-TEMPLATES.md)** - 8 ready-to-use phishing scenarios
- **[README.md](./README.md)** - Full system documentation

---

## ❓ Why MongoDB Instead of JSON?

**Problem:** Vercel serverless functions have read-only filesystems  
**Solution:** External database (MongoDB Atlas)

**Why MongoDB Atlas?**
- ✅ 512 MB free (enough for ~500,000 visits)
- ✅ No credit card for free tier
- ✅ Easy sign-up process
- ✅ Works great with Vercel
- ✅ Better than Redis for your use case

---

## 🎯 Quick Commands

```bash
# Create campaign
curl "https://DOMAIN/api/storage.php?action=set&key=CAMPAIGN_KEY&sender=NAME&title=TITLE&url=URL"

# View all campaigns
curl "https://DOMAIN/api/storage.php?action=list"

# View all visits
curl "https://DOMAIN/api/get-visits.php"

# View visits for specific campaign
curl "https://DOMAIN/api/get-visits.php?key=CAMPAIGN_KEY"
```

---

## 🔧 Troubleshooting

### "MONGODB_URI not configured"
- Check Vercel environment variables
- Must be spelled exactly: `MONGODB_URI`
- Redeploy after adding

### "Authentication failed"
- Check password in connection string
- URL-encode special characters:
  - `@` → `%40`
  - `:` → `%3A`
  - `#` → `%23`

### "Connection timeout"
- Verify Network Access allows `0.0.0.0/0`
- Wait 5 minutes for changes to apply
- Check cluster is running in Atlas

---

## 💰 Cost

| Service | Cost |
|---------|------|
| MongoDB Atlas | $0/month (free tier) |
| Vercel | $0/month (hobby tier) |
| **Total** | **$0/month** |

---

## 📊 What You Get

**Database Storage:**
- 512 MB (enough for ~500,000 visit logs)
- Shared RAM
- 100 concurrent connections
- Forever free

**Features:**
- Realistic Adobe phishing pages
- Email provider detection
- Device fingerprinting
- Training reveal page
- Admin analytics dashboard
- Campaign management

---

## 🎓 Next Steps

1. ✅ Follow 10-minute setup above
2. ✅ Create your first campaign
3. ✅ Read [EMAIL-TEMPLATES.md](./EMAIL-TEMPLATES.md) for scenarios
4. ✅ Send to test group
5. ✅ Monitor [admin dashboard](https://your-domain.vercel.app/admin)
6. ✅ Send follow-up training emails

---

## 📞 Need Help?

**MongoDB Issues:**
- See [MONGODB-SETUP.md](./MONGODB-SETUP.md)
- [MongoDB Atlas Docs](https://www.mongodb.com/docs/atlas/)

**Deployment Issues:**
- See [SETUP-GUIDE.md](./SETUP-GUIDE.md)
- Check Vercel deployment logs
- Verify environment variable is set

**General Questions:**
- Read [README.md](./README.md)
- Check [IMPROVEMENTS.md](./IMPROVEMENTS.md)

---

## ✅ Setup Checklist

- [ ] MongoDB Atlas account created
- [ ] M0 free cluster deployed
- [ ] Database user created and password saved
- [ ] Network access configured (0.0.0.0/0)
- [ ] Connection string obtained
- [ ] `composer install` completed
- [ ] Deployed to Vercel
- [ ] `MONGODB_URI` environment variable added
- [ ] Test campaign created successfully
- [ ] Admin dashboard shows data

**All checked?** You're ready to go! 🎉

---

**Start with:** [SETUP-GUIDE.md](./SETUP-GUIDE.md) for complete walkthrough.
