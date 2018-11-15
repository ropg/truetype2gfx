<?php
// Set the enviroment variable for GD
putenv('GDFONTPATH=' . realpath('./fonts'));

// Set the content-type
header('Content-Type: image/png');
header('Cache-Control: no-cache, must-revalidate');

// Create the image
$im = imagecreatetruecolor(320, 240);

$dpi = 141;
if (isset($_GET["dpi"])) $dpi = $_GET["dpi"];

$text = 'Testing 123...';
if (isset($_GET["text"])) $text = $_GET["text"];

$size = (20 * $dpi) / 96;
if (isset($_GET["size"])) $size = ($_GET["size"] * $dpi) / 96;

$font = 'FreeSans.ttf';
if (isset($_GET["font"])) $font = $_GET["font"];

// Create some colors
$white = imagecolorallocate($im, 240, 240, 240);
$black = imagecolorallocate($im, 0, 0, 0);
imagefilledrectangle($im, 0, 0, 319, 239, $white);

// First we create our bounding box for the first text
$bbox = imagettfbbox($size, 0, $font, $text);

// Center text
$w = abs($bbox[4] - $bbox[0]);

$cx = (imagesx($im) / 2) - ($w / 2) - ($bbox[0] / 2);
$cy = (imagesy($im) / 2) - ($bbox[5] / 2) - ($bbox[1] / 2);

// Add the text
imagettftext($im, $size, 0, $cx, $cy, $black, $font, $text);

// Using imagepng() results in clearer text compared with imagejpeg()
imagepng($im);
imagedestroy($im);
?>