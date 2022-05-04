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
        $file_id = rand(100, 10000);
        if ($request->hasFile('attachments')) {
            $path_dir = "files/{$file_id}";
            $attachments = $request->file('attachments');
            // For files with more than 1 we need to store them first in local then zip them
            if (count($attachments) > 1) {
                foreach($attachments as $file) {
                    $filename = $file->getClientOriginalName();
                    Storage::disk('local')->putFileAs(
                        $path_dir,
                        $file,
                        $filename
                    );
                    $this->moveFile('local', $path_dir, $file, $filename);
                }
                $this->zipFiles($file_id);
                //Let's delete the directory after
                Storage::disk('local')->deleteDirectory($path_dir);

                return response()->json($path_dir);
            } else {
                // upload directly in S3
                $this->moveFile('local', $path_dir, $attachments[0], $attachments[0]->getClientOriginalName());
            }
        }
    }

    private function zipFiles($file_id)
    {
        $zip_filename = "zip_test_{$file_id}.zip";
        $zip_file = storage_path($zip_filename);
        $zip = new ZipArchive();
        $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $path = storage_path("app/files/{$file_id}");
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

        Storage::disk('local')->put("files/{$file_id}/{$zip_filename}", file_get_contents($zip_file));
        
        return $zip_file;
    }

    private function moveFile($disk, $path, $file, $filename)
    {
        Storage::disk($disk)->putFileAs(
            $path,
            $file,
            $filename
        );
    }
}
