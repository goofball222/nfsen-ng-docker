<?php
/**
 * config file for nfsen-ng.
 *
 * remarks:
 * * database name = datasource class name (case-sensitive)
 * * log priority should be one of the predefined core constants prefixed with LOG_
 */

$nfsen_config = [
    'general' => [
        'ports' => [
            22, 23, 53, 80, 161, 443, 853, 32400,
        ],
        'sources' => [
            'router',  // DO NOT ALTER THIS LINE, it will be modified by entrypoint.sh
        ],
        'filters' => [
            'proto udp',
            'proto tcp',
        ],
        'formats' => [
            'external_interfaces' => '%ts %td %pr %in %out %sa %sp %da %dp %ipkt %ibyt %opkt %obyt %flg',
        ],
        'db' => 'RRD',
        'processor' => 'NfDump',
    ],
    'frontend' => [
        'reload_interval' => 60,
        'defaults' => [
            'view' => 'graphs', // graphs, flows, statistics
            'graphs' => [
                'display' => 'sources', // sources, protocols, ports
                'datatype' => 'flows', // flows, packets, traffic
                'protocols' => ['any'], // any, tcp, udp, icmp, others (multiple possible if display=protocols)
            ],
            'flows' => [
                'limit' => 50,
            ],
            'statistics' => [
                'order_by' => 'bytes',
            ],
            'table'=> [
                'hidden_fields' => [
                    'flg', 'fwd', 'in', 'out', 'sas', 'das'
                ],
            ]
        ],
    ],
    'nfdump' => [
        'binary' => '/usr/local/bin/nfdump',
        'profiles-data' => '/var/nfdump/profiles-data',
        'profile' => 'live',
        'max-processes' => 1, // maximum number of concurrently running nfdump processes
    ],
    'db' => [
        'Akumuli' => [
            // 'host' => 'localhost',
            // 'port' => 8282,
        ],
        'RRD' => [],
    ],
    'log' => [
        'priority' => \LOG_INFO, // LOG_DEBUG is very talkative!
    ],
];
