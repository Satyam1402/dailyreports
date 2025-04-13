<?php

namespace App\Http\Controllers;
use Yajra\DataTables\Facades\DataTables;

use App\Models\Brands;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrandsController extends Controller
{
    public function index(){

        $data = Brands::query()->get();
        return view('home.brands.brands' ,compact('data'));
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
                'id','brand_name','brand_image','display_order','status'
            ]; // value depend on datatable field not in table
    
            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table
            
            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            $data = Brands::select('*')->orderBy($sortColumn, $sortDirection);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->status == 0) {
                        return '<span class="badge bg-danger">Inactive</span>';
                    } else {
                        return '<span class="badge bg-success">Active</span>';
                    }
                })
                // ->addColumn('brand_image', function ($row) {
                //     $imgUrl = $row->brand_image ?? '';
                //     return '<img src="' . $imgUrl . '" alt="Profile Image" width="70" height="70">';
                // })
                ->addColumn('brand_image', function ($row) {
                    $imgPath = $row->brand_image ?? '';
                    $baseUrl = env('AWS_URL'); // Get the base URL from the .env file
                    $imgUrl = $baseUrl . '/' . $imgPath; // Concatenate the base URL with the stored path
                    return '<img src="' . $imgUrl . '" alt="No Image Available" width="70" height="70">';
                })
                ->editColumn('website_url', function ($row) {
                    return '<a href="'. $row->website_url. '" target="_blank">Click</a>';
                })
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
                            <a href="' . route('brands.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            '.$previewButton.'
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('brands.destroy', $row->id) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['status','action','brand_image','website_url'])
                ->make(true);
        }
    }

    public function add(){
        return view('home.brands.add_brands');
    }

    public function store( Request $request ) 
        {

            $request->validate( [
                // 'title'=>'required',

            ], [
                // 'name.required' => 'Name cannot be empty',
           
            ] );
         

            $image = upload_file_to_s3($request, 'brand_image', 'brand-image');
            // print_r($url);
            // die;

            // if ($request->hasFile('brand_image')) {
            //     // Retrieve the uploaded file
            //     $file = $request->file('brand_image');
                
            //     // Generate a unique filename
            //     $filename = time() . '_' . $file->getClientOriginalName();
                
            //     // Upload the file to the S3 bucket
            //     $path = $file->storeAs('brand-image', $filename, 's3');
                
            //     // Optionally, you can get the full URL to the file
            //     $url = Storage::disk('s3')->url($path);
            // }
          
            $userId=Auth::user()->id;
            
            $data = new Brands;
            $data->brand_name = $request->brand_name ?? '';
            //$data->brand_image = $request->brand_image;
            $data->brand_image = $image ?? '';
            $data->website_url = $request->website_url ?? '';
            $data->display_order = $request->display_order ?? 0;
            $data->user_id = $userId;
            $data->status=$request->status ?? 0;
            $data->save();
            

            return redirect('template/brands');
        }



    public function show(Request $request,$id)
        {
                // $id = $request->id;
                $data = Brands::find($id);
          
                return view('home.brands.edit_brands',compact('data'));
                // return response()->json(['data' => $data]);
        }

        public function update(Request $request)
            {
                $id = $request->id;
                
            
                // $file = $request->file('post_image');
                // $filename = time() . '_' . $file->getClientOriginalName(); // Append original filename
                // $file->move('post-image/', $filename);
                //  $imagepath= $request->file('image')->store('uploads'); //if want store in  storage file then this function
                
                $image = upload_file_to_s3($request, 'brand_image', 'brand-image');
                // if ($request->hasFile('brand_image')) {
                
                //     // Retrieve the uploaded file
                //     $file = $request->file('brand_image');
            
                //     // Generate a unique filename
                //     $filename = time() . '_' . $file->getClientOriginalName();
            
                //     // Move the new file to the specified location
                //     $file->move('brand-image/', $filename);
                // }
                
                // echo $filename;
                // die;
                $data = Brands::find($id);
                
                $userId=Auth::user()->id;
            
                $update = [

                    'brand_name' => $request->brand_name ?? '',
                    'brand_image' =>  $image ?? $data->brand_image ?? '',
                    // 'brand_image' => $filename ?? '',
                    'website_url' => $request->website_url ?? '',
                    'display_order' => $request->display_order ?? 0,
                    'user_id' => $userId,
                    'status'=> $request->status ?? 0,
                  
                ];
                // print_r($update);
                // die;

                // $data = Admin_post::find($id);
                $data->update($update);
                // $data->save();
                

                return redirect('template/brands');
            }

        public function destroy(Request $request,$id)
        {
            // $id = $request->id;
            $data = Brands::find($id);
            $data->delete();
            
            return redirect('template/brands');
            // return response()->json(['data' => $data ] );
        }
}
