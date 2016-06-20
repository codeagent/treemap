<?php
namespace codeagent\treemap\commands;

trait Utils
{
    /**
     * @see http://php.net/manual/ru/function.filesize.php#106569
     * @param $bytes
     * @param int $decimals
     * @return string
     */
    public function human_filesize($bytes, $decimals = 2)
    {
        $sz     = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }
}