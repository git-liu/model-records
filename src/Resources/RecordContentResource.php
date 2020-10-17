<?php


namespace ModifyRecord\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RecordContentResource extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            return [
                'id' => $item->id,
                'log_id' => $item->log_id,
                'tb_key' => $item->tb_key,
                'tb_zh_key' => $item->tb_zh_key,
                'origin_value' => $item->tb_value,
                'current_value' => $item->current_tb_value,
                'field_type' => $item->field_type,
            ];
        });
    }
}
