<?php

namespace App\Http\Controllers;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

use App\Models\Development_house_category;

use Illuminate\Http\Request;

class Development_house_categoryController extends Controller
{
    public function index()
    {
        $data = Development_house_category::query()->get();
        // print_r($data->toArray());
        // die;
        return view('development_house.development_house_category.development_house_category',compact('data')); 
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {

            $sortColumnIndex = $request->get('order')[0]['column']; // value depend on datatable field not in table
            // echo 'working';
            // echo $sortColumnIndex;
           
            $sortDirection = $request->get('order')[0]['dir'];
    
            // Map column index to actual column names (you can adjust this as per your columns)
            $columns = [
                'id','development_house_category_name','display_order','status'
            ]; // value depend on datatable field not in table
    
            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table
            
            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            $data = Development_house_category::select('*')->orderBy($sortColumn, $sortDirection);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->status == 0) {
                        return '<span class="badge bg-danger">Inactive</span>';
                    } else {
                        return '<span class="badge bg-success">Active</span>';
                    }
                })
                // ->editColumn('created_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
                // })
                // ->editColumn('updated_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->updated_at)->format('d/m/Y');
                // })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-flex">
                            <a href="' . route('development-house-category.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('development-house-category.destroy', $row->id) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['status','action'])
                ->make(true);
        }
    }

    public function add(){
        // $data = Top_banner::query()->get();

       return view('development_house.development_house_category.add_development_house_category');
       
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

        $data = new Development_house_category;
        $data->development_house_category_name = $request->category_name ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status=$request->status ?? 0;
        $data->save();

        return redirect('home/development_house/development_house_category');

    }


    public function show(Request $request,$id)
    {
            // $id = $request->id;
            $data = Development_house_category::find($id);
    //   echo '<pre>';
    //   print_r($data->toArray());
    //   die;
            return view('development_house.development_house_category.edit_development_house_category',compact('data'));
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

        
        $data = Development_house_category::find($id);
                
        $userId=Auth::user()->id;
    
        $update = [

            'development_house_category_name' => $request->category_name ?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status'=> $request->status ?? 0,
          
        ];
        // print_r($update);
        // die;

        $data->update($update);
        // $data->save();
        

        return redirect('home/development_house/development_house_category');
    
    }

    public function destroy($id){
            // $id = $request->id;
            $data = Development_house_category::find($id);
            $data->delete();
            
            return redirect('home/development_house/development_house_category');
            // return response()->json(['data' => $data ] );
    }

}
