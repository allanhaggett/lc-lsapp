<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}

$requiredFields = ['partner_slug', 'partner_name', 'name', 'email', 'idir', 'title', 'role'];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        http_response_code(400);
        echo "Missing required field: $field";
        exit;
    }
}

$request = [
    'partner_slug' => htmlspecialchars(trim($_POST['partner_slug'])),
    'partner_name' => htmlspecialchars(trim($_POST['partner_name'])),
    'name' => htmlspecialchars(trim($_POST['name'])),
    'email' => filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL),
    'idir' => trim($_POST['idir']),
    'title' => htmlspecialchars(trim($_POST['title'])),
    'role' => htmlspecialchars(trim($_POST['role'])),
    'timestamp' => date('c'),
];

// Load existing requests
$file = '../data/partner_contact_requests.json';
$requests = [];

if (file_exists($file)) {
    $json = file_get_contents($file);
    $requests = json_decode($json, true) ?? [];
}

// Append new request and save
$requests[] = $request;
file_put_contents($file, json_encode($requests, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

header("Location: {$_SERVER['HTTP_REFERER']}?submitted=1");
exit;
