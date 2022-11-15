<?php

namespace app\common\class\phpparser;

use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\TraitUseAdaptation\Alias;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use think\helper\Str;

class NodeVisitor extends NodeVisitorAbstract
{
    protected $cmd;
    protected $name;


    public $usedClass = [];

    public $callClass = [];

    public function __construct($cmd, $name)
    {
        $this->cmd = $cmd;
        $this->name = $name;
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Use_) {

            // $this->cmd->addUsedClass($node);

            // return NodeTraverser::REMOVE_NODE;


            foreach ($node->uses as  &$use_item) {

                $name = 'class';

                $name .= md5($this->name);

                $used_class_str = implode('\\', $use_item->name->parts);

                $name .= md5($used_class_str);

                $this->usedClass[$used_class_str] = $name;
                if (!empty($use_item->alias->name)) {
                    $this->usedClass[$use_item->alias->name] = $name;
                }

                $use_item->alias = new Identifier($name);
            }
        } else if ($node instanceof Class_) {

            if (!empty($node->extends)) {


                $used_class_str = implode('\\', $node->extends->parts);

                foreach ($this->usedClass as $class_name => $class_name_md5) {
                    if (Str::endsWith($class_name, $used_class_str)) {
                        $node->extends = new Name($class_name_md5);
                    }
                }
            }
        } else if ($node instanceof StaticCall || $node instanceof New_) {


            if($node->class instanceof Variable){
                return;
            }

            if($node->class instanceof Class_){
                return;
            }
            // exit;

            $used_class_str = implode('\\', $node->class->parts);
            

           

            if ($used_class_str != 'static' && $used_class_str != 'self' && $used_class_str != 'parent') {
                $is_replaced = false;
                foreach ($this->usedClass as $class_name => $class_name_md5) {
                    if (Str::endsWith($class_name, $used_class_str)) {
                        $is_replaced = true;
                        $node->class = new Name($class_name_md5);
                    }
                }
                if (!$is_replaced) {
                    // dump($this->name);
                    // dump($used_class_str);
                    // dump($this->usedClass);
                    // dump($node);
                }
            }
        }else if($node instanceof Param){
           

            if(is_null($node->type)){
                return;
            }

            if($node->type instanceof Identifier){
                return;
            }

         
            $used_class_str = implode('\\', $node->type->parts);

            foreach ($this->usedClass as $class_name => $class_name_md5) {
                if (Str::endsWith($class_name, $used_class_str)) {
                    $is_replaced = true;
                    $node->type = new Name($class_name_md5);
                }
            }

        }

        return null;
    }
}
