<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\ShortenedUrl;
use App\Models\SubDirectories;

class UrlRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function testRedirectWithSubDirectory()
    {
        // Preparing test data
        $subDirectory = SubDirectories::factory()->create(['name' => 'testsub']);
        $shortenedUrl = ShortenedUrl::factory()->create([
            'hash' => 'abc123',
            'original_url' => 'https://example.com',
            'sub_id' => $subDirectory->id
        ]);

        $response = $this->get("/testsub/abc123");
        $response->assertRedirect('https://example.com');
    }

    public function testRedirectWithoutSubDirectoryFallsBackCorrectly()
    {
        $subDirectory = SubDirectories::factory()->create(['name' => '']);
        $shortenedUrl = ShortenedUrl::factory()->create([
            'hash' => 'xyz789',
            'original_url' => 'https://example.net',
            'sub_id' => $subDirectory->id
        ]);

        $response = $this->get("/xyz789");
        $response->assertRedirect('https://example.net');
    }

    public function testRedirectToErrorPageWhenHashNotFound()
    {
        $response = $this->get("/nonexistentsub/unknownhash");
        $response->assertRedirect('/error-page');
    }
}
