<?php

function getBase64Font($path) {
    if (!file_exists($path)) {
        return "/* File not found: $path */";
    }
    $data = file_get_contents($path);
    $base64 = base64_encode($data);
    return "data:font/ttf;charset=utf-8;base64," . $base64;
}

$fonts = [
    'Regular' => ['weight' => 400, 'file' => 'Montserrat-Regular.ttf'],
    'Bold'    => ['weight' => 700, 'file' => 'Montserrat-Bold.ttf'],
    'Black'   => ['weight' => 900, 'file' => 'Montserrat-Black.ttf'],
];

$output = "";
foreach ($fonts as $name => $info) {
    $path = "c:/Users/Kylian/Documents/postelio/storage/fonts/" . $info['file'];
    $base64 = getBase64Font($path);
    $output .= "@font-face {\n";
    $output .= "    font-family: 'Montserrat';\n";
    $output .= "    src: url('$base64') format('truetype');\n";
    $output .= "    font-weight: {$info['weight']};\n";
    $output .= "    font-style: normal;\n";
    $output .= "    font-display: block;\n";
    $output .= "}\n\n";
}

echo $output;
