<?php
namespace App\Services;

use App\Models\ShortenedUrl;
use App\Models\SubDirectories;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class UrlShortenerService
{
    protected $apiKey;
    protected $clientId;

    public function __construct()
    {
        $this->apiKey = config('google.api_key');
        $this->clientId = config('google.client_id');
    }

    public function shortenUrl($originalUrl, $sub)
    {
        $existingUrl = ShortenedUrl::with('subDirectories')
                        ->where('original_url', $originalUrl)
                        ->first();
        
        if($existingUrl) {
            if($existingUrl->subDirectories) {
                return ['hash' => $existingUrl->hash, 'sub' => $existingUrl, 'status' => 'exist'];
            } else {
                return ['hash' => $existingUrl->hash, 'status' => 'exist'];
            }
        }

        if(!$this->validateUrlWithGoogleSafeBrowsing($originalUrl)) {
            return ['error' => 'The URL is not safe.', 'status' => 'error'];
        } 

        $hash = Str::random(6);

        $newUrl = new ShortenedUrl([
            'original_url' => $originalUrl,
            'hash' => $hash,
        ]);

        $subdir = SubDirectories::where('name', $sub)
                    ->first();

        if($subdir) {
            $newUrl->sub_id = $subdir->id;
            $newUrl->save();
            return ['hash' => $newUrl->hash, 'sub' => $subdir->name, 'status' => 'success'];
        } else {
            $newSub = new SubDirectories([
                'name' => $sub
            ]);
            $newSub->save();
            $newUrl->sub_id = $newSub->id;
            $newUrl->save();
            return ['hash' => $newUrl->hash, 'sub' => $newSub->name, 'status' => 'success'];
        }
    }

    protected function validateUrlWithGoogleSafeBrowsing($url)
    {
        $client = new Client();
        $endpoint = 'https://safebrowsing.googleapis.com/v4/threatMatches:find';
        $payload = [
            'client' => [
                'clientId'      => $this->clientId,
                'clientVersion' => '1.0.0'
            ],
            'threatInfo' => [
                'threatTypes'      => ['MALWARE', 'SOCIAL_ENGINEERING'],
                'platformTypes'    => ['ANY_PLATFORM'],
                'threatEntryTypes' => ['URL'],
                'threatEntries'    => [
                    ['url' => $url]
                ]
            ]
        ];
    
        try {
            $response = $client->post($endpoint, [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode($payload),
                'query' => [
                    'key' => $this->apiKey
                ]
            ]);
    
            $body = json_decode($response->getBody());
    
            // Check if any threats are found
            return empty($body->matches);
        } catch (\Exception $e) {
            // Handle exception or error response
            return false;
        }
    }
}
