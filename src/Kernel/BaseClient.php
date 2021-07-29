<?php

/*
 *
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace JosClient\Kernel;

use JosClient\Kernel\Traits\HasHttpRequests;
use JosClient\Kernel\Traits\SignCorrelation;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class BaseClient.
 *
 */
class BaseClient
{
    use SignCorrelation, HasHttpRequests {
        request as performRequest;
    }

    /**
     * @var \JosClient\Kernel\ServiceContainer
     */
    protected $app;
    /**
     * @var
     */
    protected $baseUri;

    /**
     * BaseClient constructor.
     * @param \JosClient\Kernel\ServiceContainer      $app
     */
    public function __construct(ServiceContainer $app)
    {
        $this->app = $app;
    }

    /**
     * @param string $url
     * @param array $query
     * @return array|Support\Collection|object|ResponseInterface|string
     * @throws Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function httpGet(array $query = [])
    {
        $request = $this->request('', 'GET', ['query' => $query]);
        if (is_array($request)) {
            foreach ($request as $propKey => $propValue) {
                $request = $propValue;
            }
        }
        return  $request;
    }


    /**
     * @param string $url
     * @param string $method
     * @param array $options
     * @param bool $returnRaw
     * @return array|Support\Collection|object|ResponseInterface|string
     * @throws Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request(string $url, string $method = 'GET', array $options = [], $returnRaw = false)
    {
        if (empty($this->middlewares)) {
            $this->registerHttpMiddlewares();
        }
        $options['query'] = $this->generateSign($options['query']);
        $response = $this->performRequest($url, $method, $options);
        return $returnRaw ? $response : $this->castResponseToType($response, $this->app->config->get('response_type'));
    }



    /**
     * Register Guzzle middlewares.
     */
    protected function registerHttpMiddlewares()
    {
        // retry
        $this->pushMiddleware($this->retryMiddleware(), 'retry');
        // log
        $this->pushMiddleware($this->logMiddleware(), 'log');
    }


    /**
     * Log the request.
     *
     * @return \Closure
     */
    protected function logMiddleware()
    {
        $formatter = new MessageFormatter($this->app['config']['http.log_template'] ?? MessageFormatter::DEBUG);

        return Middleware::log($this->app['logger'], $formatter);
    }

    /**
     * Return retry middleware.
     *
     * @return \Closure
     */
    protected function retryMiddleware()
    {
        return Middleware::retry(function (
            $retries,
            RequestInterface $request,
            ResponseInterface $response = null
        ) {
            // Limit the number of retries to 2
            if ($retries < $this->app->config->get('http.max_retries', 1) && $response && $body = $response->getBody()) {
                // Retry on server errors
                $this->app['logger']->debug($body);
                return true;
                // $response = json_decode($body, true);
                // if (!empty($response['resultCode']) && $response['resultCode'] == 2007) {
                //     // $this->accessToken->refresh();
                //     $this->app['logger']->debug('Retrying with refreshed access token.');

                //     return true;
                // }
            }

            return false;
        }, function () {
            return abs($this->app->config->get('http.retry_delay', 500));
        });
    }
}
