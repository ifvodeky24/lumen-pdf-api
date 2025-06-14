<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;


class PdfController extends Controller
{
    public function compress(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('file');

        $inputPath = $file->getPathname();

        $timestamp = time();
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $outputFileName = 'compressed_' . $timestamp . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName) . '.pdf';
        $outputPath = storage_path('app/public/' . $outputFileName);

        $level = $request->input('level', 'basic');

        $validPresets = [
            'basic' => '/ebook',
            'strong' => '/screen'
        ];

        $pdfSetting = $validPresets[$level] ?? '/ebook';

        $cmd = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS={$pdfSetting} ".
               "-dNOPAUSE -dQUIET -dBATCH -sOutputFile=" . escapeshellarg($outputPath) .
               " " . escapeshellarg($inputPath);

        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($outputPath)) {
            file_put_contents(storage_path('app/gs_error.log'), "CMD: $cmd\nCode: $returnCode\nOutput: " . implode("\n", $output));
            return response()->json(['error' => 'Compression failed'], 500);
        }

        $publicUrl = url('/storage/' . $outputFileName);

        $originalSize = filesize($inputPath);
        $compressedSize = filesize($outputPath);

        return response()->json([
            'message' => 'PDF berhasil dikompres',
            'mode' => $level,
            'original_size_kb' => round($originalSize / 1024, 2),
            'compressed_size_kb' => round($compressedSize / 1024, 2),
            'filename' => $outputFileName,
            'url' => $publicUrl
        ]);
    }

    public function convertToDocx(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('file');

        if (strtolower($file->getClientOriginalExtension()) !== 'pdf') {
            return response()->json(['error' => 'Only PDF files are supported'], 400);
        }

        // Simpan sementara di temp
        $tempName = Str::random(10) . '.pdf';
        $tempPath = storage_path('app/temp/' . $tempName);
        $file->move(storage_path('app/temp/'), $tempName);

        // Output path
        $outputName = Str::random(10) . '.docx';
        $outputPath = storage_path('app/public/converted/' . $outputName);

        // Pastikan folder output ada
        if (!file_exists(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0777, true);
        }

        // Jalankan convert_docx.py
        $cmd = 'python3 ' . base_path('convert_docx.py') . ' ' .
            escapeshellarg($tempPath) . ' ' . escapeshellarg($outputPath);

        exec($cmd, $output, $returnCode);

        // Cleanup temp file
        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        if ($returnCode !== 0 || !file_exists($outputPath)) {
            return response()->json([
                'error' => 'Conversion failed',
                'command' => $cmd,
                'output' => $output
            ], 500);
        }

        // Return URL
        $url = url('/storage/converted/' . $outputName);

        return response()->json([
            'message' => 'PDF successfully converted to DOCX',
            'url' => $url
        ]);
    }

    public function convertToXlsx(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('file');

        if (strtolower($file->getClientOriginalExtension()) !== 'pdf') {
            return response()->json(['error' => 'Only PDF files are supported'], 400);
        }

        $tempName = Str::random(10) . '.pdf';
        $tempPath = storage_path('app/temp/' . $tempName);
        $file->move(storage_path('app/temp/'), $tempName);

        $outputName = Str::random(10) . '.xlsx';
        $outputPath = storage_path('app/public/converted/' . $outputName);

        if (!file_exists(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0777, true);
        }

        $cmd = 'python3 ' . base_path('convert_xlsx.py') . ' ' .
            escapeshellarg($tempPath) . ' ' . escapeshellarg($outputPath);

        exec($cmd, $output, $returnCode);

        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        if ($returnCode !== 0 || !file_exists($outputPath)) {
            return response()->json([
                'error' => 'Conversion failed',
                'command' => $cmd,
                'output' => $output
            ], 500);
        }

        $url = url('/storage/converted/' . $outputName);

        return response()->json([
            'message' => 'PDF successfully converted to XLSX',
            'url' => $url
        ]);
    }

    public function convertToPptx(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('file');

        if (strtolower($file->getClientOriginalExtension()) !== 'pdf') {
            return response()->json(['error' => 'Only PDF files are supported'], 400);
        }

        $tempName = Str::random(10) . '.pdf';
        $tempPath = storage_path('app/temp/' . $tempName);
        $file->move(storage_path('app/temp/'), $tempName);

        $outputName = Str::random(10) . '.pptx';
        $outputPath = storage_path('app/public/converted/' . $outputName);

        if (!file_exists(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0777, true);
        }

        $cmd = 'python3 ' . base_path('convert_pptx.py') . ' ' .
            escapeshellarg($tempPath) . ' ' . escapeshellarg($outputPath);

        exec($cmd, $output, $returnCode);

        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        if ($returnCode !== 0 || !file_exists($outputPath)) {
            return response()->json([
                'error' => 'Conversion failed',
                'command' => $cmd,
                'output' => $output
            ], 500);
        }

        $url = url('/storage/converted/' . $outputName);

        return response()->json([
            'message' => 'PDF successfully converted to PPTX',
            'url' => $url
        ]);
    }

}
