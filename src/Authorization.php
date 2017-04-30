<?php

require_once __DIR__.'/../vendor/autoload.php';

class Authorization
{
    const APP_NAME = 'Gyudon';
    const WEBSITE = 'https://github.com/TomckySan/Gyudon';
    const SCOPES = 'read write';

    private $clientId;
    private $clientSecret;

    public function authorize()
    {
        $host = $this->getHost();
        $this->registerApp($host);
        $user = $this->getUser();
        $pass = $this->getPass();

        $guzzleClient = new \GuzzleHttp\Client();
        $res = $guzzleClient->request('POST', "https://{$host}/oauth/token", [
            'form_params' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'password',
                'username' => $user,
                'password' => $pass,
                'scope' => self::SCOPES,
            ],
        ]);
        $obj = json_decode($res->getBody());
        $accessToken = $obj->access_token;
        $path = __DIR__.'/../config/secret.yml';
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        $text = "host: $host".PHP_EOL
            ."access_token: $accessToken".PHP_EOL
        ;
        file_put_contents($path, $text);
    }

    private function registerApp($host)
    {
        $guzzleClient = new \GuzzleHttp\Client();
        $res = $guzzleClient->request('POST', "https://{$host}/api/v1/apps", [
            'form_params' => [
                'client_name' => self::APP_NAME,
                'redirect_uris' => 'urn:ietf:wg:oauth:2.0:oob',
                'scopes' => self::SCOPES,
                'website' => self::WEBSITE,
            ],
        ]);
        $obj = json_decode($res->getBody());
        $this->clientId = $obj->client_id;
        $this->clientSecret = $obj->client_secret;
    }

    private function getHost()
    {
        echo 'Input host > ';
        return trim(fgets(STDIN));
    }

    private function getUser()
    {
        echo 'Input login email > ';
        return trim(fgets(STDIN));
    }

    private function getPass()
    {
        echo 'Input login password > ';
        if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
            $pass = trim(fgets(STDIN));
        } else {
            system('stty -echo');
            $pass = trim(fgets(STDIN));
            system('stty echo');
        }
        return $pass;
    }
}
