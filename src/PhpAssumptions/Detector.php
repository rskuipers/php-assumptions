<?php

namespace PhpAssumptions;

use PhpParser\Node;
use PhpParser\Node\Expr;

class Detector
{
    /**
     * @param Node $node
     * @return bool
     */
    public function scan(Node $node)
    {
        $result = false;

        if ($node instanceof Expr\BinaryOp\NotIdentical || $node instanceof Expr\BinaryOp\NotEqual
            || $node instanceof Expr\BinaryOp\Equal
        ) {
            if ($this->bidirectionalCheck($node, Expr\Variable::class, Expr\ConstFetch::class)) {
                $result = true;
            } elseif (!$node instanceof Expr\BinaryOp\NotIdentical
                && $this->bidirectionalCheck($node, Expr\Variable::class, Node\Scalar::class)
            ) {
                $result = true;
            }
        } elseif (($node instanceof Expr\BooleanNot && $node->expr instanceof Expr\Variable)
            || property_exists($node, 'cond') && $node->cond instanceof Expr\Variable
        ) {
            $result = true;
        }

        return $result;
    }

    /**
     * @param Expr $condition
     * @param string $left
     * @param string $right
     * @return bool
     */
    private function bidirectionalCheck(Expr $condition, $left, $right)
    {
        return ($this->isInstanceOf($condition->left, $left) && $this->isInstanceOf($condition->right, $right))
            || ($this->isInstanceOf($condition->right, $left) && $this->isInstanceOf($condition->left, $right));
    }

    /**
     * @param object $object
     * @param string $class
     * @return bool
     */
    private function isInstanceOf($object, $class)
    {
        return get_class($object) === $class || is_subclass_of($object, $class);
    }
}
