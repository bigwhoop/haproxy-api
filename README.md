## Installation

Use composer.

    {
        ...
        "require": {
            ...
            "bigwhoop/haproxy-api" : "dev-master",
            ...
        }
        ...
    }

## Configuration
    
    <?php
    use \BigWhoop\HAProxyAPI;
    
    $api = new HAProxyAPI\API('http://example.com');
    $api = new HAProxyAPI\API('http://example.com:22002');
    $api = new HAProxyAPI\API('http://example.com:22002/status');
    $api = new HAProxyAPI\API('http://10.0.0.1/monitor');
    $api = new HAProxyAPI\API('http://example.com', 'username');
    $api = new HAProxyAPI\API('http://example.com', 'username', 'password');
    ...

## Commands

### Enable server
    
    try {
        $status = $api->execute('enableServer', array('backend' => 'webapp', 'server' => 'web-01'));
        if ($status) {
            // Action had an effect
        } else {
            // Action had no effect
        }
    }  catch (HAProxyAPI\Client\Exception $e) {
       // Server error
    } catch (HAProxyAPI\Command\Exception $e) {
       // Data error
    }


### Disable server

Same as above, just use `disableServer` as the first argument to `$api->execute()`.


### Stats

    try {
        $stats = $api->execute('stats');
    } catch (HAProxyAPI\Client\Exception $e) {
        // Server error
    } catch (HAProxyAPI\Command\Exception $e) {
        // Data error
    }

An array with plain objects of with the following properties is returned:

     pxname, svname, qcur, qmax, scur, smax, slim, stot, bin, bout, dreq, dresp, ereq, econ, eresp,
     wretr, wredis, status, weight, act, bck, chkfail, chkdown, lastchg, downtime, qlimit, pid, iid,
     sid, throttle, lbtot, tracked, type, rate, ratelim, ratemax, checkstatus, checkcode, checkduration,
     hrspxx, hrspother, hanafail, reqrate, reqratemax, reqtot, cliabrt, srvabrt

I'm pretty sure you'll find out what the mean. They're coming from HAProxy in this format.

You can also get a stats array sorted by backend.

    try {
        $stats = $api->execute('stats', array('sorting' => HAProxyAPI\Command\StatsCommand::SORTING_BACKEND));
    } catch (HAProxyAPI\Client\Exception $e) {
        // Server error
    } catch (HAProxyAPI\Command\Exception $e) {
        // Data error
    }

The array is no in the following format:

    [
        'backend1' => [
            'server1' => { ... },
            'server2' => { ... },
        ],
        'backend2' => [
            'server3' => { ... },
            ...
        ],
        ...
    ]


## License

See LICENSE file.