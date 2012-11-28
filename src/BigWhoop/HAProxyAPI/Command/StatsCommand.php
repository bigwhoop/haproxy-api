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

use BigWhoop\HAProxyAPI\Client\HTTPClient;

class StatsCommand extends AbstractCommand
{
    const GROUPING_NONE    = 'none';
    const GROUPING_BACKEND = 'backend';
    
    
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
        
        switch ($this->getOption('grouping', self::GROUPING_NONE))
        {
            case self::GROUPING_NONE:
                return $data;
            
            case self::GROUPING_BACKEND:
                $sortedData = array();
                
                foreach ($data as $server) {
                    if (!array_key_exists($server->pxname, $sortedData)) {
                        $sortedData[$server->pxname] = array();
                    }
                    
                    $sortedData[$server->pxname][] = $server;
                }
                
                foreach ($sortedData as $backend => $servers) {
                    usort($servers, function($a, $b) {
                        return strnatcasecmp($a->svname, $b->svname);
                    });
                    
                    $sortedData[$backend] = $servers;
                }
                
                return $sortedData;
            
            default:
                throw new Exception("Invalid sorting option provided for stats command.");
        }
    }
}
