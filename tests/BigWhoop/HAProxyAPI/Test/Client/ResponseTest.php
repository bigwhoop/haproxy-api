<?php
/**
 * This file is part of HAProxyAPI.
 *
 * (c) Philippe Gerber <philippe@bigwhoop.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace BigWhoop\HAProxyAPI\Test\Client;

use BigWhoop\HAProxyAPI\Client\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testBody()
    {
        $r = new Response(false);
        $this->assertEquals('', $r->getBody());
        
        $r = new Response('test');
        $this->assertEquals('test', $r->getBody());
    }
    
    
    public function testHeaders()
    {
        $r = new Response(
            '',
            array(
                'one' => 'string',
                'two' => 5,
                'three' => array('foo' => 'bar')
            )
        );
        
        $this->assertSame('string', $r->getHeader('one'));
        $this->assertSame(5, $r->getHeader('two'));
        $this->assertSame(array('foo' => 'bar'), $r->getHeader('three'));
    }
    
    
    public function testDefaultHeaders()
    {
        $r = new Response('');
        $this->assertSame(null, $r->getHeader('notset'));
        $this->assertSame('default', $r->getHeader('notset', 'default'));
    }
}
