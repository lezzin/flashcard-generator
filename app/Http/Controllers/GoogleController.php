<?php

namespace App\Http\Controllers;

use App\Actions\Google\GetAuthUrlAction;
use App\Actions\Google\HandleCallbackAction;
use Illuminate\Http\Request;

class GoogleController extends Controller
{
    public function auth(GetAuthUrlAction $action)
    {
        $url = $action->execute();

        return response()->json([
            'url' => $url,
        ]);
    }

    public function callback(Request $request, HandleCallbackAction $action)
    {
        if (! $request->has('code')) {
            return redirect()->route('home');
        }

        $action->execute($request->code);

        return inertia('Auth/GoogleSuccess');
    }
}
