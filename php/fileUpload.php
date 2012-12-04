<?php

// list of valid extensions, ex. array("jpeg", "xml", "bmp")
$allowedExtensions = array("csv");

// max file size in bytes
$sizeLimit = 10 * 1024 * 1024;

require_once('qqUploadedFileXhr.php');

$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);

// Call handleUpload() with the name of the folder, relative to PHP's getcwd()
$result = $uploader->handleUpload('uploads/');


// to pass data through iframe you will need to encode all html tags
echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);


?>