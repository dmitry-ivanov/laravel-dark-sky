<?php

namespace DmitryIvanov\DarkSkyApi\Http\Client;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client as Guzzle;
use DmitryIvanov\DarkSkyApi\Contracts\Http\Client;
use DmitryIvanov\DarkSkyApi\Contracts\Http\Request;

class GuzzleClient implements Client
{
    /**
     * The Guzzle client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Create a new instance of the client.
     *
     * @param  \GuzzleHttp\Client|null  $client
     * @return void
     */
    public function __construct(Guzzle $client = null)
    {
        $this->client = isset($client) ? $client : new Guzzle;
    }

    /**
     * Force API requests to accept `gzip` encoding and automatically decode the response.
     *
     * Implemented for all requests in the `options()` method.
     *
     * @see http://docs.guzzlephp.org/en/stable/request-options.html#decode-content
     *
     * @return \DmitryIvanov\DarkSkyApi\Contracts\Http\Client
     */
    public function gzip()
    {
        return $this;
    }

    /**
     * Make an API request by the given request object.
     *
     * @param  \DmitryIvanov\DarkSkyApi\Contracts\Http\Request  $request
     * @return array
     */
    public function request(Request $request)
    {
        $response = $this->client->get($request->url(), $this->options($request));

        return \GuzzleHttp\json_decode($response->getBody(), true);
    }

    /**
     * Make concurrent API requests by the given array of the request objects.
     *
     * @param  array  $requests
     * @return array
     *
     * @throws \Exception on error
     * @throws \Throwable on error in PHP >=7
     */
    public function concurrentRequests(array $requests)
    {
        $responses = \GuzzleHttp\Promise\unwrap($this->composePromises($requests));

        return array_map(function (Response $response) {
            return \GuzzleHttp\json_decode($response->getBody(), true);
        }, $responses);
    }

    /**
     * Compose the promises for the concurrent requests.
     *
     * @param  array  $requests
     * @return array
     */
    protected function composePromises(array $requests)
    {
        $promises = [];

        array_walk($requests, function (Request $request) use (&$promises) {
            $promises[$request->id()] = $this->client->getAsync($request->url(), $this->options($request));
        });

        return $promises;
    }

    /**
     * Compose the request options.
     *
     * @param  \DmitryIvanov\DarkSkyApi\Contracts\Http\Request  $request
     * @return array
     */
    protected function options(Request $request)
    {
        return [
            'decode_content' => 'gzip',
            'query' => $request->query(),
        ];
    }
}
