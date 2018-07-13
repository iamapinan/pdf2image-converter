<?php
namespace PDF2Image;
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

class Convert {
    /**
     * Convert pdf file to image
     * @param $file, $type, $output_to
     * @return json
     */
    public static function Run($file, $type = null, $output_to) {
        // Prepare
        $res = [];
        $url = $file;
        $id = uniqid();
        $tmp_name = rand(01,99) . $id . '.pdf';
        $folder =  $output_to . "/" .$id;
        $format = ($type == null) ? 'jpg' : $type;

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
            file_put_contents($output_to . "/content-list.txt", $data, FILE_APPEND);
            
            for ($i = 0; $i < $page_number; $i++) {
                $url = $file_path.'['.$i.']'; 
                
                $image = new Imagick($url);
                $image->setResolution(300,300);
                $image->setImageFormat($format);
                $image->writeImage($folder."/".($i+1).".".$format); 
                // Save output to json
                $res['file'][$i] = ($i+1).'.'.$format;
            }
            
            $image->clear(); 
            $image->destroy();

            // Success response
            $res['status'] = true;
            return $res;
        } else {
            // Fail response.
            $res['status'] = false;
            return $res;
        }
    }
}