<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;

class GeminiStreamController extends Controller
{
    public function __invoke(Request $request)
    {
        // 1. Auth Check
        $user = auth()->user();
        if (!$user) abort(403);
        
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) abort(500, 'API Key missing');

        $history = $request->input('history', []);
        $prompt = $request->input('prompt');
        
        // 2. FIX: Retrieve Tenant Explicitly
        // The frontend now sends 'tenant_id'. We verify the user actually belongs to it.
        $tenantId = $request->input('tenant_id');
        $tenant = null;

        if ($tenantId) {
            // Find tenant ONLY if the user is associated with it
            $tenant = Tenant::where('id', $tenantId)
                ->whereHas('users', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->first();
        }

        // Fallback: If no ID sent, grab the user's most recent tenant
        if (!$tenant) {
            $tenant = Tenant::whereHas('users', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->first();
        }
        
        // DEBUG: Confirm we found it this time
        Log::info('AI STREAM: Tenant Resolved', [
            'tenant_id' => $tenant ? $tenant->id : 'STILL NULL', 
            'name' => $tenant ? $tenant->name : 'N/A'
        ]);

        // 3. Get Data
        $businessData = $this->getBusinessData($tenant);
        
        // 4. System Prompt
        $dataString = json_encode($businessData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $systemInstruction = "Vous êtes Fido, un assistant comptable expert.\n" .
                             "Voici les données comptables RÉELLES de l'utilisateur (Source de vérité) :\n" . 
                             $dataString . "\n\n" .
                             "Instructions : Répondez en français. Utilisez le Markdown. Soyez précis avec les chiffres.";

        // 5. Prepare Request
        $contents = [];
        foreach ($history as $msg) {
            if(isset($msg['role']) && !empty($msg['parts'][0]['text'])) {
                $contents[] = [
                    'role' => $msg['role'],
                    'parts' => [['text' => $msg['parts'][0]['text']]]
                ];
            }
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $prompt]]];

        // 6. Stream
        return response()->stream(function () use ($apiKey, $systemInstruction, $contents) {
            
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:streamGenerateContent?alt=sse&key={$apiKey}";
            
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withOptions(['stream' => true])
                ->post($url, [
                    'system_instruction' => ['parts' => [['text' => $systemInstruction]]],
                    'contents' => $contents
                ]);

            $body = $response->getBody();
            $buffer = '';

            while (!$body->eof()) {
                $chunk = $body->read(1024);
                $buffer .= $chunk;

                while (($newlinePos = strpos($buffer, "\n")) !== false) {
                    $line = substr($buffer, 0, $newlinePos);
                    $buffer = substr($buffer, $newlinePos + 1);

                    $line = trim($line);
                    if (str_starts_with($line, 'data: ')) {
                        $jsonStr = substr($line, 6);
                        if ($jsonStr === '[DONE]') continue;
                        
                        $data = json_decode($jsonStr, true);
                        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                            $text = $data['candidates'][0]['content']['parts'][0]['text'];
                            echo "data: " . json_encode(['text' => $text]) . "\n\n";
                            if (ob_get_level() > 0) ob_flush();
                            flush();
                        }
                    }
                }
            }
            echo "event: stop\ndata: stopped\n\n";
            if (ob_get_level() > 0) ob_flush();
            flush();

        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    private function getBusinessData($tenant)
    {
        if (!$tenant) return [];

        $data = [];
        $type = $tenant->type ?? null;

        try {
            // CLIENTS
            if (method_exists($tenant, 'clients')) {
                $data['clients'] = $tenant->clients()
                    ->latest()->take(50)
                    ->get(['name', 'contact_person','email', 'phone', 'address',
        'notes'])
                    ->toArray();
            }

            if ($type === 'commercial' && method_exists($tenant, 'invoices')) {
                $data['invoices'] = $tenant->invoices()
                    ->with('client:id,name')
                    ->latest()->take(99)
                    ->get(['invoice_number', 'issue_date', 'total_amount', 'status', 'client_id'])
                    ->map(fn($i) => [
                        'number' => $i->invoice_number,
                        'amount' => $i->total_amount,
                        'status' => $i->status,
                        'client' => $i->client->name ?? 'N/A'
                    ])->toArray();
            } 
            elseif ($type === 'accounting' && method_exists($tenant, 'honoraires')) {
                $data['honoraires'] = $tenant->honoraires()
                    ->with('client:id,name')
                    ->latest()->take(99)
                    ->get(['honoraire_number', 'object', 'total_amount', 'client_id'])
                    ->map(fn($h) => [
                        'number' => $h->honoraire_number,
                        'object' => $h->object,
                        'amount' => $h->total_amount,
                        'client' => $h->client->name ?? 'N/A'
                    ])->toArray();
            }
        } catch (\Exception $e) {
            Log::error("AI Fetch Error: " . $e->getMessage());
        }

        return $data;
    }
}