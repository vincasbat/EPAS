<?php

function create_zip($files = [], $destination = "", $overwrite = false)
{
    //if the zip file already exists and overwrite is false, return false
    if (file_exists($destination) && !$overwrite) {
        return false;
    }
    //vars
    $valid_files = [];
    //if files were passed in...
    if (is_array($files)) {
        //cycle through each file
        foreach ($files as $file) {
            //make sure the file exists
            if (file_exists($file)) {
                $valid_files[] = $file;
            }
        }
    }
    //if we have good files...
    if (count($valid_files)) {
        //create the archive
        $zip = new ZipArchive();
        if (
            $zip->open(
                $destination,
                $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE
            ) !== true
        ) {
            return false;
        }
        //add the files
        foreach ($valid_files as $file) {
            $zip->addFile($file, basename($file));
        }
          $zip->close();

        //check to make sure the file exists
        return file_exists($destination);
    } else {
        return false;
    }
}

?>

