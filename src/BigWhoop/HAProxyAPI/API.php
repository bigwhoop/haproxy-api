<?php
/**
 * This file is part of Hynage.
 *
 * (c) Philippe Gerber <philippe@bigwhoop.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace BigWhoop\HAProxyAPI;

use BigWhoop\HAProxyAPI\Client\HTTPClient;

class API
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
     * @var null|HTTPClient
     */
    protected $client = null;
    

    /**
     * @param string $host
     * @param string|null $user
     * @param string|null $password
     */
    public function __construct($host, $user = null, $password = null)
    {
        $this->host     = (string)$host;
        $this->user     = is_null($user) ? null : (string)$user;
        $this->password = is_null($password) ? null : (string)$password;
    }


    /**
     * @param Client\HTTPClient $client
     * @return API
     */
    public function setHTTPClient(HTTPClient $client)
    {
        $this->client = $client;
        return $this;
    }


    /**
     * @return Client\HTTPClient
     */
    public function getClient()
    {
        if (!$this->client) {
            $this->client = new HTTPClient();
        }
        
        return $this->client;
    }


    /**
     * @param $commandName
     * @param array $options
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function execute($commandName, array $options = array())
    {
        $client = $this->getClient();
        $client->setHost($this->host);
        
        if (!empty($this->user) && !empty($this->password)) {
            $client->setBasicAuth($this->user, $this->password);
        }
        
        $commandClass = __NAMESPACE__ . '\\Command\\' . ucfirst($commandName) . 'Command';
        $command = new $commandClass;
        
        if (!$command instanceof Command\Executable) {
            throw new \InvalidArgumentException("Class $commandClass must implement the Command\\Exectuable interface.");
        }
        
        $command->setOptions($options);
        
        return $command->execute($client);
    }
}
