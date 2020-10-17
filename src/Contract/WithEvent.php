<?php


namespace ModifyRecord\Contract;

interface WithEvent
{
    /**
     * 注册事件
     * @return mixed
     */
    public function events(): array;
}
