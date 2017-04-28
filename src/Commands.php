<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/Authorization.php';
require_once __DIR__.'/View.php';

use Symfony\Component\Yaml\Yaml;

class Commands
{
    public static function execute($cmd, $options)
    {
        $self = new ReflectionClass('Commands');
        $methods = $self->getMethods(ReflectionMethod::IS_PRIVATE);
        foreach ($methods as $m) {
            if ($m->name === $cmd) {
                self::$cmd($options);
                return;
            }
        }
        echo "'$cmd' is invalid...";
    }

    private static function auth($options)
    {
        $authorization = new Authorization();
        $authorization->authorize();
    }

    private static function timeline($options)
    {
        $obj = Yaml::parse(file_get_contents(__DIR__.'/../config/secret.yml'));
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', "https://{$obj['host']}/api/v1/timelines/home", [
            'headers' => [
                'Authorization' => "Bearer {$obj['access_token']}",
            ],
            'query' => [
                'limit' => count($options) ? $options[0] : 20,
            ],
        ]);
        View::timeline(json_decode($res->getBody()));
    }

    private static function toot($options)
    {
        // TODO
    }
}
