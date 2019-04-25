<?php
/**
 * Created by PhpStorm.
 * User: administrato
 * Date: 2019/4/25
 * Time: 11:08
 */

namespace GoSwoole\GuzzleSaber;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\RedirectMiddleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Swlib\SaberGM;

class SaberClient implements ClientInterface
{

    private $config;

    public function __construct(array $config = [])
    {
        // Convert the base_uri to a UriInterface
        if (isset($config['base_uri'])) {
            $config['base_uri'] = Psr7\uri_for($config['base_uri']);
        }

        $this->configureDefaults($config);
    }
    /**
     * Configures the default options for a client.
     *
     * @param array $config
     */
    private function configureDefaults(array $config)
    {
        $defaults = [
            'allow_redirects' => RedirectMiddleware::$defaultSettings,
            'http_errors'     => true,
            'verify'          => false
        ];
        $this->config = $config + $defaults;
    }
    /**
     * Send an HTTP request.
     *
     * @param RequestInterface $request Request to send
     * @param array $options Request options to apply to the given
     *                                  request and to the transfer.
     *
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function send(RequestInterface $request, array $options = [])
    {
        $config = ChangeOptions::change($options);
        $config['uri'] = $request->getUri()->getPath();
        $config['uri'] = $request->getBody();
        return SaberGM::psr($options)->withRequestTarget($request->getRequestTarget())
            ->withBody($request->getBody())->withMethod($request->getMethod())->withUri($request->getUri())
            ->withProtocolVersion($request->getProtocolVersion())
            ->withHeaders($request->getHeaders())->exec()->recv();;
    }

    /**
     * Asynchronously send an HTTP request.
     *
     * @param RequestInterface $request Request to send
     * @param array $options Request options to apply to the given
     *                                  request and to the transfer.
     *
     * @return PromiseInterface
     */
    public function sendAsync(RequestInterface $request, array $options = [])
    {
        // TODO: Implement sendAsync() method.
    }

    /**
     * Create and send an HTTP request.
     *
     * Use an absolute path to override the base path of the client, or a
     * relative path to append to the base path of the client. The URL can
     * contain the query string as well.
     *
     * @param string $method HTTP method.
     * @param string|UriInterface $uri URI object or string.
     * @param array $options Request options to apply.
     *
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function request($method, $uri, array $options = [])
    {
        if(is_string($uri)){
            $options['uri'] = $uri;
            return SaberGM::psr($options)->withMethod($method)
                ->exec()->recv();
        }else{
            return SaberGM::psr($options)->withMethod($method)->withUri($uri)
                ->exec()->recv();
        }

    }

    /**
     * Create and send an asynchronous HTTP request.
     *
     * Use an absolute path to override the base path of the client, or a
     * relative path to append to the base path of the client. The URL can
     * contain the query string as well. Use an array to provide a URL
     * template and additional variables to use in the URL template expansion.
     *
     * @param string $method HTTP method
     * @param string|UriInterface $uri URI object or string.
     * @param array $options Request options to apply.
     *
     * @return PromiseInterface
     */
    public function requestAsync($method, $uri, array $options = [])
    {
        // TODO: Implement requestAsync() method.
    }

    /**
     * Get a client configuration option.
     *
     * These options include default request options of the client, a "handler"
     * (if utilized by the concrete client), and a "base_uri" if utilized by
     * the concrete client.
     *
     * @param string|null $option The config option to retrieve.
     *
     * @return mixed
     */
    public function getConfig($option = null)
    {
        return $option === null
            ? $this->config
            : (isset($this->config[$option]) ? $this->config[$option] : null);
    }


}