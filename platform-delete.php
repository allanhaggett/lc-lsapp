<?php 
require('inc/lsapp.php');

if(isSuper()):
if($_POST): 

    $platformId = $_POST['id'];
    
    // Load existing platforms
    $jsonContent = file_get_contents('data/platforms.json');
    $platforms = json_decode($jsonContent, true);
    
    // Filter out the platform to delete
    $platforms = array_filter($platforms, function($platform) use ($platformId) {
        return $platform['id'] !== $platformId;
    });
    
    // Re-index the array to ensure proper JSON encoding
    $platforms = array_values($platforms);
    
    // Save back to JSON file
    file_put_contents('data/platforms.json', json_encode($platforms, JSON_PRETTY_PRINT));
    
    header('Location: platforms.php');

endif;
endif;