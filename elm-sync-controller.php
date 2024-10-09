<?php 

header('Content-Type: text/plain; charset=utf-8');

try {
   
    // Undefined | Multiple Files | $_FILES Corruption Attack
    // If this request falls under any of them, treat it invalid.
    if (
        !isset($_FILES['elmfile']['error']) ||
        is_array($_FILES['elmfile']['error'])
    ) {
        throw new RuntimeException('Invalid parameters.');
    }
    // Check $_FILES['elmfile']['error'] value.
    switch ($_FILES['elmfile']['error']) {
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
    if ($_FILES['elmfile']['size'] > 1000000) {
        throw new RuntimeException('ELM File Exceeded filesize limit.');
    }
	if ($_FILES['elmfile']['type'] != 'text/csv' && $_FILES['elmfile']['type'] != 'text/plain' && $_FILES['elmfile']['type'] != 'application/vnd.ms-excel') {
		throw new RuntimeException('Wrong type of file. MUST be a CSV. You tried to upload: ' . $_FILES['elmfile']['type']);
	}
    if (!move_uploaded_file($_FILES['elmfile']['tmp_name'],'data/GBC_CURRENT_COURSE_INFO.csv')) {
        throw new RuntimeException('Failed to move ELM file.');   
    }
	header('Location: elm-sync-process-gbc.php');

} catch (RuntimeException $e) {

    echo $e->getMessage();

}