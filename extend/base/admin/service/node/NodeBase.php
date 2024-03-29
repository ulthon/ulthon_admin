<?php

// +----------------------------------------------------------------------
// | ulthon_admin
// +----------------------------------------------------------------------
// | PHP交流群: 207160418
// +----------------------------------------------------------------------
// | 开源协议  http://license.coscl.org.cn/MulanPSL2
// +----------------------------------------------------------------------
// | gitee开源项目：https://gitee.com/ulthon/ulthon_admin
// +----------------------------------------------------------------------

namespace base\admin\service\node;

use app\admin\service\annotation\ControllerAnnotation;
use app\admin\service\annotation\NodeAnotation;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\DocParser;
use think\helper\Str;

/**
 * 节点处理类
 * Class Node.
 */
class NodeBase
{
    /**
     * @var string 当前文件夹
     */
    protected $basePath;

    /**
     * @var string 命名空间前缀
     */
    protected $baseNamespace;

    /**
     * 构造方法
     * Node constructor.
     * @param string $basePath 读取的文件夹
     * @param string $baseNamespace  读取的命名空间前缀
     */
    public function __construct($basePath, $baseNamespace)
    {
        $this->basePath = $basePath;
        $this->baseNamespace = $baseNamespace;

        return $this;
    }

    /**
     * 获取所有节点.
     * @return array
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    public function getNodelist()
    {
        list($nodeList, $controllerList) = [[], $this->getControllerList()];

        if (!empty($controllerList)) {
            AnnotationRegistry::registerLoader('class_exists');
            $parser = new DocParser();
            $parser->setIgnoreNotImportedAnnotations(true);
            $reader = new AnnotationReader($parser);

            foreach ($controllerList as $controllerFormat => $controller) {
                // 获取类和方法的注释信息
                $reflectionClass = new \ReflectionClass($controller);
                $methods = $reflectionClass->getMethods();
                $actionList = [];

                // 遍历读取所有方法的注释的参数信息
                foreach ($methods as $method) {
                    // 读取NodeAnotation的注解
                    $nodeAnnotation = $reader->getMethodAnnotation($method, NodeAnotation::class);
                    if (!empty($nodeAnnotation) && !empty($nodeAnnotation->title)) {
                        $actionTitle = !empty($nodeAnnotation) && !empty($nodeAnnotation->title) ? $nodeAnnotation->title : null;
                        $actionAuth = !empty($nodeAnnotation) && !empty($nodeAnnotation->auth) ? $nodeAnnotation->auth : false;

                        $method_name = $nodeAnnotation->name;

                        if (empty($method_name)) {
                            $method_name = $method->name;
                        }

                        $actionList[] = [
                            'node' => $controllerFormat . '/' . $method_name,
                            'title' => $actionTitle,
                            'is_auth' => $actionAuth,
                            'type' => 2,
                        ];
                    }
                }

                // 读取挂载到控制器注解中的节点
                $nodeAnnotationInController = $reader->getClassAnnotations($reflectionClass);
                foreach ($nodeAnnotationInController as $nodeAnnotation) {
                    if ($nodeAnnotation instanceof NodeAnotation) {
                        $actionList[] = [
                            'node' => $controllerFormat . '/' . $nodeAnnotation->name,
                            'title' => $nodeAnnotation->title,
                            'is_auth' => $nodeAnnotation->auth,
                            'type' => 2,
                        ];
                    }
                }

                // 方法非空才读取控制器注解
                if (!empty($actionList)) {
                    // 读取Controller的注解
                    $controllerAnnotation = $reader->getClassAnnotation($reflectionClass, ControllerAnnotation::class);
                    $controllerTitle = !empty($controllerAnnotation) && !empty($controllerAnnotation->title) ? $controllerAnnotation->title : null;
                    $controllerAuth = !empty($controllerAnnotation) && !empty($controllerAnnotation->auth) ? $controllerAnnotation->auth : false;
                    $nodeList[] = [
                        'node' => $controllerFormat,
                        'title' => $controllerTitle,
                        'is_auth' => $controllerAuth,
                        'type' => 1,
                    ];
                    $nodeList = array_merge($nodeList, $actionList);
                }
            }
        }

        return $nodeList;
    }

    /**
     * 获取所有控制器.
     * @return array
     */
    public function getControllerList()
    {
        return $this->readControllerFiles($this->basePath);
    }

    /**
     * 遍历读取控制器文件.
     * @param $path
     * @return array
     */
    protected function readControllerFiles($path)
    {
        list($list, $temp_list, $dirExplode) = [[], scandir($path), explode($this->basePath, $path)];
        $middleDir = isset($dirExplode[1]) && !empty($dirExplode[1]) ? str_replace('/', '\\', substr($dirExplode[1], 1)) . '\\' : '';

        foreach ($temp_list as $file) {
            // 排除根目录和没有开启注解的模块
            if ($file == '..' || $file == '.') {
                continue;
            }
            if (is_dir($path . DIRECTORY_SEPARATOR . $file)) {
                // 子文件夹，进行递归
                $childFiles = $this->readControllerFiles($path . DIRECTORY_SEPARATOR . $file);
                $list = array_merge($childFiles, $list);
            } else {
                // 判断是不是控制器
                $fileExplodeArray = explode('.', $file);
                if (count($fileExplodeArray) != 2 || end($fileExplodeArray) != 'php') {
                    continue;
                }
                // 根目录下的文件
                $className = str_replace('.php', '', $file);
                $controllerFormat = str_replace('\\', '.', $middleDir) . Str::snake(lcfirst($className));

                $list[$controllerFormat] = "{$this->baseNamespace}\\{$middleDir}" . $className;
            }
        }

        return $list;
    }
}
