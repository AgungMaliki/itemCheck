<?php

namespace App\Http\Controllers;
use App\Items;
use App\Checklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
        $this->middleware('auth');
    }

    public function index(){
        // items
        $data['data'] = items::all();
        return response()->json($data);
    }

    public function update_checklist(Request $r, $checkId, $itemId){
        $url = URL::current();
        $res = $r->input('data');
        $data = Items::where('checklist_id', $checkId)
        ->where('id', $itemId);

        $updated = $data->update([
            'description' => $res['attribute']['description'],
            'due' => $res['attribute']['due'],
            'urgency' => $res['attribute']['urgency'],
        ]);

        $item = Items::where('checklist_id', $checkId)
        ->where('id', $itemId)->first();

        if($updated){
            return response()->json([
                'success' => true,
                'message' => 'Updating Items Success!',
                'data' => [
                    'type' => 'checklist',
                    'id' => $itemId,
                    'attributes' => $item,
                    'link' => $url
                ]
            ], 201);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Updating Items Fail!',
                'data' => ''
            ], 400);
        }
    }

    public function create_checklist(Request $r){
        $url = URL::current();
        $data = $r->input('data');
        $items = new Items;
        $items->description = $data['attribute']['description'];
        $items->due = $data['attribute']['due'];
        $items->urgency = $data['attribute']['due'];
        $items->is_completed = '0';
        $items->checklist_id = '0';
        $items->urgency = '0';
        $items->save();
        if($items){
            return response()->json([
                'success' => true,
                'message' => 'Adding Items Success!',
                'data' => [
                    'type' => 'checklist',
                    'id' => $items->id,
                    'attributes' => $items,
                    'link' => $url
                ]
            ], 201);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Adding Items Fail!',
                'data' => ''
            ], 400);
        }
    }

    public function getCheckList($checkId){
        $url = URL::current();
        $data = Checklist::select('*')
            ->where('id', $checkId)->first();

        $items = Items::select('*')
            ->where('checklist_id', $checkId)->get();

        if($data->is_completed == 1){
            $data->is_completed = true;
        }else{
            $data->is_completed = false;
        }
        
        if($data){
            return response()->json([
                'success' => true,
                'message' => 'Item ditemukan',
                'data' => [
                    'type' => 'checklists',
                    'id' => $data->id,
                    'attributes' => [
                        'description' => $data->description,
                        'is_completed' => $data->is_completed,
                        'due' => $data,
                        'items' => $items
                    ],
                    'links' => [
                        'self' => $url
                    ]
                ]
            ], 200);
        }
    }

    public function getChecklistByItemId($checkId, $itemId){
        $url = URL::current();
        $data = Items::select('*')
            ->where('checklist_id', $checkId)
            ->where('id', $itemId)->first();
        
        if($data){
            return response()->json([
                'success' => true,
                'message' => 'Item ditemukan',
                'data' => [
                    'type' => 'checklists',
                    'attributes' => $data,
                    'links' => [
                        'self' => $url
                    ]
                ]
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan',
                'data' => ''
            ], 404);
        }
    }

    public function GetById($id){
        $data = Items::find($id);
        if($data){
            return response()->json([
                'success' => true,
                'message' => 'Item ditemukan',
                'data' => $data
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan',
                'data' => ''
            ], 404);
        }
    }

    //
}
