<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class LogViewerController extends Controller
{
    public function index(Request $request)
    {
        $logPath = storage_path('logs');
        $logFiles = [];

        if (File::exists($logPath)) {
            $files = File::files($logPath);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'log') {
                    $logFiles[] = [
                        'name' => $file->getFilename(),
                        'path' => $file->getPathname(),
                        'size' => $this->formatBytes($file->getSize()),
                        'modified' => date('Y-m-d H:i:s', $file->getMTime())
                    ];
                }
            }
        }

        // Sort by modification time (newest first)
        usort($logFiles, function($a, $b) {
            return strtotime($b['modified']) - strtotime($a['modified']);
        });

        $selectedFile = $request->get('file');

        // If no file selected, use the most recent log file
        if (!$selectedFile && !empty($logFiles)) {
            $selectedFile = $logFiles[0]['name'];
        } elseif (!$selectedFile) {
            $selectedFile = 'laravel.log';
        }

        $selectedFilePath = storage_path('logs/' . $selectedFile);

        $logEntries = [];

        if (File::exists($selectedFilePath) && $this->isValidLogFile($selectedFile)) {
            $content = File::get($selectedFilePath);
            $logEntries = $this->parseLogFile($content);

            // Limit to last 500 entries for performance
            $logEntries = array_slice($logEntries, 0, 500);
        }

        return view('admin.logs.index', compact('logFiles', 'selectedFile', 'logEntries'));
    }

    public function download(Request $request)
    {
        $fileName = $request->get('file');
        $filePath = storage_path('logs/' . $fileName);

        if (!File::exists($filePath) || !$this->isValidLogFile($fileName)) {
            abort(404, 'Log file not found');
        }

        return Response::download($filePath);
    }

    public function clear(Request $request)
    {
        $fileName = $request->get('file');
        $filePath = storage_path('logs/' . $fileName);

        if (!File::exists($filePath) || !$this->isValidLogFile($fileName)) {
            return response()->json(['success' => false, 'message' => 'Log file not found']);
        }

        File::put($filePath, '');

        return response()->json(['success' => true, 'message' => 'Log file cleared successfully']);
    }

    public function delete(Request $request)
    {
        $fileName = $request->get('file');
        $filePath = storage_path('logs/' . $fileName);

        if (!File::exists($filePath) || !$this->isValidLogFile($fileName)) {
            return response()->json(['success' => false, 'message' => 'Log file not found']);
        }

        File::delete($filePath);

        return response()->json(['success' => true, 'message' => 'Log file deleted successfully']);
    }

    private function parseLogFile($content)
    {
        $entries = [];
        $lines = explode("\n", $content);
        $currentEntry = null;

        foreach ($lines as $line) {
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.*)/', $line, $matches)) {
                // Save previous entry if exists
                if ($currentEntry) {
                    $entries[] = $currentEntry;
                }

                // Start new entry
                $currentEntry = [
                    'timestamp' => $matches[1],
                    'environment' => $matches[2],
                    'level' => strtoupper($matches[3]),
                    'message' => $matches[4],
                    'context' => '',
                    'full_line' => $line
                ];
            } elseif ($currentEntry && !empty(trim($line))) {
                // Add to context of current entry
                $currentEntry['context'] .= $line . "\n";
                $currentEntry['full_line'] .= "\n" . $line;
            }
        }

        // Add last entry
        if ($currentEntry) {
            $entries[] = $currentEntry;
        }

        return array_reverse($entries); // Show newest first
    }

    private function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');

        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }

    private function isValidLogFile($fileName)
    {
        return pathinfo($fileName, PATHINFO_EXTENSION) === 'log' &&
               !str_contains($fileName, '..') &&
               !str_contains($fileName, '/');
    }
}
