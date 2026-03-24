<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

$apiKey = config('services.zernio.api_key');
$baseUrl = config('services.zernio.base_url', 'https://zernio.com/api/v1');

try {
    $accountsResponse = Http::withToken($apiKey)->get("$baseUrl/accounts");
    $accounts = $accountsResponse->json('accounts');
    $igAccount = collect($accounts)->firstWhere('platform', 'instagram');
    $accountId = $igAccount['_id'];

    echo "Testing /accounts/$accountId/posts with limit=5\n";
    $response = Http::withToken($apiKey)->get("$baseUrl/accounts/$accountId/posts", [
        'limit' => 5
    ]);
    $data = $response->json();
    echo "Count returned: " . count($data['posts'] ?? []) . "\n";
    print_r($data['pagination'] ?? 'No pagination info');

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
