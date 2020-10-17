<?php

namespace ModifyRecord\Contract;

interface WithOriginData
{
    /**
     * 设置原始数据格式
     * @return array
     */
    public function originData(): array ;
}
