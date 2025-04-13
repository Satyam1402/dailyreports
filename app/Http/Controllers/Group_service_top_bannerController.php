<?php

namespace App\Http\Controllers;

use App\Models\Service_category;
use App\Models\Service_item;
use App\Models\Group_service_category;
use App\Models\Group_top_banner;

use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class Group_service_top_bannerController extends Controller {

    public function index($item_id = null) {
        // $data = Group_top_banner::query()->get();
        $homeservicecategory = Service_category::query()->get();
        $homeserviceitem = Service_item::query()->get();
        $data = Group_top_banner::with( [ 'serviceCategory', 'serviceItem' ] )->get();
        // print_r( $data->toArray() );
        // die;
        return view( 'service.group_service_top_banner.group_service_top_banner', compact( 'data', 'homeservicecategory', 'homeserviceitem','item_id') );

    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {

            $sortColumnIndex = $request->get('order')[0]['column']; // value depend on datatable field not in table
            // echo 'working';
            // echo $sortColumnIndex;
           
            $sortDirection = $request->get('order')[0]['dir'];
    
            $columns = [
                'id','explore_our_service_category_id','explore_our_service_item_id','group_banner_img','group_banner_heading','group_banner_subheading','group_banner_button_text','display_order','status'
            ];
            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table
            
            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            // $data = Group_top_banner::select('*')->with(['serviceCategory','serviceItem'])->orderBy($sortColumn, $sortDirection);

            $query = Group_top_banner::select('*')->with(['serviceCategory','serviceItem']);

            // Check if item_id is passed in the request and filter based on it
            if ($request->has('item_id') && $request->item_id != null) {
                $query->where('explore_our_service_item_id',  $request->input('item_id') ); // Filter by item_id
            }

            // Apply sorting
            $query->orderBy($sortColumn, $sortDirection);

            // Get the data
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
                    return $row->serviceCategory->service_category_name ? $row->serviceCategory->service_category_name : '';
                })
                ->addColumn('service_title', function ($row) {
                    return $row->serviceItem->service_title ? $row->serviceItem->service_title : '';
                })
                // ->addColumn('group_banner_img', function ($row) {
                //     $imgUrl = $row->group_banner_img ?? '';
                //     return '<img src="' . $imgUrl . '" alt="Image" width="70" height="70">';
                // })
                ->addColumn('group_banner_img', function ($row) {
                    $imgPath = $row->group_banner_img ?? '';
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
                    if ($row->status == 1 && $row->serviceItem->status == 1 && $row->serviceItem->category->status == 1) {
                        $previewButton = '<a href="https://d1r5jv57b9z1uu.cloudfront.net/service/ '. $row->serviceItem->id .'" target="_blank" class="mb-1 btn btn-info btn-sm mr-2">
                                <i class="fas fa-eye"></i>
                            </a>';
                    }
                    return '
                        <div class="d-flex">
                            <a href="' . route('group-top-banner.show', ['id' => $row->id, 'item_id' => request('item_id')]) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            '.$previewButton.'
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('group-top-banner.destroy', ['id' => $row->id, 'item_id' => request('item_id')]) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['service_category_name','service_title','status','action','group_banner_img'])
                ->make(true);
        }
    }

    public function add(Request $request,$item_id = null) {
        // Fetch all service categories
        $homeservicecategory = Service_category::all();

        // If item_id is provided, fetch the item to find its category_id
        $selectedItem = null;
        $selectedCategoryId = null;
        if ($item_id) {
            $selectedItem = Service_item::find($item_id);  // Get the item by item_id
            if ($selectedItem) {
                $selectedCategoryId = $selectedItem->service_category_id;  // Get the category_id of the item
            }
        }

        // Check if this is an AJAX request ( filtering service items by category )
        if ( $request->ajax() ) {
            // Fetch service items based on the selected category from the AJAX request
            $homeserviceitem = Service_item::where( 'service_category_id', $request->category_id )->get();

            // Return the filtered service items as a JSON response
            return response()->json( $homeserviceitem );
        }

        // Return the view with both categories and default service items
        return view( 'service.group_service_top_banner.add_group_service_top_banner', compact( 'homeservicecategory','selectedCategoryId','item_id','selectedItem' ) );
    }

    public function store( Request $request ) {

        $item_id = $request->item_id;
        $request->validate( [
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',

        ] );

        $image = upload_file_to_s3( $request, 'group_banner_img', 'group-banner-image' );
        $userId = Auth::user()->id;

        $data = new Group_top_banner;
        $data->explore_our_service_category_id = $request->explore_our_service_category_id ?? 0;
        $data->explore_our_service_item_id = $request->explore_our_service_item_id ?? 0;
        $data->group_banner_img = $image ?? '';
        $data->group_banner_heading = $request->group_banner_heading ?? '';
        $data->group_banner_subheading = $request->group_banner_subheading ?? '';
        $data->group_banner_button_text = $request->group_banner_button_text ?? '';
        $data->group_banner_button_url = $request->group_banner_button_url ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status = $request->status ?? 0;
        $data->save();

        return redirect()->route('group-top-banner.index',['item_id'=>$item_id]);
        // return redirect( 'group/service/group_service_top_banner' );

    }

    public function show( Request $request, $id ,$item_id = null) {

        // Fetch all service categories
        $homeservicecategory = Service_category::all();

        // Fetch service items
        $homeserviceitem = Service_item::all();
        // Initially load all items ( AJAX will filter them )

        // Find the existing Group_top_banner data by ID
        $data = Group_top_banner::find( $id );

        // Get the selected category and item
        $selectedCategory = $data->explore_our_service_category_id;
        $selectedItem = $data->explore_our_service_item_id;

        // Check if the request is an AJAX request
        if ( $request->ajax() ) {
            // If it's an AJAX request, return the filtered service items for the selected category
            $categoryId = $request->get('category_id');
            
            // Fetch the service items based on the category selected
            $filteredItems = Service_item::where('service_category_id', $categoryId)->get();

            // Return the filtered items as JSON
            return response()->json($filteredItems);
        }

        // Otherwise, return the regular view with the data
        return view('service.group_service_top_banner.edit_group_service_top_banner', compact(
            'data', 
            'homeservicecategory', 
            'homeserviceitem', 
            'selectedCategory', 
            'selectedItem',
            'item_id'
        ));
    }

    public function update(Request $request)
    {
        $item_id = $request->item_id;


        $request->validate( [
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',
       
        ] );

        $image = upload_file_to_s3($request, 'group_banner_img', 'group-banner-image');
        $id = $request->id;

        
        $data = Group_top_banner::find($id);
                
        $userId=Auth::user()->id;
    
        $update = [


            'explore_our_service_category_id' => $request->explore_our_service_category_id ?? 0,
            'explore_our_service_item_id' => $request->explore_our_service_item_id ?? 0,
            'group_banner_img' => $image ?? $data->group_banner_img ?? '',
            'group_banner_heading' => $request->group_banner_heading ?? '',
            'group_banner_subheading' => $request->group_banner_subheading ?? '',
            'group_banner_button_text' => $request->group_banner_button_text ?? '',
            'group_banner_button_url' => $request->group_banner_button_url ?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status'=> $request->status ?? 0,
          
        ];
        // print_r($update);
        // die;

        $data->update($update);
        // $data->save();
        
        return redirect()->route('group-top-banner.index',['item_id'=>$item_id]);
        // return redirect('group/service/group_service_top_banner');
    
    }

    public function destroy($id,$item_id = null){
            // $id = $request->id;
            $data = Group_top_banner::find($id);
            $data->delete();
            
            return redirect()->route('group-top-banner.index',['item_id'=>$item_id]);
            // return redirect('group/service/group_service_top_banner');
        }

    }
