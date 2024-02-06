<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UrlShortenerService;
use Illuminate\Support\Facades\Validator;

class UrlShortenerController extends Controller
{
    protected $urlShortenerService;
    
    public function __construct(UrlShortenerService $urlShortenerService)
    {
        $this->urlShortenerService = $urlShortenerService;
    }

    public function shortenUrl(Request $request)
    {    
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
            'subdir' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'status'=>'error'], 422);
        }

        $originalUrl = $request->input('url');
        $sub = $request->input('subdir') ?? "";

        $result = $this->urlShortenerService->shortenUrl($originalUrl, $sub);

        return response()->json($result);
        
    }
    

    
}
