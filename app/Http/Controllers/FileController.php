<?php

namespace App\Http\Controllers;

use App\Actions\Google\Drive\DownloadFileAction;
use App\Actions\Google\Drive\GetFilesAction;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{
    public function index(GetFilesAction $action)
    {
        return response()->json($action->execute());
    }

    public function download(string $id, DownloadFileAction $action)
    {
        $result = $action->execute($id);

        $stream = $result['stream'];
        $file = $result['file'];

        return response()->stream(function () use ($stream) {
            while (! $stream->eof()) {
                echo $stream->read(1024 * 8);
            }
        }, Response::HTTP_OK, [
            'Content-Type' => $file->mimeType ?? 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.$file->name.'"',
        ]);
    }
}
