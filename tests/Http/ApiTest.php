<?php

namespace Tests\Http;

use Tests\TestCase;
use DmitryIvanov\DarkSkyApi\Http\Api;
use Psr\Http\Message\ResponseInterface;
use DmitryIvanov\DarkSkyApi\Weather\Data;
use Tests\Http\Stubs\Parameters\BaseStub;
use Tests\Http\Stubs\Parameters\ForecastStub;
use DmitryIvanov\DarkSkyApi\Contracts\Http\Client;
use DmitryIvanov\DarkSkyApi\Contracts\Http\Request;
use Tests\Http\Stubs\Parameters\ForecastWithDatesStub;
use DmitryIvanov\DarkSkyApi\Contracts\Http\RequestFactory;
use Tests\Http\Stubs\Parameters\ForecastWithMultipleDatesStub;
use Tests\Http\Stubs\Parameters\ForecastWithMultipleDatesLeanStub;

class ApiTest extends TestCase
{
    /**
     * @test
     * @param  \Tests\Http\Stubs\Parameters\BaseStub  $parameters
     * @dataProvider single_api_request_parameters_provider
     */
    public function it_makes_the_single_api_request_for_the_forecast_and_the_lean_time_machines(BaseStub $parameters)
    {
        $client = mock(Client::class);
        $factory = mock(RequestFactory::class);
        $request = $parameters->expectedRequests();
        $response = mock(ResponseInterface::class);
        $responseBody = ['status' => 'success'];
        $responseHeaders = ['X-Response-Time' => [0.123]];
        $expected = new Data($responseBody, $responseHeaders);

        $factory->shouldReceive('create')
            ->with($parameters)
            ->andReturn($request);

        $client->shouldReceive('gzip')
            ->withNoArgs()
            ->andReturnSelf();

        $client->shouldReceive('request')
            ->with($request)
            ->andReturn($response);

        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->andReturn(json_encode($responseBody));

        $response->shouldReceive('getHeaders')
            ->withNoArgs()
            ->andReturn($responseHeaders);

        $this->assertEquals($expected, (new Api($client, $factory))->request($parameters));
    }

    /**
     * The data provider for the single API request test.
     *
     * @see it_makes_the_single_api_request_for_the_forecast_and_the_lean_time_machines
     *
     * @return array
     */
    public function single_api_request_parameters_provider()
    {
        return [
            [new ForecastStub],
            [new ForecastWithDatesStub],
            [new ForecastWithMultipleDatesLeanStub],
        ];
    }

    /** @test */
    public function it_makes_the_concurrent_requests_for_the_time_machine_with_multiple_dates()
    {
        $parameters = new ForecastWithMultipleDatesStub;

        $client = mock(Client::class);
        $factory = mock(RequestFactory::class);
        $requests = $parameters->expectedRequests();
        $responses = [];
        $expected = [];

        $factory->shouldReceive('create')
            ->with($parameters)
            ->andReturn($requests);

        $client->shouldReceive('gzip')
            ->withNoArgs()
            ->andReturnSelf();

        array_walk($requests, function (Request $request) use (&$responses, &$expected) {
            $response = mock(ResponseInterface::class);
            $responseBody = ['status' => "success-{$request->id()}"];
            $responseHeaders = ['X-Response-Time' => [0.123]];

            $response->shouldReceive('getBody')
                ->withNoArgs()
                ->andReturn(json_encode($responseBody));

            $response->shouldReceive('getHeaders')
                ->withNoArgs()
                ->andReturn($responseHeaders);

            $responses[$request->id()] = $response;
            $expected[$request->id()] = new Data($responseBody, $responseHeaders);
        });

        $client->shouldReceive('concurrentRequests')
            ->with($requests)
            ->andReturn($responses);

        $this->assertEquals($expected, (new Api($client, $factory))->request($parameters));
    }
}
