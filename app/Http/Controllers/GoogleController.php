<?php

namespace App\Http\Controllers;

use App\Actions\Google\AuthAction;
use App\Actions\Google\CallbackAction;
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
        if (! $request->has('code')) {
            return redirect()->route('home');
        }

        $action->execute($request->code);

        return inertia('Auth/GoogleSuccess');
    }
}
