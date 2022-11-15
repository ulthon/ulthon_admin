<?php

declare(strict_types=1);

namespace app\common\command\build;

use app\common\exception\DirFindedException;
use app\common\tools\PathTools;
use app\common\tools\phpparser\NodeVisitorTools;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\MagicConst\Dir;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\App;
use think\facade\View;
use think\helper\Str;

class Dist extends Command
{

    public $packList = [];
    public $usedClass = [];

    protected $distPath;



    protected function configure()
    {
        // 指令配置
        $this->setName('build:dist')
            ->setDescription('the build:dist command');
    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        $output->writeln('build:dist');

        $app_path = App::getRootPath();

        $dist_path = $app_path . 'dist';

        PathTools::intiDir($dist_path . '.temp');


        $this->distPath = $dist_path;

        $app_adapter = new Local($app_path);

        $app_filesystem = new Filesystem($app_adapter);

        $dist_adapter = new Local($dist_path);

        $dist_filesystem = new Filesystem($dist_adapter);

        $list_dist = $dist_filesystem->listContents();

        foreach ($list_dist as  $file_info) {
            if ($file_info['type'] == 'file') {
                $dist_filesystem->delete($file_info['path']);
            } else {
                $dist_filesystem->deleteDir($file_info['path']);
            }
        }

        $list_content = $app_filesystem->listContents('', true);
        foreach ($list_content as  $file_info) {
            if ($file_info['type'] == 'dir') {
                continue;
            }

            $file_path = $file_info['path'];

            if ($this->isSkip($file_path)) {
                continue;
            }

            $file_content = $app_filesystem->read($file_path);
            $path_info = pathinfo($file_path);

            if (!$this->isIgnored($file_path)) {

                if (isset($path_info['extension'])) {
                    if ($path_info['extension'] == 'php') {
                        $file_content = $this->buildPhpContent($file_content, $file_path);
                    }
                }
            }
            if (!is_null($file_content)) {

                $dist_filesystem->write($file_path, $file_content);
            }
        }



        $lib_php_file = 'lib/index.' . uniqid() . '.php';
        $lib_php_path = $this->distPath . '/' . $lib_php_file;


        PathTools::intiDir($lib_php_path);

        $prettyPrinter = new  Standard();


        ksort($this->packList);

        $new_pack_list = [];
        $back_pack_list = [];

        foreach ($this->packList as $namespace_name => $stmt) {
            if (Str::startsWith($namespace_name, 'app\common')) {
                $new_pack_list[$namespace_name] = $stmt;
            } else {
                $back_pack_list[$namespace_name] = $stmt;
            }
        }

        $this->packList = array_merge($new_pack_list, $back_pack_list);

        $stmts = [];
        foreach ($this->packList as $namespace_name => $stmt) {
            dump($namespace_name);
            $namespace_node_name = null;
            if (!empty($namespace_name)) {
                $namespace_node_name = new Name($namespace_name);
            }
            $stmts[] = new Namespace_($namespace_node_name, $stmt);
        }
        $newCode = $prettyPrinter->prettyPrintFile($stmts);

        file_put_contents($lib_php_path, $newCode);

        $index_code = file_get_contents(__DIR__ . '/tpl/index.php.temp');

        $index_code = str_replace('{$lib_php_file}', $lib_php_file, $index_code);

        $dist_filesystem->put('public/index.php', $index_code);

        $think_code = file_get_contents(__DIR__ . '/tpl/think.temp');

        $think_code = str_replace('{$lib_php_file}', $lib_php_file, $think_code);

        $dist_filesystem->put('think', $think_code);

        $output->info('打包完成');
    }

    public function buildPhpContent($content, $name)
    {


        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

        $stmts = $parser->parse($content);

        $is_magic_const_dir = $this->scanForMagicConstDir($stmts);

        if ($is_magic_const_dir) {
            return $content;
        }


        $current_namespace = null;
        foreach ($stmts as $stmts_item) {
            if ($stmts_item instanceof Namespace_) {
                $current_namespace = $stmts_item->name->toString();
                if (empty($current_namespace)) {
                    $current_namespace = null;
                }
            }
        }



        if (!isset($this->packList[$current_namespace])) {
            $this->packList[$current_namespace] = [];
        }

        $stmts = $this->parseStmts($stmts, $name);

        foreach ($stmts as $stmts_item) {
            if ($stmts_item instanceof Namespace_) {

                $this->packList[$current_namespace] = array_merge($this->packList[$current_namespace], $stmts_item->stmts);
            } else {

                $this->packList[$current_namespace][] = $stmts_item;
            }
        }


        return null;
    }

    public function addUsedClass(Node $node)
    {
        if (array_search($node, $this->usedClass)) {
            dump('已存在');
        } else {
            $this->usedClass[] = $node;
        }
    }

    public function parseStmts($stmts, $name)
    {

        $traverser = new NodeTraverser();

        $node_visitor = new NodeVisitorTools($this, $name);

        $traverser->addVisitor($node_visitor);

        $stmts = $traverser->traverse($stmts);

        return $stmts;
    }

    public function scanForMagicConstDir($stmts)
    {
        $is_dir_find = false;

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new class extends NodeVisitorAbstract
        {
            public function enterNode(Node $node)
            {
                if ($node instanceof Dir) {
                    // Clean out the function body

                    throw new DirFindedException("finded", 1);
                }
            }
        });

        try {

            $stmts = $traverser->traverse($stmts);
        } catch (DirFindedException $th) {

            $is_dir_find = true;
        }

        return $is_dir_find;
    }

    public function isSkip($path)
    {

        $skip_path = [
            '/^\.git/',
            '/^dist/',
            '/^runtime/',
        ];

        foreach ($skip_path as  $rule) {
            if (preg_match($rule, $path)) {
                return true;
            }
        }

        return false;
    }

    public function isIgnored($path)
    {

        $ignore_path = [
            '/^vendor/',
            '/^config/',
            '/^database\/*/',
            '/event\.php/',
            '/middleware\.php/',
            '/provider\.php/',
            '/service\.php/',
            '/^app\/.*\/config\/.*/',
            '/app\/common.php/',
            '/config.php/',
            '/^public\/index\.php/',
            '/^public\/router\.php/',
            '/^route\/*/',
            '/^app\/admin\/service\/initAdminData\/*/',
        ];

        foreach ($ignore_path as  $rule) {

            if (preg_match($rule, $path)) {
                return true;
            }
        }

        return false;
    }
}
