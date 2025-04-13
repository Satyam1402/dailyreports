<?php

namespace App\Http\Controllers;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

use App\Models\Monthly_performance_showcase_category;
use App\Models\Monthly_performance_showcase_subcategory;

use Illuminate\Http\Request;

class Monthly_performance_showcase_subcategoryController extends Controller
{
    public function index()
    {
        $data = Monthly_performance_showcase_subcategory::query()->get();
        // print_r($data->toArray());
        // die;
        return view('monthly_performance_showcase.monthly_performance_showcase_subcategory.index',compact('data')); 
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
                'id','mps_category_id','mps_subcategory_name','display_order','status'
            ]; // value depend on datatable field not in table
    
            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table
            
            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            $data = Monthly_performance_showcase_subcategory::select('*')->orderBy($sortColumn, $sortDirection);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->status == 0) {
                        return '<span class="badge bg-danger">Inactive</span>';
                    } else {
                        return '<span class="badge bg-success">Active</span>';
                    }
                })
                ->addColumn('mps_category_name', function ($row) {
                    return $row->mps_category->mps_category_name ? $row->mps_category->mps_category_name : 'N/A';
                })
                // ->editColumn('client_description', function ($row) {
                //     return '<a href="'. $row->client_description. '" target="_blank">Click</a>';
                // })
                // ->editColumn('created_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
                // })
                // ->editColumn('updated_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->updated_at)->format('d/m/Y');
                // })
                ->addColumn('action', function ($row) {
                    $previewButton = '';
                    // Check if status is 1, and display the preview button if true
                    if ($row->status == 1) {
                        $previewButton = '<a href="https://d1r5jv57b9z1uu.cloudfront.net/" target="_blank" class="mb-1 btn btn-info btn-sm mr-2">
                                <i class="fas fa-eye"></i>
                            </a>';
                    }
                    return '
                        <div class="d-flex">
                            <a href="' . route('monthly-performance-showcase-subcategory.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                               '.$previewButton.'
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('monthly-performance-showcase-subcategory.destroy', $row->id) . '\');">
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
        $mps_category = Monthly_performance_showcase_category::query()->get();

       return view('monthly_performance_showcase.monthly_performance_showcase_subcategory.add',compact('mps_category'));
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

        $data = new Monthly_performance_showcase_subcategory;
        $data->mps_category_id = $request->category_name ?? '';
        $data->mps_subcategory_name = $request->subcategory_name ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status=$request->status ?? 0;
        $data->save();
        
        return redirect('home/performance/monthly_performance_showcase_subcategory');
    }


    // public function show(Request $request,$id)
    // {
    //         // $id = $request->id;
    //         $data = Monthly_performance_showcase_subcategory::find($id);
    //         //   echo '<pre>';
    //         //   print_r($data->toArray());
    //         //   die;
    //         return view('monthly_performance_showcase.monthly_performance_showcase_subcategory.edit',compact('data'));
    // }

    public function show(Request $request, $id)
    {
        $data = Monthly_performance_showcase_subcategory::find($id);
        // print_r($data->toArray());
        // die;
        $mps_category = Monthly_performance_showcase_category::query()->get(); // Get all categories
        return view('monthly_performance_showcase.monthly_performance_showcase_subcategory.edit', compact('data', 'mps_category'));
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
        $data = Monthly_performance_showcase_subcategory::find($id);        
        $userId=Auth::user()->id;
    
        $update = [

            'mps_category_id' => $request->category_name ?? '',
            'mps_subcategory_name' => $request->subcategory_name ?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status'=> $request->status ?? 0, 
        ];
        // print_r($update);
        // die;

        $data->update($update);
        // $data->save();

        return redirect('home/performance/monthly_performance_showcase_subcategory');
    }

    public function destroy($id){
            // $id = $request->id;
            $data = Monthly_performance_showcase_subcategory::find($id);
            $data->delete();
            
            return redirect('home/performance/monthly_performance_showcase_subcategory');
            // return response()->json(['data' => $data ] );
    }



}
