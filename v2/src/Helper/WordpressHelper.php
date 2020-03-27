<?php


namespace Biltorvet\Helper;

/**
 * This helper is mainly made for making tedious wordpress related methods shorter and prettier when used.
 *
 * Class WordpressHelper
 *
 * @package Biltorvet\Helper
 */
class WordpressHelper
{
    /**
     * @return array
     */
    public static function getOptions(int $options_number) : array
    {
        if($options_number == 2)
        {
            return get_option('bdt_options_2') ?? [];
        }
        else if ($options_number == 3)
        {
            return get_option('bdt_options_3') ?? [];
        }
        else {
            return get_option('bdt_options') ?? [];
       }
    }

    /**
     * @param string $key
     * @return string
     */
    public static function getOption(int $options_number, string $key) : string
    {
        $options = self::getOptions($options_number);

        return array_key_exists($key, $options) ? $options[$key] : '';
    }
    /**
     * @return string
     */
    public static function getApiKey() : string
    {

        $options = self::getOptions(1);

        if (array_key_exists('api_key', $options) && $options['api_key']) {
            return $options['api_key'];
        }
    }

    /**
     * @return mixed
     */
    public static function getQueryParameters()
    {
        $query = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
        parse_str($query, $queryParams);

        return $queryParams > 0 ? $queryParams : false;
    }

    /**
     * @param  string $key
     * @return array|mixed
     */
    public static function getQueryParameter(string $key)
    {
        $queryParams = self::getQueryParameters();

        if ($queryParams && array_key_exists($key, $queryParams)) {
            return $queryParams[$key];
        } else {
            return '';
        }
    }

    /**
     * @return array
     */
    public static function getActivityTypes()
    {
        return [
            'NotSet',
            'TestDrive',
            'PhoneCall',
            'Purchase',
            'Email',
            'Contact'
        ];
    }

    /**
     * @param  string $type
     * @return bool
     */
    public static function isActivityType(string $type)
    {
        return in_array($type, self::getActivityTypes());
    }

    /**
     * @param  $args
     * @return mixed|string
     */
    public static function getReplyTo($args)
    {

        if (array_key_exists('headers', $args)) {
            foreach ($args['headers'] as $header) {
                preg_match('/^Reply-To: ".*" <(.+)>$/', $header, $matches);
                if (count($matches) > 1) {
                    return $matches[1];
                }
            }
        }

        return '';
    }
}
