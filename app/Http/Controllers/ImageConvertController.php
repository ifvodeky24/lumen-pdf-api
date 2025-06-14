<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageConvertController extends Controller
{
    public function convertToImages(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('file');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $timestamp = time();
        $outputDir = storage_path('app/public/converted');
        $outputBaseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName) . '_' . $timestamp;
        $tempInputPath = storage_path("app/temp_{$timestamp}.pdf");
        $outputPath = $outputDir . '/' . $outputBaseName;

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        copy($file->getPathname(), $tempInputPath);

        // Convert using ImageMagick
        $cmd = "convert -density 150 " . escapeshellarg($tempInputPath) . " " . escapeshellarg($outputPath . ".jpg");
        exec($cmd . ' 2>&1', $output, $returnCode);

        $convertedFiles = glob($outputPath . '*.jpg');

        if (!$convertedFiles || !file_exists($convertedFiles[0])) {
            return response()->json([
                'error' => 'Converted image not found',
                'cmd' => $cmd,
                'output' => $output,
            ], 500);
        }

        // Create ZIP
        $zipFileName = $outputBaseName . '.zip';
        $zipFilePath = $outputDir . '/' . $zipFileName;

        $zip = new \ZipArchive();
        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return response()->json(['error' => 'Failed to create zip file'], 500);
        }

        foreach ($convertedFiles as $filePath) {
            $zip->addFile($filePath, basename($filePath));
        }
        $zip->close();

        // Optional: cleanup images
        foreach ($convertedFiles as $filePath) {
            unlink($filePath);
        }

        $url = url('storage/converted/' . $zipFileName);

        return response()->json([
            'message' => 'Image conversion successful',
            'format' => 'image',
            'zip_url' => $url
        ]);
    }
}
