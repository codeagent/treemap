<?php
namespace codeagent\treemap;

use \Closure;

interface IPresenter
{
    /**
     * @param Closure|null $renderrer
     * @return mixed
     */
    public function render(Closure $renderrer = null);

}