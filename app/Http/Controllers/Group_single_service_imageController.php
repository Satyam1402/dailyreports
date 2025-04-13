<?php

namespace App\Http\Controllers;

use App\Models\Service_category;
use App\Models\Service_item;
use App\Models\Group_service_category;
use App\Models\Group_single_service_image;
use App\Models\Group_service_item;

use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class Group_single_service_imageController extends Controller
{
    public function index($group_item_id = null,$group_category_id = null)
    {

        $groupitemdata = Group_service_item::find($group_item_id);
        $category_id=$group_category_id ?? '';
        $data = Group_single_service_image::with(['groupserviceitem'])->get();
        // print_r($category_id);
        // die;
        return view('service.group_single_service_image.group_single_service_image',compact('data','group_item_id','group_category_id')); 
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {

            $group_category_id=request('group_category_id');
            // dd($group_category_id);
            $sortColumnIndex = $request->get('order')[0]['column']; // value depend on datatable field not in table
            // echo 'working';
            // echo $sortColumnIndex;
           
            $sortDirection = $request->get('order')[0]['dir'];
    
            // $columns = [
            //     'id','group_service_item_id','single_service_img','single_service_upload_video','single_service_video_url','display_order','status'
            // ];  
            $columns = [
                'id','group_service_item_id','single_service_img','display_order','status'
            ];
            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table
            
            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            // $data = Group_single_service_image::select('*')->with(['groupserviceitem'])->orderBy($sortColumn, $sortDirection);

            $query = Group_single_service_image::select('*')->with(['groupserviceitem']);

            // Check if item_id is passed in the request and filter based on it
            if ($request->has('group_item_id') && $request->group_item_id != null) {
                $query->where('group_service_item_id',  $request->input('group_item_id') ); // Filter by item_id
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
                ->addColumn('group_service_item_title', function ($row) {
                    return $row->groupserviceitem->group_service_item_title ? $row->groupserviceitem->group_service_item_title : '';
                })
                // ->addColumn('single_service_img', function ($row) {
                //     $imgUrl = $row->single_service_img ?? '';
                //     return '<img src="' . $imgUrl . '" alt="Image" width="70" height="70">';
                // })
                ->addColumn('single_service_img', function ($row) {
                    $imgPath = $row->single_service_img ?? '';
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

                    $groupServiceCategory = $row->groupserviceitem->groupServiceCategories->firstWhere('id', $group_category_id);
                    $groupServiceCategoryStatus = $groupServiceCategory->status ?? null;

                    // $exploreServiceCategoryId = $groupServiceCategory->explore_our_service_category_id ?? null;
                    $serviceCategory = $groupServiceCategory->serviceCategory ?? null; 
                    $serviceCategoryStatus = $serviceCategory ? $serviceCategory->status : null;


                    $exploreServiceItemId = $groupServiceCategory->explore_our_service_item_id ?? null;
                    $serviceItem = $groupServiceCategory->serviceItem ?? null; // This will load the related Service_item
                    $serviceItemStatus = $serviceItem ? $serviceItem->status : null;
                    
                    if ($row->status == 1 &&  $row->groupserviceitem->status == 1 && $groupServiceCategoryStatus == 1 && $serviceItemStatus == 1 && $serviceCategoryStatus ==1) {
                        $previewButton = '<a href="https://d1r5jv57b9z1uu.cloudfront.net/Single_Services/ '. $row->groupserviceitem->id .'" target="_blank" class="mb-1 btn btn-info btn-sm mr-2">
                                <i class="fas fa-eye"></i>
                            </a>';
                    }
                    return '
                        <div class="d-flex">
                            <a href="' . route('group-single-service-img.show', ['id' => $row->id, 'group_item_id' => request('group_item_id'), 'group_category_id' => request('group_category_id')]) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            '.$previewButton.'
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('group-single-service-img.destroy', ['id' => $row->id, 'group_item_id' => request('group_item_id'), 'group_category_id' => request('group_category_id')]) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['group_service_item_title','single_service_img','status','action'])
                ->make(true);
        }
    }


    public function add($group_item_id = null,$group_category_id=null){
        // $data = Top_banner::query()->get();
        // $homeservicecategory = Service_category::query()->get();
        $groupserviceitem = Group_service_item::query()->get();

       return view('service.group_single_service_image.add_group_single_service_image', compact('groupserviceitem','group_item_id','group_category_id'));
       
    }

    // Method to store a new service
    public function store(Request $request)
    {
        $group_item_id= $request->group_item_id;
        $group_category_id=$request->group_category_id;
        $request->validate( [
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',
       
        ] );

        $image = upload_file_to_s3($request, 'single_service_img', 'single-service-image');
        $video = upload_file_to_s3($request, 'single_service_upload_video', 'single-service-video');
        $userId=Auth::user()->id;

        $data = new Group_single_service_image;
        // $data->explore_our_service_category_id = $request->explore_our_service_category_id ?? 0;
        $data->group_service_item_id = $request->group_service_item_id ?? 0;
        $data->single_service_img = $image ?? '';
        $data->single_service_upload_video = $video ?? '';
        $data->single_service_video_url = $request->single_service_video_url ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status=$request->status ?? 0;
        $data->save();
        
        return redirect()->route('group-single-service-img.index',['group_item_id'=>$group_item_id,'group_category_id'=>$group_category_id]);
        // return redirect('group/service/group_single_service_image');

    }


    public function show(Request $request,$id,$group_item_id = null,$group_category_id=null)
    {
        
            $data = Group_single_service_image::with(['groupserviceitem'])->get();

            $homeservicecategory = Service_category::query()->get();
            $groupserviceitem = Group_service_item::query()->get();
            $data = Group_single_service_image::find($id);
            $selectedItem = $data->group_service_item_id;
            // print_r($selectedCategory->toArray());

            return view('service.group_single_service_image.edit_group_single_service_image',compact('data','groupserviceitem', 'selectedItem','group_item_id','group_category_id'));
            // return response()->json(['data' => $data]);
    }


    // Method to update an existing service
    public function update(Request $request)
    {

        $group_item_id= $request->group_item_id;
        $group_category_id= $request->group_category_id;
        $request->validate( [
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',
       
        ] );
        $id = $request->id;
        $data = Group_single_service_image::find($id);

        $image = upload_file_to_s3($request, 'single_service_img', 'single-service-image');
        // $video = upload_file_to_s3($request, 'single_service_upload_video', 'single-service-video');
        
        // Initialize the video variable
        $video = null;

         // Check if the user uploaded a new video
        if ($request->hasFile('single_service_upload_video')) {
            // Upload the new video and store it
            $video = upload_file_to_s3($request, 'single_service_upload_video', 'single-service-video');
        } elseif ($request->has('existing_video') && $request->existing_video) {
            // If no new video is uploaded, use the existing video from the hidden input
            $video = $request->existing_video;
        } else {
            // If the user has removed the video (no video uploaded, and no existing video)
            $video = '';  // This will remove the old video
        }
                
        $userId=Auth::user()->id;
        $update = [

            // 'explore_our_service_category_id' => $request->explore_our_service_category_id ?? 0,
            'group_service_item_id' => $request->group_service_item_id ?? 0,
            'single_service_img' => $image ?? $data->single_service_img ?? '',
            'single_service_upload_video' => $video,
            'single_service_video_url' => $request->single_service_video_url ?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status'=> $request->status ?? 0,
          
        ];
        // print_r($update);
        // die;

        $data->update($update);
        // $data->save();
        
        return redirect()->route('group-single-service-img.index',['group_item_id'=>$group_item_id ,'group_category_id'=>$group_category_id]);
        // return redirect('group/service/group_single_service_image');
    
    }

    public function destroy($id,$group_item_id = null,$group_category_id=null){

            $data = Group_single_service_image::find($id);
            $data->delete();
            
            return redirect()->route('group-single-service-img.index',['group_item_id'=>$group_item_id ,'group_category_id'=>$group_category_id]);
            // return redirect('group/service/group_single_service_image');
    }



}
