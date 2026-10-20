<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Get MongoDB connection
    $mongo = getMongoClient();
    $db = $mongo->phishing;
    $campaigns = $db->campaigns;

    $action = $_GET['action'] ?? '';
    $key = $_GET['key'] ?? '';

    if ($action === 'get' && $key) {
        // Fetch campaign by key
        $campaign = $campaigns->findOne(['_id' => $key]);
        
        if ($campaign) {
            unset($campaign['_id']); // Remove MongoDB ID
            echo json_encode(['success' => true, 'data' => (array)$campaign]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Key not found or empty']);
        }
        
    } elseif ($action === 'set' && $key) {
        // Set campaign data
        $sender = $_REQUEST['sender'] ?? '';
        $title = $_REQUEST['title'] ?? '';
        $url = $_REQUEST['url'] ?? '';
        
        $fields = [];
        if ($sender !== '') $fields['sender'] = $sender;
        if ($title !== '') $fields['title'] = $title;
        if ($url !== '') $fields['url'] = $url;
        
        if (count($fields)) {
            $fields['_id'] = $key;
            $fields['updated_at'] = new MongoDB\BSON\UTCDateTime();
            
            $result = $campaigns->replaceOne(
                ['_id' => $key],
                $fields,
                ['upsert' => true]
            );
            
            echo json_encode(['success' => true, 'message' => 'Data stored']);
        } else {
            echo json_encode(['success' => false, 'error' => 'No data provided']);
        }
        
    } elseif ($action === 'delete' && $key) {
        // Delete campaign
        $result = $campaigns->deleteOne(['_id' => $key]);
        echo json_encode(['success' => true, 'deleted' => $result->getDeletedCount()]);
        
    } elseif ($action === 'list') {
        // List all campaigns
        $pattern = $_GET['pattern'] ?? '*';
        
        $filter = [];
        if ($pattern !== '*') {
            // Convert shell-style pattern to regex
            $regex = str_replace('*', '.*', preg_quote($pattern, '/'));
            $filter['_id'] = new MongoDB\BSON\Regex("^$regex$", 'i');
        }
        
        $cursor = $campaigns->find($filter);
        
        $docs = [];
        foreach ($cursor as $doc) {
            $key = $doc['_id'];
            unset($doc['_id']);
            unset($doc['updated_at']);
            $docs[$key] = (array)$doc;
        }
        
        echo json_encode(['success' => true, 'docs' => $docs]);
        
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
    }

} catch (Exception $e) {
    error_log('Storage error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database operation failed',
        'details' => $e->getMessage()
    ]);
}

// ========== HELPER FUNCTION ==========

function getMongoClient() {
    $mongoUri = getenv('MONGODB_URI');
    
    if (!$mongoUri) {
        throw new Exception('MONGODB_URI not configured');
    }

    return new MongoDB\Client($mongoUri);
}
?>
