<?php

/**
 * Created by PhpStorm.
 * User: wangpenghai
 * Date: 2018/10/12
 * Time: 上午11:54
 */
namespace kongClient\Support;

use GuzzleHttp\Client;

class BaseRequest
{

    public function __construct()
    {
        $this->httpRequest = new Client();
    }

    /**
     * http get
     */
    public function httpRequest($path, array $urlParam=[], array $postParam=[], array $header=[], $format='json')
    {
        $requestUrlString = '';
        foreach ($urlParam as $k => $v) {
            $requestUrlString .= $k."=".urlencode($v)."&";
        }

        $requestUrlString = rtrim($requestUrlString, '&');
        $path .= '?' . $requestUrlString;
        if (!empty($header))
        {
            $option = [
                'headers' => $header,
            ];
        }
        $method = 'GET';
        if (!empty($postParam))
        {
            $method = 'POST';
            $option['form_params'] = $postParam;
        }
        try{
            $response = $this->httpRequest->request($method,$path, $option);
        } catch (\Exception $e)
        {
            throw new \Exception('Invalid authentication credentials.', $e->getCode());
        }


        $body = $response->getBody();
        if ($responseCode = $response->getStatusCode() != 200)
        {
            throw new \Exception('http fail! code=' . $responseCode);
        } else {
            if ($format === 'json')
            {
                $data = json_decode($body, true);
                return $data;
            } else {
                return $body;
            }
        }
    }
}