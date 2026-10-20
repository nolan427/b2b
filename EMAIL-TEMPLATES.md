# Email Templates for Phishing Simulations

Use these templates when sending phishing simulation links to employees.

---

## 📧 Template 1: RFP/Bid Invitation (Construction/B2B)

**Campaign Setup:**
```bash
curl "https://YOUR-DOMAIN.vercel.app/api/storage.php?action=set&key=rfp2024&sender=Michael%20Johnson%20-%20ABC%20Construction&title=RFP%20%234523%20-%20Office%20Building%20Project&url=https://YOUR-DOMAIN.vercel.app/training-reveal.html"
```

**Email:**
```
Subject: Bid Invitation - Downtown Office Complex Project

Dear [Name],

You have been invited to submit a proposal for our upcoming project:

Project: Downtown Office Complex Renovation
RFP Number: #4523
Bid Deadline: [Date + 7 days]

Project Documents Available:
- RFP Addenda (PDF)
- Project Timeline (PDF)
- Sub-Bidding Conditions (Excel)
- Technical Drawings (Word)

Access project documents here:
https://YOUR-DOMAIN.vercel.app/adb_token.html#rfp2024

Important: Document access expires in 24 hours due to confidentiality requirements.

Questions? Contact: procurement@company.com

Best regards,
Michael Johnson
Procurement Director
ABC Construction
```

**Red Flags:**
- Urgency ("expires in 24 hours")
- Unexpected invitation
- External domain
- Credential request

---

## 📧 Template 2: Invoice Review (Finance Teams)

**Campaign Setup:**
```bash
curl "https://YOUR-DOMAIN.vercel.app/api/storage.php?action=set&key=invoice-urgent&sender=Accounts%20Payable%20Department&title=Invoice%20%23INV-8934%20-%20Payment%20Due&url=https://YOUR-DOMAIN.vercel.app/training-reveal.html"
```

**Email:**
```
Subject: URGENT: Invoice Requires Your Approval

Hi [Name],

An invoice from one of our vendors requires immediate review and approval.

Invoice Details:
- Invoice #: INV-8934
- Vendor: Office Supplies Plus
- Amount: $2,847.50
- Due Date: [Tomorrow's date]

Your approval is needed to avoid late payment fees.

Review invoice documents:
https://YOUR-DOMAIN.vercel.app/adb_token.html#invoice-urgent

If you have questions about this invoice, please contact Accounts Payable.

Thank you,
Accounts Payable Team
```

**Red Flags:**
- Urgency ("URGENT", "immediate")
- Late payment pressure
- Unexpected vendor
- Generic greeting

---

## 📧 Template 3: HR Benefits Enrollment

**Campaign Setup:**
```bash
curl "https://YOUR-DOMAIN.vercel.app/api/storage.php?action=set&key=hr-benefits&sender=Human%20Resources%20-%20Benefits%20Team&title=2024%20Benefits%20Enrollment%20Package&url=https://YOUR-DOMAIN.vercel.app/training-reveal.html"
```

**Email:**
```
Subject: Action Required: Benefits Enrollment Deadline Approaching

Dear [Name],

This is a reminder that open enrollment for employee benefits ends soon.

Enrollment Period: [Date range]
Deadline: [Date + 3 days]

Required Documents:
- 2024 Benefits Summary
- Plan Comparison Guide
- Enrollment Forms
- Dependent Coverage Options

Access your enrollment documents:
https://YOUR-DOMAIN.vercel.app/adb_token.html#hr-benefits

Important: Failure to complete enrollment by the deadline will result in default coverage selections.

Questions? Contact HR at: hr@company.com

Best regards,
HR Benefits Team
```

**Red Flags:**
- External portal for sensitive HR docs
- Urgency
- Vague sender
- Request for personal information

---

## 📧 Template 4: IT Security Alert

**Campaign Setup:**
```bash
curl "https://YOUR-DOMAIN.vercel.app/api/storage.php?action=set&key=security-alert&sender=IT%20Security%20Team&title=Account%20Security%20Verification%20Required&url=https://YOUR-DOMAIN.vercel.app/training-reveal.html"
```

**Email:**
```
Subject: Security Alert: Unusual Activity Detected

Dear [Name],

Our security systems have detected unusual activity on your account.

Alert Details:
- Time: [Recent timestamp]
- Location: [Different city/country]
- Device: Unknown device

For your protection, we need you to verify your identity.

Review security alert details:
https://YOUR-DOMAIN.vercel.app/adb_token.html#security-alert

If you did not authorize this activity, please verify your account immediately.

IT Security Team
security@company.com
```

**Red Flags:**
- Creates panic ("unusual activity")
- Urgency
- Requests verification
- External verification link

---

## 📧 Template 5: Adobe License Renewal

**Campaign Setup:**
```bash
curl "https://YOUR-DOMAIN.vercel.app/api/storage.php?action=set&key=adobe-renewal&sender=Adobe%20Creative%20Cloud%20Support&title=Adobe%20License%20Renewal%20Required&url=https://YOUR-DOMAIN.vercel.app/training-reveal.html"
```

**Email:**
```
Subject: Your Adobe Creative Cloud License Expires Soon

Hello [Name],

Your Adobe Creative Cloud license is set to expire:

Account: [Company name]
License: Creative Cloud All Apps
Expiration: [Date + 5 days]

To avoid service interruption, please review and renew your license:

Access renewal documents:
https://YOUR-DOMAIN.vercel.app/adb_token.html#adobe-renewal

What happens if license expires:
- Loss of access to all Adobe applications
- Projects may become inaccessible
- Subscription must be reactivated

Renew now to maintain uninterrupted access.

Adobe Creative Cloud Support
support@adobe.com
```

**Red Flags:**
- Not from @adobe.com domain
- Service interruption threat
- External renewal portal
- Urgency tactics

---

## 📧 Template 6: Contract Signature Request

**Campaign Setup:**
```bash
curl "https://YOUR-DOMAIN.vercel.app/api/storage.php?action=set&key=contract-sign&sender=Legal%20Department%20-%20Contract%20Services&title=Client%20Contract%20-%20Signature%20Required&url=https://YOUR-DOMAIN.vercel.app/training-reveal.html"
```

**Email:**
```
Subject: Contract Awaiting Your Signature

Hi [Name],

A contract with one of our clients requires your electronic signature.

Contract Details:
- Client: [Client name]
- Contract Type: Service Agreement
- Value: $125,000
- Deadline: [Date + 2 days]

Review and sign contract:
https://YOUR-DOMAIN.vercel.app/adb_token.html#contract-sign

This contract has been reviewed by legal and is ready for execution.

Legal Department
legal@company.com
```

**Red Flags:**
- Unexpected contract
- External signing portal
- No contract details provided
- Urgency

---

## 📧 Template 7: Payroll Update

**Campaign Setup:**
```bash
curl "https://YOUR-DOMAIN.vercel.app/api/storage.php?action=set&key=payroll-update&sender=Payroll%20Department&title=Direct%20Deposit%20Verification%20Required&url=https://YOUR-DOMAIN.vercel.app/training-reveal.html"
```

**Email:**
```
Subject: Action Required: Verify Your Direct Deposit Information

Dear [Name],

We are updating our payroll system and need to verify your direct deposit information.

Verification Required By: [Date + 3 days]

If we do not receive verification by this date, your next paycheck may be delayed or issued as a paper check.

Verify your information:
https://YOUR-DOMAIN.vercel.app/adb_token.html#payroll-update

Required Information:
- Bank routing number
- Account number
- Account type

Questions? Contact Payroll at: payroll@company.com

Payroll Department
```

**Red Flags:**
- Requests sensitive financial data
- External verification site
- Urgency (paycheck delay threat)
- Generic sender

---

## 📧 Template 8: CEO/Executive Request

**Campaign Setup:**
```bash
curl "https://YOUR-DOMAIN.vercel.app/api/storage.php?action=set&key=ceo-request&sender=CEO%20Office&title=Confidential%20-%20Executive%20Review%20Required&url=https://YOUR-DOMAIN.vercel.app/training-reveal.html"
```

**Email:**
```
Subject: Confidential: Need Your Review ASAP

[Name],

I need you to review these documents for our board meeting tomorrow morning.

Documents:
- Q4 Financial Summary
- Strategic Plan Updates
- Acquisition Proposal

Access confidential documents:
https://YOUR-DOMAIN.vercel.app/adb_token.html#ceo-request

Please keep this confidential and provide your feedback by end of day.

Thanks,
[CEO Name]
```

**Red Flags:**
- Authority pressure (CEO)
- Urgency ("ASAP", "tomorrow")
- Confidentiality pressure
- External document portal
- Personal tone unusual for CEO

---

## 🎯 Best Practices for Sending

### Before Sending

1. **Test First**
   - Send to yourself
   - Verify link works
   - Check training reveal page loads
   - Confirm logging works

2. **Target Appropriately**
   - Match template to recipient role
   - Use realistic context
   - Consider timing

3. **Get Approval**
   - HR review
   - Legal approval
   - Executive sign-off

### When Sending

1. **Use Internal Email**
   - Send from your corporate email system
   - Don't use external services
   - Makes it more realistic

2. **Personalize**
   - Use recipient names
   - Reference actual projects/clients when possible
   - Match company terminology

3. **Timing**
   - Monday mornings (8-10 AM)
   - End of quarter (urgency)
   - After real company announcements

### After Sending

1. **Monitor Immediately**
   - Watch dashboard for visits
   - Be ready to answer questions
   - Document who clicks

2. **Follow Up Within 24 Hours**
   - Send results email
   - Offer training
   - Don't shame employees

3. **Analyze Results**
   - Who clicked?
   - Which departments most vulnerable?
   - What red flags were missed?

---

## 📊 Template Effectiveness

**Highest Click Rates:**
1. CEO/Executive requests (45-60%)
2. Payroll/Finance urgency (40-55%)
3. IT Security alerts (35-50%)

**Medium Click Rates:**
1. HR Benefits (25-40%)
2. Contract signatures (25-35%)
3. Invoice reviews (20-35%)

**Lower Click Rates:**
1. RFP/Bid invitations (15-30%)
2. License renewals (15-25%)

**Factors Affecting Rates:**
- Urgency language
- Authority pressure
- Personal relevance
- Timing
- Previous training

---

## ✉️ Follow-Up Email After Test

**Send to all participants within 24 hours:**

```
Subject: Security Awareness Training - Recent Phishing Simulation

Dear [Name],

You recently received an email that was part of our security awareness training program.

WHAT HAPPENED:
This was a controlled phishing simulation designed to test and improve our organization's security awareness.

YOUR RESULT:
[Clicked Link / Entered Information / Reported as Phishing / Did Not Interact]

WHY THIS MATTERS:
Real attackers use identical techniques to steal credentials, install malware, and compromise company data. This training helps you recognize and avoid these threats.

NEXT STEPS:
1. Review the warning signs: [Link to training materials]
2. Complete the 10-minute security module: [Link to training]
3. Learn how to report phishing: [Link to reporting instructions]

REMEMBER:
- This is training, not punishment
- We all make mistakes
- Your awareness helps protect our entire organization

Questions? Contact IT Security: security@company.com

Thank you for participating in our security awareness program.

IT Security Team
```

---

## 🎓 Training Resources to Include

After simulations, provide:

1. **"How to Spot Phishing" Guide**
   - URL inspection tips
   - Sender verification
   - Red flag checklist

2. **Reporting Process**
   - Who to contact
   - What information to include
   - What happens after reporting

3. **Real Examples**
   - Recent phishing attempts
   - What made them suspicious
   - How they were handled

4. **Success Stories**
   - Employees who reported phishing
   - Attacks that were prevented
   - Positive impact of awareness

---

## ⚠️ Important Reminders

**Legal/Ethical Considerations:**

✅ **DO:**
- Get written authorization
- Focus on education
- Provide immediate feedback
- Offer training resources
- Track improvement over time

❌ **DON'T:**
- Punish employees for failures
- Collect actual passwords
- Use fear/shame tactics
- Test during crises
- Hide the training program

**Remember:** The goal is to make your organization more secure, not to embarrass employees.

---

## 📞 Support

Need help customizing templates?
1. Review [README.md](./README.md) for best practices
2. Check [IMPROVEMENTS.md](./IMPROVEMENTS.md) for ideas
3. Test thoroughly before sending to employees

**Good luck with your security awareness training! 🛡️**
