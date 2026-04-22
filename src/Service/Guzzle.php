<?php
/**
 * @created 11/27/2025 14:30
 */
namespace Monta\CheckoutApiWrapper\Service;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class Guzzle
{
    /** Little wrapper for calling any URI
     *
     * @param string $route - Path after the domain
     * @param string $baseUri - base URI
     * @param string $httpMethod - POST or GET
     * @param array $parameters - Request body for POST or simple query parameters for GET
     * @param array $headers - Additional headers e.g. Bearer token
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public static function call(
        string $route,
        string $baseUri,
        string $httpMethod = "POST",
        array $parameters = [],
        array $headers = [],
    ): ResponseInterface
    {
        // Merge clientData with defaults
        $clientData = [
            'verify' => false,
            'base_uri' => $baseUri,
            'timeout' => 10.0,
            'headers' => $headers,
        ];
        $client = new Client($clientData);
        switch ($httpMethod) {
            case "POST":
                $response = $client->post($route, [
                    'json' => $parameters,
                ]);
                break;
            case "GET":
                if ($parameters) {
                    $route .= "?" . http_build_query($parameters);
                }
                $response = $client->get($route);
                break;
            default:
                throw new \Exception("Unsupported HTTP method: " . $httpMethod);
        }
        return $response;
    }
}