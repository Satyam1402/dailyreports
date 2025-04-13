<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use App\Models\Marketting_house_category;
use App\Models\Marketting_house_item;

use Illuminate\Http\Request;

class Marketting_house_itemController extends Controller
{

    public function index() {

        $categorydata = Marketting_house_category::query()->get();
        // $data = Marketting_house_item::query()->get();
        $data = Marketting_house_item::query()->with('category')->get();
        // print_r( $data );
        // die;
        return view( 'marketting_house.marketting_house_item.marketting_house_item', compact( 'data','categorydata' ) );
    }

    public function add() {
                 $categorydata = Marketting_house_category::query()->get();
                 return view( 'marketting_house.marketting_house_item.add_marketting_house_item',compact('categorydata') );
    }

    public function store( Request $request )  {

        // print_r( $request->creative_house_video_url );
        // die;
        $request->validate( [
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',

        ] );

        $image = upload_file_to_s3($request, 'marketting_house_img', 'marketting-house-image');
        // if ( $request->hasFile( 'marketting_house_img' ) ) {
        //     $file = $request->file( 'marketting_house_img' );
        //     $filename = time() . '_' . $file->getClientOriginalName();
        //     // Append original filename
        //     $file->move( 'marketting-house-image/', $filename );
        //     //  $imagepath = $request->file( 'image' )->store( 'uploads' );
        //     //if want store in  storage file then this function
        // }

        $userId = Auth::user()->id;

        $data = new Marketting_house_item;
        $data->marketting_house_category_id = $request->marketting_house_category_id??0;
        $data->marketting_house_img = $image ?? '';
        $data->marketting_house_url = $request->marketting_house_url ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status = $request->status ?? '0';
        $data->save();

        return redirect( 'home/marketting_house/marketting_house_item' );
    }

    public function show( Request $request, $id ) {
        // $id = $request->id;
        $categorydata = Marketting_house_category::query()->get();
        $data = Marketting_house_item::find($id);
        // print_r( $data->toArray() );
        // die;
        return view( 'marketting_house.marketting_house_item.edit_marketting_house_item', compact( 'data','categorydata') );
    }

    public function update( Request $request ) {
        $id = $request->id;

        // $file = $request->file( 'post_image' );
        // $filename = time() . '_' . $file->getClientOriginalName();
        // Append original filename
        // $file->move( 'post-image/', $filename );
        //  $imagepath = $request->file( 'image' )->store( 'uploads' );
        //if want store in  storage file then this function

        $image = upload_file_to_s3($request, 'marketting_house_img', 'marketting-house-image');
       
        // if ( $request->hasFile( 'marketting_house_img' ) ) {

        //     // Retrieve the uploaded file
        //     $file = $request->file( 'marketting_house_img' );

        //     // Generate a unique filename
        //     $filename = time() . '_' . $file->getClientOriginalName();

        //     // Move the new file to the specified location
        //     $file->move( 'marketting-house-image/', $filename );
        // }
        // echo $filename;
        // die;
        $data = Marketting_house_item::find( $id );

        $userId = Auth::user()->id;

        $update = [

            'marketting_house_category_id' => $request->marketting_house_category_id??0,
            'marketting_house_img' => $image ??$data->marketting_house_img ?? '',
            'marketting_house_url' => $request->marketting_house_url ?? '',
            'display_order' => $request->display_order ?? '',
            'user_id' => $userId,
            'status'=> $request->status ?? '0',

        ];
        // print_r( $update );
        // die;

        // $data = Admin_post::find( $id );
        $data->update( $update );
        // $data->save();

        return redirect( 'home/marketting_house/marketting_house_item' );
    }

    public function destroy( Request $request, $id ) {
        // $id = $request->id;
        $data = Marketting_house_item::find( $id );
        $data->delete();

        return redirect( 'home/marketting_house/marketting_house_item' );
        // return response()->json( [ 'data' => $data ] );
    }

}
