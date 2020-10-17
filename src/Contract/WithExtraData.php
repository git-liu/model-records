<?php

namespace ModifyRecord\Contract;

interface WithExtraData
{
    /**
     * 添加额外数据
     * @return array
     */
    public function extraData(): array ;
}
