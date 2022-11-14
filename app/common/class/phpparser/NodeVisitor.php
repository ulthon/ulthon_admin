<?php

namespace app\common\class\phpparser;

use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
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
        } else if ($node instanceof Expression) {
            if (
                $node->expr instanceof StaticCall ||
                $node->expr instanceof New_
            ) {
                $used_class_str = implode('\\', $node->expr->class->parts);
                if ($used_class_str != 'static' && $used_class_str != 'self' && $used_class_str != 'parent') {
                    $is_replaced = false;
                    foreach ($this->usedClass as $class_name => $class_name_md5) {
                        if (Str::endsWith($class_name, $used_class_str)) {
                            $is_replaced = true;
                            $node->expr->class = new Name($class_name_md5);
                        }
                    }
                    if(!$is_replaced){
                        dump($node);
                    }
                }

            }
        }

        return null;
    }
}
