<?php
opcache_reset();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$requiredFields = ['partner_slug', 'name', 'email', 'idir', 'title', 'role'];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        http_response_code(400);
        exit("Missing required field: $field");
    }
}

$slug = $_POST['partner_slug'];
$contact = [
    'name' => $_POST['name'],
    'email' => $_POST['email'],
    'idir' => trim($_POST['idir']),
    'title' => $_POST['title'],
    'role' => $_POST['role']
];

// Load partner data
$partnerFile = '../data/partners.json';
$partners = file_exists($partnerFile) ? json_decode(file_get_contents($partnerFile), true) : [];

foreach ($partners as &$partner) {
    if ($partner['slug'] === $slug) {
        if (!isset($partner['contacts']) || !is_array($partner['contacts'])) {
            $partner['contacts'] = [];
        }
        // Ensure employee_facing_contact field exists
        if (!isset($partner['employee_facing_contact'])) {
            $partner['employee_facing_contact'] = '';
        }
        $partner['contacts'][] = $contact;
        break;
    }
}
file_put_contents($partnerFile, json_encode($partners, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Remove from contact requests
$requestsFile = '../data/partner_contact_requests.json';
$requests = file_exists($requestsFile) ? json_decode(file_get_contents($requestsFile), true) : [];
$requests = array_filter($requests, function($r) use ($slug, $contact) {
    return !(
        $r['partner_slug'] === $slug &&
        $r['idir'] === $contact['idir']
    );
});
file_put_contents($requestsFile, json_encode(array_values($requests), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

header('Location: dashboard.php');
exit;
