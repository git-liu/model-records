<?php


namespace ModifyRecord\Resources;


use Illuminate\Http\Resources\Json\ResourceCollection;
use ModifyRecord\Models\TableLogContent;

class RecordListResource extends ResourceCollection
{

    public function toArray($request)
    {
        $config = config('modify_record');
        
        return $this->collection->map(function ($item) use ($config) {
            return [
                'id' => $item->id,
                'table_name' => $item->table_name,
                'table_id' => $item->table_id,
                'operator' => $item->operator,
                'title' => $item->title,
                'comment' => $item->comment,
                'modify_type' => $item->modify_type,
                'contents' => new RecordContentResource($item->contents),
                'created_at' => $item->created_at->toDateTimeString(),
                'updated_at' => $item->updated_at->toDateTimeString()
            ];
        });
    }
}
