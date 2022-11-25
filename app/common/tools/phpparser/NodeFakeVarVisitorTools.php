<?php

namespace app\common\tools\phpparser;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Global_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class NodeFakeVarVisitorTools extends NodeVisitorAbstract
{

    protected static $varNameMap = [];

    protected $skipVariableName = [
        'GLOBALS',
        '_SERVER',
        '_REQUEST',
        '_POST',
        '_GET',
        '_FILES',
        '_ENV',
        '_COOKIE',
        '_SESSION',
        'this'
    ];

    public function enterNode(Node $node)
    {
        if ($node instanceof ClassMethod) {
            if ($node->name instanceof Identifier) {
                if ($node->name->name == '__construct') {
                    return NodeTraverser::DONT_TRAVERSE_CHILDREN;
                }
            }
        }
    }

    public function leaveNode(Node $node)
    {

        if ($node instanceof Variable) {
            if (is_string($node->name)) {

                if (in_array($node->name, $this->skipVariableName)) {
                    return;
                }

                if (!isset($this::$varNameMap[$node->name])) {
                    $var_name = 'ul' . uniqid();
                    $this::$varNameMap[$node->name] = $var_name;
                }

                $node->name = $this::$varNameMap[$node->name];
                return $node;
            }
        }
    }
}
