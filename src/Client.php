<?php
/**
 * 微服务网关kong 的客户端
 */
namespace kongClient;
use Exception;
use kongClient\Support\BaseRequest;

class Client extends BaseRequest{

    //网关url
    protected $gateWayUrl = '';
    protected $apiKey = '';//网关验证key
    protected $apiSecret = '';//数据加密密钥
    protected $clientName = '';//客户名
    protected $linkTag = '&';//干扰符

    public function __construct($config=[])
    {
        if (!empty($config))
        {
            $this->setConfig($config);
        }
        parent::__construct();
    }

    /**
     * 设置参数
     */
    public function setConfig($config)
    {
        $this->gateWayUrl = $config['gate_way_url']??'';
        $this->apiKey = $config['api_key']??'';
        $this->apiSecret = $config['api_secret']??'';
        $this->clientName = $config['client_name']??'';
        $this->linkTag = $config['link_tag']??'&';
        if (empty($this->gateWayUrl)
            || empty($this->apiKey)
            || empty($this->apiSecret)
            || empty($this->clientName)
        )
        {
            throw new Exception('缺少参数！');
        }
        return true;
    }

    /**
     * 发送请求
     */
    public function request($path, $getParams=[], $postParams=[], $format='json')
    {
        $url = $this->gateWayUrl . '/' . $path;
        // 获取签名url参数
        $urlParam = $this->mkSign($getParams, $postParams);
        $header = [
            'apikey' => $this->apiKey,
        ];
        //var_export($urlParam);exit;
        return $this->httpRequest($url, $urlParam, $postParams, $header, $format);
    }

    /**
     * 数据签名
     */
    public function mkSign(array $signParam, array $postParams=[])
    {
        $signParam['timestamp'] =  time();
        $signParam['api_key'] =  $this->apiKey;
        $signParam['api_secret'] =  $this->apiSecret;
        $signParam['client_name'] =  $this->clientName;
        if (!is_array($signParam) || empty($signParam))
        {
            throw new Exception('生成签名失败');
        }
        $postSign = '';
        if (!empty($postParams))
        {
            ksort($postParams);
            $postSign = md5(implode($this->linkTag, $postParams));;
        }
        // 根据key排序,引用传参
        ksort($signParam);
        var_export($signParam);exit;
        // 计算签名
        $sign = md5(implode($this->linkTag, $signParam) . $postSign);
        // 这是私钥,不能出现在请求url上
        unset($signParam['api_key'],$signParam['api_secret'], $signParam['client_name']);
        // 拼接计算后的签名为url请求参数
        $signParam['sign'] = $sign;

        return $signParam;
    }
}