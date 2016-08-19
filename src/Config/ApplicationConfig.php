<?php
/**
 * Config
 *
 * Application config class
 *
 * @package      Mooti
 * @subpackage   Framework     
 * @author       Ken Lalobo <ken@mooti.io>
 */

namespace Mooti\Framework\Config;

use Mooti\Framework\Config\AbstractConfig;

class ApplicationConfig extends AbstractConfig
{
    const FILENAME = 'mooti.json';

    protected $rules = [
        'name' => [
            'required' => true,
            'type'     => 'string'
        ],
        'server' => [
            'required' => false,
            'type'     => 'object',
            'properties' => [
                'type' => [
                    'required' => true,
                    'type'     => 'string'
                ],
                'web_root' => [
                    'required' => true,
                    'type'     => 'string'
                ],
                'index_file' => [
                    'required' => true,
                    'type'     => 'string'
                ]
            ]
        ],
        'scripts' => [
            'required' => false,
            'type'     => 'array',
            'items'    => [
                '*' => [
                    'type' => 'string'
                ]                
            ]
        ]
    ];

    public function __construct()
    {
        $this->filename = self::FILENAME;
    }

    public function init()
    {
        $this->configData = [
            'name' => 'mooti.example'
        ];
    }
}
