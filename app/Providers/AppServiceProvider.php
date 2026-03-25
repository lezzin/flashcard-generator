<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $startTimes = [];
        $startMemory = [];

        Queue::before(function (JobProcessing $event) use (&$startTimes, &$startMemory) {
            $jobId = $event->job->getJobId();

            $startTimes[$jobId] = microtime(true);
            $startMemory[$jobId] = memory_get_usage();
        });

        // Queue::after(function (JobProcessed $event) use (&$startTimes, &$startMemory) {
        //     $jobId = $event->job->getJobId();

        //     if (! isset($startTimes[$jobId])) {
        //         return;
        //     }

        //     $executionTime = round(microtime(true) - $startTimes[$jobId], 4);
        //     $memoryUsed = memory_get_usage() - $startMemory[$jobId];
        //     $peakMemory = memory_get_peak_usage();

        //     Log::channel('discord')->info("Job Processed: {$event->job->resolveName()}", [
        //         'ID' => $jobId,
        //         'Connection' => $event->job->getConnectionName(),
        //         'Queue' => $event->job->getQueue(),
        //         'Duration' => "{$executionTime}s",
        //         'Memory' => Number::fileSize($memoryUsed),
        //         'Peak Memory' => Number::fileSize($peakMemory),
        //         'Attempts' => $event->job->attempts(),
        //     ]);

        //     unset($startTimes[$jobId], $startMemory[$jobId]);
        // });

        Queue::failing(function (JobFailed $event) use (&$startTimes, &$startMemory) {
            $jobId = $event->job->getJobId();

            $executionTime = isset($startTimes[$jobId])
                ? round(microtime(true) - $startTimes[$jobId], 4)
                : null;

            Log::channel('discord')->error("Job Failed: {$event->job->resolveName()}", [
                'ID' => $jobId,
                'Connection' => $event->job->getConnectionName(),
                'Queue' => $event->job->getQueue(),
                'Duration' => $executionTime ? "{$executionTime}s" : 'N/A',
                'Attempts' => $event->job->attempts(),
                'Error' => $event->exception->getMessage(),
                'Location' => "{$event->exception->getFile()}:{$event->exception->getLine()}",
            ]);

            unset($startTimes[$jobId], $startMemory[$jobId]);
        });

        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(
            fn (): ?Password => app()->isProduction()
                ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
                : null,
        );
    }
}
