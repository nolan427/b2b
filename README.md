# B2B Phishing Simulator - Adobe Document Cloud Training

A comprehensive phishing simulation platform designed to train employees on recognizing Adobe-related phishing attacks.

## 🎯 Purpose

This tool helps organizations:
- Test employee vulnerability to phishing attacks
- Provide immediate educational feedback
- Track security awareness metrics
- Improve organizational security posture

## 📋 Features

### Core Functionality
- **Realistic Adobe Document Cloud simulation** - Professional-looking phishing pages
- **Email provider detection** - Identifies Office365, Google Workspace, or other providers via MX records
- **Device fingerprinting** - Tracks OS, browser, and device information
- **Visit logging** - Comprehensive analytics stored in MongoDB Atlas
- **Educational reveal page** - Immediate training after falling for the phish
- **Admin dashboard** - Real-time campaign monitoring and reporting
- **Free database** - MongoDB Atlas free tier (512 MB, no credit card)

### Training Components
- ✅ Immediate feedback on warning signs missed
- ✅ Best practices education
- ✅ Campaign statistics and benchmarking
- ✅ No actual credential theft (training-safe)

## 🏗️ Architecture

```
Frontend (HTML/JS)
├── index.html - Main RFP invitation page
├── adb_token.html - Adobe Document Cloud portal (RECOMMENDED for training)
├── training-reveal.html - Educational reveal page
└── admin-dashboard.html - Campaign management interface

Backend (PHP + MongoDB)
├── api/log-visit.php - Visit logging endpoint
├── api/get-visits.php - Analytics retrieval
└── api/storage.php - Campaign management

Database (MongoDB Atlas)
├── phishing.visits - Visit logs and analytics
└── phishing.campaigns - Campaign configurations

Infrastructure
├── Vercel (hosting)
└── MongoDB Atlas (database - free tier)
```

## 🚀 Quick Start

### 1. Prerequisites
- MongoDB Atlas account (free, no credit card!)
- Vercel account
- PHP 7.4+
- Composer

### 2. Setup MongoDB Atlas

**See detailed guide:** [MONGODB-SETUP.md](./MONGODB-SETUP.md) or [SETUP-GUIDE.md](./SETUP-GUIDE.md)

Quick steps:
1. Create free MongoDB Atlas account at [mongodb.com](https://www.mongodb.com/cloud/atlas/register)
2. Create M0 (free) cluster
3. Create database user and save password
4. Allow network access from anywhere (0.0.0.0/0)
5. Get connection string

### 3. Install Dependencies

```bash
# Clone repository
git clone <your-repo-url>
cd b2b

# Install MongoDB library
composer install
```

### 4. Deploy to Vercel

```bash
# Install Vercel CLI
npm i -g vercel
vercel login

# Deploy
vercel --prod
```

### 5. Configure Environment Variable

In Vercel dashboard:
1. Go to Settings → Environment Variables
2. Add `MONGODB_URI` with your connection string
3. Redeploy: `vercel --prod`

### 4. Create First Campaign

```bash
# Create a campaign
curl "https://your-domain.vercel.app/api/storage.php?action=set&key=campaign1&sender=John%20Smith%20-%20ABC%20Construction&title=RFP%20%234523%20-%20Office%20Building%20Project&url=https://your-training-page.com"
```

### 5. Share Campaign Link

Send this link to test subjects:
```
https://your-domain.vercel.app/adb_token.html#campaign1
```

### 6. Monitor Results

View dashboard at:
```
https://your-domain.vercel.app/admin-dashboard.html
```

## 📊 Campaign Management

### Creating Campaigns

**Via API:**
```bash
curl "https://your-domain.vercel.app/api/storage.php?action=set&key=CAMPAIGN_KEY&sender=SENDER_NAME&title=DOCUMENT_TITLE&url=REDIRECT_URL"
```

**Via Dashboard:**
1. Open `/admin-dashboard.html`
2. Click "Create Campaign"
3. Enter campaign details
4. Share the generated link

### Campaign Parameters

- **key**: Unique identifier (e.g., "campaign1", "q2-2024-test")
- **sender**: Display name (e.g., "John Smith - ABC Corp")
- **title**: Document title (e.g., "RFP #4523 - Office Building")
- **url**: Redirect URL (use `/training-reveal.html` for training mode)

## 🎓 Training vs. Live Mode

### Training Mode (RECOMMENDED)
In `adb_token.html`, the code is configured to redirect to the educational reveal page:

```javascript
// Training mode - redirects to educational page
window.location.href = '/training-reveal.html';
```

**Benefits:**
- ✅ Safe - no actual phishing occurs
- ✅ Educational - immediate feedback
- ✅ Measurable - tracks who fell for it
- ✅ Legal - clearly training exercise

### Live Mode (Use with caution)
To test real credential harvesting (for authorized red team exercises only):

```javascript
// Uncomment this line to redirect to actual phishing sites
redirectToProvider(email, mxResult);
```

⚠️ **WARNING:** Only use live mode with:
- Written authorization from management
- Legal review and approval
- Clear employee notification policy
- Proper data handling procedures

## 📈 Metrics & Analytics

### Dashboard Features

**Global Statistics:**
- Total visits across all campaigns
- Unique visitors
- Average click-through rate
- Provider breakdown (Office365, Google, Other)

**Per-Campaign Metrics:**
- Total visits
- Unique visitors
- Click rate
- Provider distribution
- Timeline of visits

**Export Options:**
- CSV export of all visits
- JSON export of complete data
- Per-campaign reports

### Tracked Data Points

Each visit logs:
- Email address entered
- IP address
- Device/browser information
- Email provider type (via MX records)
- Timestamp
- Campaign identifier

## 🔒 Security & Privacy

### Data Protection
- Data stored in local JSON files
- Passwords are NEVER collected
- IP addresses logged for analytics only
- HTTPS enforced (automatic on Vercel)
- Data persists across deployments
- Regular backups recommended

### Legal Compliance

**Required Before Deployment:**

1. **Employee Notification**
   - Add to employee handbook
   - Annual security awareness notices
   - Clear privacy policy

2. **HR Coordination**
   - No disciplinary action for first failures
   - Focus on education, not punishment
   - Support resources for employees

3. **Legal Review**
   - Written authorization from leadership
   - Compliance with local privacy laws
   - Data retention policies

4. **Ethical Guidelines**
   - Transparent about training program
   - Opt-out provisions if required
   - Support for employees who struggle

## 🎯 Best Practices

### Campaign Planning

1. **Start Easy**
   - Begin with obvious red flags
   - Announce training program
   - Focus on education

2. **Increase Difficulty**
   - Gradually improve sophistication
   - Add personalization
   - Reduce obvious tells

3. **Regular Testing**
   - Quarterly campaigns
   - Vary attack vectors
   - Track improvement over time

4. **Department Targeting**
   - Customize scenarios by role
   - Finance: invoice fraud
   - HR: employee data requests
   - Sales: contract reviews

### Follow-Up Actions

After each campaign:

1. **Send Results Email**
   ```
   Subject: Security Training - Your Results
   
   You participated in our phishing simulation.
   Result: [Clicked / Reported / Ignored]
   
   Next Steps:
   - Review training materials
   - Complete security module
   - Learn warning signs
   ```

2. **Offer Training**
   - Link to security awareness course
   - Schedule team workshops
   - Provide reference materials

3. **Track Improvement**
   - Compare to previous campaigns
   - Identify repeat offenders
   - Celebrate successes

## 📝 Customization

### Creating New Templates

1. Copy existing template (e.g., `adb_token.html`)
2. Modify branding and content
3. Update scenarios (RFP, invoice, HR document, etc.)
4. Test thoroughly before deployment
5. Add route to `vercel.json`

### Branding Customization

Update these elements:
- Company logos (`ad0be.png`, etc.)
- Color schemes in CSS
- Email sender names
- Document types and titles
- URL structures

## 🐛 Troubleshooting

### Common Issues

**No visits logged**
```
Solution: 
1. Check that data/ directory exists
2. Verify PHP has write permissions
3. Check Vercel logs for errors
4. Ensure JSON files are valid
```

**Data not persisting**
```
Solution:
1. Verify Vercel function has write access
2. Check file paths are correct
3. Review Vercel function logs
```

**MX lookup fails**
```
Solution: Google DNS API may be rate limited. Implement caching or use local DNS
```

**Dashboard shows no data**
```
Solution: Ensure campaigns have been created and received visits
```

## 📚 Additional Resources

### Documentation
- [Full Deployment Guide](./DEPLOYMENT.md)
- [Improvement Recommendations](./IMPROVEMENTS.md)
- [Vercel Documentation](https://vercel.com/docs)
- [Redis Documentation](https://redis.io/documentation)

### Security Frameworks
- NIST Cybersecurity Framework
- SANS Security Awareness
- ISO 27001 guidelines

### Similar Tools
- [Gophish](https://getgophish.com/) - Open source
- [KnowBe4](https://www.knowbe4.com/) - Commercial
- [PhishMe](https://cofense.com/) - Commercial

## ⚖️ Legal Disclaimer

This tool is designed for authorized security awareness training only. Users must:

- ✅ Obtain written authorization before deployment
- ✅ Comply with all applicable laws and regulations
- ✅ Respect employee privacy rights
- ✅ Use responsibly and ethically
- ✅ Provide educational value, not punishment

**Unauthorized use may be illegal.** The authors assume no liability for misuse of this software.

## 🤝 Support

For questions or issues:
1. Check documentation in this repository
2. Review Vercel and Redis documentation
3. Consult with legal/IT security teams
4. Test thoroughly in staging environment

## 📄 License

[Your license here]

---

**Remember:** The goal is education, not embarrassment. Use this tool to make your organization more secure, not to punish employees for falling for sophisticated attacks.
