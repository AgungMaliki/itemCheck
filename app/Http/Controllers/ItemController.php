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

    public function completed(Request $r){
        foreach($r->data as $id){
            // dd($r->data);
        }
    }

    public function delete_checklist($checkId){

        $check = Checklist::where('id', $checkId)->first();
        if(!$check){
            return response()->json([
                'success' => false,
                'message' => 'Not valid owned checklist ID!',
            ], 400);
        }else{
            // $check = $check->first();
            if(isset($check)){
                $del = $check->delete();
                if($del){
                    return response()->json([
                        'success' => true,
                        'message' => 'Deleting Checklist Success!',
                    ], 200);
                }else{
                    return response()->json([
                        'success' => true,
                        'message' => 'Deleting Checklist Fails!',
                    ], 400);
                }
            }else{
                return response()->json([
                    'success' => true,
                    'message' => 'Checklist Undefined',
                ], 400);
            }
        }
        
    }

    public function delete_checklist_item($checkId, $itemId){

        $check = Checklist::find($checkId);
        if(!$check){
            return response()->json([
                'success' => false,
                'message' => 'Not valid owned checklist ID!',
            ], 400);
        }else{
            $item = Items::where('id', $itemId)
            ->where('id', $itemId)->first();
    
            if(isset($item)){
                $del = $item->delete();
                if($del){
                    return response()->json([
                        'success' => true,
                        'message' => 'Deleting Items Success!',
                    ], 200);
                }else{
                    return response()->json([
                        'success' => true,
                        'message' => 'Deleting Items Fails!',
                    ], 400);
                }
            }else{
                return response()->json([
                    'success' => true,
                    'message' => 'Items Undefined',
                ], 400);
            }
        }
        
    }

    public function update(Request $r, $checkId){
        $check = Checklist::where('id', $checkId)->first();
        
        if(!$check){
            return response()->json([
                'success' => false,
                'message' => 'Not valid owned checklist ID!',
            ], 400);
        }

        $url = URL::current();
        $res = $r->input('data');
        $data = Checklist::where('id', $checkId);

        $updated = $data->update([
            'object_domain' => $res['attributes']['object_domain'],
            'object_id' => $res['attributes']['object_id'],
            'description' => $res['attributes']['description'],
        ]);

        if($updated){
            return response()->json([
                'success' => true,
                'message' => 'Updating Checklists Success!',
                'data' => [
                    'type' => 'checklist',
                    'id' => $checkId,
                    'attributes' => $check,
                    'link' => $url
                ]
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Updating Checklists Fail!',
                'data' => ''
            ], 400);
        }
    }

    public function update_checklist(Request $r, $checkId, $itemId){
        $check = Checklist::where('id', $checkId)->first();
        
        if(!$check){
            return response()->json([
                'success' => false,
                'message' => 'Not valid owned checklist ID!',
            ], 400);
        }

        $url = URL::current();
        $res = $r->input('data');
        $data = Items::where('id', $itemId);

        $updated = $data->update([
            'description' => $res['attribute']['description'],
            'due' => $res['attribute']['due'],
            'urgency' => $res['attribute']['urgency'],
        ]);

        $item = Items::where('id', $itemId)->first();

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
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Updating Items Fail!',
                'data' => ''
            ], 400);
        }
    }

    public function storeChecklist(Request $r){
        $url = URL::current();
        $data = $r->input('data');
        $checklist = new Checklist;
        $checklist->object_domain = $data['attributes']['object_domain'];
        $checklist->object_id = $data['attributes']['object_id'];
        $checklist->description = $data['attributes']['description'];
        $checklist->due = isset($data['attributes']['due']) ? $data['attributes']['due'] : '';
        $checklist->urgency = isset($data['attributes']['urgency']) ? $data['attributes']['urgency'] : '1';
        $checklist->save();
        if($checklist){
            return response()->json([
                'success' => true,
                'message' => 'Adding checklist Success!',
                'data' => [
                    'type' => 'checklist',
                    'id' => $checklist->id,
                    'attributes' => $checklist,
                    'link' => $url
                ]
            ], 201);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Adding checklist Fail!',
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
        $items->is_completed = false;
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

    public function getCheckListItems($checkId){
        $url = URL::current();
        $data = Checklist::select('*')
            ->where('id', $checkId)->first();

        if(!$data){
            return response()->json([
                'success' => false,
                'message' => 'Not valid owned checklist ID!',
            ], 400);
        }

        $items = Items::all();
        $data->items = $items;

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
                    'attributes' => $data,
                    'links' => [
                        'self' => $url
                    ]
                ]
            ], 200);
        }
    }

    public function getCheckList($checkId){
        $url = URL::current();
        $data = Checklist::select('*')
            ->where('id', $checkId)->first();

        if(!$data){
            return response()->json([
                'success' => false,
                'message' => 'Not valid owned checklist ID!',
            ], 400);
        }

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
                    'attributes' => $data,
                    'links' => [
                        'self' => $url
                    ]
                ]
            ], 200);
        }
    }

    public function getChecklistByItemId($checkId, $itemId){
        $url = URL::current();
        $checklist = Checklist::select('*')
            ->where('id', $checkId)->first();
        if(!$checklist){
            return response()->json([
                'success' => false,
                'message' => 'Not valid owned checklist ID!',
            ], 400);
        }
        
        $items = Items::select('*')
            ->where('id', $itemId)->first();

        $checklist->items = $items;
        
        if($checklist){
            return response()->json([
                'success' => true,
                'message' => 'Item ditemukan',
                'data' => [
                    'type' => 'checklists',
                    'id' => $checkId,
                    'attributes' => $checklist->items,
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
