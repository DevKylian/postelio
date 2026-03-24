<?php

use App\Models\User;
use App\Models\Company;
use App\Models\Post;

$company = new Company(['name' => 'Artisan Test', 'brand_color' => '#E63946', 'logo_path' => null]);
$post = new Post([
    'id' => 9999,
    'template' => 'review',
    'refined_content' => json_encode(['caption' => 'Ceci est un test de generation.', 'image_text' => 'Un travail incroyable et très rapide !'])
]);
$post->setRelation('company', $company);

echo "Generating Image for Post ID 9999...\n";

$service = new \App\Services\PostGeneration\ImageGeneratorService();
try {
    $result = $service->generate($post);
    echo "Generated successfully!\n";
    print_r($result);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
