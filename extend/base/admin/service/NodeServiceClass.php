<?php

namespace base\admin\service;

use app\admin\service\node\Node;

class NodeServiceClass
{
    /**
     * 获取节点服务
     * @return array
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    public function getNodelist()
    {
        $basePath = base_path() . 'admin' . DIRECTORY_SEPARATOR . 'controller';
        $baseNamespace = "app\admin\controller";

        $nodeList = (new Node($basePath, $baseNamespace))
            ->getNodelist();

        return $nodeList;
    }
}
