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
        if ($this->isWeakCondition($node)) {
            $checks = [
                [Expr\Variable::class, Expr\ConstFetch::class],
                [Expr\Variable::class, Node\Scalar::class],
            ];

            /** @var Expr $node */
            foreach ($checks as $check) {
                if ($this->bidirectionalCheck($node, $check[0], $check[1])) {
                    return true;
                }
            }
        }

        return false;
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

    /**
     * @param Node $node
     * @return bool
     */
    private function isWeakCondition(Node $node)
    {
        return $node instanceof Expr\BinaryOp\NotIdentical || $node instanceof Expr\BinaryOp\NotEqual
            || $node instanceof Expr\BinaryOp\Equal;
    }
}
