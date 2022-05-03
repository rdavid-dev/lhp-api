<?php

namespace App\Http\Controllers;

use ZipArchive;
use Illuminate\Http\Request;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function uploadFile(Request $request)
    {
        return response()->json($this->zipFiles());

        $arrayName = [];
        if ($request->hasFile('attachments')) {
            foreach($request->file('attachments') as $file)
            {
                $filename = $file->getClientOriginalName();
                Storage::disk('local')->putFileAs(
                    'files',
                    $file,
                    $filename
                );
                $arrayName[] = $filename;
            }

            return response()->json($this->zipFiles());
        }

        return response()->json('no files');
    }

    public function zipFiles()
    {
        $zip_file = storage_path('zip_test.zip');
        $zip = new ZipArchive();
        $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $path = storage_path('app/files');
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        foreach ($files as $file)
        {
            // We're skipping all subfolders
            if (!$file->isDir()) {
                $filePath     = $file->getRealPath();

                // extracting filename with substr/strlen
                $relativePath = substr($filePath, strlen($path) + 1);

                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();

        return $zip_file;
    }
}
