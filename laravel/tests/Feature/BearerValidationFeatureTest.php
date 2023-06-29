<?php

namespace Tests\Feature;

use App\Http\Middleware\BearerValidation as BearerMiddleware;
use Illuminate\Http\Request;
use Tests\TestCase;

class BearerValidationFeatureTest extends TestCase
{

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
     * @throws \Exception
     * @dataProvider caseDataProvider
     */
    public function testRequestWithBearerToken(string $bearer, bool $expectedResult): void
    {
        $bearerValidationMiddleware = new BearerMiddleware();
        $request = Request::create('/api/short_url', 'POST', ['url' => 'https://google.com'], [], [], [
            'Authorization' => sprintf('Bearer %s', base64_encode($bearer)),
        ]);
        $response = $this->app->handle($request);
        $middlewareResponse = $bearerValidationMiddleware->handle($request, static function ($req) use ($response) {
            return $response;
        });

        $this->assertEquals($response->getContent(), $middlewareResponse->content(), 'Unexpected response');
    }

    /**
     * @param string $bearer
     * @param bool   $expectedResult
     *
     * @return void
     * @dataProvider caseDataProvider
     * @throws \JsonException
     * @throws \Exception
     */
    public function testRequestWithoutBearerToken(string $bearer, bool $expectedResult): void
    {
        $bearerValidationMiddleware = new BearerMiddleware();
        $request = Request::create('/api/short_url', 'POST', ['url' => 'https://google.com']);
        $response = $this->app->handle($request);
        $middlewareResponse = $bearerValidationMiddleware->handle($request, static function ($req) use ($response) {
            return $response;
        });
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
