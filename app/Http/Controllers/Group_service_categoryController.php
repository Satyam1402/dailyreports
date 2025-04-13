<?php

namespace App\Http\Controllers;

use App\Models\Service_category;
use App\Models\Service_item;
use App\Models\Group_service_category;
use App\Models\Group_service_item;
use App\Models\Group_service_category_item;

use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class Group_service_categoryController extends Controller
{
    public function index($item_id = null)
    {
        // $data = Group_service_category::query()->get();
        $homeservicecategory = Service_category::query()->get();
        $homeserviceitem = Service_item::query()->get();
        $data = Group_service_category::with(['serviceCategory', 'serviceItem'])->get();
        // print_r($data->toArray());
        // die;
        return view('service.group_service_category.group_service_category',compact('data','homeservicecategory','homeserviceitem','item_id',)); 
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {

            $sortColumnIndex = $request->get('order')[0]['column']; // value depend on datatable field not in table
            // echo 'working';
            // echo $sortColumnIndex;
           
            $sortDirection = $request->get('order')[0]['dir'];
    
            $columns = [
                'id','explore_our_service_category_id','explore_our_service_item_id','group_service_category_name','display_order','status'
            ];
            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table
            
            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            // $data = Group_service_category::select('*')->with(['serviceCategory','serviceItem'])->orderBy($sortColumn, $sortDirection);

            $query = Group_service_category::select('*')->with(['serviceCategory','serviceItem']);

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
                            <a href="' . route('group-service-category.show',  ['id' => $row->id, 'item_id' => request('item_id')]) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            '.$previewButton.'
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('group-service-category.destroy',  ['id' => $row->id, 'item_id' => request('item_id')]) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->addColumn('navigate', function ($row) {
                    $groupServiceItemCount = $row->groupServiceItems()->count();
                    return '
                        <div class="d-flex flex-column">
                             <a href="' . route('group-service-item.index',['group_category_id' => $row->id]) . '" class="mb-1 btn btn-primary btn-sm text-nowrap">
                                Service Section Items( ' . $groupServiceItemCount . ' )
                            </a>                         
                        </div>
                    ';
                })
                
                ->rawColumns(['service_category_name','service_title','status','action','navigate'])
                ->make(true);
        }
    }


    // public function add(){
    //     // $data = Top_banner::query()->get();
    //     $homeservicecategory = Service_category::query()->get();
    //     $homeserviceitem = Service_item::query()->get();



    //    return view('service.group_service_category.add_group_service_category', compact('homeservicecategory','homeserviceitem'));
       
    // }

    public function add( Request $request ,$item_id = null) {
        // Fetch all service categories
        $homeservicecategory = Service_category::all();
        $groupServiceItems = Group_service_item::all();

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

        // If it's not an AJAX request, load the page with the first category's service items ( default behavior )
        // $homeserviceitem = Service_item::where( 'service_category_id', 1 )->get();
         return view('service.group_service_category.add_group_service_category', compact('homeservicecategory','selectedCategoryId','item_id','selectedItem','groupServiceItems'));
 
    }

    // Method to store a new service
    public function store(Request $request)
    {
        // print_r($request->all());
        // die;
        $item_id = $request->item_id;
        $request->validate( [
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',
       
        ] );


        $userId=Auth::user()->id;

        $data = new Group_service_category;
        $data->explore_our_service_category_id = $request->explore_our_service_category_id ?? 0;
        $data->explore_our_service_item_id = $request->explore_our_service_item_id ?? 0;
        $data->group_service_category_name = $request->group_service_category_name ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status=$request->status ?? 0;
        $data->save();

        $Group_service_categoryId = $data->id;

    if ($request->has('group_service_items') && !empty($request->group_service_items)) {
        foreach ($request->group_service_items as $itemId) {
            $secondTableData = new Group_service_category_item();
            $secondTableData->group_service_category_id = $Group_service_categoryId;  
            $secondTableData->group_service_item_id = $itemId;  
            $secondTableData->user_id = $userId;
            $secondTableData->save();  // Save the second table row
        }
    }

        return redirect()->route('group-service-category.index',['item_id'=>$item_id]);
        // return redirect('group/service/group_service_category');

    }


    // public function show(Request $request,$id)
    // {
    //         // $id = $request->id;
    //         $homeservicecategory = Service_category::query()->get();
    //         $homeserviceitem = Service_item::query()->get();
    //         $data = Group_service_category::find($id);
    //         $selectedCategory = $data->explore_our_service_category_id;
    //         $selectedItem = $data->explore_our_service_item_id;
    //         // print_r($selectedCategory->toArray());
    //         // print_r($selectedItem);
    //         // die;
    //         return view('service.group_service_category.edit_group_service_category',compact('data','homeservicecategory', 'homeserviceitem', 'selectedCategory', 'selectedItem'));
    //         // return response()->json(['data' => $data]);
    // }

    public function show( Request $request, $id,$item_id = null ) {
        
        // Fetch all service categories
        $homeservicecategory = Service_category::all();
        $homeserviceitem = Service_item::all();
        $groupServiceItems = Group_service_item::all();
        // Initially load all items ( AJAX will filter them )

        // Find the existing Group_top_banner data by ID
        $data = Group_service_category::find( $id );
        $selectedCategory = $data->explore_our_service_category_id;
        $selectedItem = $data->explore_our_service_item_id;

        $groupServiceCategoryItem = Group_service_category_item::where('group_service_category_id',$id)->get();
        $selectedGroupServiceItemIds = $groupServiceCategoryItem->pluck('group_service_item_id')->toArray();
        // echo '<br>';
        // print_r($selectedGroupServiceItemIds);
        // die;

        // Check if the request is an AJAX request
        if ( $request->ajax() ) {
            // If it's an AJAX request, return the filtered service items for the selected category
            $categoryId = $request->get('category_id');
            
            // Fetch the service items based on the category selected
            $filteredItems = Service_item::where('service_category_id', $categoryId)->get();

            // Return the filtered items as JSON
            return response()->json($filteredItems);
        }

        return view('service.group_service_category.edit_group_service_category',compact('data','homeservicecategory', 'homeserviceitem', 'selectedCategory', 'selectedItem','groupServiceItems',
        'item_id','selectedGroupServiceItemIds'));
    }


    // Method to update an existing service
    public function update(Request $request)
    {

        $request->validate( [
            // 'title'=>'required',
        ], [
            // 'name.required' => 'Name cannot be empty',
        ] );

        $item_id = $request->item_id;
        $id = $request->id;

        $data = Group_service_category::find($id);
                
        $userId=Auth::user()->id;
    
        $update = [

            'explore_our_service_category_id' => $request->explore_our_service_category_id ?? 0,
            'explore_our_service_item_id' => $request->explore_our_service_item_id ?? 0,
            'group_service_category_name' => $request->group_service_category_name ?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status'=> $request->status ?? 0,
          
        ];
        // print_r($update);
        // die;

        $data->update($update);
        // $data->save();

        // Step 1: Delete existing group_service_category_item records related to this category
        Group_service_category_item::where('group_service_category_id', $id)->delete();

        // Step 2: Insert the new selected group_service_item_ids
        if ($request->has('group_service_items') && !empty($request->group_service_items)) {
            foreach ($request->group_service_items as $groupServiceItemId) {
                // Insert new records into the group_service_category_item table
                Group_service_category_item::create([
                    'group_service_category_id' => $id,
                    'group_service_item_id' => $groupServiceItemId,
                    'user_id' => $userId, // Optional, if you want to track the user who made the change
                ]);
            }
        }
        
        return redirect()->route('group-service-category.index',['item_id'=>$item_id]);
        // return redirect('group/service/group_service_category');
    
    }

    public function destroy($id,$item_id = null){
            // $id = $request->id;
            $data = Group_service_category::find($id);
            $data->delete();

            return redirect()->route('group-service-category.index',['item_id'=>$item_id]);
            // return redirect('group/service/group_service_category');

    }



}
