# Quick Start Guide - B2B Phishing Simulator

## 🚀 Get Running in 5 Minutes

**No database required!** This system uses local JSON files for storage.

### Step 1: Deploy to Vercel (2 minutes)

```bash
# Install Vercel CLI
npm i -g vercel

# Login to Vercel
vercel login

# Deploy (from project directory)
vercel --prod
```

When prompted, set your project name and confirm settings.

### Step 2: Create Your First Campaign (2 minutes)

**Example: RFP Bid Invitation**

```bash
curl "https://YOUR-DOMAIN.vercel.app/api/storage.php?action=set&key=rfp2024&sender=Michael%20Johnson%20-%20Smith%20Construction&title=Bid%20Invitation%20-%20Downtown%20Office%20Complex&url=https://YOUR-DOMAIN.vercel.app/training-reveal.html"
```

Replace `YOUR-DOMAIN` with your actual Vercel domain.

### Step 3: Test It (1 minute)

1. **Open the phishing page:**
   ```
   https://YOUR-DOMAIN.vercel.app/adb_token.html#rfp2024
   ```

2. **Enter a test email** (use your own email for testing)

3. **See the training reveal page** - You should be redirected to the educational page

4. **Check the dashboard:**
   ```
   https://YOUR-DOMAIN.vercel.app/admin
   ```

✅ **Done!** Your phishing simulator is now live.

### Data Storage

Your data is automatically stored in:
- `data/campaigns.json` - Campaign configurations
- `data/visits.json` - Visit logs and analytics

These files are created automatically and persist across deployments.

---

## 📋 Common Scenarios

### Scenario 1: Finance Team - Invoice Scam

```bash
curl "https://YOUR-DOMAIN.vercel.app/api/storage.php?action=set&key=invoice-q1&sender=Accounts%20Payable%20-%20Vendor%20Services&title=Urgent%20Invoice%20Review%20Required%20-%20Payment%20Due&url=https://YOUR-DOMAIN.vercel.app/training-reveal.html"
```

**Campaign Link:**
```
https://YOUR-DOMAIN.vercel.app/adb_token.html#invoice-q1
```

**Email to send:**
```
Subject: Urgent: Invoice Requires Your Review

Dear Finance Team,

An invoice from one of our vendors requires immediate review.
Please access the document portal to review and approve:

[Link to campaign]

Time sensitive - payment due in 48 hours.

Thank you,
Accounts Payable
```

---

### Scenario 2: HR Team - Benefits Enrollment

```bash
curl "https://YOUR-DOMAIN.vercel.app/api/storage.php?action=set&key=hr-benefits&sender=HR%20Department%20-%20Benefits%20Coordinator&title=Annual%20Benefits%20Enrollment%20Documents&url=https://YOUR-DOMAIN.vercel.app/training-reveal.html"
```

**Campaign Link:**
```
https://YOUR-DOMAIN.vercel.app/adb_token.html#hr-benefits
```

---

### Scenario 3: Sales Team - Contract Review

```bash
curl "https://YOUR-DOMAIN.vercel.app/api/storage.php?action=set&key=contract-review&sender=Legal%20Department%20-%20Contract%20Review&title=Client%20Contract%20-%20Requires%20Signature&url=https://YOUR-DOMAIN.vercel.app/training-reveal.html"
```

**Campaign Link:**
```
https://YOUR-DOMAIN.vercel.app/adb_token.html#contract-review
```

---

### Scenario 4: IT Team - Software License Renewal

```bash
curl "https://YOUR-DOMAIN.vercel.app/api/storage.php?action=set&key=adobe-license&sender=Adobe%20Creative%20Cloud%20Support&title=Your%20Adobe%20License%20Requires%20Renewal&url=https://YOUR-DOMAIN.vercel.app/training-reveal.html"
```

**Campaign Link:**
```
https://YOUR-DOMAIN.vercel.app/adb_token.html#adobe-license
```

---

## 📊 Viewing Results

### Real-Time Dashboard

Access at: `https://YOUR-DOMAIN.vercel.app/admin`

**What You'll See:**
- Total visits
- Click-through rates
- Email addresses captured
- Device/browser breakdown
- Provider breakdown (Office365, Google, Other)
- Per-campaign statistics

### Export Data

**CSV Export (for spreadsheets):**
1. Open dashboard
2. Click "Export CSV" in Recent Visits section
3. Open in Excel/Google Sheets

**JSON Export (for advanced analysis):**
1. Open dashboard
2. Click "Export Report"
3. Use with data analysis tools

### API Access

**Get all visits:**
```bash
curl "https://YOUR-DOMAIN.vercel.app/api/get-visits.php"
```

**Get visits for specific campaign:**
```bash
curl "https://YOUR-DOMAIN.vercel.app/api/get-visits.php?key=rfp2024"
```

---

## 🎯 Campaign Best Practices

### 1. Email Delivery Tips

**Don't:**
- ❌ Send from external email services (gets flagged)
- ❌ Use URL shorteners (suspicious)
- ❌ Include attachments

**Do:**
- ✅ Send from internal email if possible
- ✅ Use full URLs (looks more legitimate)
- ✅ Include context relevant to recipient's role

### 2. Timing

**Best Times to Send:**
- Monday morning (8-10 AM) - people clearing inbox
- End of quarter - creates urgency
- After company announcements - capitalizes on activity

**Avoid:**
- Friday afternoons
- Holidays
- During major company events

### 3. Follow-Up

**Within 24 hours:**
1. Send results email to participants
2. Offer training resources
3. Schedule team discussion

**Within 1 week:**
1. Analyze results
2. Identify trends
3. Plan next campaign

---

## 🔧 Quick Troubleshooting

### Problem: "No visits showing in dashboard"

**Solution:**
1. Check data file exists: Visit admin dashboard
2. Verify PHP has write permissions
3. Test by submitting a visit yourself
4. Check Vercel logs for errors

### Problem: "Email provider detection not working"

**Solution:**
- MX record lookups use Google DNS API
- May be rate limited
- Check browser console for errors
- Test with common domains (gmail.com, outlook.com)

### Problem: "Page not loading"

**Solution:**
1. Verify deployment succeeded: `vercel --prod`
2. Check Vercel logs in dashboard
3. Ensure all routes are in vercel.json
4. Clear browser cache

### Problem: "Campaign URL not working"

**Solution:**
1. Verify campaign was created:
   ```bash
   curl "https://YOUR-DOMAIN.vercel.app/api/storage.php?action=list"
   ```
2. Check hash in URL matches campaign key
3. Ensure `#` is included: `/adb_token.html#campaign-key`

---

## 📈 Measuring Success

### Key Metrics

**Click-Through Rate (CTR):**
- Industry Average: 20-30%
- Target: < 10% after training
- Track improvement over time

**Credential Entry Rate:**
- Industry Average: 10-15%
- Target: < 5% after training
- Focus education here

**Report Rate:**
- Target: > 25% report suspicious emails
- Celebrate reporters
- Track who reports vs. clicks

### Success Indicators

✅ **Good:**
- CTR decreasing over time
- More employees reporting
- Faster reporting times
- Department improvements

❌ **Needs Attention:**
- Same people clicking repeatedly
- Credential entry rate increasing
- No one reporting
- Department-wide failures

---

## 🎓 Next Steps

### Week 1: First Campaign
- ✅ Deploy simulator
- ✅ Create first campaign
- ✅ Test with small group
- ✅ Review results

### Week 2: Expand Testing
- ✅ Create department-specific campaigns
- ✅ Send to wider audience
- ✅ Monitor dashboard
- ✅ Send follow-up training

### Week 3-4: Training
- ✅ Schedule team workshops
- ✅ Share best practices
- ✅ Provide resources
- ✅ Answer questions

### Month 2: Retest
- ✅ Launch second campaign
- ✅ Measure improvement
- ✅ Adjust training
- ✅ Celebrate successes

---

## 📞 Getting Help

**Common Issues:**
1. Check [README.md](./README.md) for detailed docs
2. Review [DEPLOYMENT.md](./DEPLOYMENT.md) for setup
3. See [IMPROVEMENTS.md](./IMPROVEMENTS.md) for enhancements

**Still stuck?**
- Check Vercel logs
- Test Redis connection
- Review browser console errors
- Verify environment variables

---

## ⚡ Pro Tips

1. **Test First:** Always test campaigns with your own email before sending to employees

2. **Start Easy:** Begin with obvious phishing attempts, increase difficulty over time

3. **Communicate:** Announce the training program to employees (but not specific campaigns)

4. **Don't Punish:** Focus on education, not punishment for falling for phishing

5. **Track Improvement:** Run campaigns quarterly and measure progress

6. **Customize:** Adapt scenarios to your organization's context and industry

7. **Legal Review:** Get approval from HR/Legal before launching

8. **Celebrate Success:** Recognize employees who report phishing

---

## 🎉 Ready to Launch?

**Pre-Launch Checklist:**
- [ ] Deployed to Vercel
- [ ] Test campaign created and tested
- [ ] Dashboard accessible
- [ ] Data files working correctly
- [ ] Legal/HR approval obtained
- [ ] Employee notification sent
- [ ] Training materials prepared

**Launch Day:**
- [ ] Send campaign emails
- [ ] Monitor dashboard
- [ ] Be ready to answer questions
- [ ] Document results

**Post-Launch:**
- [ ] Send follow-up emails
- [ ] Analyze results
- [ ] Schedule training
- [ ] Plan next campaign

---

**Questions?** Review the full documentation in [README.md](./README.md)

**Good luck with your security awareness training! 🛡️**
