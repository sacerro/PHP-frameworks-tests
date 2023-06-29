<?php

namespace Tests\Unit;

use App\Http\Data\TinyUrl;
use Tests\TestCase;

class TinyUrlUnitTest extends TestCase
{
    public function urlDataProvider(): array
    {
        return [
            ['not_valid', false],
            ['https://google.com', true],
            ['https://lifeofchaos.com', true],
        ];
    }

    /**
     * @param string $url
     * @param bool $isValidUrl
     *
     * @return void
     * @dataProvider urlDataProvider
     */
    public function testUrlShortener(string $url, bool $isValidUrl): void
    {
        $shortenedUrl = TinyUrl::shortUrl($url);
        if (!$isValidUrl) {
            $this->assertEquals('Error', $shortenedUrl, 'An error was expected');
        } else {
            $this->assertTrue(str_contains($shortenedUrl, 'https://tinyurl.com/'), 'Expected a tiny url link.');
        }
    }
}
