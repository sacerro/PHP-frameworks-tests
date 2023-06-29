<?php

namespace App\Tests\Feature;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShortUrlControllerTest extends WebTestCase
{
    private const TEST_URL = 'https://google.com';

    public function caseDataProvider(): array
    {
        return [
            ['{}', true],
            ['{}[]()', true],
            ['{)', false],
            ['[{]}', false],
            ['{([])}', true],
            ['(((((((()', false],
        ];
    }

    /**
     * @param string $bearer
     * @param bool   $isValid
     *
     * @return void
     * @dataProvider    caseDataProvider
     * @throws \JsonException
     */
    public function testShortUrlEndpoint(string $bearer, bool $isValid): void
    {
        $client = static::createClient([], [
            'HTTP_AUTHORIZATION' => sprintf('Bearer %s', $bearer),
            'HTTP_CONTENT-TYPE'  => 'application/json',
        ]);

        $client->request('POST', '/api/short_url', ['url' => self::TEST_URL]);

        $response = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        if ($isValid) {
            $this->assertArrayHasKey('url', $response, 'Expected response from the Controller');
            return;
        }
        $this->assertTrue(
            array_key_exists('status', $response) && false === $response['status'],
            'Bearer token error expected'
        );
    }
}
