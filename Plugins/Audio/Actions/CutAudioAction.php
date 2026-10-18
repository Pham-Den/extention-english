<?php

namespace Plugins\Audio\Actions;

use Illuminate\Http\Request;

class CutAudioAction
{
    public function handle(Request $request)
    {
        app(SplitAudio::class)->handle($request);
        return [
            'status' => 'success',
            // 'data' => $data
        ];
    }
}
