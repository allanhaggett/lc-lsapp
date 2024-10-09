<?php 

header('Content-Type: text/plain; charset=utf-8');

try {

    if (!move_uploaded_file($_FILES['catsfile']['tmp_name'],'data/GBC_LEARNINGHUB_SYNC2-temp.csv')) {
        throw new RuntimeException('Failed to move ELM file.');   
    } else {
        //$archivename = date('Y-m-d') . '-GBC_ATWORK_CATALOG.csv';
        //rename('GBC_ATWORK_CATALOG.csv',$archivename);
        rename('data/GBC_LEARNINGHUB_SYNC2-temp.csv','data/GBC_LEARNINGHUB_SYNC2.csv');
    }

    if (!move_uploaded_file($_FILES['keysfile']['tmp_name'],'data/GBC_ATWORK_CATALOG_KEYWORDS-temp.csv')) {
        throw new RuntimeException('Failed to move ELM file.');   
    } else {
        //$archivename = date('Y-m-d') . '-GBC_ATWORK_CATALOG_KEYWORDS.csv';
        //rename('GBC_ATWORK_CATALOG_KEYWORDS.csv',$archivename);
        rename('data/GBC_ATWORK_CATALOG_KEYWORDS-temp.csv','data/GBC_ATWORK_CATALOG_KEYWORDS.csv');
    }

	header('Location: process.php');

} catch (RuntimeException $e) {

    echo $e->getMessage();

}