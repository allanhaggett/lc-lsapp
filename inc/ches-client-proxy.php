<?php

class CHESClient {
    private $clientId;
    private $clientSecret;
    private $baseUrl;
    private $token;
    private $tokenExpiry;
    private $proxyUrl;
    private $proxyAuth;
    
    public function __construct() {
        $this->clientId = $_SERVER['CHES_CLIENT_ID'] ?? '';
        $this->clientSecret = $_SERVER['CHES_CLIENT_SECRET'] ?? '';
        $this->baseUrl = 'https://ches-dev.api.gov.bc.ca/api/v1';
        $this->token = null;
        $this->tokenExpiry = null;
        
        // Configure proxy if needed - update these values
        $this->proxyUrl = $_SERVER['HTTP_PROXY'] ?? null; // e.g., 'proxy.yourorg.gov.bc.ca:8080'
        $this->proxyAuth = $_SERVER['PROXY_AUTH'] ?? null; // e.g., 'username:password'
    }
    
    /**
     * Configure cURL with proxy settings if needed
     */
    private function configureCurlProxy($ch, $url) {
        // Check if this URL needs proxy (external URLs only)
        $needsProxy = false;
        $host = parse_url($url, PHP_URL_HOST);
        
        // These hosts need proxy (external auth server)
        $externalHosts = ['dev.loginproxy.gov.bc.ca', 'loginproxy.gov.bc.ca'];
        if (in_array($host, $externalHosts)) {
            $needsProxy = true;
        }
        
        if ($needsProxy && $this->proxyUrl) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxyUrl);
            if ($this->proxyAuth) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyAuth);
            }
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
        }
    }
    
    /**
     * Get OAuth2 token from CHES
     */
    private function getToken() {
        // Check if we have a valid token
        if ($this->token && $this->tokenExpiry && time() < $this->tokenExpiry) {
            return $this->token;
        }
        
        // Request new token
        $tokenUrl = 'https://dev.loginproxy.gov.bc.ca/auth/realms/comsvcauth/protocol/openid-connect/token';
        
        $postData = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret
        ];
        
        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        
        // Configure proxy for external auth server
        $this->configureCurlProxy($ch, $tokenUrl);
        
        // Windows/IIS specific SSL handling
        if (stripos(PHP_OS, 'WIN') === 0) {
            $cainfo = ini_get('curl.cainfo');
            if (empty($cainfo) || !file_exists($cainfo)) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                error_log("CHES Warning: SSL verification disabled - no CA bundle found");
            } else {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            }
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        }
        
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $errno = curl_errno($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($response === false) {
            error_log("CHES Token cURL Error: $error (errno: $errno)");
            error_log("Token URL: $tokenUrl");
            if ($this->proxyUrl) {
                error_log("Using proxy: $this->proxyUrl");
            }
            return false;
        }
        
        if ($httpCode !== 200) {
            error_log("CHES Token Error: HTTP $httpCode - $response");
            return false;
        }
        
        $tokenData = json_decode($response, true);
        if (!isset($tokenData['access_token'])) {
            error_log("CHES Token Error: No access token in response");
            return false;
        }
        
        $this->token = $tokenData['access_token'];
        // Set expiry to 5 minutes before actual expiry for safety
        $this->tokenExpiry = time() + ($tokenData['expires_in'] ?? 3600) - 300;
        
        return $this->token;
    }
    
    /**
     * Send email via CHES API
     */
    public function sendEmail($to, $subject, $body, $from = null, $isHtml = true) {
        $token = $this->getToken();
        if (!$token) {
            error_log("CHES Email Error: Could not obtain token");
            return false;
        }
        
        // Prepare email data
        $emailData = [
            'bodyType' => $isHtml ? 'html' : 'text',
            'body' => $body,
            'from' => $from ?? 'LSApp@gov.bc.ca',
            'subject' => $subject,
            'to' => is_array($to) ? $to : [$to],
            'priority' => 'normal'
        ];
        
        // Send email request
        $emailUrl = $this->baseUrl . '/email';
        
        $ch = curl_init($emailUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        
        // CHES API is internal, no proxy needed
        // $this->configureCurlProxy($ch, $emailUrl);
        
        // Windows/IIS specific SSL handling
        if (stripos(PHP_OS, 'WIN') === 0) {
            $cainfo = ini_get('curl.cainfo');
            if (empty($cainfo) || !file_exists($cainfo)) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            } else {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            }
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        }
        
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $errno = curl_errno($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($response === false) {
            error_log("CHES Email Send Error: $error (errno: $errno)");
            return false;
        }
        
        if ($httpCode !== 201 && $httpCode !== 200) {
            error_log("CHES Email Error: HTTP $httpCode - $response");
            return false;
        }
        
        $result = json_decode($response, true);
        return $result['msgId'] ?? true;
    }
    
    /**
     * Send course creation notification
     */
    public function sendCourseCreationNotification($courseData) {
        $subject = "New Course Created: " . $courseData['name'];
        
        $body = "<h2>New Course Created</h2>";
        $body .= "<p>A new course has been created in LSApp:</p>";
        $body .= "<ul>";
        $body .= "<li><strong>Course ID:</strong> " . htmlspecialchars($courseData['id']) . "</li>";
        $body .= "<li><strong>Course Name:</strong> " . htmlspecialchars($courseData['name']) . "</li>";
        $body .= "<li><strong>Description:</strong> " . htmlspecialchars($courseData['description']) . "</li>";
        $body .= "<li><strong>Owner:</strong> " . htmlspecialchars($courseData['owner']) . "</li>";
        $body .= "<li><strong>Partner:</strong> " . htmlspecialchars($courseData['partner']) . "</li>";
        $body .= "<li><strong>Platform:</strong> " . htmlspecialchars($courseData['platform']) . "</li>";
        $body .= "<li><strong>Method:</strong> " . htmlspecialchars($courseData['method']) . "</li>";
        $body .= "<li><strong>Effective Date:</strong> " . htmlspecialchars($courseData['effectiveDate']) . "</li>";
        $body .= "<li><strong>Created:</strong> " . htmlspecialchars($courseData['created']) . "</li>";
        $body .= "</ul>";
        $body .= "<p><a href='https://learning.gov.bc.ca/lsapp/course.php?courseid=" . urlencode($courseData['id']) . "'>View Course</a></p>";
        
        return $this->sendEmail('allan.haggett@gov.bc.ca', $subject, $body);
    }
}