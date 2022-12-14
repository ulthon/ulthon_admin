<?php

namespace app\common\tools\phpparser;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\ClassMethod;

class FindClassNodeVisitorTools extends NodeVisitorAbstract
{

    public function leaveNode(Node $node)
    {
        if ($node instanceof Class_) {
            if (!empty($node->extends)) {
                $this->findClassNodeName($node->extends);
            }

            if (!empty($node->implements)) {
                foreach ($node->implements as &$node_implements) {
                    $used_class_str = $node_implements->toString();
                    $this->findClassNodeName($node_implements);
                }
            }
        } else if (
            $node instanceof StaticCall ||
            $node instanceof New_ ||
            $node instanceof ClassConstFetch ||
            $node instanceof Instanceof_ ||
            $node instanceof StaticPropertyFetch
        ) {
            if ($node->class instanceof Variable) {
                return;
            }

            if ($node->class instanceof Class_) {
                return;
            }

            if ($node->class instanceof PropertyFetch) {

                if ($node->class->var->name == 'this') {
                    return;
                }
            }

            $used_class_str = $node->class->toString();

            if ($used_class_str != 'static' && $used_class_str != 'self' && $used_class_str != 'parent') {
                $this->findClassNodeName($node->class);
            }
        } else if ($node instanceof Param) {
            if (is_null($node->type)) {
                return;
            }

            if ($node->type instanceof Identifier) {
                return;
            }

            $this->findClassNodeName($node->type);
        } else if ($node instanceof TraitUse) {
            foreach ($node->traits as  &$node_name) {
                $used_class_str = $node_name->toString();

                $this->findClassNodeName($node_name);
            }
        } else if ($node instanceof Catch_) {
            foreach ($node->types as  &$node_name) {
                $this->findClassNodeName($node_name);
            }
        } else if ($node instanceof ClassMethod) {

            if (empty($node->returnType)) {
                return;
            }

            if ($node->returnType instanceof Identifier) {
                return;
            }

            $this->findClassNodeName($node->returnType);
        }
    }

    public function findClassNodeName(Name &$name)
    {
    }
}
