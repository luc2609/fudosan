<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Config;

class FileService  implements WithStartRow
{
    public function saveFileToS3($filePath, $exportedObject)
    {
        // $csvContent = Excel::raw($exportedObject, \Maatwebsite\Excel\Excel::CSV);
        // $csvContent = mb_convert_encoding($csvContent, 'SJIS', 'auto');
        $s3 = Storage::disk('s3');
        $s3->put($filePath, $exportedObject);

        $client = $s3->getDriver()->getAdapter()->getClient();
        $bucket = env('AWS_BUCKET');

        $command = $client->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key' => $filePath
        ]);

        $request = $client->createPresignedRequest($command, '+20 minutes');

        return (string) $request->getUri();
    }

    public function uploadFileToS3($file, $filePath)
    {
        $fileName = $file->getClientOriginalName();
        $fileName = preg_replace('/\s+/', '_', $fileName);
        $randomStr = Str::random(10);
        $filePath = $filePath . '/' . $randomStr;
        Log::warning($filePath);
        Log::warning($fileName);
        return $file->storeAs($filePath, $fileName, 's3');
    }

    public function deleteFileS3($fileUrl)
    {
        $filePath = parse_url($fileUrl)['path'];
        Storage::disk('s3')->delete($filePath);
    }

    function setInputEncoding($file)
    {
        $fileContent = file_get_contents($file);
        $enc = mb_detect_encoding($fileContent, mb_list_encodings(), true);
        Config::set('excel.imports.csv.input_encoding', $enc);
    }

    // import file
    public function importFile($request, $validator, $fileImport)
    {
        $this->setInputEncoding($request->file('file'));
        Excel::import($validator, $request->file('file'));
        if (!empty($validator->errors)) {
            return _error($validator->errors, __('error'), HTTP_BAD_REQUEST);
        } else {
            Excel::import($fileImport, $request->file('file'));
            return _success(null, __('message.import_success'), HTTP_SUCCESS);
        }
    }

    public function push($export, $fileName)
    {
        if ($export) {
            $file = $export->getFile();
            $uploadDir = 'export' . '/';
            $fullpath = $uploadDir . $fileName;
            Storage::disk('public')->put($fullpath, mb_convert_encoding(file_get_contents($file), 'SHIFT-JIS', 'UTF-8'), 'public');
            if (Storage::disk('public')->exists($fullpath)) {
                $url = Storage::disk('public')->url($fullpath);
            }
            @unlink(storage_path('framework/laravel-excel/' . $file->getFilename()));
        }
        return $url;
    }

    public function startRow(): int
    {
        return 2;
    }
}
