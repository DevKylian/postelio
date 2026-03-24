<?php
use App\Models\Post;
use App\Models\Company;

// Mock a company
$company = Company::first();
if (!$company) {
    $company = Company::create(['name' => 'Artisan Test', 'brand_color' => '#F59E0B']);
}

// Mock a post
$contentData = [
    'caption' => 'Voici un chantier incroyable que nous avons réalisé à Nantes le mois dernier. Nous avons tout refait ! #travaux #nantes',
    'title' => 'Rénovation Toiture Nantes',
    'slides' => [
        'Étape 1 : Protection du chantier et depose',
        'Étape 2 : Pose de la nouvelle charpente',
        'Étape 3 : Finitions et nettoyage final'
    ]
];

$post = new Post([
    'company_id' => $company->id,
    'template' => 'chantier_detail',
    'refined_content' => json_encode($contentData),
    'image_path' => 'posts/dummy1.jpg',
    'extra_images' => ['posts/dummy2.jpg', 'posts/dummy3.jpg']
]);

// Manually set relation
$post->setRelation('company', $company);

// Simulate render
$slideIndex = 1; // Slide 1 should NOT have a banner now
$html = view('render.post', ['post' => $post, 'slideIndex' => $slideIndex])->render();

file_put_contents('test_html.html', $html);
echo "Rendered slide $slideIndex to test_html.html\n";
