<?php

namespace App\Http\Controllers;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Service_category;
use Illuminate\Support\Facades\Auth;


use Illuminate\Http\Request;

class Service_categoryController extends Controller
{
    public function index()
    {
        $data = Service_category::query()->get();
        // print_r($data->toArray());
        // die;
        return view('service.service_category.service_category',compact('data')); 
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
                'id','service_category_name','service_icon','display_order','status'
            ]; // value depend on datatable field not in table
    
            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table
            
            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            $data = Service_category::select('*')->orderBy($sortColumn, $sortDirection);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->status == 0) {
                        return '<span class="badge bg-danger">Inactive</span>';
                    } else {
                        return '<span class="badge bg-success">Active</span>';
                    }
                })
                ->addColumn('service_icon', function ($row) {
                    $imgPath = $row->service_icon ?? '';
                    $baseUrl = env('AWS_URL'); // Get the base URL from the .env file
                    
                    if (empty($imgPath)) {
                        return '<span>No Image Available</span>';  // You can also leave this as empty string if you prefer
                    }

                    $imgUrl = $baseUrl . '/' . $imgPath; // Concatenate the base URL with the stored path
                    return '<img src="' . $imgUrl . '" alt="No Image Available" width="70" height="70">';
                })
                // ->addColumn('banner_video_thumbnail', function ($row) {
                //     $imgUrl = $row->banner_video_thumbnail ?? '';
                //     return '<img src="' . $imgUrl . '" alt="Profile Image" width="70" height="70">';
                // })
                // ->editColumn('banner_video_url', function ($row) {
                //     return '<a href="'. $row->banner_video_url. '" target="_blank">Click</a>';
                // })
                // ->editColumn('created_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
                // })
                // ->editColumn('updated_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->updated_at)->format('d/m/Y');
                // })
                ->addColumn('action', function ($row) {
                    
                    if ($row->service_category_name == 'Service Platform') {
                        return ''; // Return empty string to hide actions for "Service Platform"
                    }
                    $previewButton = '';
                    // Check if status is 1, and display the preview button if true
                    if ($row->status == 1) {
                        $previewButton = '<a href="https://d1r5jv57b9z1uu.cloudfront.net/" target="_blank" class="mb-1 btn btn-info btn-sm mr-2">
                                <i class="fas fa-eye"></i>
                            </a>';
                    }
                    return '
                        <div class="d-flex">
                            <a href="' . route('service-category.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            '.$previewButton.'
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('service-category.destroy', $row->id) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['status','action','service_icon'])
                ->make(true);
        }
    }


    public function add(){
        // $data = Top_banner::query()->get();

       return view('service.service_category.add_service_category');
       
    }

    // Method to store a new service
    public function store(Request $request)
    {
        $request->validate( [
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',
       
        ] );

        $image = upload_file_to_s3($request, 'service_icon', 'Department-icon');


        $userId=Auth::user()->id;

        $data = new Service_category;
        $data->service_category_name = $request->category_name ?? '';
        $data->service_icon = $image ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status=$request->status ?? 0;
        $data->save();

        // return redirect('group/service/service_category');
        return redirect()->route('service-category.index');


    }


    public function show(Request $request,$id)
    {
            // $id = $request->id;
            $data = Service_category::find($id);
            //   echo '<pre>';
            //   print_r($data->toArray());
            //   die;
            return view('service.service_category.edit_service_category',compact('data'));
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

        
        $data = Service_category::find($id);
        $image = upload_file_to_s3($request, 'service_icon', 'Department-icon');
          
        
        $userId=Auth::user()->id;
    
        $update = [

            'service_category_name' => $request->category_name ?? '',
            'service_icon' => $image ?? $data->service_icon ?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status'=> $request->status ?? 0,
          
        ];
        // print_r($update);
        // die;

        $data->update($update);
        // $data->save();
        

        return redirect()->route('service-category.index');
        // return redirect('group/service/service_category');
    
    }

    public function destroy($id){
            // $id = $request->id;
            $data = Service_category::find($id);
            $data->delete();
          
            return redirect()->route('service-category.index');
            // return redirect('group/service/service_category');
            // return response()->json(['data' => $data ] );
    }



}
