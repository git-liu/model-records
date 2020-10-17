<?php


namespace ModifyRecord\Controllers;


use App\Http\Controllers\Controller;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ModifyRecord\Resources\RecordListResource;

class RecordController extends Controller
{
    /**
     * 变更记录列表
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function index(Request $request)
    {
        $validator = validator($request->all(), [
            'table_name' => 'nullable|string',
            'table_id' => 'nullable|integer',
            'user_id' => 'nullable|integer'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'code' => -1000,
                'msg' => '参数错误',
                'result' => ''
            ]);
        }
        
        $logClass = config('modify_record.log');
        if (! class_exists($logClass)) {
            throw new Exception('变更记录表不存在');
        }
        $records = $logClass::when($request->filled('table_name'), function ($query) use ($request) {
                $query->where('table_name', $request->get('table_name'));
            })
            ->when($request->filled('table_id'), function ($query) use ($request) {
                $query->where('table_id', $request->get('table_id'));
            })
            ->when($request->filled('user_id'), function ($query) use ($request) {
                $query->where('user_id', $request->get('user_id'));
            })
            ->with([
                'contents',
                'operator'
            ])
            ->paginate($request->get('page_size', 10));
        
        return response()->json([
            'code' => 1,
            'msg' => '成功',
            'result' => new RecordListResource($records)
        ]);
    }
    
    /**
     * 测试变更记录
     * @param Request $request
     * @return JsonResponse
     */
    public function test(Request $request)
    {
        $user = User::first();
        
        $user->save();
        
        return response()->json([
            'code' => 1,
            'msg' => '成功',
            'result' => ''
        ]);
    }
    
}
