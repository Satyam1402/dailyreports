<?php

namespace App\Http\Controllers;

use App\Models\Service_platform;
use Illuminate\Support\Facades\Auth;


use Illuminate\Http\Request;

class Service_platformController extends Controller
{

    public function index()
    {
        $data = Service_platform::query()->get();
        // print_r($data);
        // die;
        return view('service.service_platform.service_platform',compact('data'));
    }

    public function add(){
        return view('service.service_platform.add_service_platform');
    }

   

    public function store( Request $request ) 
    {

        $request->validate( [
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',
       
        ] );
     
        $image = upload_file_to_s3($request, 'platform_image', 'platform-image');
        // if ( $request->hasFile( 'platform_image' ) ) {
        //     $file = $request->file( 'platform_image' );
        //     $filename = time() . '_' . $file->getClientOriginalName();
        //     // Append original filename
        //     $file->move( 'platform-image/', $filename );
        //     //  $imagepath = $request->file( 'image' )->store( 'uploads' );
        //     //if want store in  storage file then this function
        // }

       
        $userId=Auth::user()->id;
        
        $data = new Service_platform;
        $data->platform_image = $image ?? '';
        $data->platform_title = $request->platform_title ?? '';
        $data->platform_button_text = $request->button_text ?? '';
        $data->platform_button_url = $request->button_url ?? '';
        $data->display_order = $request->display_order ?? '';
        $data->user_id = $userId;
        $data->status = $request->status ?? '0';
        $data->save();
        

        return redirect('group/service/service_platform');
    }

    public function show(Request $request,$id)
    {
            // $id = $request->id;
            $data = Service_platform::find($id);
            return view('service.service_platform.edit_service_platform',compact('data'));
    }

    public function update(Request $request)
    {
        $id = $request->id;
    
        // $file = $request->file('post_image');
        // $filename = time() . '_' . $file->getClientOriginalName(); // Append original filename
        // $file->move('post-image/', $filename);
        //  $imagepath= $request->file('image')->store('uploads'); //if want store in  storage file then this function
        

        $image = upload_file_to_s3($request, 'platform_image', 'platform-image');
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
        $data = Service_platform::find($id);
        
        $userId=Auth::user()->id;
    
        $update = [

            // 'brand_image' =>  $filename ?? $request->brand_image ?? '',
            'platform_title' => $request->platform_title ?? '',
            'platform_image' => $image ?? $data->platform_image ?? '',
            'platform_button_text' => $request->button_text ?? '',
            'platform_button_url' => $request->button_url ?? '',
            'display_order'=> $request->display_order ?? '',
            'platform_button_text' => $request->button_text ?? '',
            'platform_button_url' => $request->button_url ?? '',
            'display_order' => $request->display_order ?? '',
            'user_id' => $userId,
            'status'=> $request->status ?? '0',
          
        ];
        // print_r($update);
        // die;

        // $data = Admin_post::find($id);
        $data->update($update);
        // $data->save();
        

        return redirect('group/service/service_platform');
    }

public function destroy(Request $request,$id)
{
    // $id = $request->id;
    $data = Service_platform::find($id);
    $data->delete();
    
    return redirect('group/service/service_platform');
    // return response()->json(['data' => $data ] );
}

}
