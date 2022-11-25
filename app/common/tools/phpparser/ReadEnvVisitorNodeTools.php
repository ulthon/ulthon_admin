<?php

namespace app\common\tools\phpparser;

use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Cast\Bool_;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class ReadEnvVisitorNodeTools  extends NodeVisitorAbstract
{
    protected $path;

    protected $skipFiles = [
        'app/common/tools/phpparser/ReadEnvVisitorNodeTools.php'
    ];

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function enterNode(Node $node)
    {
        if (in_array($this->path, $this->skipFiles)) {
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }
    }

    public function leaveNode(Node $node)
    {


        if ($node instanceof FuncCall) {
            if ($node->name instanceof Name) {
                $function_name = $node->name->toString();

                if ($function_name == 'env') {

                    $env_arg_1 = $this->getArg($node, 0);
                    $env_arg_2 = $this->getArg($node, 1);

                    $env_value = env($env_arg_1, $env_arg_2);

                    $return_node = $this->returnEnvValue($env_value);
                    return $return_node;
                }
            }
        } else if ($node instanceof StaticCall) {

            if ($node->class instanceof FullyQualified) {
                $class_name = $node->class->toString();
                if ($class_name == 'think\\facade\\Env') {
                    if ($node->name instanceof Identifier) {
                        if ($node->name == 'get') {
                            $env_arg_1 = $this->getArg($node, 0);
                            $env_arg_2 = $this->getArg($node, 1);

                            $env_value = env($env_arg_1, $env_arg_2);

                            $return_node = $this->returnEnvValue($env_value);
                            return $return_node;
                        }
                    }
                }
            }
        }
    }

    public function getArg($node, $index)
    {
        $env = null;
        if (isset($node->args[$index])) {
            if ($node->args[$index]->value instanceof String_) {
                $env = $node->args[$index]->value->value;
            } else {
                // 发现非字符串方式读取，应当提示
            }
        }

        return $env;
    }

    public function returnEnvValue($value)
    {
        if (is_array($value)) {
            return new Array_($value);
        } else if (is_string($value)) {
            return new String_($value);
        } else if (is_integer($value)) {
            return new LNumber($value);
        } else if (is_float($value)) {
            return new DNumber($value);
        } else if (is_bool($value)) {
            if ($value) {
                return new ConstFetch(new Name('true'));
            } else {
                return new ConstFetch(new Name('false'));
            }
        }
    }
}
