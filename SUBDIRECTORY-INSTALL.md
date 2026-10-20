# Installing in a Subdirectory

## ✅ Works in ANY Directory!

Your phishing simulator now works from any subdirectory without touching your main website files.

---

## 📂 Example Installations

You can install in ANY of these locations:

```
✅ public_html/phishing/
✅ public_html/training/
✅ public_html/security/
✅ public_html/test/
✅ public_html/simulator/
✅ public_html/admin/phishing/
✅ Any subdirectory you want!
```

Your main site files stay completely untouched!

---

## 🚀 Installation Steps

### 1. **Create Your Subdirectory**

In cPanel File Manager:
1. Navigate to `public_html`
2. Click **"+ Folder"**
3. Name it (e.g., `phishing` or `training`)
4. Open the new folder

### 2. **Upload ZIP**

Inside your new subdirectory:
1. Click **"Upload"**
2. Select `b2b-cpanel-deploy.zip`
3. Wait for upload

### 3. **Extract**

1. Right-click the ZIP
2. Select **"Extract"**
3. Extract to current directory
4. Delete the ZIP after extraction

### 4. **Run Setup**

Visit (replace "phishing" with your folder name):
```
https://yourdomain.com/phishing/setup.php
```

Click **"Initialize Data Storage"**

### 5. **Secure**

Delete `setup.php`:
```
https://yourdomain.com/phishing/
```

Navigate to setup.php and delete it.

---

## ✅ Access Your Simulator

### If installed at `public_html/phishing/`:

**Admin Dashboard:**
```
https://yourdomain.com/phishing/admin
```

**Setup:**
```
https://yourdomain.com/phishing/setup.php
```

**Create Campaign:**
```
https://yourdomain.com/phishing/api/storage.php?action=set&key=test&sender=Test&title=Test&url=https://yourdomain.com/phishing/training
```

**Campaign Page:**
```
https://yourdomain.com/phishing/adb_token.html#test
```

---

## 📋 File Structure

After extraction in `public_html/phishing/`:

```
public_html/
├── (your existing site files - untouched!)
└── phishing/
    ├── index.html
    ├── adb_token.html
    ├── admin-dashboard.html
    ├── config.php
    ├── setup.php
    ├── .htaccess
    ├── api/
    │   ├── get-visits.php
    │   ├── log-visit.php
    │   └── storage.php
    ├── data/
    │   ├── campaigns.json
    │   └── visits.json
    └── js/
        └── config.js
```

---

## 🔧 How It Works

The app auto-detects its location:

**JavaScript (client-side):**
```javascript
// Automatically detects: /phishing/
function getApiUrl(endpoint) {
    const path = window.location.pathname;
    const dir = path.substring(0, path.lastIndexOf('/'));
    return dir + '/api/' + endpoint;
}
```

**PHP (server-side):**
```php
// Automatically uses correct paths
define('DATA_DIR', __DIR__ . '/data');
```

No configuration needed - it just works!

---

## 🎯 Creating Campaigns

### Template:
```
https://yourdomain.com/SUBDIRECTORY/api/storage.php?action=set&key=CAMPAIGN_KEY&sender=SENDER&title=TITLE&url=https://yourdomain.com/SUBDIRECTORY/training
```

### Example for `/phishing/`:
```
https://yourdomain.com/phishing/api/storage.php?action=set&key=rfp2024&sender=John%20Smith&title=RFP%20Project&url=https://yourdomain.com/phishing/training
```

### Share Link:
```
https://yourdomain.com/phishing/adb_token.html#rfp2024
```

---

## 🔒 Security

### Each installation is isolated:
- ✅ Separate data directory
- ✅ Own .htaccess file
- ✅ Independent permissions
- ✅ No conflict with main site

### Protected directories:
```
phishing/data/ - Protected by .htaccess
phishing/config.php - Can be protected
```

---

## 💡 Multiple Installations

You can even run multiple instances:

```
public_html/
├── phishing-test/    ← Test environment
├── phishing-prod/    ← Production
└── training-demo/    ← Demo for management
```

Each works independently!

---

## 🔄 Moving Between Directories

To move your installation:

1. **Backup data:**
   - Download `data/campaigns.json`
   - Download `data/visits.json`

2. **Extract to new location**

3. **Run setup**

4. **Restore data files**

---

## 🆚 Advantages

### vs. Root Installation:
✅ **Safe** - Main site untouched  
✅ **Clean** - All files in one folder  
✅ **Portable** - Easy to move/delete  
✅ **Multiple** - Run several instances  

### vs. Separate Domain:
✅ **Cheaper** - No extra domain needed  
✅ **Simpler** - One hosting account  
✅ **Faster** - No DNS setup  

---

## 📊 Examples

### Marketing Team Test:
```
public_html/test-phishing/
→ https://yourdomain.com/test-phishing/
```

### IT Security Training:
```
public_html/security-training/
→ https://yourdomain.com/security-training/
```

### Demo for Management:
```
public_html/demo/
→ https://yourdomain.com/demo/
```

---

## 🔧 Troubleshooting

### "404 Not Found" on admin page

**Check:**
1. Is `.htaccess` file uploaded?
2. Try: `https://yourdomain.com/phishing/admin-dashboard.html` (with extension)

### "Failed to load dashboard data"

**Check:**
1. Did you run setup.php?
2. Is `data/` directory created?
3. Try: `https://yourdomain.com/phishing/api/get-visits.php` directly

### API returns errors

**Make sure:**
1. PHP version is 7.4+
2. `data/` folder has 755 permissions
3. JSON files exist in `data/`

---

## ✅ Quick Checklist

- [ ] Created subdirectory in public_html
- [ ] Uploaded ZIP to subdirectory
- [ ] Extracted files
- [ ] Ran setup.php
- [ ] Deleted setup.php
- [ ] Tested admin dashboard
- [ ] Created test campaign
- [ ] Verified it works

---

## 🎓 Best Practices

1. **Use descriptive folder names:**
   - ✅ `phishing-training`
   - ✅ `security-test`
   - ❌ `folder1`

2. **Keep it simple:**
   - One level deep is best
   - Avoid deep nesting

3. **Regular backups:**
   - Backup `data/` folder weekly
   - Keep `data/*.json` files safe

4. **Clean up:**
   - Delete test installations
   - Remove old campaigns

---

## 💰 Cost

**Same as root installation:**
- Standard cPanel hosting: $3-10/month
- No additional costs
- Uses existing hosting space

---

**Your main website is completely safe!** 🛡️

All phishing simulator files stay in their own subdirectory.
