#!/usr/bin/env php
<?php
/**
 * Script to manage email subscriptions from BC Gov Digital Forms API
 * Stores subscribe/unsubscribe actions in a SQLite database
 */

// Set timezone to PST/PDT (America/Vancouver covers BC)
date_default_timezone_set('America/Vancouver');

class SubscriptionManager {
    private $db;
    private $dbPath;
    
    public function __construct($dbPath = '../data/subscriptions.db') {
        $this->dbPath = $dbPath;
        $this->initDatabase();
    }
    
    private function initDatabase() {
        try {
            $this->db = new PDO("sqlite:{$this->dbPath}");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create subscriptions table
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS subscriptions (
                    email TEXT PRIMARY KEY,
                    status TEXT NOT NULL,
                    created_at TIMESTAMP NOT NULL,
                    updated_at TIMESTAMP NOT NULL,
                    source TEXT DEFAULT 'form'
                )
            ");
            
            // Create subscription history table
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS subscription_history (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    email TEXT NOT NULL,
                    action TEXT NOT NULL,
                    timestamp TIMESTAMP NOT NULL,
                    submission_id TEXT,
                    raw_data TEXT
                )
            ");
            
            // Create last sync tracking table
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS last_sync (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    last_sync_timestamp TEXT NOT NULL,
                    sync_type TEXT DEFAULT 'submissions',
                    records_processed INTEGER DEFAULT 0,
                    created_at TIMESTAMP NOT NULL
                )
            ");
            
        } catch (PDOException $e) {
            die("Database initialization failed: " . $e->getMessage() . "\n");
        }
    }
    
    private function getLastSyncTime() {
        $stmt = $this->db->query("SELECT last_sync_timestamp FROM last_sync WHERE sync_type = 'submissions' ORDER BY id DESC LIMIT 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['last_sync_timestamp'] : null;
    }
    
    private function updateLastSyncTime($recordsProcessed = 0) {
        $timestamp = date('c'); // ISO 8601 format
        $now = date('Y-m-d H:i:s');
        
        $stmt = $this->db->prepare("
            INSERT INTO last_sync (last_sync_timestamp, sync_type, records_processed, created_at)
            VALUES (?, 'submissions', ?, ?)
        ");
        $stmt->execute([$timestamp, $recordsProcessed, $now]);
    }
    
    public function fetchSubmissions($sinceDate = null) {
        $url = "https://submit.digital.gov.bc.ca/app/api/v1/forms/fd03b54b-84aa-4a05-b5ff-c5536b733f57/export";
        
        $params = [
            'format' => 'json',
            'type' => 'submissions'
        ];
        
        // Add preference parameter to fetch only new/updated submissions
        if ($sinceDate) {
            $preference = json_encode([
                'updatedMinDate' => $sinceDate
            ]);
            $params['preference'] = $preference;
            echo "Fetching submissions updated since: $sinceDate\n";
        } else {
            echo "Fetching all submissions (initial sync)\n";
        }
        
        $queryString = http_build_query($params);
        
        $username = "fd03b54b-84aa-4a05-b5ff-c5536b733f57";
        $password = "eb907268-25c6-4a3f-b48d-7c7cc93d24e1";
        
        $context = stream_context_create([
            'http' => [
                'header' => "Authorization: Basic " . base64_encode("$username:$password")
            ]
        ]);
        
        $response = @file_get_contents("$url?$queryString", false, $context);
        
        if ($response === false) {
            echo "Error fetching data from API\n";
            return null;
        }
        
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Error parsing JSON response: " . json_last_error_msg() . "\n";
            return null;
        }
        
        $count = is_array($data) ? count($data) : 1;
        if ($count > 0) {
            echo "Successfully fetched $count submission(s)\n";
        } else {
            echo "No new submissions found\n";
        }
        
        return $data;
    }
    
    public function processSubmission($submission) {
        if (!is_array($submission)) {
            return false;
        }
        
        // Extract relevant fields
        $submissionId = $submission['form']['submissionId'] ?? $submission['_id'] ?? 'unknown';
        $formData = $submission['form'] ?? [];
        
        $email = null;
        $options = null;
        
        // Check for email and options in form data
        if (isset($formData['data'])) {
            $data = $formData['data'];
            $email = $data['simpleemail'] ?? $data['email'] ?? $data['simpleEmail'] ?? null;
            $options = $data['options'] ?? $data['action'] ?? $data['subscriptionAction'] ?? null;
        }
        
        // Alternative structure - check top level first
        if (!$email && isset($submission['simpleemail'])) {
            $email = $submission['simpleemail'];
        }
        if (!$options && isset($submission['options'])) {
            $options = $submission['options'];
        }
        
        
        if (!$email) {
            echo "  Skipping submission $submissionId: No email found\n";
            return false;
        }
        
        // Normalize email
        $email = strtolower(trim($email));
        
        // Determine action
        $action = null;
        if ($options) {
            $optionsLower = strtolower($options);
            // Check for unsubscribe first since it contains "subscribe"
            if (strpos($optionsLower, 'unsubscribe') !== false) {
                $action = 'unsubscribe';
            } elseif (strpos($optionsLower, 'subscribe') !== false) {
                $action = 'subscribe';
            }
        }
        
        if (!$action) {
            echo "  Skipping submission $submissionId: No clear action found (options: $options)\n";
            return false;
        }
        
        // Check if this submission has already been processed
        $checkStmt = $this->db->prepare("
            SELECT id FROM subscription_history 
            WHERE submission_id = ? 
            LIMIT 1
        ");
        $checkStmt->execute([$submissionId]);
        
        if ($checkStmt->fetch()) {
            echo "  Skipping submission $submissionId: Already processed\n";
            return false;
        }
        
        echo "  Processing: $action for $email (submission: $submissionId)\n";
        
        $now = date('Y-m-d H:i:s');
        
        try {
            // Record in history
            $stmt = $this->db->prepare("
                INSERT INTO subscription_history (email, action, timestamp, submission_id, raw_data)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$email, $action, $now, $submissionId, json_encode($submission)]);
            
            // Update subscription status
            if ($action == 'subscribe') {
                // Check if email exists
                $checkStmt = $this->db->prepare("SELECT email FROM subscriptions WHERE email = ?");
                $checkStmt->execute([$email]);
                
                if ($checkStmt->fetch()) {
                    // Update existing
                    $updateStmt = $this->db->prepare("
                        UPDATE subscriptions 
                        SET status = 'active', updated_at = ?
                        WHERE email = ?
                    ");
                    $updateStmt->execute([$now, $email]);
                } else {
                    // Insert new
                    $insertStmt = $this->db->prepare("
                        INSERT INTO subscriptions (email, status, created_at, updated_at)
                        VALUES (?, 'active', ?, ?)
                    ");
                    $insertStmt->execute([$email, $now, $now]);
                }
                
            } elseif ($action == 'unsubscribe') {
                // Check if email exists
                $checkStmt = $this->db->prepare("SELECT email FROM subscriptions WHERE email = ?");
                $checkStmt->execute([$email]);
                
                if ($checkStmt->fetch()) {
                    // Update existing
                    $updateStmt = $this->db->prepare("
                        UPDATE subscriptions 
                        SET status = 'unsubscribed', updated_at = ?
                        WHERE email = ?
                    ");
                    $updateStmt->execute([$now, $email]);
                } else {
                    // Insert new as unsubscribed
                    $insertStmt = $this->db->prepare("
                        INSERT INTO subscriptions (email, status, created_at, updated_at)
                        VALUES (?, 'unsubscribed', ?, ?)
                    ");
                    $insertStmt->execute([$email, $now, $now]);
                }
            }
            
            return true;
            
        } catch (PDOException $e) {
            echo "  Database error: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    public function processAllSubmissions() {
        // Get last sync time
        $lastSync = $this->getLastSyncTime();
        
        // Fetch submissions (new ones only if we have a last sync time)
        $submissions = $this->fetchSubmissions($lastSync);
        
        if (!$submissions) {
            echo "No submissions to process\n";
            return;
        }
        
        if (!is_array($submissions)) {
            $submissions = [$submissions];
        }
        
        // Sort submissions by creation date to process in chronological order
        usort($submissions, function($a, $b) {
            $timeA = $a['form']['createdAt'] ?? $a['form']['submittedAt'] ?? '1970-01-01T00:00:00Z';
            $timeB = $b['form']['createdAt'] ?? $b['form']['submittedAt'] ?? '1970-01-01T00:00:00Z';
            return strcmp($timeA, $timeB);
        });
        
        echo "Processing submissions in chronological order...\n";
        
        $processed = 0;
        foreach ($submissions as $submission) {
            if ($this->processSubmission($submission)) {
                $processed++;
            }
        }
        
        echo "\nProcessed $processed out of " . count($submissions) . " submissions\n";
        
        // Update last sync time after successful processing
        if ($processed > 0 || count($submissions) > 0) {
            $this->updateLastSyncTime($processed);
            echo "Updated last sync timestamp\n";
        }
    }
    
    public function getActiveSubscriptions() {
        $stmt = $this->db->query("
            SELECT email, created_at, updated_at 
            FROM subscriptions 
            WHERE status = 'active'
            ORDER BY email
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllSubscriptions() {
        $stmt = $this->db->query("
            SELECT email, status, created_at, updated_at 
            FROM subscriptions 
            ORDER BY status, email
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function displayStatistics() {
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "SUBSCRIPTION STATISTICS\n";
        echo str_repeat("=", 80) . "\n";
        
        // Count by status
        $stmt = $this->db->query("
            SELECT status, COUNT(*) as count 
            FROM subscriptions 
            GROUP BY status
        ");
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo ucfirst($row['status']) . ": " . $row['count'] . "\n";
        }
        
        // Show recent activity
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "RECENT ACTIVITY (Last 10)\n";
        echo str_repeat("=", 80) . "\n";
        
        $stmt = $this->db->query("
            SELECT email, action, timestamp 
            FROM subscription_history 
            ORDER BY timestamp DESC 
            LIMIT 10
        ");
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo $row['timestamp'] . ": " . $row['action'] . " - " . $row['email'] . "\n";
        }
    }
    
    public function exportActiveEmails($filename = "active_subscribers.txt") {
        $active = $this->getActiveSubscriptions();
        
        $file = fopen($filename, 'w');
        foreach ($active as $subscriber) {
            fwrite($file, $subscriber['email'] . "\n");
        }
        fclose($file);
        
        echo "\nExported " . count($active) . " active email addresses to $filename\n";
    }
}

// Main execution
function main() {
    echo "BC Gov Digital Forms - Subscription Manager (PHP)\n";
    echo str_repeat("=", 80) . "\n";
    echo "Started at: " . date('Y-m-d H:i:s') . "\n\n";
    
    // Initialize manager
    $manager = new SubscriptionManager();
    
    // Process all submissions
    $manager->processAllSubmissions();
    
    // Display statistics
    $manager->displayStatistics();
    
    // Show active subscriptions
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "ACTIVE SUBSCRIPTIONS\n";
    echo str_repeat("=", 80) . "\n";
    
    $active = $manager->getActiveSubscriptions();
    if ($active) {
        foreach ($active as $subscriber) {
            echo $subscriber['email'] . " (created: " . $subscriber['created_at'] . 
                 ", updated: " . $subscriber['updated_at'] . ")\n";
        }
    } else {
        echo "No active subscriptions\n";
    }
    
    // Export to file
    if ($active) {
        $manager->exportActiveEmails();
    }
    
    echo "\nCompleted at: " . date('Y-m-d H:i:s') . "\n";
}

// Run if executed directly
if (php_sapi_name() === 'cli') {
    main();
}
?>