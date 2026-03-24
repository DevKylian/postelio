<?php
require 'vendor/autoload.php';
use Spatie\Browsershot\Browsershot;

$html = '<h1>Test</h1>';
$path = 'storage/app/public/test_format.jpg';
Browsershot::html($html)
    ->windowSize(360, 360)
    ->deviceScaleFactor(3)
    ->format('jpeg')
    ->noSandbox()
    ->save($path);

echo "File saved to $path\n";
echo "File type: " . shell_exec("file -i $path");
