<?php
/**
 * Setup Script for B2B Phishing Simulator
 * Run this once after uploading to cPanel
 */

// Check if already configured
$configFile = __DIR__ . '/config.php';
$isConfigured = false;

if (file_exists($configFile)) {
    $configContent = file_get_contents($configFile);
    if (strpos($configContent, 'your_database_name') === false) {
        $isConfigured = true;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$isConfigured) {
    $dbHost = $_POST['db_host'] ?? 'localhost';
    $dbName = $_POST['db_name'] ?? '';
    $dbUser = $_POST['db_user'] ?? '';
    $dbPass = $_POST['db_pass'] ?? '';
    
    if (empty($dbName) || empty($dbUser)) {
        $error = "Database name and username are required!";
    } else {
        // Test connection
        try {
            $pdo = new PDO(
                "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
                $dbUser,
                $dbPass
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Update config file
            $configContent = file_get_contents($configFile);
            $configContent = str_replace("define('DB_HOST', 'localhost');", "define('DB_HOST', '$dbHost');", $configContent);
            $configContent = str_replace("define('DB_NAME', 'your_database_name');", "define('DB_NAME', '$dbName');", $configContent);
            $configContent = str_replace("define('DB_USER', 'your_database_user');", "define('DB_USER', '$dbUser');", $configContent);
            $configContent = str_replace("define('DB_PASS', 'your_database_password');", "define('DB_PASS', '$dbPass');", $configContent);
            
            file_put_contents($configFile, $configContent);
            
            // Initialize database
            require_once $configFile;
            initializeDatabase();
            
            $success = "Setup complete! Database configured and tables created.";
            $isConfigured = true;
            
        } catch (PDOException $e) {
            $error = "Database connection failed: " . $e->getMessage();
        }
    }
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
        .alert-info {
            background: #bee3f8;
            color: #2c5282;
            border: 1px solid #90cdf4;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #2d3748;
            font-weight: 600;
            font-size: 14px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e0;
            border-radius: 8px;
            font-size: 15px;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .help-text {
            font-size: 13px;
            color: #718096;
            margin-top: 5px;
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
        .steps {
            background: #f7fafc;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .steps h3 {
            color: #2d3748;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .steps ol {
            margin-left: 20px;
            color: #4a5568;
            line-height: 1.8;
        }
        .success-actions {
            margin-top: 20px;
        }
        .success-actions a {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin-right: 10px;
            margin-top: 10px;
        }
        .success-actions a:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛡️ Setup Phishing Simulator</h1>
        <p class="subtitle">Configure your database connection</p>

        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <strong>Success!</strong> <?php echo htmlspecialchars($success); ?>
            </div>
            <div class="success-actions">
                <a href="admin">View Dashboard</a>
                <a href="index.html">View Main Page</a>
            </div>
            <div class="alert alert-info" style="margin-top: 20px;">
                <strong>Next Steps:</strong><br>
                1. Delete or rename setup.php for security<br>
                2. Create your first campaign via API or dashboard<br>
                3. Test with the admin dashboard
            </div>
        <?php elseif ($isConfigured): ?>
            <div class="alert alert-success">
                <strong>Already Configured!</strong> Your database is already set up.
            </div>
            <div class="success-actions">
                <a href="admin">View Dashboard</a>
                <a href="index.html">View Main Page</a>
            </div>
        <?php else: ?>
            <div class="steps">
                <h3>Before you start:</h3>
                <ol>
                    <li>Create a MySQL database in cPanel</li>
                    <li>Create a database user and assign it to the database</li>
                    <li>Note the database name, username, and password</li>
                    <li>Enter them below</li>
                </ol>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label for="db_host">Database Host</label>
                    <input type="text" id="db_host" name="db_host" value="localhost" required>
                    <div class="help-text">Usually "localhost" for cPanel hosting</div>
                </div>

                <div class="form-group">
                    <label for="db_name">Database Name</label>
                    <input type="text" id="db_name" name="db_name" required placeholder="username_dbname">
                    <div class="help-text">The database you created in cPanel</div>
                </div>

                <div class="form-group">
                    <label for="db_user">Database Username</label>
                    <input type="text" id="db_user" name="db_user" required placeholder="username_dbuser">
                    <div class="help-text">The database user you created</div>
                </div>

                <div class="form-group">
                    <label for="db_pass">Database Password</label>
                    <input type="password" id="db_pass" name="db_pass" required>
                    <div class="help-text">The password for your database user</div>
                </div>

                <button type="submit">Install & Configure</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
