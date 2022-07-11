<?php

namespace think\filesystem\driver;;

use League\Flysystem\AdapterInterface;
use Qcloud\Cos\Client;
use think\filesystem\Driver;

class Txcos extends Driver
{
    protected function createAdapter(): AdapterInterface
    {

        $secretId = sysconfig('upload', 'txcos_secret_id');
        $secretKey = sysconfig('upload', 'txcos_secret_key');
        $region = sysconfig('upload', 'txcos_region'); //set a default bucket region 设置一个默认的存储桶地域 
        $cosClient = new Client(
            array(
                'region' => $region,
                'schema' => 'https', //协议头部，默认为http
                'credentials' => array(
                    'secretId'  => $secretId,
                    'secretKey' => $secretKey
                ),
                'signHost' => false
            )
        );
        $bucket = sysconfig('upload', 'tecos_bucket'); //存储桶名称 格式：BucketName-APPID



        $adapter = new \Chunpat\FlysystemTencentCos\Adapter($cosClient, $bucket);

        return $adapter;
    }
}
