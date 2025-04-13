<?php

namespace App\Http\Controllers;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

use App\Models\Service_category;
use App\Models\Service_item;
use App\Models\Group_top_banner;
use App\Models\Group_service_category;

use Illuminate\Http\Request;

class Service_itemController extends Controller
{
    public function index(Request $request)
    {
          // Check if the request is an AJAX request
    if ($request->ajax()) {
        // If it's an AJAX request, filter titles based on the selected category
        $category_id = $request->get('category_id');
        
        // $titles = Service_item::where('service_category_id', $category_id)
        //     ->distinct()
        //     ->get(['service_title']); // Get unique service titles for the selected category

        if ($category_id) {
            $titles = Service_item::where('service_category_id', $category_id)
                ->distinct()
                ->get(['service_title']);
        } else {
            // Fetch all titles if no category is selected
            $titles = Service_item::select('service_title')
                ->distinct()
                ->get();
        }
    

        // Return the filtered titles as JSON response
        return response()->json(['titles' => $titles]);
    }

        $categorydata = Service_category::query()->get();
        $titles = Service_item::select('service_title')
        ->distinct()
        ->get();
        // $data = Service_item::query()->get();
        $data = Service_item::query()->with('category')->get();
        // print_r($data->toArray());
        // die;
        return view('service.service_item.service_item',compact('data','categorydata','titles'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {

            $sortColumnIndex = $request->get('order')[0]['column']; // value depend on datatable field not in table
            // echo 'working';
            // echo $sortColumnIndex;
           
            $sortDirection = $request->get('order')[0]['dir'];
    
            $columns = [
                'id','service_category_id','service_title','service_image','button_text','display_order','status'
            ];
            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table
            
            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            // $data = Service_item::select('*')->with('category')->orderBy($sortColumn, $sortDirection);

        // Start building the query, including the relation 'category'
        $query = Service_item::select('explore_our_service_item.*', 'explore_our_service_category.service_category_name')
        ->join('explore_our_service_category', 'explore_our_service_item.service_category_id', '=', 'explore_our_service_category.id') // Correct table name used here
        ->orderBy($sortColumn, $sortDirection);

              // Apply category filter if selected
        if ($request->has('category_id') && $request->get('category_id') != '') {
            $category_id = $request->get('category_id');
            $query->where('explore_our_service_item.service_category_id', $category_id);
        }

        if ($request->has('title') && $request->get('title') != '') {
            $title = $request->get('title');
            $query->where('service_title', 'like', '%' . $title . '%');
        }

        if ($request->has('status') && $request->get('status') != '') {
            $status = $request->get('status');
            $query->where('explore_our_service_item.status', $status);
        }

        // If there is a search term, apply it to relevant fields
        if ($request->has('search') && $request->get('search')['value'] != '') {
            $search = $request->get('search')['value'];

            $query->where(function($q) use ($search) {
                $q->where('explore_our_service_item.id', 'LIKE', "%$search%")
                  ->orWhere('explore_our_service_category.service_category_name', 'LIKE', "%$search%") // Searching the related category
                  ->orWhere('explore_our_service_item.service_title', 'LIKE', "%$search%")
                  ->orWhere('explore_our_service_item.button_text', 'LIKE', "%$search%")
                //   ->orWhere('explore_our_service_item.button_url', 'LIKE', "%$search%")
                  ->orWhere('explore_our_service_item.display_order', 'LIKE', "%$search%");
                //   ->orWhere('explore_our_service_item.status', 'LIKE', "%$search%");
            });
        }

            $data = $query->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->status == 0) {
                        return '<span class="badge bg-danger">Inactive</span>';
                    } else {
                        return '<span class="badge bg-success">Active</span>';
                    }
                })
                ->addColumn('service_category_name', function ($row) {
                    return $row->category->service_category_name ? $row->category->service_category_name : '';
                })
                // ->addColumn('service_image', function ($row) {
                //     $imgUrl = $row->service_image ?? '';
                //     return '<img src="' . $imgUrl . '" alt="Image" width="70" height="70">';
                // })
                ->addColumn('service_image', function ($row) {
                    $imgPath = $row->service_image ?? '';
                    $baseUrl = env('AWS_URL'); // Get the base URL from the .env file
                    $imgUrl = $baseUrl . '/' . $imgPath; // Concatenate the base URL with the stored path
                    return '<img src="' . $imgUrl . '" alt="No Image Available" width="70" height="70">';
                })
                // ->editColumn('creative_house_video_url', function ($row) {
                //     return '<a href="'. $row->creative_house_video_url. '" target="_blank">Click</a>';
                // })
                // ->editColumn('created_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
                // })
                // ->editColumn('updated_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->updated_at)->format('d/m/Y');
                // })
                ->addColumn('action', function ($row) {
                    $previewButton = '';
                    if ($row->status == 1 && $row->category->status == 1) {
                        $previewButton = '<a href="https://d1r5jv57b9z1uu.cloudfront.net/" target="_blank" class="mb-1 btn btn-info btn-sm mr-2">
                                <i class="fas fa-eye"></i>
                            </a>';
                    }
                    return '
                        <div class="d-flex">
                            <a href="' . route('service-item.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            '.$previewButton.'
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('service-item.destroy', $row->id) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->addColumn('navigate', function ($row) {
                    $bannerCount = Group_top_banner::where('explore_our_service_item_id', $row->id)->count();
                    $groupServiceCateogryCount = Group_service_category::where('explore_our_service_item_id', $row->id)->count();

                    return '
                        <div class="d-flex flex-column">
                            <a href="' . route('group-top-banner.index',['item_id' => $row->id]) . '" class="mb-1 btn btn-primary btn-sm text-nowrap">
                                Service Category Banners( ' . $bannerCount . ' )
                            </a>
                             <a href="' . route('group-service-category.index',['item_id' => $row->id]) . '" class="mb-1 btn btn-primary btn-sm text-nowrap">
                                Service Category Display Sections( ' . $groupServiceCateogryCount . ' )
                            </a>                         
                        </div>
                    ';
                })
                ->rawColumns(['service_category_name','status','action','service_image','navigate'])
                ->make(true);
        }
    }

    public function add(){

        $categorydata = Service_category::query()->get();
        return view('service.service_item.add_service_item',compact('categorydata'));
    }

   

    public function store( Request $request ) 
    {

        $request->validate( [
            // 'title'=>'required',
        ], [
            // 'name.required' => 'Name cannot be empty',
        ] );
     
        $image = upload_file_to_s3($request, 'service_image', 'service-image');
        // if ( $request->hasFile( 'service_image' ) ) {
        //     $file = $request->file( 'service_image' );
        //     $filename = time() . '_' . $file->getClientOriginalName();
        //     // Append original filename
        //     $file->move( 'service-image/', $filename );
        //     //  $imagepath = $request->file( 'image' )->store( 'uploads' );
        //     //if want store in  storage file then this function
        // }
       
        $userId=Auth::user()->id;
        
        $data = new Service_item;
        $data->service_category_id = $request->service_category_id ?? 0;
        $data->service_image = $image ?? '';
        $data->service_title = $request->service_title ?? '';
        $data->button_text = $request->button_text ?? '';
        $data->button_url = $request->button_url ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status = $request->status ?? 0;
        $data->save();
        
        return redirect()->route('service-item.index');
        // return redirect('group/service/service_item');
    }

    public function show(Request $request,$id)
    {
            // $id = $request->id;
            $categorydata = Service_category::query()->get();
            $data = Service_item::find($id);
            return view('service.service_item.edit_service_item',compact('data','categorydata'));
    }

    public function update(Request $request)
    {
        $id = $request->id;
    
        // $file = $request->file('post_image');
        // $filename = time() . '_' . $file->getClientOriginalName(); // Append original filename
        // $file->move('post-image/', $filename);
        //  $imagepath= $request->file('image')->store('uploads'); //if want store in  storage file then this function
        
        $image = upload_file_to_s3($request, 'service_image', 'service-image');
        // if ($request->hasFile('service_image')) {
        
        //     // Retrieve the uploaded file
        //     $file = $request->file('service_image');
    
        //     // Generate a unique filename
        //     $filename = time() . '_' . $file->getClientOriginalName();
    
        //     // Move the new file to the specified location
        //     $file->move('service-image/', $filename);
        // }
        // echo $filename;
        // die;
        $data = Service_item::find($id);
        
        $userId=Auth::user()->id;
    
        $update = [

            // 'brand_image' =>  $filename ?? $request->brand_image ?? '',
            'service_category_id' => $request->service_category_id ?? 0,
            'service_image' => $image ?? $data->service_image ?? '',
            'service_title' => $request->service_title ?? '',
            'button_text' => $request->button_text ?? '',
            'button_url' => $request->button_url ?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status'=> $request->status ?? 0,
          
        ];
        // print_r($update);
        // die;

        // $data = Admin_post::find($id);
        $data->update($update);
        // $data->save();
        

         return redirect()->route('service-item.index');
        // return redirect('group/service/service_item');
    }

public function destroy(Request $request,$id)
{
    // $id = $request->id;
    $data = Service_item::find($id);
    $data->delete();
    
    return redirect()->route('service-item.index');
    // return redirect('group/service/service_item');
    // return response()->json(['data' => $data ] );
}


}
