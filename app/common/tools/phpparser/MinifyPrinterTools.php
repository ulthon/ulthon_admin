<?php

namespace app\common\tools\phpparser;


class MinifyPrinterTools extends PrettyPrinterTools
{
    /**
     * Reset pretty printing state.
     */
    protected function resetState()
    {
        $this->indentLevel = 0;
        $this->nl = "";
        $this->origTokens = null;
    }

    /**
     * Set indentation level
     *
     * @param int $level Level in number of spaces
     */
    protected function setIndentLevel(int $level)
    {
        $this->indentLevel = $level;
        $this->nl = "" . \str_repeat('', $level);
    }

    /**
     * Increase indentation level.
     */
    protected function indent()
    {
        $this->indentLevel += 4;
        $this->nl .= '';
    }

    /**
     * Decrease indentation level.
     */
    protected function outdent()
    {
        assert($this->indentLevel >= 4);
        $this->indentLevel -= 4;
        $this->nl = "" . str_repeat('', $this->indentLevel);
    }
}
