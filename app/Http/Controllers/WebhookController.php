<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function plaid()
    {
        return response()->json(['message' => 'Successfully validated!']);
    }
}
