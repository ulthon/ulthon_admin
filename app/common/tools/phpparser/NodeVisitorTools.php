<?php

namespace app\common\tools\phpparser;

use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\Stmt\TraitUseAdaptation\Alias;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use think\helper\Str;

class NodeVisitorTools extends NodeVisitorAbstract
{
    protected $cmd;
    protected $name;


    public $usedClass = [];

    public $callClass = [];
    protected $skipClass = [
        // 'Doctrine\Common\Annotations\Annotation\Attributes',
        // 'app\admin\service\annotation\ControllerAnnotation',
        // 'app\admin\service\annotation\NodeAnotation',
    ];

    public function __construct($cmd, $name)
    {
        $this->cmd = $cmd;
        $this->name = $name;
    }

    public function leaveNode(Node $node)
    {

        if ($node instanceof Stmt) {
            if (isset($node->attributes['comments'])) {

                $comments = $node->attributes['comments'];
                $new_comments = [];
                foreach ($comments as  $comment_item) {
                    if ($comment_item instanceof Comment) {
                    } else {
                        $new_comments[] = $comment_item;
                    }
                }

                $node->attributes['comments'] = $new_comments;
            }
        }

        if ($node instanceof Use_) {

            foreach ($node->uses as  &$use_item) {

                $name = 'class';

                $name .= md5($this->name);

                $used_class_str = implode('\\', $use_item->name->parts);


                if (in_array($used_class_str, $this->skipClass)) {
                    return;
                }

                $name .= md5($used_class_str);

                $this->usedClass[$used_class_str] = $name;
                if (!empty($use_item->alias->name)) {
                    $this->usedClass[$use_item->alias->name] = $name;
                }

                $use_item->alias = new Identifier($name);
            }
        } else if ($node instanceof Class_) {

            if (!empty($node->extends)) {


                $used_class_str = $node->extends->toString();
                $result_name = $this->findClassName($used_class_str);
                if (!empty($result_name)) {
                    $node->extends = new Name($result_name);
                }
            }

            if (!empty($node->implements)) {
                foreach ($node->implements as &$node_implements) {
                    $used_class_str = implode('\\', $node_implements->parts);

                    foreach ($this->usedClass as $class_name => $class_name_md5) {
                        if (Str::endsWith($class_name, '\\' . $used_class_str)) {
                            $node_implements = new Name($class_name_md5);
                        }
                    }
                }
            }
        } else if (
            $node instanceof StaticCall ||
            $node instanceof New_ ||
            $node instanceof ClassConstFetch ||
            $node instanceof Instanceof_
        ) {

            if ($node->class instanceof Variable) {
                return;
            }

            if ($node->class instanceof Class_) {
                return;
            }


            $used_class_str = $node->class->toString();

            if ($used_class_str != 'static' && $used_class_str != 'self' && $used_class_str != 'parent') {
                $result_name = $this->findClassName($used_class_str);

                if (!empty($result_name)) {
                    $node->class = new Name($result_name);
                }
            }
        } else if ($node instanceof Param) {


            if (is_null($node->type)) {
                return;
            }

            if ($node->type instanceof Identifier) {
                return;
            }

            // dump($node);
            $used_class_str = $node->type->toString();

            $result_name = $this->findClassName($used_class_str);

            if (!empty($result_name)) {


                $node->type = new Name($result_name);
            }
        } else if ($node instanceof Declare_) {
            return NodeTraverser::REMOVE_NODE;
        } else if ($node instanceof TraitUse) {
            foreach ($node->traits as  &$node_name) {
                $used_class_str = $node_name->toString();

                $result_name = $this->findClassName($used_class_str);
                if (!empty($result_name)) {
                    $node_name = new Name($result_name);
                }
            }
        } else if ($node instanceof Catch_) {
            foreach ($node->types as  &$node_name) {
                $used_class_str = $node_name->toString();

                $result_name = $this->findClassName($used_class_str);

                if (!empty($result_name)) {
                    $node_name = new Name($result_name);
                }
            }
        } else if ($node instanceof ClassMethod) {

            if (empty($node->returnType)) {
                return;
            }

            if ($node->returnType instanceof Identifier) {
                return;
            }

            $used_class_str = $node->returnType->toString();
            $result_name = $this->findClassName($used_class_str);

            if (!empty($result_name)) {

                $node->returnType = new Name($result_name);
            }
        }

        return null;
    }

    public function findClassName($class_name_str)
    {
        $name = null;
        foreach ($this->usedClass as $class_name => $class_name_md5) {

            $class_name_arr = explode('\\', $class_name);

            $class_name_str_arr = explode('\\', $class_name_str);

            $class_name_str_arr = array_reverse($class_name_str_arr);

            $last_index = 0;
            foreach ($class_name_str_arr as $class_item) {
                $last_class_item = array_pop($class_name_arr);
                if ($last_class_item != $class_item) {
                    break;
                }

                $last_index++;
            }

            if ($last_index == count($class_name_str_arr)) {
                $name = $class_name_md5;
                break;
            }
        }
        return $name;
    }
}
