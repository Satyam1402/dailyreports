<?php

namespace App\Http\Controllers;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

use App\Models\Monthly_performance_showcase_category;
use App\Models\Monthly_performance_showcase_subcategory;
use App\Models\Monthly_performance_showcase;

use Illuminate\Http\Request;

class Monthly_performance_showcase_itemController extends Controller
{
    public function index()
    {
        $categorydata = Monthly_performance_showcase_category::query()->get();
        $subcategorydata = Monthly_performance_showcase_subcategory::query()->get();
        $data = Monthly_performance_showcase::query()->get();
        // print_r($data->toArray());
        // die;
        return view('monthly_performance_showcase.monthly_performance_showcase_item.index',compact('data','categorydata','subcategorydata')); 
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
                'id','mps_category_name','mps_img','mps_title','mps_description','display_order','status'
            ]; // value depend on datatable field not in table
    
            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table
            
            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            // $data = Monthly_performance_showcase::select('*')->with('category')->orderBy($sortColumn, $sortDirection);

            $query = Monthly_performance_showcase::select('monthly_performance_showcase.*', 'monthly_performance_showcase_category.mps_category_name','monthly_performance_showcase_category.status as category_status')
            ->join('monthly_performance_showcase_category', 'monthly_performance_showcase.mps_category_id', '=', 'monthly_performance_showcase_category.id') // Correct table name used here
            ->orderBy($sortColumn, $sortDirection);

            
              // If you are searching, include the search in the query
              if ($request->has('search') && $request->get('search')['value'] != '') {
                $search = $request->get('search')['value'];

                $query->where(function($q) use ($search) {
                    $q->where('monthly_performance_showcase.id', 'LIKE', "%$search%")
                    ->orWhere('monthly_performance_showcase_category.mps_category_name', 'LIKE', "%$search%") // Corrected to match the correct table
                    ->orWhere('monthly_performance_showcase.display_order', 'LIKE', "%$search%");
                    // ->orWhere('monthly_performance_showcase.status', 'LIKE', "%$search%");
                });
            }
            
            $data=$query->get();
            // dd($data);

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
                    return $row->mps_category->mps_category_name ? $row->mps_category->mps_category_name : '';
                })
                ->addColumn('mps_subcategory_name', function ($row) {
                    return $row->mps_subcategory->mps_subcategory_name ? $row->mps_subcategory->mps_subcategory_name : '';
                })
                // ->addColumn('mps_img', function ($row) {
                //     $imgUrl = $row->mps_img ?? '';
                //     return '<img src="' . $imgUrl . '" alt="No Image" width="70" height="70">';
                // })
                ->addColumn('mps_img', function ($row) {
                    $imgPath = $row->mps_img ?? '';
                    $baseUrl = env('AWS_URL'); // Get the base URL from the .env file
                    $imgUrl = $baseUrl . '/' . $imgPath; // Concatenate the base URL with the stored path
                    return '<img src="' . $imgUrl . '" alt="No Image Available" width="70" height="70">';
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
              
                    if ($row->status == 1 && $row->mps_category->status == 1) {
                        $previewButton = '<a href="https://d1r5jv57b9z1uu.cloudfront.net/Single-Video/' . $row->id . '" target="_blank" class="mb-1 btn btn-info btn-sm mr-2">
                                <i class="fas fa-eye"></i>
                            </a>';
                    }
                    return '
                        <div class="d-flex">
                            <a href="' . route('monthly-performance-showcase-item.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                               '.$previewButton.'
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('monthly-performance-showcase-item.destroy', $row->id) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['status','action','mps_img','mps_category_name','mps_category_name'])
                ->make(true);
        }
    }


    public function add(Request $request){
        $categorydata = Monthly_performance_showcase_category::query()->get();
        $subcategorydata = [];
        if ($request->ajax()) { // Check if the request is AJAX
            $categoryId = $request->category_id;
    
            // Fetch subcategories based on the selected category
            $subcategorydata = Monthly_performance_showcase_subcategory::where('mps_category_id', $categoryId)->get();
    
            return response()->json($subcategorydata);
        }

       return view('monthly_performance_showcase.monthly_performance_showcase_item.add',compact('categorydata','subcategorydata'));
    }

    // Method to store a new service
    public function store(Request $request)
    {
        $request->validate( [
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',
       
        ] );

        $image = upload_file_to_s3($request, 'mps_img', 'monthly-performance-image');

        $userId=Auth::user()->id;

        $data = new Monthly_performance_showcase;
        $data->mps_category_id = $request->mps_category_id ?? 0;
        $data->mps_subcategory_id = $request->mps_subcategory_id ?? 0;
        $data->mps_title = $request->mps_title ?? '';
        $data->mps_description = $request->mps_description ?? '';
        $data->mps_img = $image ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status=$request->status ?? 0;
        $data->save();

        return redirect('home/performance/monthly_performance_showcase_item');

    }


    public function show(Request $request,$id)
    {
            // $id = $request->id;
            $categorydata = Monthly_performance_showcase_category::query()->get();
            $subcategorydata = Monthly_performance_showcase_subcategory::query()->get();
            $data = Monthly_performance_showcase::find($id);

            if ($request->ajax()) {
                $categoryId = $request->category_id;
                $subcategories = Monthly_performance_showcase_subcategory::where('mps_category_id', $categoryId)->get();
                return response()->json($subcategories);
            }
            
            //   echo '<pre>';
            //   print_r($data->toArray());
            //   die;
            return view('monthly_performance_showcase.monthly_performance_showcase_item.edit',compact('data','categorydata','subcategorydata'));
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

        $image = upload_file_to_s3($request, 'mps_img', 'monthly-performance-image');
        
        $data = Monthly_performance_showcase::find($id);
                
        $userId=Auth::user()->id;
    
        $update = [

            'mps_category_id' => $request->mps_category_id ?? 0,
            'mps_subcategory_id' => $request->mps_subcategory_id ?? 0,
            'mps_title' => $request->mps_title ?? '',
            'mps_description' => $request->mps_description ?? '',
            'mps_img' => $image ?? $data->mps_img ?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status'=> $request->status ?? 0,
          
        ];
        // print_r($update);
        // die;

        $data->update($update);
        // $data->save();
        

        return redirect('home/performance/monthly_performance_showcase_item');
    
    }

    public function destroy($id){
            // $id = $request->id;
            $data = Monthly_performance_showcase::find($id);
            $data->delete();
            
            return redirect('home/performance/monthly_performance_showcase_item');
            // return response()->json(['data' => $data ] );
    }



}
