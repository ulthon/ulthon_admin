<?php

namespace app\common\tools\phpparser;

use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Cast\Bool_;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeVisitorAbstract;
class ReadEnvVisitorNodeTools extends \PhpParser\NodeVisitorAbstract
{
    public function leaveNode(\PhpParser\Node $node)
    {
        if ($node instanceof \PhpParser\Node\Expr\FuncCall) {
            if ($node->name instanceof \PhpParser\Node\Name\FullyQualified) {
                $function_name = $node->name->toString();
                if ($function_name == 'env') {
                    dump($node);
                    $env_arg_1 = $this->getArg($node, 0);
                    $env_arg_2 = $this->getArg($node, 1);
                    $env_value = env($env_arg_1, $env_arg_2);
                    return $this->returnEnvValue($env_value);
                }
            }
        }
    }
    public function getArg($node, $index)
    {
        $env = null;
        if (isset($node->args[$index])) {
            if ($node->args[$index]->value instanceof \PhpParser\Node\Scalar\String_) {
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
            return new \PhpParser\Node\Expr\Array_($value);
        } else {
            if (is_string($value)) {
                return new \PhpParser\Node\Scalar\String_($value);
            } else {
                if (is_integer($value)) {
                    return new \PhpParser\Node\Scalar\LNumber($value);
                } else {
                    if (is_float($value)) {
                        return new \PhpParser\Node\Scalar\DNumber($value);
                    } else {
                        if (is_bool($value)) {
                            if ($value) {
                                return new \PhpParser\Node\Expr\ConstFetch(new \PhpParser\Node\Name('true'));
                            } else {
                                return new \PhpParser\Node\Expr\ConstFetch(new \PhpParser\Node\Name('false'));
                            }
                        }
                    }
                }
            }
        }
    }
}