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
            $contents = $item->contents->map(function ($content) use ($item, $config) {
                if (! empty($config['modelRouting'][$item->model])) {
                    $mappingClass = $config['modelRouting'][$item->model];
                } else {
                    $mappingClass = $config['modelMappings'][$item->model] ?? [];
                }
                $mappings = [];
                foreach ($mappingClass as $class) {
                    array_push($mappings, new $class(new $item->model));
                }
                $content->setAttribute('mappings', $mappings);
                $content->setAttribute('model', $item->model);
    
                return $content;
            });
            
            return [
                'id' => $item->id,
                'table_name' => $item->table_name,
                'table_id' => $item->table_id,
                'operator' => $item->operator,
                'title' => $item->title,
                'comment' => $item->comment,
                'modify_type' => $item->modify_type,
                'contents' => new RecordContentResource($contents),
                'created_at' => $item->created_at->toDateTimeString(),
                'updated_at' => $item->updated_at->toDateTimeString()
            ];
        });
    }
}
