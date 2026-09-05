<?php

namespace App\Http\Controllers;

use App\Models\UserAward;
use Illuminate\View\View;

class PublicAwardController extends Controller
{
    public function show(string $token): View
    {
        $award = UserAward::where('share_token', $token)->with('user')->firstOrFail();

        return view('public.award.share', compact('award'));
    }
}
