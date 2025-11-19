<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class LogViewerController extends Controller
{
    public function index(Request $request)
    {
        // Return JSON if requested via AJAX or explicitly asked for json
        if ($request->ajax() || $request->wantsJson() || $request->has('json')) {
            return response()->json($this->getLogData($request));
        }

        return view('log-viewer');
    }

    private function getLogData(Request $request)
    {
        // 1. Find the latest log file automatically (handles daily logs vs single log)
        $logPath = storage_path('logs');
        $files = glob($logPath . '/*.log');
        
        if (empty($files)) {
            return $this->emptyResponse("No log files found in storage/logs.");
        }

        // Sort files by modified time, newest first
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $logFile = $files[0]; // Take the newest file

        if (!File::exists($logFile)) {
            return $this->emptyResponse("Log file not readable.");
        }

        // 2. Read the file (Max 5MB to prevent memory crashes)
        // Only reading the end of the file for performance
        $maxBytes = 5 * 1024 * 1024; 
        $fileSize = File::size($logFile);
        $offset = max(0, $fileSize - $maxBytes);
        $content = file_get_contents($logFile, false, null, $offset);

        // 3. Parse Logs
        // Matches: [2023-11-20 10:30:00] Env.LEVEL: Message
        $pattern = '/\[(?<date>\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (?<env>\w+)\.(?<level>\w+): (?<message>[\s\S]*?)(?=\n\[\d{4}-\d{2}-\d{2}|$)/';
        
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        $logs = [];
        foreach (array_reverse($matches) as $match) {
            $logs[] = [
                'date' => $match['date'],
                'env' => $match['env'],
                'level' => strtoupper($match['level']),
                'message' => trim($match['message']),
                'summary' => Str::limit(trim($match['message']), 120),
                'color' => $this->getLevelColor($match['level']),
            ];
        }

        $collection = collect($logs);

        // 4. Filters
        if ($request->has('level') && $request->level !== 'ALL') {
            $collection = $collection->where('level', $request->level);
        }

        if ($request->has('search') && $request->search) {
            $term = strtolower($request->search);
            $collection = $collection->filter(function ($item) use ($term) {
                return str_contains(strtolower($item['message']), $term) || 
                       str_contains(strtolower($item['date']), $term);
            });
        }

        // 5. Pagination
        $perPage = 20;
        $page = $request->input('page', 1);
        $values = $collection->slice(($page - 1) * $perPage, $perPage)->values();

        return [
            'data' => $values,
            'current_page' => (int)$page,
            'last_page' => ceil($collection->count() / $perPage),
            'total' => $collection->count(),
            'file_name' => basename($logFile), // Tell UI which file we are reading
            'stats' => [
                'total' => count($logs),
                'errors' => collect($logs)->where('level', 'ERROR')->count(),
                'warnings' => collect($logs)->where('level', 'WARNING')->count(),
            ]
        ];
    }

    private function emptyResponse($message)
    {
        return [
            'data' => [],
            'current_page' => 1,
            'last_page' => 1,
            'stats' => ['total' => 0, 'errors' => 0, 'warnings' => 0],
            'file_name' => $message
        ];
    }

    private function getLevelColor($level)
    {
        return match (strtolower($level)) {
            'error', 'critical', 'alert', 'emergency' => 'text-red-500 bg-red-50 border-red-200',
            'warning' => 'text-orange-500 bg-orange-50 border-orange-200',
            'info', 'notice' => 'text-blue-500 bg-blue-50 border-blue-200',
            'debug' => 'text-gray-500 bg-gray-50 border-gray-200',
            default => 'text-gray-500 bg-gray-50 border-gray-200',
        };
    }
}