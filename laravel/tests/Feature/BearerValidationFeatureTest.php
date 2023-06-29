<?php

namespace Tests\Feature;

use App\Http\Middleware\BearerValidation as BearerMiddleware;
use Exception;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\Request;
use JsonException;
use Tests\TestCase;

class BearerValidationFeatureTest extends TestCase
{
    use WithoutMiddleware;


    private const TEST_URL = 'https://google.com';

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(BearerMiddleware::class);
    }

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
     * @param bool   $expectedResult
     *
     * @return void
     * @throws Exception
     * @dataProvider caseDataProvider
     */
    public function testRequestWithBearerToken(string $bearer, bool $expectedResult): void
    {
        $bearerValidationMiddleware = new BearerMiddleware();
        $request = Request::create('/api/short_url', 'POST', ['url' => self::TEST_URL], [], [], [
            'HTTP_AUTHORIZATION' => sprintf('Bearer %s', $bearer),
        ]);
        $response = $this->app->handle($request);
        $middlewareResponse = $bearerValidationMiddleware->handle($request, static function ($req) use ($response) {
            return $response;
        });

        if ($expectedResult) {
            $this->assertEquals($response->getContent(), $middlewareResponse->content(), 'Unexpected response');
            return;
        }
        $response = json_decode($middlewareResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertTrue(array_key_exists('status', $response) && false === $response['status'], 'Expected an error');
    }

    /**
     * @param string $bearer
     * @param bool   $expectedResult
     *
     * @return void
     * @dataProvider caseDataProvider
     * @throws JsonException
     * @throws Exception
     */
    public function testRequestWithoutBearerToken(string $bearer, bool $expectedResult): void
    {
        $bearerValidationMiddleware = new BearerMiddleware();
        $request = Request::create('/api/short_url', 'POST', ['url' => 'https://google.com']);
        $response = $this->app->handle($request);
        $middlewareResponse = $bearerValidationMiddleware->handle(
            $request,
            static function (Request $req) use ($response) {
                return $response;
            }
        );
        $expectedError = [
            'status' => false,
            'error'  => 'Missing token',
        ];
        $this->assertEquals(
            json_encode($expectedError, JSON_THROW_ON_ERROR),
            $middlewareResponse->content(),
            'Request is replied without Bearer'
        );
    }
}
