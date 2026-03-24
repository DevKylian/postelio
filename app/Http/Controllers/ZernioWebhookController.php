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
        $signature = $request->header('X-Late-Signature');
        $payload = $request->all();
        $rawPayload = $request->getContent();

        Log::info('Zernio Webhook received', [
            'event' => $payload['event'] ?? 'unknown',
            'signature' => $signature ? 'present' : 'missing'
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

        $event = $payload['event'] ?? null;
        $data  = $payload['data'] ?? $payload; // User's example had top-level 'post'

        switch ($event) {
            case 'post.published':
                $this->handlePostPublished($data);
                break;

            case 'post.failed':
                $this->handlePostFailed($data);
                break;

            case 'account.connected':
                $this->handleAccountConnected($data);
                break;

            case 'account.disconnected':
                $this->handleAccountDisconnected($data);
                break;

            default:
                Log::info('Zernio Webhook event ignored', ['event' => $event]);
                break;
        }

        return response()->json(['status' => 'ok']);
    }

    private function handlePostPublished(array $data)
    {
        $zernioPostId = $data['post']['id'] ?? $data['post']['_id'] ?? $data['_id'] ?? null;
        if (!$zernioPostId) return;

        $post = Post::where('zernio_id', $zernioPostId)->first();
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
        } else {
            Log::warning('Zernio Post Published but not found in DB', ['zernio_id' => $zernioPostId]);
        }
    }

    private function handlePostFailed(array $data)
    {
        $zernioPostId = $data['post']['id'] ?? $data['post']['_id'] ?? $data['_id'] ?? null;
        $error = $data['error'] ?? 'Unknown error';
        
        if (!$zernioPostId) return;

        $post = Post::where('zernio_id', $zernioPostId)->first();
        if ($post) {
            $post->update(['status' => Post::STATUS_FAILED]);

            Activity::create([
                'user_id' => $post->user_id,
                'company_id' => $post->company_id,
                'type' => 'post.failed',
                'message' => "Échec de la publication : {$error}",
                'data' => $data
            ]);

            Log::error('Post status updated to failed via Webhook', ['post_id' => $post->id, 'zernio_id' => $zernioPostId, 'error' => $error]);
        }
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
