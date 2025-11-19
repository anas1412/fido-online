<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GeminiStreamController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = auth()->user();
        if (!$user) abort(403);
        
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) abort(500, 'API Key missing');

        $history = $request->input('history', []);
        $prompt = $request->input('prompt');
        
        // Fetch Business Data
        $tenant = $user->currentTenant;
        $businessData = $this->getBusinessData($tenant);
        
        $dataString = json_encode($businessData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $systemInstruction = "Vous êtes Fido, un assistant comptable. Répondez en Markdown.\n" .
                             "Données : " . $dataString;

        // Prepare Contents
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

        return response()->stream(function () use ($apiKey, $systemInstruction, $contents) {
            
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:streamGenerateContent?alt=sse&key={$apiKey}";
            
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withOptions(['stream' => true])
                ->post($url, [
                    'system_instruction' => ['parts' => [['text' => $systemInstruction]]],
                    'contents' => $contents
                ]);

            $body = $response->getBody();
            $buffer = ''; // Buffer to hold incomplete lines

            while (!$body->eof()) {
                $chunk = $body->read(1024);
                $buffer .= $chunk;

                // Loop while we find a newline in the buffer
                while (($newlinePos = strpos($buffer, "\n")) !== false) {
                    // Extract the complete line
                    $line = substr($buffer, 0, $newlinePos);
                    // Remove line from buffer
                    $buffer = substr($buffer, $newlinePos + 1);

                    // Process the SSE line
                    $line = trim($line);
                    if (str_starts_with($line, 'data: ')) {
                        $jsonStr = substr($line, 6); // Remove 'data: ' prefix
                        
                        if ($jsonStr === '[DONE]') continue; // specific to some APIs, good safety

                        $data = json_decode($jsonStr, true);
                        
                        // Navigate the Gemini JSON structure safely
                        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                            $text = $data['candidates'][0]['content']['parts'][0]['text'];
                            
                            // Send to browser
                            echo "data: " . json_encode(['text' => $text]) . "\n\n";
                            
                            // Force flush output to browser immediately
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
        
        // Quick implementation of your logic
        $data = [];
        if ($tenant->type === 'commercial') {
            $data['clients'] = $tenant->clients()->pluck('name')->toArray();
            $data['invoices'] = $tenant->invoices()->latest()->take(20)->get(['amount', 'status'])->toArray();
        } elseif ($tenant->type === 'accounting') {
            $data['clients'] = $tenant->clients()->pluck('name')->toArray();
            $data['honoraires'] = $tenant->honoraires()->latest()->take(20)->get(['description', 'amount'])->toArray();
        }
        return $data;
    }
}