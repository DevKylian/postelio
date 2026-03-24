<?php
require 'vendor/autoload.php';
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

$path = 'storage/app/public/test_format.jpg'; // This is a PNG from previous test
$outputPath = 'storage/app/public/test_fixed.jpg';

$manager = new ImageManager(new Driver());
$image = $manager->read($path);
$image->toJpeg(90)->save($outputPath);

echo "File fixed and saved to $outputPath\n";
echo "New file type: " . shell_exec("file -i $outputPath");
