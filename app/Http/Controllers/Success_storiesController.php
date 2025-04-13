<?php

namespace App\Http\Controllers;


use App\Models\Group_success_stories;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class Success_storiesController extends Controller
{
    public function index()
    {
 
        $data = Group_success_stories::query()->get();
        // print_r($data->toArray());
        // die;
        return view('success_stories.success_stories',compact('data')); 
    }


    public function add(){

       return view('success_stories.add_success_stories');
       
    }

    // Method to store a new service
    public function store(Request $request)
    {
        $request->validate( [
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',
       
        ] );

        $image = upload_file_to_s3($request, 'success_stories_img', 'success-stories-image');
        $userId=Auth::user()->id;

        $data = new Group_success_stories;
        // $data->explore_our_service_category_id = $request->explore_our_service_category_id ?? 0;
        $data->success_stories_title = $request->success_stories_title ?? 0;
        $data->success_stories_img = $image ?? '';
        $data->success_stories_description = $request->success_stories_description ?? '';
        $data->success_stories_url = $request->success_stories_url ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status=$request->status ?? 0;
        $data->save();

        return redirect('group/success_stories');

    }


    public function show(Request $request,$id)
    {
            // $id = $request->id;
            $data = Group_success_stories::find($id);    
            //   echo '<pre>';
            //   print_r($data->toArray());
            //   die;
            return view('success_stories.edit_success_stories',compact('data'));
            // return response()->json(['data' => $data]);
    }


    // Method to update an existing service
    public function update(Request $request)
    {

        $request->validate( [
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',
       
        ] );

        $image = upload_file_to_s3($request, 'success_stories_img', 'success-stories-image');
        $id = $request->id;

      
        
        $data = Group_success_stories::find($id);
                
        $userId=Auth::user()->id;
        $update = [


            // 'explore_our_service_category_id' => $request->explore_our_service_category_id ?? 0,
            'success_stories_title' => $request->success_stories_title ?? 0,
            'success_stories_img' => $image ?? $data->success_stories_img ?? '',
            'success_stories_description'=> $request->success_stories_description ?? '',
            'success_stories_url'=> $request->success_stories_url ?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status'=> $request->status ?? 0,
          
        ];
        // print_r($update);
        // die;

        $data->update($update);
        // $data->save();
        

        return redirect('group/success_stories');
    
    }

    public function destroy($id){
            // $id = $request->id;
            $data = Group_success_stories::find($id);
            $data->delete();
            
            return redirect('group/success_storiese');
            // return response()->json(['data' => $data ] );
    }



}
