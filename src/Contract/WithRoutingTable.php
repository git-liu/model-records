<?php


namespace ModifyRecord\Contract;


interface WithRoutingTable
{
    /**
     * 需要挂载的表名称
     * @return string
     */
    public function table(): string ;
    
    /**
     * 需要挂载的数据行id
     * @return int
     */
    public function tableId(): int ;
}
