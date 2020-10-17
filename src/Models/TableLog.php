<?php


namespace ModifyRecord\Models;


use Illuminate\Database\Eloquent\Model;

class TableLog extends Model
{
    public $table = 'tb_log';
    
    public $guarded = ['id'];
    
    public $casts = [
        'mapping_class' => 'array'
    ];
    
    public function contents()
    {
        return $this->hasMany(TableLogContent::class, 'log_id', 'id');
    }
    
    public function operator()
    {
        $auth = config('auth');
        
        $provider = $auth['guards'][config('modify_record.auth')]['provider'];
        
        return $this->hasOne($auth['providers'][$provider]['model'], 'id', 'user_id');
    }
}
