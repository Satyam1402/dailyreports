<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Top_banner;
use App\Models\Brands;
use App\Models\Service_category;
use App\Models\Service_item;
use App\Models\Service_platform;
use App\Models\Video;
use App\Models\Client;
use App\Models\Marketting_house_category;
use App\Models\Creative_house_category;
use App\Models\Development_house_category;
use App\Models\Social_work_category;
use App\Models\User_choice;



use App\Models\Group_top_banner;
use App\Models\Group_service_category;
use App\Models\Group_creator_platform;
use App\Models\Group_success_stories;

use App\Models\Group_service_item;
use App\Models\Group_single_service_image;
use App\Models\Group_single_service_recent_work;
use App\Models\Group_single_service_portfolio_category;
use App\Models\Group_single_service_portfolio_item;



class Single_serviceApiController extends Controller
{

    public function single_service($id)
    {

        // $group_service_item=Group_service_item::where('status',1)
        // ->orderBy('display_order', 'asc')
        // ->select('id','group_service_category_id','group_service_item_thumbnail','group_service_item_title','group_service_item_description as featureed_description','group_service_item_description2','display_order','status')
        // ->get();

        $group_service_item = Group_service_item::where('status', 1)
        ->where('id', $id) // Filter by the provided 'id'
        ->orderBy('display_order', 'asc')
        ->select('id', 'group_service_category_id', 'group_service_item_thumbnail', 'group_service_item_title', 'group_service_item_description as featureed_description', 'group_service_item_description2', 'display_order', 'status')
        ->first(); // Use first() to get a single result

        if (!$group_service_item) {
            return response()->json([
                'status' => 'success',
                'message' => 'No data found',
                'data' => [
                    'group_service_item' => [],
                    'other_group_service_items' => [],
                    'single_service_image' => [],
                    'service_recent_work' => [],
                    'service_portfolio_category' => [],
                    'service_portfolio_item' => [],
                ]
            ]);
        }

          // Fetch all data for the same 'group_service_category_id' but exclude the current 'id'
        $other_group_service_items = Group_service_item::where('status', 1)
        ->where('group_service_category_id', $group_service_item->group_service_category_id) // Filter by category ID
        ->where('id', '!=', $id) // Exclude the current item by its 'id'
        ->orderBy('display_order', 'asc')
        ->select('id', 'group_service_category_id', 'group_service_item_thumbnail', 'group_service_item_title', 'group_service_item_description as featureed_description', 'group_service_item_description2', 'display_order', 'status')
        ->get();

           // Fetch images related to the current group_service_item
        $single_service_image = Group_single_service_image::where('status', 1)
        ->where('group_service_item_id', $group_service_item->id) // Filter images by the current group_service_item's ID
        ->orderBy('display_order', 'asc')
        ->select('id', 'group_service_item_id', 'single_service_img', 'display_order', 'status')
        ->get();

        $service_recent_work=Group_single_service_recent_work::where('status',1)
        ->where('group_service_item_id', $group_service_item->id) // Filter images by the current group_service_item's ID
        ->orderBy('display_order', 'asc')
        ->select('id','group_service_item_id','recent_work_video','display_order','status')
        ->get();

        $service_portfolio_category=Group_single_service_portfolio_category::where('status',1)
        ->where('group_service_item_id', $group_service_item->id) 
        ->orderBy('display_order', 'asc')
        ->select('id','group_service_item_id','portfolio_category_name','display_order','status')
        ->get();

        // $service_portfolio_item = Group_single_service_portfolio_item::where('status', 1)
        //     ->orderBy('display_order', 'asc')
        //     ->select('id', 'portfolio_category_id', 'portfolio_video_url', 'portfolio_video_thumbnail', 'display_order', 'status')
        //     ->get();

      if ($service_portfolio_category->isNotEmpty()) {
            // Get all category IDs from the portfolio categories
            $category_ids = $service_portfolio_category->pluck('id')->toArray();

            // Fetch the portfolio items that belong to these categories
            $service_portfolio_item = Group_single_service_portfolio_item::where('status', 1)
                ->whereIn('portfolio_category_id', $category_ids)  // Filter by category IDs
                ->orderBy('display_order', 'asc')
                ->select('id', 'portfolio_category_id', 'portfolio_video_url', 'portfolio_video_thumbnail', 'display_order', 'status')
                ->get();
        } else {
            // If no portfolio categories were found, return an empty collection
            $service_portfolio_item = collect();  // This returns an empty collection
        }


        $data=[

            // 'group_service_category'=>$group_service_category,
            'group_service_item'=>$group_service_item,
            'other_group_service_items'=>$other_group_service_items,
            'single_service_image'=>$single_service_image,
            'service_recent_work'=>$service_recent_work,
            'service_portfolio_category'=>$service_portfolio_category ,
            'service_portfolio_item'=>$service_portfolio_item,
            ];



            // Return response with top_banner and sorted brands
            return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    } 

}