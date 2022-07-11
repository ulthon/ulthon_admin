<?php
namespace think\filesystem\driver;

use League\Flysystem\AdapterInterface;
use think\filesystem\Driver;
use Overtrue\Flysystem\Qiniu\QiniuAdapter;

class Qiniu  extends Driver
{
    protected function createAdapter(): AdapterInterface
    {
        return new QiniuAdapter(
            sysconfig('upload','qnoss_access_key'),
            sysconfig('upload','qnoss_secret_key'),
            sysconfig('upload','qnoss_bucket'),
            sysconfig('upload','qnoss_domain')
        );
    }
}

