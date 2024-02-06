<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UrlShortenerTest extends TestCase
{
    use RefreshDatabase;

    public function testShortenUrlSuccessfully()
    {
        $response = $this->postJson('/api/shorten-url', ['url' => 'https://validsite.com', 'subdir' => 'customsub']);
        $response->assertStatus(200);
    
        $responseData = $response->json();
    
        // Assert that the response has the expected structure and values for 'status' and 'sub'
        $this->assertEquals('success', $responseData['status']);
        $this->assertEquals('customsub', $responseData['sub']);
    
        // Assert that the 'hash' key exists and is not empty
        $this->assertArrayHasKey('hash', $responseData);
        $this->assertNotEmpty($responseData['hash']);
    }

    public function testShortenUrlFailsWithoutUrl()
    {
        $response = $this->postJson('/api/shorten-url', ['subdir' => 'customsub']);
        $response->assertStatus(422);
    }

    public function testShortenUrlFailsWithInvalidUrl()
    {
        $response = $this->postJson('/api/shorten-url', ['url' => 'invalid-url', 'subdir' => 'customsub']);
        $response->assertStatus(422);
    }
}
