<?php
/**
 * This file is part of HAProxyAPI.
 *
 * (c) Philippe Gerber <philippe@bigwhoop.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace BigWhoop\HAProxyAPI\Command;

abstract class AbstractCommand implements Executable
{
    /**
     * @var array
     */
    protected $options = array();


    /**
     * @param array $params
     * @return AbstractCommand
     */
    public function setOptions(array $params)
    {
        $this->options = $params;
        return $this;
    }


    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getOption($key, $default = null)
    {
        if (array_key_exists($key, $this->options)) {
            return $this->options[$key];
        }
        
        return $default;
    }
}
