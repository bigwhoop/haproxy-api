<?php
/**
 * This file is part of HAProxyAPI.
 *
 * (c) Philippe Gerber <philippe@bigwhoop.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace BigWhoop\HAProxyAPI\Client;

class Response
{
    /**
     * @var string
     */
    private $body = '';
    
    /**
     * @var array
     */
    private $headers = array();
    

    /**
     * @param $body
     * @param array $headers
     */
    public function __construct($body, array $headers = array())
    {
        $this->body    = (string)$body;
        $this->headers = $headers;
    }


    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }


    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getHeader($key, $default = null)
    {
        if (array_key_exists($key, $this->headers)) {
            return $this->headers[$key];
        }
        
        return $default;
    }
}
