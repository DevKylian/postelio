<?php
require 'vendor/autoload.php';
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

try {
    $manager = new ImageManager(new Driver());
    $image = $manager->create(1080, 1080);
    $image->fill('#1E293B');
    
    $fontPath = __DIR__ . '/storage/fonts/Montserrat-Bold.ttf';
    $fontPath = str_replace('\\', '/', $fontPath);
    echo "Font path: $fontPath" . PHP_EOL;
    echo "Exists: " . (file_exists($fontPath) ? 'Yes' : 'No') . PHP_EOL;

    $image->text('Hello World', 540, 540, function ($font) {
        // $font->file($fontPath);
        $font->size(60);
        $font->color('#FFFFFF');
        $font->align('center');
        $font->valign('middle');
    });

    $image->toPng()->save('test_render.png');
    echo "SUCCESS: Image saved to test_render.png" . PHP_EOL;
} catch (\Exception $e) {
    echo "FAILED: " . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}
