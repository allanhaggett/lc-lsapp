<?php
$partnersFile = "partners.json";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Load existing data
    $existingData = file_exists($partnersFile) ? json_decode(file_get_contents($partnersFile), true) : [];

    // DELETE a Partner
    if (isset($_POST["delete_id"])) {
        $deleteId = intval($_POST["delete_id"]);
        $existingData = array_filter($existingData, function ($partner) use ($deleteId) {
            return $partner["id"] !== $deleteId;
        });

        file_put_contents($partnersFile, json_encode(array_values($existingData), JSON_PRETTY_PRINT));
        echo "Partner deleted successfully!";
        exit;
    }

    // ADD or EDIT a Partner
    $partnerIndex = -1;
    if (!empty($_POST["slug"])) {
        foreach ($existingData as $index => $partner) {
            if ($partner["slug"] === $_POST["slug"]) {
                $partnerIndex = $index;
                break;
            }
        }
    }

    $newPartner = [
        "id" => ($partnerIndex !== -1) ? $existingData[$partnerIndex]["id"] : (count($existingData) ? max(array_column($existingData, 'id')) + 1 : 1),
        "name" => $_POST["name"],
        "slug" => $_POST["slug"],
        "description" => $_POST["description"],
        "link" => $_POST["link"],
        "contacts" => []
    ];

    if (isset($_POST["contacts"]) && is_array($_POST["contacts"])) {
        foreach ($_POST["contacts"] as $contact) {
            $newPartner["contacts"][] = [
                "idir" => $contact["idir"],
                "email" => $contact["email"],
                "name" => $contact["name"],
                "title" => $contact["title"],
                "role" => $contact["role"]
            ];
        }
    }

    if ($partnerIndex !== -1) {
        $existingData[$partnerIndex] = $newPartner;
    } else {
        $existingData[] = $newPartner;
    }

    file_put_contents($partnersFile, json_encode(array_values($existingData), JSON_PRETTY_PRINT));
    echo "Partner saved successfully!";
}
