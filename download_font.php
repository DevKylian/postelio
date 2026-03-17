<?php
$url = "https://raw.githubusercontent.com/JulietaUla/Montserrat/master/fonts/ttf/Montserrat-Bold.ttf";
$dest = __DIR__ . "/storage/fonts/Montserrat-Bold.ttf";
echo "Downloading $url to $dest..." . PHP_EOL;
$content = file_get_contents($url);
if ($content === false) {
    echo "FAILED to download font." . PHP_EOL;
} else {
    file_put_contents($dest, $content);
    echo "SUCCESS: Font downloaded." . PHP_EOL;
}
