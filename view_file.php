<?php

session_start();
   if(@$_SESSION['auth'] != "yes")
   {
      header("Location: nera_teisiu.php");
      exit();
   }


if($_GET['id']) {
$string = base64_decode($_GET['id']);
$kel = 'uploaded_files/'.$string;
} 

if($_GET['f']) {
$string = base64_decode($_GET['f']);
$kel = 'pazymos/'.$string;
} 

if($_GET['sk']) {
$string = base64_decode($_GET['sk']);
$kel = 'skundai/'.$string;
}

$ctype = content_type($kel);
$fname = basename($kel);
header('Content-type:"' . $ctype . '"');
//header('Content-type:application/pdf');
header('Content-Disposition:inline; filename="' . $fname . '"');

$fs = filesize($kel);
header ("Content-Length:$fs\n");
readfile ($kel);
exit();



function content_type($name) {
    // Defines the content type based upon the extension of the file
    $contenttype  = 'application/octet-stream';
    $contenttypes = array( 'html' => 'text/html',
                           'htm'  => 'text/html',
                           'txt'  => 'text/plain',
                           'gif'  => 'image/gif',
                           'jpg'  => 'image/jpeg',
                           'png'  => 'image/png',
                           'sxw'  => 'application/vnd.sun.xml.writer',
                           'sxg'  => 'application/vnd.sun.xml.writer.global',
                           'sxd'  => 'application/vnd.sun.xml.draw',
                           'sxc'  => 'application/vnd.sun.xml.calc',
                           'sxi'  => 'application/vnd.sun.xml.impress',
                           'xls'  => 'application/vnd.ms-excel',
                           'ppt'  => 'application/vnd.ms-powerpoint',
                           'doc'  => 'application/msword',
			   'docx'  => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                           'rtf'  => 'text/rtf',
                           'zip'  => 'application/zip',
                           'mp3'  => 'audio/mpeg',
                           'pdf'  => 'application/pdf',
                           'tgz'  => 'application/x-gzip',
                           'gz'   => 'application/x-gzip',
                           'vcf'  => 'text/vcf' );

    $name = ereg_replace("§", " ", $name);
    foreach ($contenttypes as $type_ext => $type_name) {
        if (preg_match ("/$type_ext$/i",  $name)) $contenttype = $type_name;
    }
    return $contenttype;
} 

function getContentType($inFileName){ 
        //--strip path 
        $inFileName = basename($inFileName); 
        //--check for no extension 
        if(strrchr($inFileName,".") == false){ 
            return "application/octet-stream"; 
        } 
        //--get extension and check cases 
        $extension = strrchr($inFileName,"."); 
        switch($extension){ 
            case ".gif":    return "image/gif"; 
            case ".gz":     return "application/x-gzip"; 
            case ".htm":    return "text/html"; 
            case ".html":   return "text/html"; 
            case ".jpg":    return "image/jpeg"; 
            case ".tar":    return "application/x-tar"; 
            case ".txt":    return "text/plain"; 
            case ".zip":    return "application/zip";
            case ".pdf":    return "application/pdf";
            case ".doc":    return "application/msword";
            case ".ai":        return "application/postscript"; 
            default:        return "application/octet-stream"; 
        } 
        return "application/octet-stream"; 
    }

?>


