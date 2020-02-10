<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/19/2019
 * Time: 4:55 PM
 */

namespace AsyncDis;

use GuzzleHttp\Client;


class Http extends Client
{
    public static function post(string $uri, array $params)
    {
        $client   = new Client();
        $response = $client->post($uri, ['form_params' => $params]);
        $result   = (string)$response->getBody();
        $result   = json_decode($result, true);
        debug(compact('uri', 'params', 'result'), 'http.post');
        if ($result['code'] != 200) {
            throw new \Exception(sprintf("http error! url:%s, errInfo:%s", $uri, $result['message'] ?? $result['msg']));
        }
        return $result;
    }

    public static function get(string $uri, array $params = [])
    {
        $client = new Client();
        if (!empty($params)) {
            if (false === stripos($uri, '?')) {
                $uri .= '?';
            }
            $uri .= self::buildQuery($params);
        }
        $response = $client->request('GET', $uri);
        $result   = (string)$response->getBody();
        $result   = json_decode($result, true);
        debug(compact('uri', 'params', 'result'), 'http.get');
        if ($result['code'] != 200) {
            throw new \Exception(sprintf("http error! url:%s, errInfo:%s", $uri, $result['msg']));
        }
        return $result;
    }

    public static function buildQuery(array $queryData): string
    {
        return http_build_query($queryData, null, '&', PHP_QUERY_RFC3986);
    }
}
