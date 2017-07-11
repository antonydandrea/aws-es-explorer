<?php
namespace ESExplorer;

class AWSClient
{
    private $awsClient;
    
    public function __construct($location, $accessToken, $secretKey, $region = 'eu-west-1')
    {
        $hosts = [
            [
                'host' => $location,
                'port' => 443,
                'scheme' => 'https',
            ]
        ];
        $client = \Elasticsearch\ClientBuilder::create()->setHosts($hosts);
        if (!empty($accessToken) && !empty($secretKey)) {
            $client->setHandler($this->getHandler($accessToken, $secretKey, $region));
        }
        $this->awsClient = $client->build();
    }
    
    public function getClient()
    {
        return $this->awsClient;
    }
    
    private function getHandler($key, $secret, $region)
    {
        $psr7Handler = \Aws\default_http_handler();
        $signer = new \Aws\Signature\SignatureV4('es', $region);
        $credentials = new \Aws\Credentials\Credentials($key, $secret);
        return $handler = function (array $request) use ($psr7Handler, $signer, $credentials) {
            // Amazon ES listens on standard ports (443 for HTTPS, 80 for HTTP).
            $request['headers']['host'][0] = parse_url($request['headers']['host'][0])['host'];

            // Create a PSR-7 request from the array passed to the handler
            $psr7Request = new \GuzzleHttp\Psr7\Request(
                $request['http_method'],
                (new \GuzzleHttp\Psr7\Uri($request['uri']))
                    ->withScheme($request['scheme'])
                    ->withHost($request['headers']['host'][0]),
                $request['headers'],
                $request['body']
            );

            // Sign the PSR-7 request with credentials from the environment
            $signedRequest = $signer->signRequest(
                $psr7Request,
                $credentials
            );

            // Send the signed request to Amazon ES
            /** @var \Psr\Http\Message\ResponseInterface $response */
            $response = $psr7Handler($signedRequest)->wait();

            // Convert the PSR-7 response to a RingPHP response
            return new \GuzzleHttp\Ring\Future\CompletedFutureArray([
                'status' => $response->getStatusCode(),
                'headers' => $response->getHeaders(),
                'body' => $response->getBody()->detach(),
                'transfer_stats' => ['total_time' => 0],
                'effective_url' => (string) $psr7Request->getUri(),
            ]);
        };
    }
}