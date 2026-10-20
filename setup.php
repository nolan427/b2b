<?php
/**
 * Setup Script for B2B Phishing Simulator
 * Run this once after uploading to check everything works
 */

require_once __DIR__ . '/config.php';

// Check if data directory exists and is writable
$dataDir = DATA_DIR;
$isWritable = false;
$setupComplete = false;
$error = null;
$success = null;

// Handle setup
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Initialize data files
        initializeData();
        
        // Test write permissions
        $testFile = $dataDir . '/test.txt';
        if (file_put_contents($testFile, 'test') === false) {
            throw new Exception('Cannot write to data directory');
        }
        unlink($testFile);
        
        $setupComplete = true;
        $success = 'Setup complete! Data directory created and initialized.';
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Check current status
$dataExists = file_exists($dataDir);
$isWritable = $dataExists && is_writable($dataDir);
$campaignsExist = file_exists($dataDir . '/campaigns.json');
$visitsExist = file_exists($dataDir . '/visits.json');
$settingsExist = file_exists($dataDir . '/settings.json');

if ($campaignsExist && $visitsExist && $settingsExist && $isWritable) {
    $setupComplete = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup - B2B Phishing Simulator</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        h1 {
            color: #2d3748;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #718096;
            margin-bottom: 30px;
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #c6f6d5;
            color: #22543d;
            border: 1px solid #9ae6b4;
        }
        .alert-error {
            background: #fed7d7;
            color: #742a2a;
            border: 1px solid #fc8181;
        }
        .alert-warning {
            background: #fef5e7;
            color: #744210;
            border: 1px solid #f6d29c;
        }
        .alert-info {
            background: #bee3f8;
            color: #2c5282;
            border: 1px solid #90cdf4;
        }
        .status-grid {
            display: grid;
            gap: 15px;
            margin: 20px 0;
        }
        .status-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            background: #f7fafc;
            border-radius: 8px;
            border-left: 3px solid #cbd5e0;
        }
        .status-item.success {
            border-left-color: #48bb78;
        }
        .status-item.error {
            border-left-color: #f56565;
        }
        .status-icon {
            font-size: 20px;
        }
        .status-label {
            font-weight: 600;
            color: #2d3748;
        }
        .status-value {
            margin-left: auto;
            color: #718096;
            font-size: 14px;
        }
        button {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        button:hover {
            transform: translateY(-2px);
        }
        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .actions {
            margin-top: 20px;
        }
        .actions a {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin-right: 10px;
            margin-top: 10px;
        }
        .actions a:hover {
            background: #5568d3;
        }
        .instructions {
            background: #f7fafc;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            line-height: 1.6;
        }
        .instructions h3 {
            color: #2d3748;
            margin-bottom: 10px;
        }
        .instructions code {
            background: #e2e8f0;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛡️ Setup Phishing Simulator</h1>
        <p class="subtitle">Initialize data storage (JSON files)</p>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <strong>Success!</strong> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <div class="status-grid">
            <div class="status-item <?php echo $dataExists ? 'success' : 'error'; ?>">
                <span class="status-icon"><?php echo $dataExists ? '✅' : '❌'; ?></span>
                <span class="status-label">Data Directory</span>
                <span class="status-value"><?php echo $dataExists ? 'Exists' : 'Missing'; ?></span>
            </div>

            <div class="status-item <?php echo $isWritable ? 'success' : 'error'; ?>">
                <span class="status-icon"><?php echo $isWritable ? '✅' : '❌'; ?></span>
                <span class="status-label">Write Permission</span>
                <span class="status-value"><?php echo $isWritable ? 'OK' : 'Not Writable'; ?></span>
            </div>

            <div class="status-item <?php echo $campaignsExist ? 'success' : 'error'; ?>">
                <span class="status-icon"><?php echo $campaignsExist ? '✅' : '❌'; ?></span>
                <span class="status-label">Campaigns File</span>
                <span class="status-value"><?php echo $campaignsExist ? 'Ready' : 'Not Created'; ?></span>
            </div>

            <div class="status-item <?php echo $visitsExist ? 'success' : 'error'; ?>">
                <span class="status-icon"><?php echo $visitsExist ? '✅' : '❌'; ?></span>
                <span class="status-label">Visits File</span>
                <span class="status-value"><?php echo $visitsExist ? 'Ready' : 'Not Created'; ?></span>
            </div>

            <div class="status-item <?php echo $settingsExist ? 'success' : 'error'; ?>">
                <span class="status-icon"><?php echo $settingsExist ? '✅' : '❌'; ?></span>
                <span class="status-label">Settings File</span>
                <span class="status-value"><?php echo $settingsExist ? 'Ready' : 'Not Created'; ?></span>
            </div>
        </div>

        <?php if ($setupComplete): ?>
            <div class="alert alert-success">
                <strong>✅ Setup Complete!</strong> Your phishing simulator is ready to use.
            </div>
            
            <div class="actions">
                <a href="admin">View Dashboard</a>
                <a href="index.html">View Main Page</a>
            </div>

            <div class="alert alert-info" style="margin-top: 20px;">
                <strong>Next Steps:</strong><br>
                1. Delete or rename <code>setup.php</code> for security<br>
                2. Create your first campaign<br>
                3. Test with the admin dashboard
            </div>

            <div class="instructions">
                <h3>Create Your First Campaign:</h3>
                <p>Visit this URL (replace yourdomain.com):</p>
                <code style="display: block; margin: 10px 0; word-break: break-all;">
https://yourdomain.com/api/storage.php?action=set&key=test&sender=Test%20User&title=Test%20Campaign&url=https://yourdomain.com/training
                </code>
                <p>Then test it at: <code>https://yourdomain.com/adb_token.html#test</code></p>
            </div>

        <?php else: ?>
            <div class="alert alert-warning">
                <strong>Setup Required:</strong> Click the button below to initialize the data files.
            </div>

            <?php if (!$isWritable && $dataExists): ?>
                <div class="alert alert-error">
                    <strong>Permission Issue:</strong> The <code>data</code> directory exists but is not writable.
                    <br><br>
                    <strong>Fix this in cPanel File Manager:</strong><br>
                    1. Right-click the <code>data</code> folder<br>
                    2. Select "Change Permissions"<br>
                    3. Set to <code>755</code><br>
                    4. Click "Change Permissions"
                </div>
            <?php endif; ?>

            <form method="POST">
                <button type="submit" <?php echo (!$isWritable && $dataExists) ? 'disabled' : ''; ?>>
                    Initialize Data Storage
                </button>
            </form>

            <div class="instructions" style="margin-top: 20px;">
                <h3>What This Does:</h3>
                <p>
                    • Creates the <code>data</code> directory<br>
                    • Initializes <code>campaigns.json</code> and <code>visits.json</code><br>
                    • Sets proper permissions<br>
                    • No database required!
                </p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
