<?php

class CHESClient {
    private $clientId;
    private $clientSecret;
    private $baseUrl;
    private $token;
    private $tokenExpiry;
    private $cacheFile;
    private $cacheDir;
    
    public function __construct() {
        $this->clientId = $_SERVER['CHES_CLIENT_ID'] ?? '';
        $this->clientSecret = $_SERVER['CHES_CLIENT_SECRET'] ?? '';
        $this->baseUrl = 'https://ches-dev.api.gov.bc.ca/api/v1';
        
        // Set up cache directory and file
        $this->cacheDir = dirname(__FILE__) . '/cache';
        $this->cacheFile = $this->cacheDir . '/ches_token.json';
        
        // Create cache directory if it doesn't exist
        if (!file_exists($this->cacheDir)) {
            mkdir($this->cacheDir, 0750, true);
            // Create .htaccess to protect cache directory on Apache
            file_put_contents($this->cacheDir . '/.htaccess', 'Deny from all');
            // Create web.config for IIS
            file_put_contents($this->cacheDir . '/web.config', '<?xml version="1.0"?>
<configuration>
    <system.webServer>
        <authorization>
            <remove users="*" roles="" verbs="" />
        </authorization>
    </system.webServer>
</configuration>');
        }
        
        // Load cached token if available
        $this->loadCachedToken();
    }
    
    /**
     * Load token from cache file
     */
    private function loadCachedToken() {
        if (file_exists($this->cacheFile)) {
            $cache = json_decode(file_get_contents($this->cacheFile), true);
            if ($cache && isset($cache['token']) && isset($cache['expiry'])) {
                // Check if token is still valid (with 5 minute buffer)
                if (time() < ($cache['expiry'] - 300)) {
                    $this->token = $cache['token'];
                    $this->tokenExpiry = $cache['expiry'];
                    error_log("CHES: Using cached token (expires: " . date('Y-m-d H:i:s', $this->tokenExpiry) . ")");
                } else {
                    error_log("CHES: Cached token expired");
                }
            }
        }
    }
    
    /**
     * Save token to cache file
     */
    private function saveTokenToCache($token, $expiresIn) {
        $expiry = time() + $expiresIn;
        $cache = [
            'token' => $token,
            'expiry' => $expiry,
            'cached_at' => time(),
            'cached_date' => date('Y-m-d H:i:s')
        ];
        
        $result = file_put_contents($this->cacheFile, json_encode($cache, JSON_PRETTY_PRINT));
        if ($result === false) {
            error_log("CHES: Failed to cache token to file");
        } else {
            error_log("CHES: Token cached until " . date('Y-m-d H:i:s', $expiry));
        }
    }
    
    /**
     * Manually set a token (for emergency use when auth server is unreachable)
     * You would get this token from another environment or tool
     */
    public function setManualToken($token, $expiresIn = 3600) {
        $this->token = $token;
        $this->tokenExpiry = time() + $expiresIn;
        $this->saveTokenToCache($token, $expiresIn);
        error_log("CHES: Manual token set and cached");
    }
    
    /**
     * Get OAuth2 token from CHES
     */
    private function getToken() {
        // Check if we have a valid token in memory or cache
        if ($this->token && $this->tokenExpiry && time() < ($this->tokenExpiry - 300)) {
            return $this->token;
        }
        
        // Try to load from cache again (in case another process updated it)
        $this->loadCachedToken();
        if ($this->token && $this->tokenExpiry && time() < ($this->tokenExpiry - 300)) {
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
            error_log("CHES Token cURL Error: $error (errno: $errno)");
            
            // If we still have an old cached token, use it even if expired
            // Better to try with expired token than fail completely
            if ($this->token) {
                error_log("CHES: Using expired cached token as fallback");
                return $this->token;
            }
            
            return false;
        }
        
        if ($httpCode !== 200) {
            error_log("CHES Token Error: HTTP $httpCode - $response");
            
            // Fallback to cached token if available
            if ($this->token) {
                error_log("CHES: Using cached token as fallback");
                return $this->token;
            }
            
            return false;
        }
        
        $tokenData = json_decode($response, true);
        if (!isset($tokenData['access_token'])) {
            error_log("CHES Token Error: No access token in response");
            return false;
        }
        
        $this->token = $tokenData['access_token'];
        $expiresIn = $tokenData['expires_in'] ?? 3600;
        $this->tokenExpiry = time() + $expiresIn;
        
        // Cache the token
        $this->saveTokenToCache($this->token, $expiresIn);
        
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
            
            // If we get 401 (unauthorized), our token might be invalid
            if ($httpCode == 401) {
                error_log("CHES: Token appears invalid, clearing cache");
                $this->token = null;
                $this->tokenExpiry = null;
                @unlink($this->cacheFile);
            }
            
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
    
    /**
     * Get current token info (for debugging)
     */
    public function getTokenInfo() {
        return [
            'has_token' => !empty($this->token),
            'expires_at' => $this->tokenExpiry ? date('Y-m-d H:i:s', $this->tokenExpiry) : null,
            'is_valid' => $this->token && $this->tokenExpiry && time() < ($this->tokenExpiry - 300),
            'cache_file' => $this->cacheFile,
            'cache_exists' => file_exists($this->cacheFile)
        ];
    }
}