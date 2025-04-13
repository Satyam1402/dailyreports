<?php

namespace App\Http\Controllers;

use App\Models\Group_creator_platform;
use Illuminate\Support\Facades\Auth;


use Illuminate\Http\Request;

class Creator_platformController extends Controller
{

    public function index()
    {
        $data = Group_creator_platform::query()->get();
        // print_r($data);
        // die;
        return view('creator_platform_service.creator_platform_service',compact('data'));
    }

    public function add(){
        return view('creator_platform_service.add_creator_platform_service');
    }

   

    public function store( Request $request ) 
    {

        $request->validate( [
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',
       
        ] );
     
        $image = upload_file_to_s3($request, 'creator_thumbnail', 'content-thumbnail');
        // if ( $request->hasFile( 'platform_image' ) ) {
        //     $file = $request->file( 'platform_image' );
        //     $filename = time() . '_' . $file->getClientOriginalName();
        //     // Append original filename
        //     $file->move( 'platform-image/', $filename );
        //     //  $imagepath = $request->file( 'image' )->store( 'uploads' );
        //     //if want store in  storage file then this function
        // }

       
        $userId=Auth::user()->id;
        
        $data = new Group_creator_platform;
        $data->creator_title = $request->creator_title ?? '';
        $data->creator_thumbnail = $image ?? '';
        $data->creator_thumbnail_url = $request->creator_thumbnail_url ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status = $request->status ?? 0;
        $data->save();
        

        return redirect('group/creator_platform');
    }

    public function show(Request $request,$id)
    {
            // $id = $request->id;
            $data = Group_creator_platform::find($id);
            return view('creator_platform_service.edit_creator_platform_service',compact('data'));
    }

    public function update(Request $request)
    {
        $id = $request->id;
    
        // $file = $request->file('post_image');
        // $filename = time() . '_' . $file->getClientOriginalName(); // Append original filename
        // $file->move('post-image/', $filename);
        //  $imagepath= $request->file('image')->store('uploads'); //if want store in  storage file then this function
        

        $image = upload_file_to_s3($request, 'creator_thumbnail', 'content-thumbnail');
        // if ($request->hasFile('platform_image')) {
        
        //     // Retrieve the uploaded file
        //     $file = $request->file('platform_image');
    
        //     // Generate a unique filename
        //     $filename = time() . '_' . $file->getClientOriginalName();
    
        //     // Move the new file to the specified location
        //     $file->move('platform-image/', $filename);
        // }
        // echo $filename;
        // die;
        $data = Group_creator_platform::find($id);
        
        $userId=Auth::user()->id;
    
        $update = [

            // 'brand_image' =>  $filename ?? $request->brand_image ?? '',
            'creator_title' => $request->creator_title ?? '',
            'creator_thumbnail' => $image ?? $data->creator_thumbnail ?? '',
            'creator_thumbnail_url' => $request->creator_thumbnail_url ?? '',
            'display_order'=> $request->display_order ?? 0,
            'user_id' => $userId,
            'status'=> $request->status ?? 0,
          
        ];
        // print_r($update);
        // die;

        // $data = Admin_post::find($id);
        $data->update($update);
        // $data->save();
        

        return redirect('group/creator_platform');
    }

public function destroy(Request $request,$id)
{
    // $id = $request->id;
    $data = Group_creator_platform::find($id);
    $data->delete();
    
    return redirect('group/creator_platform');
    // return response()->json(['data' => $data ] );
}

}
