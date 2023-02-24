<?php

declare(strict_types=1);

namespace app\common\command\build;

use app\common\tools\PathTools;
use app\common\tools\phpparser\FindClassNodeVisitorTools;
use app\common\tools\phpparser\MinifyPrinterTools;
use app\common\tools\phpparser\NodeFakeVarVisitorTools;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
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
use PhpParser\Node\Scalar\MagicConst\File;
use PhpParser\Node\Scalar\MagicConst\Line;
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

    public $magicVarMap = [];

    public $includeLibPath = [];

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

    protected $distPath;

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

        $app_adapter = new LocalFilesystemAdapter($app_path);
        $app_filesystem = new Filesystem($app_adapter);
        $this->appFilesystem = $app_filesystem;


        $dist_adapter = new LocalFilesystemAdapter($dist_path);
        $dist_filesystem = new Filesystem($dist_adapter);
        $this->distFilesystem = $dist_filesystem;


        $temp_adapter = new LocalFilesystemAdapter($temp_path);
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

        $this->log('编译混淆变量名');
        $this->packFakeVar();

        $this->log('编译魔术变量');
        $this->packMagickVar();
        $this->buildMagicVarMapFile();

        $this->log('函数库文件');
        // 根据配置function_path加载函数库并且编译成单独的文件
        $this->buildFunctionFile();

        $this->log('编译标准类库');
        // 根据配置pack_app扫描编译
        // 不标准的或排除的原样返回
        // 编译的最终将打包到一个文件中
        $this->packMainClassFile();
        $this->buildMainClassFile();



        $this->log('生成索引文件');
        $this->buildIncludeIndexFile();




        $this->log('输出到文件夹');
        $this->outputToDistApp();

        $this->log('生成TP多应用目录');
        $this->buildAllAppDir();

        $this->log('清理临时目录');
        $this->clearTempDir();

        $this->write('编译完成');
    }

    public function outputToDistApp()
    {
        $list_file = $this->tempFilesystem->listContents('', true);

        foreach ($list_file as  $item_file) {

            if ($item_file['type'] != 'file') {
                continue;
            }

            $this->distFilesystem->write($item_file['path'], $this->tempFilesystem->read($item_file['path']));
        }
    }
    public function clearDistDir()
    {

        $list_dist = $this->distFilesystem->listContents('/');

        foreach ($list_dist as  $file_info) {
            if ($file_info['type'] == 'file') {
                $this->distFilesystem->delete($file_info['path']);
            } else {
                $this->distFilesystem->deleteDirectory($file_info['path']);
            }
        }
    }
    public function clearTempDir()
    {
        $list_dist = $this->tempFilesystem->listContents('/');

        foreach ($list_dist as  $file_info) {
            if ($file_info['type'] == 'file') {
                $this->tempFilesystem->delete($file_info['path']);
            } else {
                $this->tempFilesystem->deleteDirectory($file_info['path']);
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

            $this->tempFilesystem->write($item_file['path'], $this->appFilesystem->read($item_file['path']));
        }
    }

    protected function clearCommentAndGenerateCopyright()
    {
        $list_file = $this->tempFilesystem->listContents('', true);


        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

        $pretty_printer = new PrettyPrinterTools();

        $target_include = $this->getAllInclude();
        $target_exclude = $this->getAllExclude();

        foreach ($list_file as $item_file) {
            $path = $item_file['path'];



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

            $this->tempFilesystem->write($path, $result_content);
        }
    }

    protected function getAllInclude()
    {
        return array_merge(
            Config::get('dist.include_path', []),
            Config::get('dist.pack_app.include_path', []),
        );
    }

    protected function getAllExclude()
    {
        return array_merge(
            Config::get('dist.exclude_path', []),
            Config::get('dist.pack_app.exclude_path', []),
        );
    }

    public function packClassUseName()
    {
        $list_file = $this->tempFilesystem->listContents('', true);


        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

        $pretty_printer = new PrettyPrinterTools();

        $target_include = $this->getAllInclude();

        $target_exclude = $this->getAllExclude();

        foreach ($list_file as $item_file) {
            $path = $item_file['path'];


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

            $this->tempFilesystem->write($path, $result_content);
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

        $path_info = pathinfo($item_file['path']);


        if (!isset($path_info['extension'])) {
            return false;
        }
        if ($path_info['extension'] != 'php') {
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

        $target_include = $this->getAllInclude();

        $target_exclude = $this->getAllExclude();
        foreach ($list_files as  $item_file) {
            $path = $item_file['path'];



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

            $this->tempFilesystem->write($path, $result_content);
        }

        if ($this->tempFilesystem->fileExists('.env')) {
            $this->tempFilesystem->delete('.env');
        }
    }
    public function packFakeVar()
    {


        $pack_vars_mode = Config::get('dist.pack_vars.pack_vars_mode');

        if ($pack_vars_mode == 1) {
            return;
        }

        $list_files = $this->tempFilesystem->listContents('', true);

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

        $pretty_printer = new  PrettyPrinterTools();

        $target_include = $this->getAllInclude();

        $target_exclude = $this->getAllExclude();


        if ($pack_vars_mode == 2) {
            $target_exclude = array_merge($target_exclude, Config::get('dist.pack_vars.controller_path', []));
        }

        foreach ($list_files as  $item_file) {
            $path = $item_file['path'];



            if (!$this->checkPregMatchPhp($target_include, $item_file) || $this->checkPregMatch($target_exclude, $path, false)) {
                continue;
            }

            $this->debug('编译: ' . $path);

            $file_content = $this->tempFilesystem->read($path);
            $file_stmts = $parser->parse($file_content);

            $fake_var_traverser = new NodeTraverser;
            $fake_var_traverser->addVisitor(
                new NodeFakeVarVisitorTools()
            );

            $file_stmts = $fake_var_traverser->traverse($file_stmts);

            // 生成代码
            $result_content = $pretty_printer->prettyPrintFile($file_stmts);

            $this->tempFilesystem->write($path, $result_content);
        }
    }

    public function packMagickVar()
    {
        $list_files = $this->tempFilesystem->listContents('', true);

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

        $pretty_printer = new  PrettyPrinterTools();

        $target_include = $this->getAllInclude();

        $target_exclude = $this->getAllExclude();


        foreach ($list_files as  $item_file) {
            $path = $item_file['path'];

            if (!$this->checkPregMatchPhp($target_include, $item_file) || $this->checkPregMatch($target_exclude, $path, false)) {
                continue;
            }

            $this->debug('编译: ' . $path);

            $file_content = $this->tempFilesystem->read($path);
            $file_stmts = $parser->parse($file_content);

            $magic_var_traverser = new NodeTraverser;
            $magic_var_traverser->addVisitor(
                new class($item_file['path'], $this) extends NodeVisitorAbstract
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
                            $const_key = 'ul' . uniqid();

                            $this->mainClass->magicVarMap[$const_key] = dirname($name);
                            return new ConstFetch(new Name($const_key));
                        } else if ($node instanceof File) {
                            $const_key = 'ul' . uniqid();
                            $this->mainClass->magicVarMap[$const_key] = $name;
                            return new ConstFetch(new Name($const_key));
                        } else if ($node instanceof Line) {
                            $const_key = 'ul' . uniqid();
                            $this->mainClass->magicVarMap[$const_key] = $node->getStartLine();
                            return new ConstFetch(new Name($const_key));
                        }
                    }
                }
            );

            $file_stmts = $magic_var_traverser->traverse($file_stmts);

            // 生成代码
            $result_content = $pretty_printer->prettyPrintFile($file_stmts);

            $this->tempFilesystem->write($path, $result_content);
        }
    }

    public function buildMagicVarMapFile()
    {
        $dir_const_stmts = [];

        foreach ($this->magicVarMap as $const_key => $const_value) {

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

        $dir_const_path = 'lib/' . uniqid() . '.php';

        $this->includeLibPath['magic_var_map'] = $dir_const_path;
        $this->tempFilesystem->write($dir_const_path, $dir_const_code);
    }


    public function packMainClassFile()
    {


        $list_content = $this->tempFilesystem->listContents('/', true);
        $target_include = Config::get('dist.pack_app.include_path', []);
        $target_exclude = Config::get('dist.pack_app.exclude_path', []);
        foreach ($list_content as  $file_info) {

            $file_path = $file_info['path'];
            
            if (!$this->checkPregMatchPhp($target_include, $file_info) || $this->checkPregMatch($target_exclude, $file_path, false)) {
                continue;
            }
            
            $file_content = $this->tempFilesystem->read($file_path);

            $file_content = $this->buildPhpContent($file_content, $file_path);

            if (empty($file_content)) {
                $this->tempFilesystem->delete($file_path);
            }
        }
    }

    public function buildMainClassFile()
    {
        $prettyPrinter = new PrettyPrinterTools();
        // 根据调用次数排序
        $this->parsePackList();

        $stmts = [];
        foreach ($this->packList as $class_name => $class_item) {
            foreach ($class_item['stmts'] as $stmt_item) {
                $stmts[] = $stmt_item;
            }
        }

        $newCode = $prettyPrinter->prettyPrintFile($stmts);

        $lib_class_file = 'lib/' . uniqid() . '.php';
        $this->includeLibPath['main_class_file'] = $lib_class_file;
        $this->tempFilesystem->write($lib_class_file, $newCode);
    }



    /**
     * 打包类库文件
     *
     * @param string $lib_function_path
     * @return void
     */
    public function buildFunctionFile()
    {
        $function_path = Config::get('dist.function_path', []);

        $function_stmts = [];

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

        foreach ($function_path as  $function_file) {


            $function_content = $this->tempFilesystem->read($function_file);

            $stmts = $parser->parse($function_content);

            $traverser = new NodeTraverser();
            $traverser->addVisitor(new class extends NodeVisitorAbstract
            {

                public function leaveNode(Node $node)
                {

                    if ($node->hasAttribute('comments')) {
                        $node->setAttribute('comments', []);
                    }
                }
            });



            $stmts = $traverser->traverse($stmts);

            foreach ($stmts as  $stmt_item) {
                $function_stmts[] = $stmt_item;
            }

            $this->tempFilesystem->delete($function_file);
        }


        $traverser = new NodeTraverser();

        $traverser->addVisitor(new NodeFakeVarVisitorTools);

        $function_stmts = $traverser->traverse($function_stmts);

        $prettyPrinter = new  MinifyPrinterTools();

        $function_code = $prettyPrinter->prettyPrintFile($function_stmts);

        $lib_path = 'lib/' . uniqid() . '.php';
        $this->includeLibPath['function_lib_file'] = $lib_path;
        $this->tempFilesystem->write($lib_path, $function_code);
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

            // if ($this->distFilesystem->($item['path'])) {
            //     continue;
            // }

            $this->distFilesystem->createDirectory($item['path']);
        }
    }

    /**
     * 生成引入文件
     *
     * @param string[] $files
     * @return void
     */
    public function buildIncludeIndexFile()
    {
        $file_stmts = [];

        $file_stmts[] = new Expression(new FuncCall(
            new Name('define'),
            [
                new Arg(new String_('ULTHON_ADMIN_BUILD_DIST')),
                new Arg(new String_('1'))
            ]
        ));
        $include_path = [];

        $include_path[] = $this->includeLibPath['magic_var_map'];
        $include_path[] = $this->includeLibPath['function_lib_file'];
        $include_path[] = $this->includeLibPath['main_class_file'];

        foreach ($include_path as  $path) {
            $_path = substr($path, 3);
            $file_stmts[] = new Expression(new Include_(new Concat(new Dir, new String_($_path)), Include_::TYPE_REQUIRE_ONCE));
        }

        $prettyPrinter = new  MinifyPrinterTools();


        $newCode = $prettyPrinter->prettyPrintFile($file_stmts);

        $this->tempFilesystem->write('lib/index.php', $newCode);
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
     * @return void|string
     */
    public function buildPhpContent($content, $name)
    {

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

        $stmts = $parser->parse($content);

        try {
            $this->checkStmts($stmts, $name);
        } catch (\Throwable $th) {
            return $content;
        }

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
}
