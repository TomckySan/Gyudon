<?php

require_once 'vendor/autoload.php';
require_once __DIR__.'/Authorization.php';

use Symfony\Component\Yaml\Yaml;

class Commands
{
    private $arg;

    public function __construct($arg)
    {
        $this->arg = $arg;
    }

    public function execute()
    {
        $func = $this->arg;
        $this->$func();
    }

    private function auth()
    {
        $authorization = new Authorization();
        $authorization->authorize();
    }

    private function timeline()
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
            $msg = $this->color((empty($displayName) ? $userName : $displayName),'33').PHP_EOL . $this->color(preg_replace($pattern, '', $content));
            $this->puts($msg);
        }
    }

    private function puts($msg)
    {
        echo $msg.PHP_EOL.PHP_EOL;
    }

    private function color($txt, $code = '39')
    {
        return "\033[0;{$code}m".$txt."\033[0m";
    }
}
