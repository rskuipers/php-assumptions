<?php

namespace PhpAssumptions;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

class Detector
{
    /**
     * @param Node $node
     * @return bool
     */
    public function scan(Node $node)
    {
        if (($node instanceof Stmt\Expression)) {
            $node = $node->expr;
        }

        if (($node instanceof Expr\BinaryOp\BooleanOr || $node instanceof Expr\BinaryOp\BooleanAnd)
            && $this->bidirectionalCheck($node, Expr\Variable::class, Expr\BinaryOp::class)
        ) {
            return true;
        }

        if ($node instanceof Expr\BinaryOp\Equal || $node instanceof Expr\BinaryOp\NotEqual
            || $node instanceof Expr\BinaryOp\NotIdentical
        ) {
            return true;
        }

        if ($this->isVariableExpression($node)) {
            return true;
        }

        return false;
    }

    /**
     * @param Node $node
     * @return bool
     */
    public function isBoolExpression(Node $node)
    {
        if ($node instanceof Expr\Ternary || $node instanceof Stmt\If_
            || $node instanceof Stmt\ElseIf_ || $node instanceof Stmt\While_
            || $node instanceof Expr\BinaryOp\BooleanAnd || $node instanceof Expr\BinaryOp\BooleanOr
            || $node instanceof Stmt\For_
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param Node $node
     * @return bool
     */
    private function isVariableExpression(Node $node)
    {
        if ($node instanceof Expr\BooleanNot && $node->expr instanceof Expr\Variable) {
            return true;
        }

        if ($node instanceof Expr\Ternary || $node instanceof Stmt\If_
            || $node instanceof Stmt\ElseIf_ || $node instanceof Stmt\While_
            || $node instanceof Stmt\For_
        ) {
            if ($node->cond instanceof Expr\Variable) {
                return true;
            }

            if (is_array($node->cond)) {
                foreach ($node->cond as $condition) {
                    if ($condition instanceof Expr\Variable) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param Expr   $condition
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
