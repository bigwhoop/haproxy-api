<?php
/**
 * This file is part of Hynage.
 *
 * (c) Philippe Gerber <philippe@bigwhoop.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace BigWhoop\HAProxyAPI\Command;

use BigWhoop\HAProxyAPI\Client\HTTPClient;

abstract class ActionCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected $action = '';
    
    
    /**
     * @param \BigWhoop\HAProxyAPI\Client\HTTPClient $client
     * @return bool     true if the action made any changes, otherwise false.
     * @throws \Exception
     */
    public function execute(HTTPClient $client)
    {
        $server  = $this->getOption('server');
        $action  = $this->action;
        $backend = $this->getOption('backend');
        
        // The order of the params is important. For whatever silly reason.
        $response = $client->request(
            '/',
            array(
                's'      => $server,
                'action' => $action,
                'b'      => $backend,
            ),
            'post'
        );
        
        $statusCode  = $response->getHeader('http_code', 500);
        $redirectUrl = $response->getHeader('redirect_url', false);
        
        if ($statusCode === 303) {
            $redirectUrlPath = parse_url($redirectUrl, PHP_URL_PATH);
            
            if ($redirectUrlPath == '/;st=DONE') {
                return true;
            } elseif ($redirectUrlPath == '/;st=NONE') {
                return false;
            }
        }
        
        throw new \Exception("HAProxy '$action' request for '$backend/$server' failed.");
    }
}
