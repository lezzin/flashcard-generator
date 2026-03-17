<?php

namespace App\Http\Controllers;

use App\Actions\Google\AuthAction;
use App\Actions\Google\CallbackAction;
use App\Actions\Google\UploadFileAction;
use App\Http\Requests\Google\UploadFileRequest;
use Illuminate\Http\Request;

class GoogleController extends Controller
{
    public function auth(AuthAction $action)
    {
        $url = $action->execute();

        return response()->json([
            'url' => $url,
        ]);
    }

    public function callback(Request $request, CallbackAction $action)
    {
        $result = $action->execute($request->code);

        return response()->json([
            ...$result,
        ]);
    }

    public function uploadFile(UploadFileRequest $request, UploadFileAction $action)
    {
        $uploadedId = $action->execute($request->file('file'));

        return response()->json([
            'message' => 'Uploaded successfully!',
            'file_id' => $uploadedId,
        ]);
    }
}
