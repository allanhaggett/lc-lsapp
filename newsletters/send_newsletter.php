<?php
/**
 * Newsletter Sending Interface
 * Web UI for composing and sending newsletters to active subscribers
 */
require('../inc/lsapp.php');
require_once '../inc/ches_client.php';

// Database connection - use the database in data folder
try {
    $db = new PDO("sqlite:../data/subscriptions.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Initialize email history table
try {
    $db->exec("
        CREATE TABLE IF NOT EXISTS email_campaigns (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            subject TEXT NOT NULL,
            html_body TEXT NOT NULL,
            text_body TEXT,
            from_email TEXT NOT NULL,
            sent_to_count INTEGER DEFAULT 0,
            sent_at TIMESTAMP NOT NULL,
            ches_transaction_id TEXT,
            status TEXT DEFAULT 'pending',
            error_message TEXT,
            created_at TIMESTAMP NOT NULL
        )
    ");
} catch (PDOException $e) {
    error_log("Failed to create email_campaigns table: " . $e->getMessage());
}

// Handle form submission
$message = '';
$messageType = '';
$isPreview = false;
$previewData = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = $_POST['action'] ?? '';
        $subject = trim($_POST['subject'] ?? '');
        $htmlBody = trim($_POST['html_body'] ?? '');
        $fromEmail = trim($_POST['from_email'] ?? 'LearningHUB.Notification@gov.bc.ca');
        // Always use individual email sending for privacy
        
        // Validate inputs
        if (empty($subject)) {
            throw new Exception("Subject is required");
        }
        
        if (empty($htmlBody)) {
            throw new Exception("Message content is required");
        }
        
        if (!filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid sender email address");
        }
        
        // Generate plain text version from HTML
        $textBody = strip_tags($htmlBody);
        $textBody = html_entity_decode($textBody);
        $textBody = preg_replace('/\s+/', ' ', $textBody);
        $textBody = trim($textBody);
        
        if ($action === 'preview') {
            // Preview mode - show what will be sent
            $isPreview = true;
            
            // Get active subscribers
            $stmt = $db->query("SELECT email FROM subscriptions WHERE status = 'active' ORDER BY email");
            $activeSubscribers = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $previewData = [
                'subject' => $subject,
                'html_body' => $htmlBody,
                'text_body' => $textBody,
                'from_email' => $fromEmail,
                'recipient_count' => count($activeSubscribers),
                'recipients' => $activeSubscribers
            ];
            
            $message = "Preview generated successfully. " . count($activeSubscribers) . " active subscribers will receive this email.";
            $messageType = 'success';
            
        } elseif ($action === 'send') {
            // Send the email
            
            // Get active subscribers
            $stmt = $db->query("SELECT email FROM subscriptions WHERE status = 'active' ORDER BY email");
            $activeSubscribers = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (empty($activeSubscribers)) {
                throw new Exception("No active subscribers found");
            }
            
            // Create CHES client using environment variables
            try {
                $chesClient = new CHESClient();
            } catch (Exception $e) {
                throw new Exception("CHES configuration error: " . $e->getMessage());
            }
            
            // Test health check
            if (!$chesClient->healthCheck()) {
                throw new Exception("CHES API is not available");
            }
            
            $now = date('Y-m-d H:i:s');
            
            // Create campaign record
            $campaignStmt = $db->prepare("
                INSERT INTO email_campaigns (subject, html_body, text_body, from_email, sent_to_count, sent_at, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, 'sending', ?)
            ");
            $campaignStmt->execute([$subject, $htmlBody, $textBody, $fromEmail, count($activeSubscribers), $now, $now]);
            $campaignId = $db->lastInsertId();
            
            try {
                // Queue emails for background processing instead of sending directly
                $db->beginTransaction();
                
                try {
                    // Insert all emails into the queue
                    $queueStmt = $db->prepare("
                        INSERT INTO email_queue (campaign_id, recipient_email, subject, html_body, text_body, from_email, status, created_at)
                        VALUES (?, ?, ?, ?, ?, ?, 'pending', ?)
                    ");
                    
                    $queuedCount = 0;
                    foreach ($activeSubscribers as $subscriber) {
                        $queueStmt->execute([
                            $campaignId,
                            $subscriber,
                            $subject,
                            $htmlBody,
                            $textBody,
                            $fromEmail,
                            $now
                        ]);
                        $queuedCount++;
                    }
                    
                    // Update campaign status to queued
                    $updateStmt = $db->prepare("
                        UPDATE email_campaigns 
                        SET status = 'queued'
                        WHERE id = ?
                    ");
                    $updateStmt->execute([$campaignId]);
                    
                    $db->commit();
                    
                    // Calculate estimated sending time
                    $batchesNeeded = ceil($queuedCount / 30);
                    $estimatedMinutes = $batchesNeeded;
                    
                    $message = "Newsletter queued successfully! $queuedCount emails have been added to the sending queue.";
                    $message .= " Estimated sending time: ~$estimatedMinutes minute" . ($estimatedMinutes > 1 ? 's' : '');
                    $message .= " (30 emails/minute rate limit).";
                    $message .= " The emails will be sent automatically in the background.";
                    $messageType = 'success';
                
                    // Clear form data after successful queuing
                    $subject = '';
                    $htmlBody = '';
                    $fromEmail = 'LearningHUB.Notification@gov.bc.ca';
                    
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
                
            } catch (Exception $e) {
                // Update campaign with error
                $updateStmt = $db->prepare("
                    UPDATE email_campaigns 
                    SET status = 'failed', error_message = ?
                    WHERE id = ?
                ");
                $updateStmt->execute([$e->getMessage(), $campaignId]);
                
                throw $e;
            }
        }
        
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = 'error';
    }
}

// Get recent campaigns with queue progress
$recentCampaigns = [];
try {
    $stmt = $db->query("
        SELECT 
            c.id, 
            c.subject, 
            c.from_email, 
            c.sent_to_count, 
            c.sent_at, 
            c.status, 
            c.ches_transaction_id, 
            c.error_message,
            c.processed_count,
            (SELECT COUNT(*) FROM email_queue WHERE campaign_id = c.id AND status = 'sent') as sent_count,
            (SELECT COUNT(*) FROM email_queue WHERE campaign_id = c.id AND status = 'pending') as pending_count,
            (SELECT COUNT(*) FROM email_queue WHERE campaign_id = c.id AND status = 'failed') as failed_count
        FROM email_campaigns c
        ORDER BY c.sent_at DESC 
        LIMIT 10
    ");
    $recentCampaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Failed to fetch recent campaigns: " . $e->getMessage());
}

// Get subscriber count
$subscriberCount = 0;
try {
    $stmt = $db->query("SELECT COUNT(*) FROM subscriptions WHERE status = 'active'");
    $subscriberCount = $stmt->fetchColumn();
} catch (PDOException $e) {
    error_log("Failed to get subscriber count: " . $e->getMessage());
}
?>
<?php getHeader() ?>
<title>Send Newsletter</title>
<?php getScripts() ?>
</head>
<body>
<?php getNavigation() ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Send Newsletter</h1>
            <p class="text-secondary">Compose and send newsletters to your active subscribers</p>
            <div class="mb-3">
                <a href="index.php" class="btn btn-sm btn-outline-primary">← Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>

    <div class="container">
        <?php if (!empty($message)): ?>
            <?php 
                $alertClass = 'alert';
                if($messageType === 'success') $alertClass .= ' alert-success';
                elseif($messageType === 'error') $alertClass .= ' alert-danger';
                elseif($messageType === 'warning') $alertClass .= ' alert-warning';
                else $alertClass .= ' alert-info';
            ?>
            <div class="<?php echo $alertClass; ?>" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($isPreview && $previewData): ?>
            <section class="card border-warning mb-4">
                <div class="card-header bg-warning-subtle">
                    <h2 class="card-title h4 mb-0">📧 Email Preview</h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        Will be sent to <strong><?php echo $previewData['recipient_count']; ?></strong> active subscribers via individual emails (privacy protected)
                        <?php 
                        $estimatedBatches = ceil($previewData['recipient_count'] / 30);
                        if ($estimatedBatches > 1): 
                            $estimatedTime = ($estimatedBatches - 1) * 60;
                            $minutes = floor($estimatedTime / 60);
                            $seconds = $estimatedTime % 60;
                        ?>
                            <br><strong>⏱️ Rate Limiting:</strong> Emails will be sent in <?php echo $estimatedBatches; ?> batches (30 emails/minute).
                            Estimated sending time: <?php echo $minutes > 0 ? "$minutes min $seconds sec" : "$seconds seconds"; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="bg-light-subtle p-3 rounded mb-3">
                        <h3 class="h5">Subject: <?php echo htmlspecialchars($previewData['subject']); ?></h3>
                        <p class="mb-1"><strong>From:</strong> <?php echo htmlspecialchars($previewData['from_email']); ?></p>
                        <p class="mb-1"><strong>Delivery:</strong> Individual emails to each subscriber</p>
                        <p class="small text-secondary mb-0">Note: Each subscriber receives a separate email - they won't see other recipients.</p>
                    </div>
                    
                    <h4 class="h6">HTML Content:</h4>
                    <div class="border p-3 mb-3 bg-white text-dark rounded">
                        <?php echo $previewData['html_body']; ?>
                    </div>
                    
                    <h4 class="h6">Plain Text Version:</h4>
                    <pre class="bg-light-subtle p-3 rounded"><?php echo htmlspecialchars($previewData['text_body']); ?></pre>
                    
                    <details class="mt-3">
                        <summary class="fw-bold">View Recipients (<?php echo count($previewData['recipients']); ?>)</summary>
                        <div class="bg-light-subtle p-3 mt-2 rounded" style="max-height: 200px; overflow-y: auto; font-family: monospace; font-size: 0.9rem;">
                            <?php foreach ($previewData['recipients'] as $email): ?>
                                <?php echo htmlspecialchars($email); ?><br>
                            <?php endforeach; ?>
                        </div>
                    </details>
                    
                    <form method="post" class="mt-4">
                        <input type="hidden" name="action" value="send">
                        <input type="hidden" name="subject" value="<?php echo htmlspecialchars($previewData['subject']); ?>">
                        <input type="hidden" name="html_body" value="<?php echo htmlspecialchars($previewData['html_body']); ?>">
                        <input type="hidden" name="from_email" value="<?php echo htmlspecialchars($previewData['from_email']); ?>">
                        <button type="submit" class="btn btn-success me-2" onclick="return confirm('Are you sure you want to send this newsletter to <?php echo $previewData['recipient_count']; ?> subscribers via individual emails?')">
                            ✉️ Send Newsletter Now
                        </button>
                        <a href="send_newsletter.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </section>
        <?php endif; ?>

        <section class="card bg-light-subtle mb-4">
            <div class="card-body">
                <h2 class="card-title">Compose Newsletter</h2>
                
                <div class="alert alert-info">
                    📊 You have <strong><?php echo $subscriberCount; ?></strong> active subscribers
                    <?php 
                    $estimatedBatches = ceil($subscriberCount / 30);
                    if ($estimatedBatches > 1): 
                        $estimatedTime = ($estimatedBatches - 1) * 60;
                        $minutes = floor($estimatedTime / 60);
                        $seconds = $estimatedTime % 60;
                    ?>
                        <br><small>⏱️ Rate limiting: Emails will be sent in <?php echo $estimatedBatches; ?> batches (30/minute, ~<?php echo $minutes > 0 ? "$minutes min $seconds sec" : "$seconds sec"; ?> total)</small>
                    <?php endif; ?>
                </div>
                
                <form method="post">
                    <div class="mb-3">
                        <label for="from_email" class="form-label">From Email Address</label>
                        <input type="email" id="from_email" name="from_email" class="form-control" value="<?php echo htmlspecialchars($fromEmail ?? 'LearningHUB.Notification@gov.bc.ca'); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject Line</label>
                        <input type="text" id="subject" name="subject" class="form-control" value="<?php echo htmlspecialchars($subject ?? ''); ?>" placeholder="Enter your newsletter subject..." required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="html_body" class="form-label">HTML Message Content</label>
                        <textarea id="html_body" name="html_body" class="form-control" style="min-height: 300px; font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;" placeholder="Enter your HTML newsletter content here..." required><?php echo htmlspecialchars($htmlBody ?? ''); ?></textarea>
                        <div class="form-text text-secondary">
                            You can use HTML tags for formatting. A plain text version will be automatically generated.
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" name="action" value="preview" class="btn btn-primary">
                            👀 Preview Newsletter
                        </button>
                        <?php if ($subscriberCount > 0): ?>
                            <button type="submit" name="action" value="send" class="btn btn-success" onclick="return confirm('Are you sure you want to send this newsletter to <?php echo $subscriberCount; ?> subscribers without previewing?')">
                                ✉️ Send Immediately
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary" disabled title="No active subscribers">
                                ✉️ No Active Subscribers
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </section>

        <?php if (!empty($recentCampaigns)): ?>
            <section class="card bg-light-subtle">
                <div class="card-header">
                    <h2 class="card-title h4 mb-0">Recent Newsletter Campaigns</h2>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentCampaigns as $campaign): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <strong><?php echo htmlspecialchars($campaign['subject']); ?></strong><br>
                                    <small class="text-secondary">
                                        Created <?php echo date('M j, Y g:i A', strtotime($campaign['sent_at'])); ?> 
                                        for <?php echo $campaign['sent_to_count']; ?> subscribers
                                        
                                        <?php if ($campaign['status'] == 'queued' || $campaign['status'] == 'sending'): ?>
                                            <br>📊 Progress: <?php echo $campaign['sent_count']; ?> sent, 
                                            <?php echo $campaign['pending_count']; ?> pending
                                            <?php if ($campaign['failed_count'] > 0): ?>
                                                , <span class="text-danger"><?php echo $campaign['failed_count']; ?> failed</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        
                                        <?php if ($campaign['status'] == 'sent' || $campaign['status'] == 'completed_with_errors'): ?>
                                            <br>✅ Completed: <?php echo $campaign['sent_count']; ?> sent
                                            <?php if ($campaign['failed_count'] > 0): ?>
                                                , <span class="text-danger"><?php echo $campaign['failed_count']; ?> failed</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        
                                        <?php if ($campaign['error_message']): ?>
                                            <br><span class="text-danger">Error: <?php echo htmlspecialchars($campaign['error_message']); ?></span>
                                        <?php endif; ?>
                                    </small>
                                </div>
                                <div>
                                    <?php 
                                    $statusText = str_replace('_', ' ', $campaign['status']);
                                    $badgeClass = 'badge ';
                                    switch($campaign['status']) {
                                        case 'sent':
                                            $badgeClass .= 'bg-success';
                                            break;
                                        case 'failed':
                                            $badgeClass .= 'bg-danger';
                                            break;
                                        case 'sending':
                                        case 'queued':
                                            $badgeClass .= 'bg-warning text-dark';
                                            break;
                                        case 'completed_with_errors':
                                            $badgeClass .= 'bg-warning text-dark';
                                            break;
                                        default:
                                            $badgeClass .= 'bg-secondary';
                                    }
                                    ?>
                                    <span class="<?php echo $badgeClass; ?>">
                                        <?php echo ucfirst($statusText); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </div>
<?php include('../templates/footer.php') ?>