<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/Command/Authorization.php';
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
        if (empty($options)) {
            echo "Please input text. (For example, 'gyudon toot \"I want to eat Gyudon.\"')";
            return;
        }

        $obj = Yaml::parse(file_get_contents(__DIR__.'/../config/secret.yml'));
        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', "https://{$obj['host']}/api/v1/statuses", [
            'headers' => [
                'Authorization' => "Bearer {$obj['access_token']}",
            ],
            'form_params' => [
                'status' => $options[0],
            ],
        ]);
        View::status(json_decode($res->getBody()));
    }

    private static function bt($options)
    {
        if (empty($options)) {
            echo "Please input id. (For example, 'gyudon bt 12345')";
            return;
        }

        $obj = Yaml::parse(file_get_contents(__DIR__.'/../config/secret.yml'));
        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', "https://{$obj['host']}/api/v1/statuses/{$options[0]}/reblog", [
            'headers' => [
                'Authorization' => "Bearer {$obj['access_token']}",
            ],
        ]);
        View::status(json_decode($res->getBody()));
    }

    private static function fav($options)
    {
        if (empty($options)) {
            echo "Please input id. (For example, 'gyudon bt 12345')";
            return;
        }

        $obj = Yaml::parse(file_get_contents(__DIR__.'/../config/secret.yml'));
        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', "https://{$obj['host']}/api/v1/statuses/{$options[0]}/favourite", [
            'headers' => [
                'Authorization' => "Bearer {$obj['access_token']}",
            ],
        ]);
        View::status(json_decode($res->getBody()));
    }
}
