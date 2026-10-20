# B2B Phishing Simulator - Deployment Guide

## Prerequisites
- MongoDB Atlas account (free, no credit card!)
- Vercel account
- Composer installed

**For detailed MongoDB setup, see:** [MONGODB-SETUP.md](./MONGODB-SETUP.md)

## Step 1: Setup MongoDB Atlas

1. Create free account at [MongoDB Atlas](https://www.mongodb.com/cloud/atlas/register)
2. Create M0 (free) cluster
3. Create database user and save password
4. Allow network access: 0.0.0.0/0
5. Get connection string

## Step 2: Install Dependencies

```bash
composer install
```

## Step 3: Deploy to Vercel

### Option A: Using Vercel CLI
```bash
npm i -g vercel
vercel login
vercel --prod
```

### Option B: Using Git Integration
1. Push code to GitHub
2. Import project in Vercel dashboard
3. Deploy

## Step 4: Add Environment Variable

In Vercel dashboard:
1. Settings → Environment Variables
2. Add `MONGODB_URI` with your connection string
3. Redeploy: `vercel --prod`

## Step 5: Initialize Document Keys

Create phishing campaigns by setting document keys:

```bash
# Set up a campaign
curl "https://your-domain.vercel.app/api/storage.php?action=set&key=campaign1&sender=John%20Smith%20-%20ABC%20Construction&title=RFP%20%234523%20-%20Office%20Building%20Project&url=https://your-phishing-page.com"
```

### Example Campaign URLs

After creating keys, share links like:
- `https://your-domain.vercel.app/#campaign1`
- `https://your-domain.vercel.app/adb_token.html#campaign2`

## Step 6: Monitor Results

View collected data:
```bash
# Get all visits
curl "https://your-domain.vercel.app/api/get-visits.php"

# Get visits for specific campaign
curl "https://your-domain.vercel.app/api/get-visits.php?key=campaign1"
```

## Testing Checklist

- [ ] Document keys can be created/retrieved
- [ ] Email capture logs to JSON file
- [ ] MX record detection works
- [ ] Redirects work for different providers
- [ ] Training reveal page displays correctly
- [ ] Admin dashboard shows data
- [ ] Data persists between requests

## Data Storage Notes

- Data is stored in `data/visits.json` and `data/campaigns.json`
- Files are created automatically on first use
- Data persists across deployments
- Backup these files regularly for disaster recovery
- `.gitignore` prevents committing sensitive visit data

## Security Notes

- Use HTTPS only (Vercel provides this automatically)
- Data files have file-locking to prevent corruption
- Monitor for abuse/unexpected traffic
- Consider implementing rate limiting for production use
