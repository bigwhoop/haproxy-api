<?php
/**
 * This file is part of HAProxyAPI.
 *
 * (c) Philippe Gerber <philippe@bigwhoop.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace BigWhoop\HAProxyAPI\Test;

use BigWhoop\HAProxyAPI\API;

class APITest extends \PHPUnit_Framework_TestCase
{
    public function testConstruction()
    {
        new API('http://example.com');
        new API('http://example.com', 'user');
        new API('http://example.com', 'user', 'pass');
    }
    
    
    public function testClient()
    {
        $api = new API('http://example.com');
        $client = $api->getClient();
        
        $this->assertInstanceOf('BigWhoop\HAProxyAPI\Client\HTTPClient', $client);
    }
}
