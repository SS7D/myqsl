<?php
session_start();
header('Content-Type: image/png');

$code = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 5);
$_SESSION['captcha'] = $code;

$image = imagecreatetruecolor(100, 36);
$bg = imagecolorallocate($image, 255, 255, 255);
$fg = imagecolorallocate($image, 0, 0, 0);
imagefilledrectangle($image, 0, 0, 100, 36, $bg);
imagestring($image, 5, 15, 10, $code, $fg);

imagepng($image);
imagedestroy($image);
