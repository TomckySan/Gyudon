<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/Authorization.php';
require_once __DIR__.'/View.php';

use Symfony\Component\Yaml\Yaml;

class Commands
{
    public static function execute($cmd)
    {
        $self = new ReflectionClass('Commands');
        $methods = $self->getMethods(ReflectionMethod::IS_PRIVATE);
        foreach ($methods as $m) {
            if ($m->name === $cmd) {
                self::$cmd();
                return;
            }
        }
        echo "'$cmd' is invalid...";
    }

    private static function auth()
    {
        $authorization = new Authorization();
        $authorization->authorize();
    }

    private static function timeline()
    {
        $obj = Yaml::parse(file_get_contents(__DIR__.'/../config/secret.yml'));
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', "https://{$obj['host']}/api/v1/timelines/home", [
            'headers' => [
                'Authorization' => "Bearer {$obj['access_token']}",
            ],
        ]);
        $timeLines = json_decode($res->getBody());
        foreach($timeLines as $tl) {
            $userName = $tl->account->username;
            $displayName = $tl->account->display_name;
            $content = $tl->content;
            $pattern = '/<("[^"]*"|\'[^\']*\'|[^\'">])*>/';
            $msg = View::color((empty($displayName) ? $userName : $displayName),'33').PHP_EOL . View::color(preg_replace($pattern, '', $content));
            View::puts($msg);
        }
    }
}
