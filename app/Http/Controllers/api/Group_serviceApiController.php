<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


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
use App\Models\Group_service_item;
use App\Models\Group_creator_platform;
use App\Models\Group_success_stories;

class Group_serviceApiController extends Controller
 {
    // public function group_service($id) {

    //     // $service_category = Service_category::where( 'status', 1 )
    //     // ->orderBy( 'display_order', 'asc' )
    //     // ->select( 'id', 'service_category_name', 'display_order', 'status' )
    //     // ->get();

    //     $service_item = Service_item::where( 'status', 1 )
    //     ->where('id', $id)
    //     ->orderBy( 'display_order', 'asc' )
    //     ->select( 'id', 'service_category_id', 'service_image', 'service_title', 'button_text as service_button_text', 'display_order', 'status' )
    //     ->first();

    //     if (!$service_item) {
    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'No data found',
    //             'data' => []
    //         ]);
    //     }

    //     $group_service_banner = Group_top_banner::where( 'status', 1 )
    //     ->where('explore_our_service_item_id', $id) 
    //     ->orderBy( 'display_order', 'asc' )
    //     ->select( 'id', 'explore_our_service_category_id as service_category_id', 'explore_our_service_item_id as service_item_id', 'group_banner_heading', 'group_banner_subheading', 'group_banner_button_text', 'group_banner_button_url', 'group_banner_img', 'display_order', 'status' )
    //     ->get();

    //     // $brands = Brands::where( 'status', 1 )
    //     // ->orderBy( 'display_order', 'asc' )  // Order by display_order
    //     // ->select( 'id', 'brand_name', 'brand_image', 'display_order', 'status' )  // Select only needed columns
    //     // ->get();

    //     $group_service_category = Group_service_category::where( 'status', 1 )
    //     ->where('explore_our_service_item_id', $id) 
    //     ->orderBy( 'display_order', 'asc' )
    //     ->select( 'id', 'explore_our_service_category_id as service_category_id', 'explore_our_service_item_id as service_item_id', 'group_service_category_name', 'display_order', 'status' )
    //     ->get();

    //     // $group_service_item = Group_service_item::where( 'status', 1 )
    //     // ->where('group_service_category_id', $group_service_category->id) 
    //     // ->orderBy( 'display_order', 'asc' )
    //     // ->select( 'id', 'group_service_category_id', 'group_service_item_thumbnail', 'group_service_item_title', 'group_service_item_description as featureed_description', 'group_service_item_description2', 'display_order', 'status' )
    //     // ->get();
    //     $group_service_item=[];
    //     foreach ($group_service_category as $category) {
    //         $group_service_item = Group_service_item::where('status', 1)
    //             ->where('group_service_category_id', $category->id)  // Access the id from each item in the collection
    //             ->orderBy('display_order', 'asc')
    //             ->select('id', 'group_service_category_id', 'group_service_item_thumbnail', 'group_service_item_title', 'group_service_item_description as featureed_description', 'group_service_item_description2', 'display_order', 'status')
    //             ->get();
            
    //         // Process the $group_service_item here
    //     }

    //     $group_creator_platform = Group_creator_platform::where( 'status', 1 )
    //     ->orderBy( 'display_order', 'asc' )
    //     ->select( 'id', 'creator_title', 'creator_thumbnail', 'creator_thumbnail_url', 'display_order', 'status' )
    //     ->get();

    //     $group_success_stories = Group_success_stories::where( 'status', 1 )
    //     ->orderBy( 'display_order', 'asc' )
    //     ->select( 'id', 'success_stories_title', 'success_stories_img', 'success_stories_description', 'success_stories_url', 'display_order', 'status' )
    //     ->get();

    //     $data = [

    //         // 'services_category'=>$service_category,
    //         'service_item'=>$service_item,
    //         'group_service_banner'=>$group_service_banner,
    //         // 'brands'=>$brands,
    //         'group_service_category'=>$group_service_category,
    //         'group_service_item'=>$group_service_item,
    //         'group_creator_platform'=>$group_creator_platform,
    //         'group_success_stories'=>$group_success_stories,
    //     ];

    //     // Return response with top_banner and sorted brands
    //     return response()->json( [
    //         'status' => 'success',
    //         'data' => $data
    //     ] );
    // }

    public function service_detail(){

        $services = Service_category::with(['service_items' => function ($query) {
            $query->where('status', 1) // Filter active services
                  ->select('id','service_category_id',
                  DB::raw("CONCAT(
                    CASE 
                        WHEN service_image IS NULL OR service_image = '' THEN '' 
                        ELSE '" . env('AWS_URL') . "/' 
                    END, service_image) AS service_image"),
                    'service_title','button_text as service_button_text','button_url as service_button_url','display_order','status')
                  ->orderBy('display_order', 'asc')
                ->with([
                    'group_top_banner' => function ($query){
                    $query->where('status',1)
                          ->orderBy( 'display_order', 'asc' )
                          ->select( 'id', 'explore_our_service_category_id', 'explore_our_service_item_id', 'group_banner_heading', 'group_banner_subheading', 'group_banner_button_text', 'group_banner_button_url',
                          DB::raw("CONCAT(
                            CASE 
                                WHEN group_banner_img IS NULL OR group_banner_img = '' THEN '' 
                                ELSE '" . env('AWS_URL') . "/' 
                            END, group_banner_img) AS group_banner_img"),
                            'display_order', 'status' );
                    },
                    'group_service_category' => function ($query) {
                    $query->where('status', 1)
                        ->orderBy( 'display_order', 'asc' )
                        ->select( 'id', 'explore_our_service_category_id as service_category_id', 'explore_our_service_item_id', 'group_service_category_name', 'display_order', 'status' )
                
                        ->with([
                            'apiGroupServiceItems' => function ($query) {
                            $query->where('status', 1)
                                ->orderBy( 'display_order', 'asc' )
                                ->select('group_service_item.id',
                                DB::raw("CONCAT(
                                    CASE 
                                        WHEN group_service_item_thumbnail IS NULL OR group_service_item_thumbnail = '' THEN '' 
                                        ELSE '" . env('AWS_URL') . "/' 
                                    END, group_service_item_thumbnail) AS group_service_item_thumbnail"),
                                 'group_service_item_title', 'group_service_item_description as featureed_description', 'group_service_item_description2', 'display_order', 'status')
                                ->with([
                                    'group_single_service_image' => function ($query) {
                                        $query->where('status', 1)
                                        ->orderBy( 'display_order', 'asc' )
                                        ->select('id', 'group_service_item_id',
                                        DB::raw("CONCAT(
                                            CASE 
                                                WHEN single_service_img IS NULL OR single_service_img = '' THEN '' 
                                                ELSE '" . env('AWS_URL') . "/' 
                                            END, single_service_img) AS single_service_img"),
                                        DB::raw("CONCAT(
                                            CASE 
                                                WHEN single_service_upload_video IS NULL OR single_service_upload_video = '' THEN '' 
                                                ELSE '" . env('AWS_URL') . "/' 
                                            END, single_service_upload_video) AS single_service_upload_video"),
                                        'single_service_video_url','display_order', 'status');

                                    },
                                    'group_single_service_recent_work' => function ($query) {
                                        // Optionally, filter or select fields for group_single_service_recent_work
                                        $query->where('status', 1)
                                        ->orderBy( 'display_order', 'asc' )
                                        ->select('id','group_service_item_id',
                                        DB::raw("CONCAT(
                                            CASE 
                                                WHEN recent_work_video_thumbnail IS NULL OR recent_work_video_thumbnail = '' THEN '' 
                                                ELSE '" . env('AWS_URL') . "/' 
                                            END, recent_work_video_thumbnail) AS recent_work_video_thumbnail"),
                                            'recent_work_video','display_order','status');
                                    },
                                    'group_single_service_portfolio_category' => function ($query) {
                                        // Optionally, filter or select fields for group_single_service_portfolio_category
                                        $query->where('status', 1)
                                        ->orderBy( 'display_order', 'asc' )
                                        ->select('id','group_service_item_id','portfolio_category_name','display_order','status')
                                        ->with([
                                            'group_single_service_portfolio_item' => function ($query) {
                                                $query->where('status', 1)
                                                ->orderBy( 'display_order', 'asc' )
                                                ->select('id', 'portfolio_category_id', 'portfolio_video_url',
                                                DB::raw("CONCAT(
                                                    CASE 
                                                        WHEN portfolio_video_thumbnail IS NULL OR portfolio_video_thumbnail = '' THEN '' 
                                                        ELSE '" . env('AWS_URL') . "/' 
                                                    END, portfolio_video_thumbnail) AS portfolio_video_thumbnail"),
                                                'display_order', 'status');

                                            }
                                        ]);

                                    }

                                ])
                                ;
                            }]);
                
                }]);
        }])
        ->where('status', 1)
        ->select('id','service_category_name',
        DB::raw("CONCAT(
            CASE 
                WHEN service_icon IS NULL OR service_icon = '' THEN '' 
                ELSE '" . env('AWS_URL') . "/' 
            END, service_icon) AS service_icon"),
            'display_order','status')
        ->orderBy('display_order', 'asc') // Order by display_order
        ->get();


                $data = [

                    // 'services_category'=>$service_category,
                    'services'=>$services,
                    
                ];
        
                // Return response with top_banner and sorted brands
                return response()->json( [
                    'status' => 'success',
                    'data' => $data
                ] );

    }
    // public function service_detail(){

    //     $services = Service_category::with(['service_items' => function ($query) {
    //         $query->where('status', 1) // Filter active services
    //               ->select('id','service_category_id','service_image','service_title','button_text as service_button_text','button_url as service_button_url','display_order','status')
    //               ->orderBy('display_order', 'asc')
    //             ->with([
    //                 'group_top_banner' => function ($query){
    //                 $query->where('status',1)
    //                       ->orderBy( 'display_order', 'asc' )
    //                       ->select( 'id', 'explore_our_service_category_id', 'explore_our_service_item_id', 'group_banner_heading', 'group_banner_subheading', 'group_banner_button_text', 'group_banner_button_url', 'group_banner_img', 'display_order', 'status' );
    //                 },
    //                 'group_service_category' => function ($query) {
    //                 $query->where('status', 1)
    //                     ->orderBy( 'display_order', 'asc' )
    //                     ->select( 'id', 'explore_our_service_category_id as service_category_id', 'explore_our_service_item_id', 'group_service_category_name', 'display_order', 'status' )
                
    //                     ->with([
    //                         'group_service_item' => function ($query) {
    //                         $query->where('status', 1)
    //                             ->orderBy( 'display_order', 'asc' )
    //                             ->select('id', 'group_service_category_id', 'group_service_item_thumbnail', 'group_service_item_title', 'group_service_item_description as featureed_description', 'group_service_item_description2', 'display_order', 'status')
    //                             ->with([
    //                                 'group_single_service_image' => function ($query) {
    //                                     $query->where('status', 1)
    //                                     ->orderBy( 'display_order', 'asc' )
    //                                     ->select('id', 'group_service_item_id', 'single_service_img','single_service_upload_video','single_service_video_url','display_order', 'status');

    //                                 },
    //                                 'group_single_service_recent_work' => function ($query) {
    //                                     // Optionally, filter or select fields for group_single_service_recent_work
    //                                     $query->where('status', 1)
    //                                     ->orderBy( 'display_order', 'asc' )
    //                                     ->select('id','group_service_item_id','recent_work_video_thumbnail','recent_work_video','display_order','status');
    //                                 },
    //                                 'group_single_service_portfolio_category' => function ($query) {
    //                                     // Optionally, filter or select fields for group_single_service_portfolio_category
    //                                     $query->where('status', 1)
    //                                     ->orderBy( 'display_order', 'asc' )
    //                                     ->select('id','group_service_item_id','portfolio_category_name','display_order','status')
    //                                     ->with([
    //                                         'group_single_service_portfolio_item' => function ($query) {
    //                                             $query->where('status', 1)
    //                                             ->orderBy( 'display_order', 'asc' )
    //                                             ->select('id', 'portfolio_category_id', 'portfolio_video_url', 'portfolio_video_thumbnail', 'display_order', 'status');

    //                                         }
    //                                     ]);

    //                                 }

    //                             ]);
    //                         }]);
                
    //             }]);
    //     }])
    //     ->where('status', 1)
    //     ->select('id','service_category_name','display_order','status')
    //     ->orderBy('display_order', 'asc') // Order by display_order
    //     ->get();


    //             $data = [

    //                 // 'services_category'=>$service_category,
    //                 'services'=>$services,
                    
    //             ];
        
    //             // Return response with top_banner and sorted brands
    //             return response()->json( [
    //                 'status' => 'success',
    //                 'data' => $data
    //             ] );

    // }

}