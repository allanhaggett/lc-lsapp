<?php require('inc/lsapp.php') ?>

<?php

header('Content-Type: text/plain; charset=utf-8');

try {
   
    // Undefined | Multiple Files | $_FILES Corruption Attack
    // If this request falls under any of them, treat it invalid.
    if (
        !isset($_FILES['file']['error']) ||
        is_array($_FILES['file']['error'])
    ) {
        throw new RuntimeException('Invalid parameters.');
    }
    // Check $_FILES['file']['error'] value.
    switch ($_FILES['file']['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit.');
        default:
            throw new RuntimeException('Unknown errors.');
    }
    // You should also check filesize here.
    if ($_FILES['file']['size'] > 1000000) {
        throw new RuntimeException('ELM File Exceeded filesize limit.');
    }
	if ($_FILES['file']['type'] != 'text/plain' && $_FILES['file']['type'] != 'application/vnd.ms-excel') {
		throw new RuntimeException('Wrong type of file. MUST be a CSV. You tried to upload: ' . $_FILES['file']['type']);
	}
    if (!move_uploaded_file($_FILES['file']['tmp_name'],'data/GBC_CURRENT_COURSE_INFO.csv')) {
        throw new RuntimeException('Failed to move ELM file.');   
    }
	header('Location: /lsapp/uploads-process-gbc.php');

} catch (RuntimeException $e) {

    echo $e->getMessage();

}