<?php

namespace App\Http\Controllers;


use App\Models\Service_category;
use App\Models\Service_item;
use App\Models\Group_service_category;
use App\Models\Group_service_category_item;
use App\Models\Group_service_item;

use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Http\Request;

class Group_service_itemController extends Controller
{
    public function index($group_category_id= null)
    {
        // print_r($group_category_id);
        // die;
        $groupcategorydata = Group_service_category::find($group_category_id);
        $item_id=$groupcategorydata->explore_our_service_item_id ?? '';
        // $groupitems = $groupcategorydata->groupServiceItems ?? '';

        $data = Group_service_item::with(['groupservicecategory'])->get();

        // $categorydata = Group_service_category::with(['serviceCategory', 'serviceItem'])->get();
        // print_r($groupitems->toArray());
        // // print_r($item_id);
        // die;
        return view('service.group_service_item.group_service_item',compact('data','group_category_id','item_id')); 
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {

            $sortColumnIndex = $request->get('order')[0]['column']; // value depend on datatable field not in table
           
            $sortDirection = $request->get('order')[0]['dir'];
    
            $columns = [
                'id','group_service_category_id','group_service_item_thumbnail','group_service_item_title','group_service_item_description','display_order','status'
            ];
            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table

            // Apply the sorting to the query
            // $data = Group_service_item::select('*')->with(['groupservicecategory'])->orderBy($sortColumn, $sortDirection);

            // $query = Group_service_item::select('*')->with(['groupservicecategory']);

            $query = Group_service_item::select('*')->with('groupServiceCategories'); // Using the relation with pivot table
            
            // Check if item_id is passed in the request and filter based on it
            // if ($request->has('group_category_id') && $request->group_category_id != null) {
            //     $query->where('group_service_category_id',  $request->input('group_category_id') ); // Filter by item_id
            // }

            $group_category_id=$request->input('group_category_id');
          
            if ($request->has('group_category_id') && $request->group_category_id != null) {
                $group_category_id = $request->input('group_category_id');

                // Apply the filter on the pivot table to ensure only items linked to the specific category_id are returned
                $query->whereHas('groupServiceCategories', function ($query) use ($group_category_id) {
                    $query->where('group_service_category_item.group_service_category_id', $group_category_id);
                });
            }

            // Apply sorting
            $query->orderBy($sortColumn, $sortDirection);

            // Get the data
            $data = $query->get();
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
                // ->addColumn('group_service_category_name', function ($row) {
                //     return $row->groupservicecategory->group_service_category_name ? $row->groupservicecategory->group_service_category_name : '';
                ->addColumn('group_service_category_name', function ($row) use ($group_category_id) {
                    // Filter the related categories based on the passed group_category_id
                    // $category = $row->groupServiceCategories->first();
                    $category = $row->groupServiceCategories->firstWhere('id', $group_category_id);
                
                    // Return the category name if found, else return an empty string
                    return $category ? $category->group_service_category_name : 'Unknown';
                })
                
                // ->addColumn('group_service_item_thumbnail', function ($row) {
                //     $imgUrl = $row->group_service_item_thumbnail ?? '';
                //     return '<img src="' . $imgUrl . '" alt="Image" width="70" height="70">';
                // })
                ->addColumn('group_service_item_thumbnail', function ($row) {
                    $imgPath = $row->group_service_item_thumbnail ?? '';
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
                ->addColumn('action', function ($row) use ($group_category_id) {
                    $previewButton = '';

                    $groupServiceCategory = $row->groupServiceCategories->firstWhere('id', $group_category_id);
                    $groupServiceCategoryStatus = $groupServiceCategory->status ?? null;

                    // $exploreServiceCategoryId = $groupServiceCategory->explore_our_service_category_id ?? null;
                    $serviceCategory = $groupServiceCategory->serviceCategory ?? null; 
                    $serviceCategoryStatus = $serviceCategory ? $serviceCategory->status : null;


                    $exploreServiceItemId = $groupServiceCategory->explore_our_service_item_id ?? null;
                    $serviceItem = $groupServiceCategory->serviceItem ?? null; // This will load the related Service_item
                    $serviceItemStatus = $serviceItem ? $serviceItem->status : null;


                    if ($row->status == 1 &&  $groupServiceCategoryStatus == 1 && $serviceCategoryStatus ==1 && $serviceItemStatus == 1
                    ) {
                        $previewButton = '<a href="https://d1r5jv57b9z1uu.cloudfront.net/service/ '. $exploreServiceItemId .'" target="_blank" class="mb-1 btn btn-info btn-sm mr-2">
                                <i class="fas fa-eye"></i>
                            </a>';
                    }
                    return '
                        <div class="d-flex">
                            <a href="' . route('group-service-item.show', ['id' => $row->id, 'group_category_id' => request('group_category_id')]) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                         '.$previewButton.'
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('group-service-item.destroy', ['id' => $row->id, 'group_category_id' => request('group_category_id')]) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->addColumn('navigate', function ($row) {
                    $singleServiceImageCount = $row->group_single_service_image()->count();
                    $recentWorkCount = $row->group_single_service_recent_work()->count();
                    $portfolioCategoryCount = $row->group_single_service_portfolio_category()->count();
                    return '
                        <div class="d-flex flex-column">
                             <a href="' . route('group-single-service-img.index',['group_item_id' => $row->id,'group_category_id' => request('group_category_id')]) . '" class="mb-1 btn btn-primary btn-sm text-nowrap">
                                Service Media( ' . $singleServiceImageCount . ' )
                            </a>    
                              <a href="' . route('group-single-service-recent-work.index',['group_item_id' => $row->id , 'group_category_id' => request('group_category_id')]) . '" class="mb-1 btn btn-primary btn-sm text-nowrap">
                                Service Recent Work( ' . $recentWorkCount . ' )
                            </a> 
                            <a href="' . route('group-single-service-portfolio-category.index',['group_item_id' => $row->id, 'group_category_id' => request('group_category_id')]) . '" class="mb-1 btn btn-primary btn-sm text-nowrap">
                                Service Portfolio Category( ' . $portfolioCategoryCount . ' )
                            </a>                        
                        </div>
                    ';
                })
                ->rawColumns(['group_service_category_name','group_service_item_thumbnail','status','action','navigate'])
                ->make(true);
        }
    }


    public function add($group_category_id= null){
        $categorydata = Group_service_category::query()->get();
        // print_r($categorydata->toArray());
        // die;

       return view('service.group_service_item.add_group_service_item', compact('categorydata','group_category_id'));
       
    }

    // Method to store a new service
    public function store(Request $request)
    {

        $group_category_id = $request->group_category_id;

        $request->validate( [
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',
       
        ] );

        $image = upload_file_to_s3($request, 'group_service_item_thumbnail', 'group-service-item-image');

        $userId=Auth::user()->id;

        $data = new Group_service_item;
        // $data->group_service_category_id = 0;
        $data->group_service_category_id = $request->group_service_category_id ?? 0;
        $data->group_service_item_thumbnail = $image ?? '';
        $data->group_service_item_title = $request->group_service_item_title ?? '';
        $data->group_service_item_description = $request->group_service_item_description ?? '';
        $data->group_service_item_description2 = $request->group_service_item_description2 ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status=$request->status ?? 0;
        $data->save();

        $Group_service_itemId = $data->id;

    if (!empty($group_category_id)) {
        $secondTableData = new Group_service_category_item();
        $secondTableData->group_service_category_id = $request->group_service_category_id;  
        $secondTableData->group_service_item_id = $Group_service_itemId;  
        $secondTableData->user_id = $userId;
        $secondTableData->save();  // Save the second table row
    }
        return redirect()->route('group-service-item.index',['group_category_id'=>$group_category_id]);
        // return redirect('group/service/group_service_item');

    }


    public function show(Request $request,$id,$group_category_id= null)
    {
            // $id = $request->id;

            $data= Group_service_item::find($id);
            $categorydata = Group_service_category::query()->get();
            // $selectedCategory = $data->id;

            return view('service.group_service_item.edit_group_service_item',compact('data','categorydata','group_category_id'));
            // return response()->json(['data' => $data]);
    }


    // Method to update an existing service
    public function update(Request $request)
    {

        $group_category_id = $request->group_category_id;

        $request->validate( [
            // 'title'=>'required',
        ], [
            // 'name.required' => 'Name cannot be empty',
        ] );

        $id = $request->id;

        $image = upload_file_to_s3($request, 'group_service_item_thumbnail', 'group-service-item-image');
        
        $data = Group_service_item::find($id);
                
        $userId=Auth::user()->id;
    
        $update = [


            // 'group_service_category_id' => $request->group_service_category_id ?? 0,
            'group_service_item_thumbnail' => $image ?? $data->group_service_item_thumbnail ?? '',
            'group_service_item_title' => $request->group_service_item_title ?? '',
            'group_service_item_description' => $request->group_service_item_description ?? '',
            'group_service_item_description2' => $request->group_service_item_description2 ?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status'=> $request->status ?? 0,
          
        ];
        // print_r($update);
        // die;

        $data->update($update);
        // $data->save();

        
    if (!empty($group_category_id)) {
       // Step 1: Find the existing Group_service_category_item record
       $categoryItem = Group_service_category_item::where('group_service_item_id', $id)
       ->where('group_service_category_id', $request->group_service_category_id ?? $group_category_id)
       ->first();

       if ($categoryItem) {
        // Step 2: Update the record if it exists
        $categoryItem->update([
            'group_service_category_id' => $request->group_service_category_id ?? $group_category_id,
            'group_service_item_id' => $id,
            'user_id' => $userId,
        ]);
        } else {
            // Step 3: If the record does not exist, create a new one (optional)
            Group_service_category_item::create([
                'group_service_category_id' => $request->group_service_category_id ?? $group_category_id,
                'group_service_item_id' => $id,
                'user_id' => $userId,
            ]);
        }
    }
        
               
           
        
        return redirect()->route('group-service-item.index',['group_category_id'=>$group_category_id]);
        // return redirect('group/service/group_service_item');
    
    }

    // public function destroy($id,$group_category_id= null){

    //         $data = Group_service_item::find($id);
    //         $data->delete();
            
    //         return redirect()->route('group-service-item.index',['group_category_id'=>$group_category_id]);
    //         // return redirect('group/service/group_service_item');
    // }
    public function destroy($id, $group_category_id = null)
{
    // Find the group_service_item by its ID
    $groupServiceItem = Group_service_item::find($id);

    if ($groupServiceItem) {
        // Delete all related group_service_category_item records that reference this group_service_item_id
        Group_service_category_item::where('group_service_item_id', $id)
            ->delete();  // Delete the row


        $groupServiceItem->delete();
    }

    // Redirect back to the group-service-item index with the group_category_id if provided
    return redirect()->route('group-service-item.index', ['group_category_id' => $group_category_id]);
}




}
