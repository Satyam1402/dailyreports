<?php

namespace App\Http\Controllers;

use App\Models\Service_category;
use App\Models\Service_item;
use App\Models\Group_service_category;
use App\Models\Group_single_service_image;
use App\Models\Group_service_item;
use App\Models\Group_single_service_portfolio_category;
use App\Models\Group_single_service_portfolio_item;

use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class Group_single_service_portfolio_itemController extends Controller
{
    public function index($portfolio_category_id = null,$group_category_id = null)
    {
        $groupitemdata = Group_single_service_portfolio_category::find($portfolio_category_id);
        $group_item_id=$groupitemdata->group_service_item_id ?? '';
        $data = Group_single_service_portfolio_item::with(['portfoliocategory','groupserviceitem'])->get();
        // print_r($group_item_id);
        // die;
        return view('service.group_single_service_portfolio_item.group_single_service_portfolio_item',compact('data','portfolio_category_id','group_item_id','group_category_id')); 
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {

            $group_category_id =request('group_category_id'); 

            $sortColumnIndex = $request->get('order')[0]['column']; // value depend on datatable field not in table
            // echo 'working';
            // echo $sortColumnIndex;
           
            $sortDirection = $request->get('order')[0]['dir'];
    
            $columns = [
                'id','group_service_item_id','portfolio_category_id','portfolio_video_thumbnail','portfolio_video_url','display_order','status'
            ];
            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table
            
            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            // $data = Group_single_service_portfolio_item::select('*')->with(['portfoliocategory','groupserviceitem'])->orderBy($sortColumn, $sortDirection);

            $query = Group_single_service_portfolio_item::select('*')->with(['portfoliocategory','groupserviceitem']);

            // Check if item_id is passed in the request and filter based on it
            if ($request->has('portfolio_category_id') && $request->portfolio_category_id != null) {
                $query->where('portfolio_category_id',  $request->input('portfolio_category_id') ); // Filter by item_id
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
                ->addColumn('portfolio_category_name', function ($row) {
                    return $row->portfoliocategory->portfolio_category_name ? $row->portfoliocategory->portfolio_category_name : '';
                })
                // ->addColumn('portfolio_video_thumbnail', function ($row) {
                //     $imgUrl = $row->portfolio_video_thumbnail ?? '';
                //     return '<img src="' . $imgUrl . '" alt="Image" width="70" height="70">';
                // })
                ->addColumn('portfolio_video_thumbnail', function ($row) {
                    $imgPath = $row->portfolio_video_thumbnail ?? '';
                    $baseUrl = env('AWS_URL'); // Get the base URL from the .env file
                    $imgUrl = $baseUrl . '/' . $imgPath; // Concatenate the base URL with the stored path
                    return '<img src="' . $imgUrl . '" alt="No Image Available" width="70" height="70">';
                })
                ->editColumn('portfolio_video_url', function ($row) {
                    return '<a href="'. $row->portfolio_video_url. '" target="_blank">Click</a>';
                })
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
                    
                    if ($row->status == 1 && $row->portfoliocategory->status == 1 && $row->groupserviceitem->status == 1 && $groupServiceCategoryStatus == 1 && $serviceItemStatus == 1 && $serviceCategoryStatus ==1) {
                        $previewButton = '<a href="https://d1r5jv57b9z1uu.cloudfront.net/Single_Services/ '. $row->groupserviceitem->id .'" target="_blank" class="mb-1 btn btn-info btn-sm mr-2">
                                <i class="fas fa-eye"></i>
                            </a>';
                    }
                    // if ($row->status == 1 &&  $row->portfoliocategory->status == 1 && $row->groupserviceitem->status == 1 && $row->groupserviceitem->groupservicecategory->status == 1 && $row->groupserviceitem->groupservicecategory->serviceItem->status == 1 && $row->groupserviceitem->groupservicecategory->serviceItem->category->status == 1) {
                    //     $previewButton = '<a href="https://d1r5jv57b9z1uu.cloudfront.net/Single_Services/ '. $row->groupserviceitem->id .'" target="_blank" class="mb-1 btn btn-info btn-sm mr-2">
                    //             <i class="fas fa-eye"></i>
                    //         </a>';
                    // }
                    return '
                        <div class="d-flex">
                            <a href="' . route('group-single-service-portfolio-item.show', ['id' => $row->id, 'portfolio_category_id'=>request('portfolio_category_id'),'group_category_id'=>request('group_category_id')]) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            '.$previewButton.'
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('group-single-service-portfolio-item.destroy', ['id' => $row->id, 'portfolio_category_id'=>request('portfolio_category_id'),'group_category_id'=>request('group_category_id')]) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['group_service_item_title','portfolio_category_name','portfolio_video_thumbnail','portfolio_video_url','status','action'])
                ->make(true);
        }
    }



    // public function add(){
    //     // $data = Top_banner::query()->get();
    //     // $homeservicecategory = Service_category::query()->get();
    //     $groupserviceitem = Group_service_item::query()->get();
    //     $portfolioservice = Group_single_service_portfolio_category::query()->get();

    //    return view('service.group_single_service_portfolio_item.add_group_single_service_portfolio_item', compact('portfolioservice','groupserviceitem'));
       
    // }
    public function add(Request $request, $portfolio_category_id = null,$group_category_id = null) {
        // Fetch all service categories
        $groupserviceitem = Group_service_item::all();
    
        // Fetch the selected portfolio service if portfolio_category_id is provided
        $selectedGroupServiceItemId = null;
        if ($portfolio_category_id) {
            $selectedPortfolioService = Group_single_service_portfolio_category::find($portfolio_category_id);
            if ($selectedPortfolioService) {
                $selectedGroupServiceItemId = $selectedPortfolioService->group_service_item_id; // Get the group_service_item_id
            }
        }
    
        // Fetch portfolio categories only if a group_service_item_id is selected
        $portfolioCategories = $selectedGroupServiceItemId ?
            Group_single_service_portfolio_category::where('group_service_item_id', $selectedGroupServiceItemId)->get() :
            [];
    
        // Check if this is an AJAX request (filtering service items by category)
        if ($request->ajax()) {
            $portfolioservice = Group_single_service_portfolio_category::where('group_service_item_id', $request->item_id)->get();
            return response()->json($portfolioservice);
        }
    
        // Return the view with both categories and default service items
        return view('service.group_single_service_portfolio_item.add_group_single_service_portfolio_item', compact(
            'groupserviceitem', 'portfolio_category_id', 'selectedGroupServiceItemId', 'portfolioCategories','group_category_id'
        ));
    }
    
    // public function add1( Request $request ,$portfolio_category_id = null) {
    //     // Fetch all service categories
    //     $groupserviceitem = Group_service_item::all();

    //         // If portfolio_category_id is provided, fetch the specific portfolio service
    //     $selectedPortfolioService = null;
    //     $selectedGroupServiceItemId = null;
    //     if ($portfolio_category_id) {
    //         $selectedPortfolioService = Group_single_service_portfolio_category::find($portfolio_category_id);  // Get the portfolio service by portfolio_category_id
    //         if ($selectedPortfolioService) {
    //             $selectedGroupServiceItemId = $selectedPortfolioService->group_service_item_id;  // Get the group_service_item_id of the selected portfolio
    //         }
    //     }

    //     // Check if this is an AJAX request ( filtering service items by category )
    //     if ( $request->ajax() ) {
    //         // Fetch service items based on the selected category from the AJAX request
    //         $portfolioservice = Group_single_service_portfolio_category::where( 'group_service_item_id', $request->item_id )->get();
    //         // $homeserviceitem = Service_item::where( 'service_category_id', $request->category_id )->get();

    //         // Return the filtered service items as a JSON response
    //         return response()->json( $portfolioservice );
    //     }


    //     // Return the view with both categories and default service items
    //     return view('service.group_single_service_portfolio_item.add_group_single_service_portfolio_item', compact('groupserviceitem','portfolio_category_id','selectedGroupServiceItemId'));
    //     // return view( 'service.group_service_top_banner.add_group_service_top_banner', compact( 'homeservicecategory' ) );
    // }

    // Method to store a new service
    public function store(Request $request)
    {
        $portfolio_category_id=$request->portfolio_category_id;
        $group_category_id=$request->group_category_id;
        $request->validate( [
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',
       
        ] );
        // $video = upload_file_to_s3($request, 'portfolio_video_url', 'service-portfolio-video');
        $image = upload_file_to_s3($request, 'portfolio_video_thumbnail', 'service-portfolio-thumbnail');
        $userId=Auth::user()->id;

        $data = new Group_single_service_portfolio_item;
        // $data->explore_our_service_category_id = $request->explore_our_service_category_id ?? 0;
        $data->group_service_item_id = $request->group_service_item_id ?? 0;
        $data->portfolio_category_id = $request->portfolio_category_id ?? 0;
        // $data->portfolio_video_url = $video ?? '';
        $data->portfolio_video_url = $request->portfolio_video_url ?? '';
        $data->portfolio_video_thumbnail = $image ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status=$request->status ?? 0;
        $data->save();

        return redirect()->route('group-single-service-portfolio-item.index',['portfolio_category_id'=>$portfolio_category_id,'group_category_id'=>$group_category_id]);
        // return redirect('group/service/portfolio/group_single_service_portfolio_item');

    }


    // public function show1(Request $request,$id)
    // {

    //         $potfoliocategory = Group_single_service_portfolio_category::query()->get();
    //         $groupserviceitem = Group_service_item::query()->get();
    //         $data = Group_single_service_portfolio_item::find($id);
    //         $selectedgroupItem = $data->group_service_item_id;
    //         $selectedItem = $data-> portfolio_category_id ;
    //         //   echo '<pre>';
    //         //   print_r($data->toArray());
    //         //   die;
    //         return view('service.group_single_service_portfolio_item.edit_group_single_service_portfolio_item',compact('data','potfoliocategory','selectedItem','groupserviceitem','selectedgroupItem'));
    // }

    public function show( Request $request, $id , $portfolio_category_id = null, $group_category_id=null) 
    {
        
        $groupserviceitem = Group_service_item::all();

        $potfoliocategory = Group_single_service_portfolio_category::all();

        $data = Group_single_service_portfolio_item::find($id);

        // selected category and item
        $selectedgroupItem = $data->group_service_item_id;
        $selectedItem = $data-> portfolio_category_id ;

        // Check if the request is an AJAX request
        if ( $request->ajax() ) {
            // If it's an AJAX request, return the filtered service items for the selected category
            $itemId = $request->get('item_id');
            $filteredItems = Group_single_service_portfolio_category::where('group_service_item_id', $itemId)->get();

            // print_r($filteredItems);
            // die;
            // Return the filtered items as JSON
            return response()->json($filteredItems);
        }

        return view('service.group_single_service_portfolio_item.edit_group_single_service_portfolio_item',compact('data','potfoliocategory','selectedItem','groupserviceitem','selectedgroupItem','portfolio_category_id','group_category_id'));

    }


    // update an existing service
    public function update(Request $request)
    {

        $portfolio_category_id=$request->portfolio_category_id;
        $group_category_id=$request->group_category_id;
        $request->validate( [
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',
       
        ] );

        // $video = upload_file_to_s3($request, 'portfolio_video_url', 'service-portfolio-video');
        $image = upload_file_to_s3($request, 'portfolio_video_thumbnail', 'service-portfolio-thumbnail');
        $id = $request->id;

      
        
        $data = Group_single_service_portfolio_item::find($id);
                
        $userId=Auth::user()->id;
        $update = [


            // 'explore_our_service_category_id' => $request->explore_our_service_category_id ?? 0,
            'group_service_item_id' => $request->group_service_item_id ?? 0,
            'portfolio_category_id' => $request->portfolio_category_id ?? 0,
            // 'portfolio_video_url' => $video ?? $data->portfolio_video_url ?? '',
            'portfolio_video_url' => $request->portfolio_video_url ?? '',
            'portfolio_video_thumbnail' => $image ?? $data->portfolio_video_thumbnail ?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status'=> $request->status ?? 0,
          
        ];
        // print_r($update);
        // die;

        $data->update($update);
        // $data->save();
        
        return redirect()->route('group-single-service-portfolio-item.index',['portfolio_category_id'=>$portfolio_category_id,'group_category_id'=>$group_category_id]);
        // return redirect('group/service/portfolio/group_single_service_portfolio_item');
    
    }

    public function destroy($id ,$portfolio_category_id = null,$group_category_id=null){
            // $id = $request->id;
            $data = Group_single_service_portfolio_item::find($id);
            $data->delete();
            
            return redirect()->route('group-single-service-portfolio-item.index',['portfolio_category_id'=>$portfolio_category_id ,'group_category_id'=>$group_category_id]);
            // return redirect('group/service/portfolio/group_single_service_portfolio_item');
    }



}
