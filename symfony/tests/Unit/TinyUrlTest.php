<?php

namespace App\Tests\Unit;

use App\Data\TinyUrl;
use PHPUnit\Framework\TestCase;

class TinyUrlTest extends TestCase
{
    public function dataUrl(): array
    {
        return [
            ['not_valid', false],
            ['https://google.com', true],
            ['https://lifeofchaos.com', true],
        ];
    }

    /**
     * @param string $url
     * @param bool   $isValidUrl
     *
     * @return void
     * @dataProvider dataUrl
     */
    public function testShortUrl(string $url, bool $isValidUrl): void
    {
        $shortenedUrl = TinyUrl::shortUrl($url);
        if (!$isValidUrl) {
            $this->assertEquals('Error', $shortenedUrl, 'An error was expected');
        } else {
            $this->assertTrue(str_contains($shortenedUrl, 'https://tinyurl.com/'), 'Expected a tiny url link.');
        }
    }
}
