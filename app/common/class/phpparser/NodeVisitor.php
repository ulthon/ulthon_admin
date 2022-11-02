<?php

namespace app\common\class\phpparser;

use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\TraitUseAdaptation\Alias;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class NodeVisitor extends NodeVisitorAbstract
{
    protected $cmd;
    protected $name;
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

                $name .= md5(end($use_item->name->parts));

                $use_item->alias = new Identifier($name);
            }
        }else if ($node instanceof Class_){
        
            
            if(!empty($node->extends)){

                $name = 'class';

                $name .= md5($this->name);

                $name .= md5(end($node->extends->parts));

                $node->extends = new Name($name);
            }
        }

        return null;
    }
}
