<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Company;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ZernioWebhookController extends Controller
{
    /**
     * Handle the incoming Zernio webhook.
     */
    public function __invoke(Request $request)
    {
        // Try both possible signature headers (Zernio vs legacy Late)
        $signature = $request->header('X-Zernio-Signature') ?? $request->header('X-Late-Signature');
        $payload = $request->all();
        $rawPayload = $request->getContent();

        Log::info('Zernio Webhook received', [
            'event' => $payload['event'] ?? $payload['type'] ?? 'unknown',
            'has_signature' => $signature ? 'yes' : 'no',
            'header_type' => $request->hasHeader('X-Zernio-Signature') ? 'Zernio' : ($request->hasHeader('X-Late-Signature') ? 'Late' : 'none')
        ]);

        // Verify signature if secret is configured
        $secret = config('services.zernio.webhook_secret');
        if ($secret && $signature) {
            $computedSignature = hash_hmac('sha256', $rawPayload, $secret);
            if (!hash_equals($computedSignature, $signature)) {
                Log::warning('Zernio Webhook signature mismatch');
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        }

        $event = $payload['event'] ?? $payload['type'] ?? null;
        $data  = $payload['data'] ?? $payload;

        $result = ['status' => 'ok', 'event' => $event];

        switch ($event) {
            case 'post.published':
            case 'post.sent':
            case 'post.created':
                $match = $this->handlePostPublished($data);
                $result['matched'] = $match ? 'yes' : 'no';
                $result['zernio_id'] = $data['post']['id'] ?? $data['post']['_id'] ?? $data['post_id'] ?? $data['id'] ?? $data['_id'] ?? 'not_found';
                break;

            case 'post.failed':
                $match = $this->handlePostFailed($data);
                $result['matched'] = $match ? 'yes' : 'no';
                break;
            
            default:
                Log::info('Zernio Webhook event ignored', ['event' => $event, 'payload' => $payload]);
                $result['status'] = 'ignored';
                break;
        }

        return response()->json($result);
    }

    private function handlePostPublished(array $data): bool
    {
        // Try all possible nested or top-level ID fields
        $zernioPostId = $data['post']['id'] ?? $data['post']['_id'] ?? $data['post_id'] ?? $data['id'] ?? $data['_id'] ?? null;
        $ref = $data['ref'] ?? $data['post']['ref'] ?? $data['metadata']['ref'] ?? null;

        if (!$zernioPostId && !$ref) {
            Log::warning('Zernio Webhook: Could not find post ID or Ref in payload', ['data' => $data]);
            return false;
        }

        // 1. Search by Ref (our internal ID)
        $post = null;
        if ($ref) {
            $post = Post::find($ref);
        }

        // 2. Search in the new JSON array
        if (!$post) {
            $post = Post::whereJsonContains('zernio_ids', $zernioPostId)->first();
        }

        // 3. Fallback to the single string column
        if (!$post) {
            $post = Post::where('zernio_id', $zernioPostId)->first();
        }

        if ($post) {
            $post->update(['status' => Post::STATUS_PUBLISHED]);
            
            Activity::create([
                'user_id' => $post->user_id,
                'company_id' => $post->company_id,
                'type' => 'post.published',
                'message' => "Votre post a été publié avec succès sur Instagram !",
                'data' => $data
            ]);

            Log::info('Post status updated to published via Webhook', ['post_id' => $post->id, 'zernio_id' => $zernioPostId]);
            return true;
        } else {
            Log::warning('Zernio Post Published but not found in DB', ['zernio_id' => $zernioPostId, 'data' => $data]);
            return false;
        }
    }

    private function handlePostFailed(array $data): bool
    {
        $zernioPostId = $data['post']['id'] ?? $data['post']['_id'] ?? $data['post_id'] ?? $data['id'] ?? $data['_id'] ?? null;
        $ref = $data['ref'] ?? $data['post']['ref'] ?? $data['metadata']['ref'] ?? null;
        $error = $data['error'] ?? $data['message'] ?? 'Unknown error';
        
        if (!$zernioPostId && !$ref) {
            Log::warning('Zernio Webhook Failed: Could not find post ID or Ref in payload', ['data' => $data]);
            return false;
        }

        $post = null;
        if ($ref) {
            $post = Post::find($ref);
        }

        if (!$post) {
            $post = Post::whereJsonContains('zernio_ids', $zernioPostId)->first();
        }

        if (!$post) {
            $post = Post::where('zernio_id', $zernioPostId)->first();
        }

        if ($post) {
            $post->update(['status' => Post::STATUS_FAILED]);

            Activity::create([
                'user_id' => $post->user_id,
                'company_id' => $post->company_id,
                'type' => 'post.failed',
                'message' => "La publication a échoué : " . $error,
                'data' => $data
            ]);

            Log::error('Post status updated to failed via Webhook', ['post_id' => $post->id, 'zernio_id' => $zernioPostId, 'error' => $error]);
            return true;
        }

        return false;
    }

    private function handleAccountConnected(array $data)
    {
        $accountId = $data['account']['id'] ?? $data['account']['_id'] ?? $data['id'] ?? null;
        $platform = $data['account']['platform'] ?? $data['platform'] ?? 'unknown';

        if ($accountId) {
            $company = Company::where('zernio_instagram_account_id', $accountId)
                ->orWhere('zernio_facebook_account_id', $accountId)
                ->first();

            if ($company) {
                Activity::create([
                    'user_id' => $company->users()->first()?->id ?? 1, // Fallback to first user or admin
                    'company_id' => $company->id,
                    'type' => 'account.connected',
                    'message' => "Compte {$platform} connecté avec succès via Zernio.",
                    'data' => $data
                ]);
            }
        }
    }

    private function handleAccountDisconnected(array $data)
    {
        $accountId = $data['account']['id'] ?? $data['account']['_id'] ?? $data['id'] ?? null;
        $platform = $data['account']['platform'] ?? $data['platform'] ?? 'unknown';
        
        Log::warning("Zernio Account Disconnected: {$platform}", ['account_id' => $accountId]);

        if ($accountId) {
            $company = Company::where('zernio_instagram_account_id', $accountId)
                ->orWhere('zernio_facebook_account_id', $accountId)
                ->first();

            if ($company) {
                Activity::create([
                    'user_id' => $company->users()->first()?->id ?? 1,
                    'company_id' => $company->id,
                    'type' => 'account.disconnected',
                    'message' => "Attention : Votre compte {$platform} a été déconnecté.",
                    'data' => $data
                ]);

                if ($platform === 'instagram') {
                    $company->update(['zernio_instagram_account_id' => null]);
                } elseif ($platform === 'facebook') {
                    $company->update(['zernio_facebook_account_id' => null]);
                }
            }
        }
    }
}
