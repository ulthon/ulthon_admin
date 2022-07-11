<?php

namespace think\filesystem\driver;

use League\Flysystem\AdapterInterface;
use think\filesystem\Driver;
use Xxtime\Flysystem\Aliyun\OssAdapter;

class Alioss  extends Driver
{
    protected function createAdapter(): AdapterInterface
    {

        $config = [
            'accessId'       => sysconfig('upload', 'alioss_access_key_id'),
            'accessSecret'   => sysconfig('upload', 'alioss_access_key_secret'),
            'endpoint'         => sysconfig('upload', 'alioss_endpoint'),
            'bucket'       => sysconfig('upload', 'alioss_bucket'),
        ];

        return new OssAdapter(
            $config
        );
    }
}
