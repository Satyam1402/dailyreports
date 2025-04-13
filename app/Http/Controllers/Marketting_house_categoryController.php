<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use App\Models\Marketting_house_category;

use Illuminate\Http\Request;

class Marketting_house_categoryController extends Controller
{
    public function index()
    {
        $data = Marketting_house_category::query()->get();
        // print_r($data->toArray());
        // die;
        return view('marketting_house.marketting_house_category.marketting_house_category',compact('data')); 
    }


    public function add(){
        // $data = Top_banner::query()->get();

       return view('marketting_house.marketting_house_category.add_marketting_house_category');
       
    }

    // Method to store a new service
    public function store(Request $request)
    {
        $request->validate( [
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',
       
        ] );


        $userId=Auth::user()->id;

        $data = new Marketting_house_category;
        $data->marketting_house_category_name = $request->category_name ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status=$request->status ?? '0';
        $data->save();

        return redirect('home/marketting_house/marketting_house_category');

    }


    public function show(Request $request,$id)
    {
            // $id = $request->id;
            $data = Marketting_house_category::find($id);
    //   echo '<pre>';
    //   print_r($data->toArray());
    //   die;
            return view('marketting_house.marketting_house_category.edit_marketting_house_category',compact('data'));
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

        $id = $request->id;

        
        $data = Marketting_house_category::find($id);
                
        $userId=Auth::user()->id;
    
        $update = [

            'marketting_house_category_name' => $request->category_name ?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status'=> $request->status ?? '0',
          
        ];
        // print_r($update);
        // die;

        $data->update($update);
        // $data->save();
        

        return redirect('home/marketting_house/marketting_house_category');
    
    }

    public function destroy($id){
            // $id = $request->id;
            $data = Marketting_house_category::find($id);
            $data->delete();
            
            return redirect('home/marketting_house/marketting_house_category');
            // return response()->json(['data' => $data ] );
    }

}
