<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\UrlShortenerService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class UrlShortenerServiceTest extends TestCase
{
    public function testShortenUrlServiceSuccess()
    {
        // Mocking the external API call
        $mock = new MockHandler([
            new Response(200, [], json_encode(['matches' => []])) // No threats found
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        $service = new UrlShortenerService($client); // Adjust UrlShortenerService to accept Client in constructor

        $result = $service->shortenUrl('https://safeurl.com', 'safe');
        $this->assertEquals('success', $result['status']);
    }

    public function testShortenUrlServiceDetectsUnsafeUrl()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['matches' => ['threats']])) // Threats found
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        $service = new UrlShortenerService($client);

        $result = $service->shortenUrl('http://malware.testing.google.test/testing/malware/', 'unsafe');
        $this->assertEquals('error', $result['status']);
    }
}
