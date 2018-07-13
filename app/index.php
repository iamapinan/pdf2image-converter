<?php
/**
 * PDF to image converter.
 * Required imagemagick library for PHP. Allow to convert file using API supported by GET, POST method.
 * Licensed by IOTech Enterprise Co.,Ltd.
 * 
 * @author Apinan Woratrakun <apinan@iotech.co.th>
 * @param 
 * - url    = Url of pdf
 * - format = Convert file type
 * @license GNU 2.0
 * @version 1.0.0
 */

// Set JSON header
header('Content-type: application/json');

// Prepare
$res = [];
$url = $_REQUEST['url'];
$id = uniqid();
$tmp_name = rand(01,99) . $id . '.pdf';
$folder =  __DIR__ . "/temp/output/".$id;
$format = (!$_REQUEST['format']) ? 'jpg' : $_REQUEST['format'];

// Create folder
if(!is_dir($folder)) {
    mkdir($folder, 0755);
}

// Assign file path
$file_path = $folder . '/' . $tmp_name;

// Download pdf from url.
file_put_contents($file_path, file_get_contents($url));

// Get pdf page count.
$im = new imagick($file_path);
$page_number = $im->getNumberImages();
$im->clear(); 
$im->destroy();

// Start convert using imagemagick
if ($page_number) { 
    // Add log
    $data = date('d/m/Y, H:i:s') . ", ID:" . $id .", File:". $tmp_name .", NumberOfPage: ".$page_number."\n";
    file_put_contents(__DIR__ . "/temp/content-list.txt", $data, FILE_APPEND);
    
    for ($i = 0; $i < $page_number; $i++) {
        $url = $file_path.'['.$i.']'; 
        
        $image = new Imagick($url);
        $image->setResolution(300,300);
        $image->setImageFormat($format); 
        $image->writeImage($folder."/".($i+1).".".$format); 
        // Save output to json
        $res['file'][$i] = $_ENV['ServerURL'] . "/temp/output/".$id."/".($i+1).'.'.$format;
    }
    
    $image->clear(); 
    $image->destroy();

    // Success response
    $res['status'] = "All pages of PDF is converted to images";
    http_response_code(200);
    echo json_encode($res);
} else {
    // Fail response.
    $res['status'] = "Can not convert pdf.";
    http_response_code(501);
    echo json_encode($res);
}