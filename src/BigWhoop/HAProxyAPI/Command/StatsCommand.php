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

class StatsCommand extends AbstractCommand
{
    const OPT_SORTING     = 'sorting';
    const SORTING_NONE    = 'none';
    const SORTING_BACKEND = 'backend';
    
    
    /**
     * @param \BigWhoop\HAProxyAPI\Client\HTTPClient $client
     * @return array
     * @throws Exception
     */
    public function execute(HTTPClient $client)
    {
        $csv = $client->request('/', array('csv'))->getBody();
        
        $rows = array_map(
            function($val) {
                return explode(',', $val);
            },
            explode("\n", trim($csv))
        );
        
        $fields = array_map(
            function($val) {
                return preg_replace('/[^a-z]+/', '', $val);
            },
            array_shift($rows)
        );
        
        $data = array();
        for ($rowIdx = 0, $numRows = count($rows); $rowIdx < $numRows; $rowIdx++) {
            for ($colIdx = 0, $numCols = count($fields); $colIdx < $numCols; $colIdx++) {
                if (!array_key_exists($rowIdx, $data)) {
                    $data[$rowIdx] = new \stdClass();
                }
                
                $field = $fields[$colIdx];
                
                if (!empty($field)) {
                    $data[$rowIdx]->$field = $rows[$rowIdx][$colIdx];
                }
            }
        }
        
        switch ($this->getOption(self::OPT_SORTING, self::SORTING_NONE))
        {
            case self::SORTING_NONE:
                return $data;
            
            case self::SORTING_BACKEND:
                $sortedData = array();
                
                foreach ($data as $server) {
                    if (!array_key_exists($server->pxname, $sortedData)) {
                        $sortedData[$server->pxname] = array();
                    }
                    
                    $sortedData[$server->pxname][] = $server;
                }
                
                return $sortedData;
            
            default:
                throw new Exception("Invalid sorting option provided for stats command.");
        }
    }
}
