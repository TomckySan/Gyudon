<?php

class View
{
    public static function timeline($lines)
    {
        foreach ($lines as $l) {
            self::extractDisplayData($l);
        }
    }

    public static function status($l)
    {
        self::extractDisplayData($l);
    }

    private static function extractDisplayData($line)
    {
        $data = [
            'displayName' => self::getDisplayName($line),
            'acct' => self::getTargetObject($line)->account->acct,
            'createdAt' => self::getTargetObject($line)->created_at,
            'rebloggedBy' => is_null($line->reblog) ? null : $line->account->acct,
            'reblogsCount' => self::getTargetObject($line)->reblogs_count,
            'favouritesCount' => self::getTargetObject($line)->favourites_count,
            'content' => self::rmHtmlTags(self::getTargetObject($line)->content),
        ];
        self::outputLine($data);
    }

    private static function getDisplayName($line)
    {
        if(is_null($line->reblog)) {
            return $line->account->display_name ?: $line->account->username;
        } else {
            return $line->reblog->account->display_name ?: $line->reblog->account->username;
        }
    }

    private static function getTargetObject($line)
    {
        return $line->reblog ?: $line;
    }

    private static function rmHtmlTags($txt)
    {
        return preg_replace('/<("[^"]*"|\'[^\']*\'|[^\'">])*>/', '', $txt);
    }

    private static function outputLine($data)
    {
        $result = self::color("{$data['displayName']} ", '33');
        $result .= "(@{$data['acct']}) ";
        $result .= "[{$data['createdAt']}] ";
        if (!is_null($data['rebloggedBy'])) $result .= "(reblogged by @{$data['rebloggedBy']}) ";
        $result .= self::color("{$data['reblogsCount']}Boosts ", '32');
        $result .= self::color("{$data['favouritesCount']}Favs ", '31');
        $result .= PHP_EOL;
        // displayName (@acct) [createAt] (reblogged by @acct) reblogsCount favouritesCount

        $result .= "{$data['content']}";
        $result .= PHP_EOL.PHP_EOL;
        // content

        echo $result;
    }

    private static function color($txt, $code)
    {
        return "\033[0;{$code}m".$txt."\033[0m";
    }
}
