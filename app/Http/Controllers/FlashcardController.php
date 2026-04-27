<?php

namespace App\Http\Controllers;

use App\Actions\Anki\GenerateFlashcardAction;
use App\DTOs\SourceContentDto;
use App\Http\Requests\Flashcard\FlashcardGenerateByBackupRequest;
use App\Http\Requests\Flashcard\FlashcardGenerateRequest;
use App\Jobs\Flashcard\GenerateFlashcardJob;
use App\Services\Anki\AnkiConnectClient;

class FlashcardController extends Controller
{
    public function store(FlashcardGenerateRequest $request, AnkiConnectClient $ankiConnectClient)
    {
        $ankiConnectClient->validateConnection();

        dispatch(new GenerateFlashcardJob(
            new SourceContentDto(
                title: $request->input('title'),
                content: $request->input('content')
            )
        ))->onQueue('flashcard:generate');

        return response()->noContent();
    }

    public function storeByBackup(FlashcardGenerateByBackupRequest $request, GenerateFlashcardAction $action)
    {
        $action->execute(new SourceContentDto(
            title: $request->input('title'),
            isBackup: true
        ));

        return response()->noContent();
    }
}
