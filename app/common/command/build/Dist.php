<?php

declare(strict_types=1);

namespace app\common\command\build;

use app\commmon\tools\phpparser\PackUseNodeVisitorTools;
use app\common\tools\PathTools;
use app\common\tools\phpparser\FindClassNodeVisitorTools;
use app\common\tools\phpparser\MinifyPrinterTools;
use app\common\tools\phpparser\NodeFakeVarVisitorTools;
use app\common\tools\phpparser\NodeVisitorTools;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Include_;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\MagicConst\Dir;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use app\common\tools\phpparser\PrettyPrinterTools as Standard;
use app\common\tools\phpparser\PrettyPrinterTools;
use app\common\tools\phpparser\ReadEnvVisitorNodeTools;
use PhpParser\Comment;
use PhpParser\Comment\Doc;
use PhpParser\NodeVisitor\NameResolver;
use think\Collection;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\App;
use think\facade\Config;
use think\facade\View;
use think\helper\Str;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Nop;

class Dist extends Command
{

    public $packList = [];
    public $usedClass = [];
    public $hasClass = [];
    public $newPackList = [];
    public $classExtendList = [];

    /**
     * 编译过程中遇到的目录魔术变量和文件魔术变量
     * 包括__DIR__,__FILE__,__LINE__
     *
     * @var array
     */
    public $constDirList = [];

    /**
     * @var FileSystem
     */
    protected $appFilesystem;

    /**
     * @var FileSystem
     */
    protected $distFilesystem;

    /**
     * @var FileSystem
     */
    protected $tempFilesystem;

    protected function configure()
    {
        // 指令配置
        $this->setName('build:dist')
            ->setDescription('the build:dist command');
    }

    protected function log($content, $type = Output::VERBOSITY_VERY_VERBOSE)
    {
        if ($type <= $this->output->getVerbosity()) {
            $this->output($content);
        }
    }
    protected function write($content, $type = Output::VERBOSITY_NORMAL)
    {
        if ($type <= $this->output->getVerbosity()) {
            $this->output($content);
        }
    }
    protected function debug($content, $type = Output::VERBOSITY_DEBUG)
    {
        if ($type <= $this->output->getVerbosity()) {
            $this->output($content);
        }
    }

    protected function output($content)
    {
        $this->output->writeln(date('Y-m-d H:i:s') . ' ' . $content);
    }

    protected function execute(Input $input, Output $output)
    {

        // 指令输出
        $this->write('开始编译');

        $this->log('创建编译环境相关目录');
        $app_path = App::getRootPath();

        $dist_path = $app_path . 'build/dist';
        PathTools::intiDir($dist_path . '.temp');


        $temp_path = $app_path . 'build/temp';
        PathTools::intiDir($temp_path . '.temp');


        $this->distPath = $dist_path;

        $app_adapter = new Local($app_path);
        $app_filesystem = new Filesystem($app_adapter);
        $this->appFilesystem = $app_filesystem;


        $dist_adapter = new Local($dist_path);
        $dist_filesystem = new Filesystem($dist_adapter);
        $this->distFilesystem = $dist_filesystem;


        $temp_adapter = new Local($temp_path);
        $temp_filesystem = new Filesystem($temp_adapter);
        $this->tempFilesystem = $temp_filesystem;



        $this->log('清理编译环境相关目录');
        $this->clearDistDir();
        $this->clearTempDir();

        $this->log('将源码移动到临时目录');
        $this->moveCodeToTemp();

        $this->log('删除注释并生成版权声明');
        $this->clearCommentAndGenerateCopyright();

        $this->log('编译env配置信息');
        $this->packEnv();


        $this->log('编译所有代码以全命名空间路径调用');
        $this->packClassUseName();

        return;


        $this->log('编译混淆变量名');
        // 根据配置pack_var扫描编译


        $this->log('编译标准类库');
        // 根据配置pack_app扫描编译
        // 不标准的或排除的原样返回
        // 编译的最终将打包到一个文件中

        $this->log('编译配置文件');
        // 根据pack_config扫描编译
        // 凡是直接return的都需要编译，比如config，middleware等，其他的原样跳过
        // 只需要压缩配置文件

        $this->log('函数库文件');
        // 根据配置function_path加载函数库并且编译成单独的文件

        $this->log('编译路由文件');
        // 根据tp规则编译路由文件
        // 多个路由合并成一个路由

        $this->log('输出到文件夹');

        $this->log('生成TP多应用目录');

        $this->log('编译完成');




        $list_content = $temp_filesystem->listContents('', true);
        foreach ($list_content as  $file_info) {
            if ($file_info['type'] == 'dir') {
                continue;
            }

            $file_path = $file_info['path'];

            if ($this->isSkip($file_path)) {
                continue;
            }

            $file_content = $temp_filesystem->read($file_path);
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





        $lib_php_file = '/lib.' . uniqid() . '.php';
        $lib_php_path = $this->distPath . '/lib' . $lib_php_file;

        $this->buildMainClassFile($lib_php_path);

        $lib_function_file = '/lib.' . uniqid() . '.php';
        $lib_function_path = $this->distPath . '/lib' . $lib_function_file;
        $this->buildFunctionFile($lib_function_path);

        $this->buildMigrateFile();
        $this->buildRouteFile();

        $lib_dir_const_file = '/lib.' . uniqid() . '.php';
        $lib_dir_const_path = $this->distPath . '/lib' . $lib_dir_const_file;
        $this->buildDirConstFile($lib_dir_const_path);



        PathTools::intiDir($lib_php_path);

        $this->buildIncludeIndexFile([
            $lib_dir_const_file,
            $lib_php_file,
            $lib_function_file,
        ]);



        $this->buildAllAppDir();
        $this->clearTempDir();

        $output->info('打包完成');
    }
    public function clearDistDir()
    {

        $list_dist = $this->distFilesystem->listContents();

        foreach ($list_dist as  $file_info) {
            if ($file_info['type'] == 'file') {
                $this->distFilesystem->delete($file_info['path']);
            } else {
                $this->distFilesystem->deleteDir($file_info['path']);
            }
        }
    }
    public function clearTempDir()
    {
        $list_dist = $this->tempFilesystem->listContents();

        foreach ($list_dist as  $file_info) {
            if ($file_info['type'] == 'file') {
                $this->tempFilesystem->delete($file_info['path']);
            } else {
                $this->tempFilesystem->deleteDir($file_info['path']);
            }
        }
    }

    protected function moveCodeToTemp()
    {

        $skip_path = Config::get('dist.skip_path');

        $list_file = $this->appFilesystem->listContents('', true);

        foreach ($list_file as  $item_file) {

            if ($item_file['type'] != 'file') {
                continue;
            }

            if ($this->checkPregMatch($skip_path, $item_file['path'])) {
                continue;
            }

            $this->tempFilesystem->put($item_file['path'], $this->appFilesystem->read($item_file['path']));
        }
    }

    protected function clearCommentAndGenerateCopyright()
    {
        $list_file = $this->tempFilesystem->listContents('', true);


        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

        $pretty_printer = new PrettyPrinterTools();


        foreach ($list_file as $item_file) {
            $path = $item_file['path'];


            $target_include = $this->getAllInclude();

            $target_exclude = $this->getAllExclude();

            if (!$this->checkPregMatchPhp($target_include, $item_file) || $this->checkPregMatch($target_exclude, $path, false)) {
                continue;
            }

            $this->debug('编译: ' . $path);

            $file_content = $this->tempFilesystem->read($path);
            $file_stmts = $parser->parse($file_content);

            $comment_traverser = new NodeTraverser;
            $comment_traverser->addVisitor(
                new class extends NodeVisitorAbstract
                {
                    protected $visitCount = 0;
                    public function leaveNode(Node $node)
                    {
                        $this->visitCount++;

                        $comments = $node->getComments();
                        if (!empty($comments)) {
                            $new_comments = [];
                            foreach ($comments as  $comment_item) {
                                if ($comment_item instanceof Doc) {
                                    $new_comments[] = $comment_item;
                                }
                            }
                            $node->setAttribute('comments', $new_comments);
                        }
                    }
                }
            );

            $file_stmts = $comment_traverser->traverse($file_stmts);


            $copyright_stmts = [];

            $copyright = Config::get('dist.copyright', []);

            foreach ($copyright as  $text) {
                $copyright_stmts[] = new Comment('// ' . $text);
            }

            $copyright_stmts_item = new Nop();

            $copyright_stmts_item->setAttribute('comments', $copyright_stmts);

            array_unshift($file_stmts, $copyright_stmts_item);

            // 生成代码
            $result_content = $pretty_printer->prettyPrintFile($file_stmts);

            $this->tempFilesystem->put($path, $result_content);
        }
    }

    protected function getAllInclude()
    {
        return array_merge(
            Config::get('dist.pack_app.include_path', []),
            Config::get('dist.pack_vars.include_path', []),
            Config::get('dist.pack_config.include_path', []),
            Config::get('dist.pack_env.include_path', []),
        );
    }

    protected function getAllExclude()
    {
        return array_merge(
            Config::get('dist.pack_app.exclude_path', []),
            Config::get('dist.pack_vars.exclude_path', []),
            Config::get('dist.pack_config.exclude_path', []),
            Config::get('dist.pack_env.exclude_path', []),
        );
    }

    public function packClassUseName()
    {
        $list_file = $this->tempFilesystem->listContents('', true);


        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

        $pretty_printer = new PrettyPrinterTools();

        foreach ($list_file as $item_file) {
            $path = $item_file['path'];

            $target_include = $this->getAllInclude();

            $target_exclude = $this->getAllExclude();

            if (!$this->checkPregMatchPhp($target_include, $item_file) || $this->checkPregMatch($target_exclude, $path, false)) {
                continue;
            }

            $this->debug('编译: ' . $path);

            $file_content = $this->tempFilesystem->read($path);
            $file_stmts = $parser->parse($file_content);

            $name_resolver = new NameResolver();
            $node_name_traverser = new NodeTraverser;
            $node_name_traverser->addVisitor($name_resolver);
            $file_stmts = $node_name_traverser->traverse($file_stmts);

            // 去除use，统计class调用
            $list_used_class = new Collection();

            $pack_use_traverser = new NodeTraverser;
            $pack_use_traverser->addVisitor(
                new class($list_used_class) extends FindClassNodeVisitorTools
                {
                    /**
                     * @var Collection
                     */
                    protected $listUsedClass;
                    public function __construct($list_used_class)
                    {
                        $this->listUsedClass = $list_used_class;
                    }

                    public function leaveNode(Node $node)
                    {
                        if ($node instanceof Use_) {
                            return NodeTraverser::REMOVE_NODE;
                        } else {
                            parent::leaveNode($node);
                        }
                    }

                    public function findClassNodeName(Name &$name)
                    {
                        $this->addUsedClass($name->toString());
                    }

                    protected function addUsedClass($class_name)
                    {
                        if (!isset($this->listUsedClass[$class_name])) {
                            $class_alias = 'ul' . uniqid();
                            $this->listUsedClass->push($class_alias, $class_name);
                        }
                    }
                }
            );

            $file_stmts = $pack_use_traverser->traverse($file_stmts);

            // 替换使用Use alias替换Class 
            $useuse = [];

            foreach ($list_used_class as $class_name => $alias) {
                $useuse[] = new UseUse(new Name($class_name), $alias);
            }

            if (!empty($useuse)) {

                $use_stmt = new Use_($useuse);

                $has_namespace = false;
                foreach ($file_stmts as  $item_stmts) {
                    if ($item_stmts instanceof Namespace_) {
                        $has_namespace = true;
                        array_unshift($item_stmts->stmts, $use_stmt);
                    }
                }

                if (!$has_namespace) {
                    array_unshift($file_stmts, $use_stmt);
                }
            }

            $pack_use_replace_traverser = new NodeTraverser;
            $pack_use_replace_traverser->addVisitor(
                new class($list_used_class) extends FindClassNodeVisitorTools
                {
                    /**
                     * @var Collection
                     */
                    protected $listUsedClass;
                    public function __construct($list_used_class)
                    {
                        $this->listUsedClass = $list_used_class;
                    }

                    public function findClassNodeName(Name &$name)
                    {

                        $class_name = $name->toString();

                        if (isset($this->listUsedClass[$class_name])) {
                            $name = new Name($this->listUsedClass[$class_name]);
                        }
                    }
                }
            );
            $file_stmts = $pack_use_replace_traverser->traverse($file_stmts);

            // 生成代码
            $result_content = $pretty_printer->prettyPrintFile($file_stmts);

            $this->tempFilesystem->put($path, $result_content);
        }
    }

    protected function checkPregMatch($rules, $path, $empty_rules_result = true)
    {
        if (empty($rules)) {
            return $empty_rules_result;
        }

        foreach ($rules as  $rule) {
            // dump($rule);
            if (preg_match($rule, $path)) {
                return true;
            }
        }

        return false;
    }

    protected function checkPregMatchPhp($rules, $item_file, $empty_rules_result = true)
    {
        if ($item_file['type'] != 'file') {
            return false;
        }
        if (!isset($item_file['extension'])) {
            return false;
        }
        if ($item_file['extension'] != 'php') {
            return false;
        }


        return $this->checkPregMatch($rules, $item_file['path'], $empty_rules_result);
    }

    public function packEnv()
    {

        $env_pack_mode = Config::get('dist.pack_env.pack_env_mode');

        if ($env_pack_mode == 3) {
            $this->debug('跳过ENV配置打包');
            return;
        }
        $list_files = $this->tempFilesystem->listContents('', true);

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

        $pretty_printer = new  PrettyPrinterTools();

        foreach ($list_files as  $item_file) {
            $path = $item_file['path'];


            $target_include = $this->getAllInclude();

            $target_exclude = $this->getAllExclude();

            if (!$this->checkPregMatchPhp($target_include, $item_file) || $this->checkPregMatch($target_exclude, $path, false)) {
                continue;
            }

            $this->debug('编译: ' . $path);

            $file_content = $this->tempFilesystem->read($path);
            $file_stmts = $parser->parse($file_content);

            $env_traverser = new NodeTraverser;
            $env_traverser->addVisitor(
                new NameResolver()
            );
            $env_traverser->addVisitor(
                new ReadEnvVisitorNodeTools($path)
            );

            $file_stmts = $env_traverser->traverse($file_stmts);

            // 生成代码
            $result_content = $pretty_printer->prettyPrintFile($file_stmts);

            $this->tempFilesystem->put($path, $result_content);
        }

        if ($this->tempFilesystem->has('.env')) {
            $this->tempFilesystem->delete('.env');
        }
    }



    /**
     * 打包路由文件
     *
     * @return void
     */
    public function buildRouteFile()
    {
        $route_dir = 'route';

        $list_files = $this->tempFilesystem->listContents($route_dir, true);

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

        $prettyPrinter = new  MinifyPrinterTools();

        foreach ($list_files as  $item_file) {
            if ($item_file['type'] == 'dir') {
                continue;
            }

            $file_content = $this->tempFilesystem->read($item_file['path']);

            $file_stmts = $parser->parse($file_content);

            $traverser = new NodeTraverser();
            $traverser->addVisitor(new class($item_file['path'], $this) extends NodeVisitorAbstract
            {
                protected $name;
                protected $mainClass;
                public function __construct($name, $main_class)
                {
                    $this->name = $name;
                    $this->mainClass = $main_class;
                }

                public function leaveNode(Node $node)
                {
                    $name = $this->name;
                    if ($node instanceof Dir) {
                        // Clean out the function body
                        $const_key = 'dirconst' . uniqid();

                        $this->mainClass->constDirList[$const_key] = dirname($name);
                        return new ConstFetch(new Name($const_key));
                    }

                    if ($node->hasAttribute('comments')) {
                        $node->setAttribute('comments', []);
                    }
                }
            });


            $traverser_var_faker = new NodeTraverser();
            $traverser_var_faker->addVisitor(new NodeFakeVarVisitorTools);

            $file_stmts = $traverser_var_faker->traverse($file_stmts);


            $function_code = $prettyPrinter->prettyPrintFile($file_stmts);

            $this->distFilesystem->put($item_file['path'], $function_code);
        }
    }

    /**
     * 处理数据库迁移代码
     *
     * @return void
     */
    public function buildMigrateFile()
    {
        $database_dir =  'database';

        $list_files = $this->tempFilesystem->listContents($database_dir, true);

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);


        $prettyPrinter = new  MinifyPrinterTools();

        foreach ($list_files as  $item_file) {
            if ($item_file['type'] == 'dir') {
                continue;
            }

            $file_content = $this->tempFilesystem->read($item_file['path']);

            $file_stmts = $parser->parse($file_content);

            $traverser = new NodeTraverser();
            $traverser->addVisitor(new class($item_file['path'], $this) extends NodeVisitorAbstract
            {
                protected $name;
                protected $mainClass;
                public function __construct($name, $main_class)
                {
                    $this->name = $name;
                    $this->mainClass = $main_class;
                }

                public function leaveNode(Node $node)
                {
                    $name = $this->name;
                    if ($node instanceof Dir) {
                        // Clean out the function body
                        $const_key = 'dirconst' . uniqid();

                        $this->mainClass->constDirList[$const_key] = dirname($name);
                        return new ConstFetch(new Name($const_key));
                    }

                    if ($node->hasAttribute('comments')) {
                        $node->setAttribute('comments', []);
                    }
                }
            });

            $file_stmts = $traverser->traverse($file_stmts);

            $traverser_var_faker = new NodeTraverser();
            $traverser_var_faker->addVisitor(new NodeFakeVarVisitorTools);

            $file_stmts = $traverser_var_faker->traverse($file_stmts);


            $function_code = $prettyPrinter->prettyPrintFile($file_stmts);

            $this->distFilesystem->put($item_file['path'], $function_code);
        }
    }


    /**
     * 打包标准类文件（核心）
     *
     * @param string $lib_php_path
     * @return void
     */
    public function buildMainClassFile($lib_php_path)
    {
        $prettyPrinter = new  Standard();
        // 根据调用次数排序
        $this->parsePackList();

        $stmts = [];
        foreach ($this->packList as $class_name => $class_item) {
            foreach ($class_item['stmts'] as $stmt_item) {
                $stmts[] = $stmt_item;
            }
        }

        $traverser = new NodeTraverser();

        $traverser->addVisitor(new NodeFakeVarVisitorTools);

        $stmts = $traverser->traverse($stmts);

        $newCode = $prettyPrinter->prettyPrintFile($stmts);

        file_put_contents($lib_php_path, $newCode);
    }

    /**
     * 统一声明目录魔术常量
     *
     * @param string $lib_dir_const_path
     * @return void
     */
    public function buildDirConstFile($lib_dir_const_path)
    {
        $dir_const_stmts = [];

        foreach ($this->constDirList as $const_key => $const_value) {

            $dir_const_stmts[] = new Expression(new FuncCall(
                new Name('define'),
                [
                    new Arg(new String_($const_key)),
                    new Arg(new Concat(
                        new Dir(),
                        new String_('/../' . $const_value)
                    )),
                ]
            ));
        }

        $prettyPrinter = new  MinifyPrinterTools();

        $dir_const_code = $prettyPrinter->prettyPrintFile($dir_const_stmts);

        file_put_contents($lib_dir_const_path, $dir_const_code);
    }

    /**
     * 打包类库文件
     *
     * @param string $lib_function_path
     * @return void
     */
    public function buildFunctionFile($lib_function_path)
    {
        $function_path = Config::get('dist.function_path', []);

        $function_stmts = [];

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

        foreach ($function_path as  $function_file) {

            $function_file_path = App::getRootPath() . '' . $function_file;


            $function_content = file_get_contents($function_file_path);

            $stmts = $parser->parse($function_content);

            $traverser = new NodeTraverser();
            $traverser->addVisitor(new class($function_file, $this) extends NodeVisitorAbstract
            {
                protected $name;
                protected $mainClass;
                public function __construct($name, $main_class)
                {
                    $this->name = $name;
                    $this->mainClass = $main_class;
                }

                public function leaveNode(Node $node)
                {
                    $name = $this->name;
                    if ($node instanceof Dir) {
                        // Clean out the function body
                        $const_key = 'dirconst' . uniqid();

                        $this->mainClass->constDirList[$const_key] = dirname($name);
                        return new ConstFetch(new Name($const_key));
                    }

                    if ($node->hasAttribute('comments')) {
                        $node->setAttribute('comments', []);
                    }
                }
            });


            $stmts = $traverser->traverse($stmts);

            foreach ($stmts as  $stmt_item) {
                $function_stmts[] = $stmt_item;
            }
        }


        $traverser = new NodeTraverser();

        $traverser->addVisitor(new NodeFakeVarVisitorTools);

        $function_stmts = $traverser->traverse($function_stmts);

        $prettyPrinter = new  MinifyPrinterTools();

        $function_code = $prettyPrinter->prettyPrintFile($function_stmts);

        file_put_contents($lib_function_path, $function_code);
    }

    /**
     * 创建多应用空间
     *
     * @return void
     */
    public function buildAllAppDir()
    {
        $list = $this->appFilesystem->listContents('app');

        foreach ($list as  $item) {
            if ($item['type'] != 'dir') {
                continue;
            }

            if ($this->distFilesystem->has($item['path'])) {
                continue;
            }

            $this->distFilesystem->createDir($item['path']);
        }
    }

    /**
     * 生成引入文件
     *
     * @param string[] $files
     * @return void
     */
    public function buildIncludeIndexFile($files)
    {
        $file_stmts = [];

        $file_stmts[] = new Expression(new FuncCall(
            new Name('define'),
            [
                new Arg(new String_('ULTHON_ADMIN_BUILD_DIST')),
                new Arg(new String_('1'))
            ]
        ));

        foreach ($files as  $file_name) {
            $file_stmts[] = new Expression(new Include_(new Concat(new Dir, new String_($file_name)), Include_::TYPE_REQUIRE_ONCE));
        }
        $prettyPrinter = new  MinifyPrinterTools();


        $newCode = $prettyPrinter->prettyPrintFile($file_stmts);

        file_put_contents($this->distPath . '/lib/index.php', $newCode);
    }

    /**
     * 后处理核心类库
     *
     * @return void
     */
    public function parsePackList()
    {

        foreach ($this->packList as $class_name => $class_item) {

            $extend_name_orginal = $this->usedClass[$class_item['extend_name']] ?? '';

            $this->classExtendList[$class_name] = $extend_name_orginal;
        }

        foreach ($this->packList as $class_name => $class_item) {
            $this->insertToNewPackList($class_name, $class_item);
        }

        $this->packList = $this->newPackList;

        $this->newPackList = [];
    }

    /**
     * 调整类加载顺序
     *
     * @param string $class_name
     * @param array $class_item
     * @return void
     */
    public function insertToNewPackList($class_name, $class_item)
    {
        if (isset($this->newPackList[$class_name])) {
            return;
        }

        $extend_name = $this->classExtendList[$class_name] ?? '';

        if (!empty($extend_name)) {
            if (isset($this->packList[$extend_name])) {
                $this->insertToNewPackList($extend_name, $this->packList[$extend_name]);
            }
        } else {
            $extend_name = $class_item['extend_name'];


            if (!empty($extend_name)) {
                $try_namse_extend_name = $extend_name;

                if (isset($this->packList[$try_namse_extend_name])) {
                    $this->insertToNewPackList($try_namse_extend_name, $this->packList[$try_namse_extend_name]);
                }
            }
        }

        $this->newPackList[$class_name] = $class_item;
    }

    /**
     * 
     *
     * @param Node\Stmt[] $stmts
     * @return void
     */
    public function checkStmts($stmts, $name)
    {

        $class_count = 0;
        $namsepace_count = 0;
        foreach ($stmts as  $stmt_item) {
            if ($stmt_item instanceof Namespace_) {
                $namsepace_count++;
                foreach ($stmt_item->stmts as  $class_item) {

                    if ($class_item instanceof ClassLike) {
                        $class_count++;
                    }
                }
            }
        }

        if ($namsepace_count !== 1) {
            throw new \Exception('一个文件只能有一个命名空间：' . $name);
        }
        if ($class_count !== 1) {
            throw new \Exception('一个文件只能有一个类：' . $name);
        }
    }

    /**
     * 扫描解析代码
     *
     * @param string $content
     * @param string $name
     * @return void
     */
    public function buildPhpContent($content, $name)
    {

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

        $stmts = $parser->parse($content);

        $this->scanForMagicConstDir($stmts, $name);

        $this->checkStmts($stmts, $name);


        $stmts = $this->parseStmts($stmts, $name);

        $class_name = null;
        $extend_name = null;
        $namespace_name = null;
        foreach ($stmts as  $stmt_item) {
            if ($stmt_item instanceof Namespace_) {
                $namespace_name = $stmt_item->name->toString();
                foreach ($stmt_item->stmts as  $class_item) {

                    if ($class_item instanceof Use_) {
                        foreach ($class_item->uses as $stmt_use) {
                            $use_class = $stmt_use->name->toString();
                            $alias = $use_class;

                            if (!empty($stmt_use->alias)) {
                                $alias = $stmt_use->alias->toString();
                            }
                            $this->usedClass[$alias] = $use_class;
                        }
                    }

                    if ($class_item instanceof ClassLike) {
                        $class_name = $namespace_name . '\\' . $class_item->name->toString();

                        if ($class_item instanceof Class_) {

                            if (!empty($class_item->extends)) {
                                $extend_name = $class_item->extends->toString();
                            }
                        }
                    }
                }
            }
        }

        $this->hasClass[] = $class_name;
        $pack_item = [
            'file_name' => $name,
            'class_name' => $class_name,
            'extend_name' => $extend_name,
            'namespace_name' => $namespace_name,
            'stmts' => $stmts
        ];

        $this->packList[$class_name] = $pack_item;

        return null;
    }


    /**
     * 遍历扫描代码
     *
     * @param Node\Stmt[]|null $stmts
     * @param string $name
     * @return Node\Stmt[]
     */
    public function parseStmts($stmts, $name)
    {

        $traverser = new NodeTraverser();

        $node_visitor = new NodeVisitorTools($this, $name);

        $traverser->addVisitor($node_visitor);

        $stmts = $traverser->traverse($stmts);

        return $stmts;
    }

    /**
     * 扫描魔术变量
     *
     * @param Node\Stmt[]|null $stmts
     * @param string $name
     * @return void
     */
    public function scanForMagicConstDir($stmts, $name)
    {

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new class($name, $this) extends NodeVisitorAbstract
        {
            protected $name;
            protected $mainClass;
            public function __construct($name, $main_class)
            {
                $this->name = $name;
                $this->mainClass = $main_class;
            }

            public function leaveNode(Node $node)
            {
                $name = $this->name;
                if ($node instanceof Dir) {
                    // Clean out the function body
                    $const_key = 'dirconst' . uniqid();

                    $this->mainClass->constDirList[$const_key] = dirname($name);
                    return new ConstFetch(new Name($const_key));
                }
            }
        });

        $stmts = $traverser->traverse($stmts);
    }

    public function isSkip($path)
    {

        $system_skip_path = [
            '/app\/common.php/',
            '/^database\/*/',
            '/^route\/*/',
        ];

        $skip_path = Config::get('dist.skip_path', []);

        $skip_path = array_merge($system_skip_path, $skip_path);

        foreach ($skip_path as $rule) {
            if (preg_match($rule, $path)) {
                return true;
            }
        }

        return false;
    }

    public function isIgnored($path)
    {

        $ignore_path = Config::get('dist.ignore_path', []);

        foreach ($ignore_path as  $rule) {

            if (preg_match($rule, $path)) {
                return true;
            }
        }

        return false;
    }
}
