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

class HTTPClient
{
    /**
     * @var string
     */
    private $host = '';

    /**
     * @var string|null
     */
    private $user = null;

    /**
     * @var string|null
     */
    private $password = null;


    /**
     * @param string $host
     * @return HTTPClient
     */
    public function setHost($host)
    {
        $this->host = rtrim($host, '/');
        return $this;
    }


    /**
     * @param string $user
     * @param string $password
     * @return HTTPClient
     */
    public function setBasicAuth($user, $password)
    {
        $this->user     = (string)$user;
        $this->password = (string)$password;
        return $this;
    }
    
    
    /**
     * @param string $path
     * @param array $params
     * @param string $method
     * @return Response
     * @throws Exception
     */
    public function request($path, array $params = array(), $method = 'get')
    {
        $url = $this->host . $path;
        
        if ($method == 'get') {
            foreach ($params as $key => $value) {
                if (is_numeric($key)) {
                    $url .= ";$value";
                } else {
                    $url .= ";$key=$value";
                }
            }
        }
        
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->user:$this->password");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        if ($method == 'post') {
            $postData = http_build_query($params, null, '&');
            curl_setopt($ch, CURLOPT_POST, count($params));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }
        
        $responseBody = curl_exec($ch);
        
        if (curl_errno($ch) !== 0) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("Request to HAProxy failed: $error.");
        }
        
        $info = curl_getinfo($ch);
        curl_close($ch);
        
        return new Response($responseBody, $info);
    }
}
