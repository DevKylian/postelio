<?php

$dir = __DIR__ . '/resources/views/components/post-templates/glass';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($files as $file) {
    if ($file->isDir() || $file->getExtension() !== 'php' || str_contains($file->getFilename(), 'contact-slide')) {
        continue;
    }

    $path = $file->getRealPath();
    $content = file_get_contents($path);

    // 1. Add Props if not present
    if (!str_contains($content, "'phone' =>")) {
        $propsPattern = "/@props\(\[\s*/";
        $newProps = "@props([\n    'phone' => null,\n    'email' => null,\n    'website' => null,\n    'trade' => null,\n    'slideIndex' => null,\n";
        $content = preg_replace($propsPattern, $newProps, $content);
    }

    // 2. Update preview logic with default contact info
    if (!str_contains($content, "\$phone = \$phone ??")) {
        $previewLogic = "        \$phone = \$phone ?? '01 23 45 67 89';\n        \$email = \$email ?? 'contact@votre-artisan.fr';\n        \$website = \$website ?? 'www.votre-artisan.fr';\n        \$trade = \$trade ?? 'Artisan';\n";
        $content = preg_replace("/(@if\(\$preview\)\s*@php.*?)(\s*@endphp)/s", "$1\n$2", $content); // Ensure spacing
        $content = preg_replace("/(@if\(\$preview\)\s*@php.*?)\s*(@endphp)/s", "$1$previewLogic    $2", $content);
    }

    // 3. Handle $total and x-data
    if (preg_match("/\\\$total\s*=\s*(\d+)/", $content, $matches)) {
        $oldTotal = (int)$matches[1];
        $newTotal = $oldTotal + 1;
        $content = str_replace("\$total = $oldTotal", "\$total = $newTotal", $content);
        $contactIndex = $newTotal - 1;
    } else {
        // Single image template
        $newTotal = 2;
        $totalLogic = "\n@php\n    \$total = 2;\n    \$alpineData = \$preview ? \"x-data=\\\"{ current: 0, total: \$total }\\\"\" : '';\n@endphp\n";
        $content = preg_replace("/(@if\(\$preview\)\s*@php.*?@endphp)/s", "$1$totalLogic", $content);
        
        // Add x-data to the first div if not present
        if (!str_contains($content, 'x-data')) {
            $content = preg_replace("/<div(.*?class=\".*?\")/", "<div {!! \$alpineData !!}$1", $content);
        }
        $contactIndex = 1;
    }

    // 4. Wrap existing content in Slide 0 if it's a single slide template and not already wrapped
    if ($newTotal == 2 && !str_contains($content, 'isSlide0')) {
        $slide0Start = "\n    @php\n        \$isSlide0 = (int)\$slideIndex === 0 || \$slideIndex === null;\n        \$slide0Alpine = \$slideIndex === null ? \"x-show=\\\"current === 0\\\"\" : '';\n        \$slide0Style = (int)\$slideIndex !== 0 && \$slideIndex !== null ? 'display: none;' : '';\n    @endphp\n    <div class=\"absolute inset-0\" {!! \$slide0Alpine !!} style=\"{{ \$slide0Style }}\">\n";
        $slide0End = "\n    </div>\n";
        
        // Find the main div start and end
        if (preg_match("/<div {!! \\\$alpineData !!}.*?>(.*)<\/div>/s", $content, $matches)) {
            $inner = $matches[1];
            $newInner = $slide0Start . $inner . $slide0End;
            $content = str_replace($inner, $newInner, $content);
        }
    }

    // 5. Append Contact Slide before the last </div>
    if (!str_contains($content, 'x-post-templates.contact-slide')) {
        $contactSlide = "\n    {{-- Final Contact Slide --}}\n    @php\n        \$isContactSlide = (int)\$slideIndex === $contactIndex;\n        \$contactAlpine = \$slideIndex === null ? \"x-show=\\\"current === $contactIndex\\\" style=\\\"display: none;\\\"\" : '';\n        \$contactStyle = (int)\$slideIndex !== $contactIndex && \$slideIndex !== null ? 'display: none;' : '';\n    @endphp\n    <div class=\"absolute inset-0 bg-white\" {!! \$contactAlpine !!} style=\"{{ \$contactStyle }}\">\n         <x-post-templates.contact-slide \n            :brand-color=\"\$brandColor\" \n            :company-label=\"\$companyLabel\"\n            :phone=\"\$phone\"\n            :email=\"\$email\"\n            :website=\"\$website\"\n            :trade=\"\$trade\"\n        />\n    </div>\n";

        // Find the matching last </div> of the main container
        $lastDivPos = strrpos($content, '</div>');
        if ($lastDivPos !== false) {
            $content = substr_replace($content, $contactSlide, $lastDivPos, 0);
        }
    }

    file_put_contents($path, $content);
    echo "Updated: $path\n";
}
