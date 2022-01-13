<?php

namespace Spatie\Health\Checks;

use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\PingCheck;
use Spatie\Health\Checks\Checks\RedisCheck;

class CheckingItem
{
    protected static $items = [
        'cache'         => CacheCheck::class,
        'DB_connection' => DatabaseCheck::class,
        'debug'         => DebugModeCheck::class,
        'env'           => EnvironmentCheck::class,
        'ping'          => PingCheck::class,
        'redis'         => RedisCheck::class,
    ];

    public static function getChecks(): array
    {
        $configCheck = config('health.checks');

        $checkItems = [];

        foreach ($configCheck as $item => $value) {
            if ($value) {
                $tmpItem = self::$items[$item]::new();

                if (gettype($value) == 'string') {
                    switch ($item) {
                        case 'cache':
                            $tmpItem = $tmpItem->driver($value);
                            break;
                        case 'env':
                            $tmpItem = $tmpItem->expectEnvironment($value);
                            break;
                        case 'ping':
                            $tmpItem = $tmpItem->url($value);
                            break;
                        default:
                            $tmpItem = $tmpItem->connectionName($value);
                    }
                }
                array_push($checkItems, $tmpItem);
            }
        }

        return $checkItems; 
    }

}
