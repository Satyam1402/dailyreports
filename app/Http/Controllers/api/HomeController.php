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
use App\Models\Monthly_performance_showcase_category;
use App\Models\Monthly_performance_showcase_subcategory;
use App\Models\Monthly_performance_showcase;
use App\Models\Development_house_category;
use App\Models\Social_work_category;
use App\Models\User_choice;


// for Group Service Page
use App\Models\Group_top_banner;
use App\Models\Group_service_category;
use App\Models\Group_service_item;
use App\Models\Group_creator_platform;
use App\Models\Group_success_stories;


class HomeController extends Controller
{
 
    public function index(){

        $topBanner = Top_banner::where('status', 1)
                                ->orderBy('display_order', 'asc')
                                ->select('id','book_call_template_id', 'heading', 'sub_heading', 'banner_button_text','banner_button_url',
                                DB::raw("CONCAT(
                                    CASE 
                                        WHEN banner_video_thumbnail IS NULL OR banner_video_thumbnail = '' THEN '' 
                                        ELSE '" . env('AWS_URL') . "/' 
                                    END, banner_video_thumbnail) AS banner_video_thumbnail"),'banner_video_url', 'display_order', 'status')
                                ->first();

        $video = Video::where('status', 1 )
                        ->orderBy('display_order','asc')
                        ->select('id',
                        DB::raw("CONCAT(
                            CASE 
                                WHEN video_url IS NULL OR video_url = '' THEN '' 
                                ELSE '" . env('AWS_URL') . "/' 
                            END, video_url) AS video_url"),
                        'display_order','status')
                        ->first();
                            
        $client = Client::where('status', 1 )
                        ->orderBy('display_order','asc')
                        ->select('id',
                        DB::raw("CONCAT(
                            CASE 
                                WHEN client_img IS NULL OR client_img = '' THEN '' 
                                ELSE '" . env('AWS_URL') . "/' 
                            END, client_img) AS client_img")
                        ,'client_title','client_description','display_order','status')
                        ->get();

        $development_house =Development_house_category::with([
            'items' => function ($query) {
                $query->where('status', 1) // Filter active services
                    ->select('id','development_house_category_id',
                    DB::raw("CONCAT(
                        CASE 
                            WHEN development_house_img IS NULL OR development_house_img = '' THEN '' 
                            ELSE '" . env('AWS_URL') . "/' 
                        END, development_house_img) AS development_house_img"),
                        'development_house_url','display_order','status')
                    ->orderBy('display_order', 'asc'); // Order by display_order
            }])->where('status', 1)
               ->select('id','development_house_category_name', 'display_order','status')
                ->orderBy('display_order', 'asc') // Order by display_order
                ->get();


        $social_work =Social_work_category::with([
                    'items' => function ($query) {
                          $query->where('status', 1) // Filter active services
                                ->select('id','social_work_category_id',
                                DB::raw("CONCAT(
                                    CASE 
                                        WHEN social_work_img IS NULL OR social_work_img = '' THEN '' 
                                        ELSE '" . env('AWS_URL') . "/' 
                                    END, social_work_img) AS social_work_img"),
                                    'social_work_title','display_order','status')
                                ->orderBy('display_order', 'asc'); // Order by display_order
                    }])->where('status', 1)
                       ->select('id','social_work_category_name', 'display_order','status')
                       ->orderBy('display_order', 'asc') // Order by display_order
                       ->get();

        $data=[
            'top_banner' => $topBanner,
            'video' =>$video,
            'client'=>$client,
            'development_house'=> $development_house,
            'social_work' => $social_work,
            ];

            // Return response with top_banner and sorted brands
            return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
 

    public function client()
    {
        $client = Client::where('status',1)
        ->orderBy('service_display_order','asc')
        ->select('id',
        DB::raw("CONCAT(
            CASE 
                WHEN client_img IS NULL OR client_img = '' THEN '' 
                ELSE '" . env('AWS_URL') . "/' 
            END, client_img) AS client_img"),
            'client_title','client_description','display_order','service_display_order','status')
        ->get(); 

        $data=[
            
            'client'=>$client,

            ];

            // Return response with top_banner and sorted brands
            return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function monthly_performance_showcase()
    {
        // Retrieve categories where status is 1, order them by 'display_order'
        $monthly_performance = Monthly_performance_showcase_category::where('status', 1)
            ->orderBy('display_order', 'asc')
            ->select('id',
            DB::raw("CONCAT(
                CASE 
                    WHEN mps_icon IS NULL OR mps_icon = '' THEN '' 
                    ELSE '" . env('AWS_URL') . "/' 
                END, mps_icon) AS mps_icon"),
                'mps_category_name', 'display_order', 'status') // Selecting only required fields
            ->with([
                'mps_subcategory' => function ($query) {
                    $query->select('id', 'mps_category_id', 'mps_subcategory_name', 'display_order', 'status')
                        ->where('status', 1) // Only active subcategories
                        ->orderBy('display_order', 'asc')
                        ->with([
                            'mps_items' => function ($query) {
                                $query->select(
                                    'id', 'mps_category_id', 'mps_subcategory_id', 'mps_title', 'mps_description',
                                    DB::raw("CONCAT(
                                        CASE 
                                            WHEN mps_img IS NULL OR mps_img = '' THEN '' 
                                            ELSE '" . env('AWS_URL') . "/' 
                                        END, mps_img) AS mps_img"),
                                    'display_order', 'status'
                                )
                                ->where('status', 1) // Only active items
                                ->orderBy('display_order', 'asc');
                            }
                        ]);
                }
            ])
    ->get();


          
            $data=[
                'monthly_performance' => $monthly_performance,
           
                ];

            // Return response with top_banner and sorted brands
            return response()->json([
                'status' => 'success',
                'data' => $data
            ]);
    }


    //    public function home(){

    //         $topBanner = Top_banner::where('status', 1)
    //                                 ->select('id', 'heading', 'sub_heading', 'banner_button_text','banner_button_url','banner_video_url','banner_video_thumbnail', 'display_order', 'status')
    //                                 ->first();

    //         //    $topBanner = Top_banner::where('status', 1)
    //         //                         ->select(
    //         //                             'id', 
    //         //                             'heading', 
    //         //                             'sub_heading', 
    //         //                             'button_text as banner_button_text', 
    //         //                             'button_url as banner_button_url', 
    //         //                             'banner_video_url', 
    //         //                             'display_order', 
    //         //                             'status',
    //         //                             \DB::raw("'https://example.com/path/to/dummy-thumbnail.jpg' as banner_video_thumbnail") // Raw expression for dummy field
    //         //                         )->first();

    //         $brands = Brands::where('status', 1)
    //                         ->orderBy('display_order', 'asc')  // Order by display_order
    //                         ->select('id', 'brand_name', 'brand_image', 'display_order', 'status')  // Select only needed columns
    //                         ->get();

    //         // $services = Service_item::with('category') // Eager load the category relationship
    //         //                         ->where('status', 1)  // Filter active services
    //         //                         ->orderBy('display_order', 'asc')  // Order by display_order
    //         //                         ->get();
            
    //         $services = Service_category::with(['items' => function ($query) {
    //             $query->where('status', 1) // Filter active services
    //                 ->select('id','service_category_id','service_image','service_title','button_text as service_button_text','button_url as service_button_url','display_order','status')
    //                 ->orderBy('display_order', 'asc'); // Order by display_order
    //             }])->where('status', 1)
    //                 ->select('id','service_category_name','display_order','status')
    //                 ->orderBy('display_order', 'asc') // Order by display_order
    //                 ->get();

    //         $service_platform = Service_platform::where('status',1)
    //                                             ->orderBy('display_order','asc')
    //                                             ->select('id','platform_image','platform_title','platform_button_text','platform_button_url','display_order','status')
    //                                             ->get();

    //         $video = Video::where('status',1)
    //                         ->orderBy('display_order','asc')
    //                         ->select('id','video_thumbnail_img','video_url','display_order','status')
    //                         ->first();
                                
    //         $client = Client::where('status',1)
    //                                     ->orderBy('display_order','asc')
    //                                     ->select('id','client_img','client_description','display_order','status')
    //                                     ->get();

    //         // $marketting_category =1;

    //         $marketting_house =Marketting_house_category::with(['items' => function ($query) {
    //             $query->where('status', 1) // Filter active services
    //                 ->select('id','marketting_house_category_id','marketting_house_img','marketting_house_url','display_order','status')
    //                 ->orderBy('display_order', 'asc'); // Order by display_order
    //         }])->where('status', 1)
    //             ->select('id','marketting_house_category_name', 'display_order','status')
    //             ->orderBy('display_order', 'asc') // Order by display_order
    //             ->get();   
            
    //         // $creative_category =1;

    //         $creative_house =Creative_house_category::with(['items' => function ($query) {
    //             $query->where('status', 1) // Filter active services
    //                 ->select('id','creative_house_category_id','creative_house_thumbnail','creative_house_video_url','display_order','status')
    //                 ->orderBy('display_order', 'asc'); // Order by display_order
    //         }])->where('status', 1)
    //             ->select('id','creative_house_category_name', 'display_order','status')
    //             ->orderBy('display_order', 'asc') // Order by display_order
    //             ->get();

    //         $development_category =1;

    //         $development_house =Development_house_category::with(['items' => function ($query) {
    //             $query->where('status', 1) // Filter active services
    //                 ->select('id','development_house_category_id','development_house_img','development_house_url','display_order','status')
    //                 ->orderBy('display_order', 'asc'); // Order by display_order
    //         }])->where('status', 1)
    //             ->select('id','development_house_category_name', 'display_order','status')
    //             ->orderBy('display_order', 'asc') // Order by display_order
    //             ->get();

    //         $social_work_category =1;

    //         $social_work =Social_work_category::with(['items' => function ($query) {
    //             $query->where('status', 1) // Filter active services
    //                 ->select('id','social_work_category_id','social_work_img','social_work_title','display_order','status')
    //                 ->orderBy('display_order', 'asc'); // Order by display_order
    //         }])->where('status', 1)
    //             ->select('id','social_work_category_name', 'display_order','status')
    //             ->orderBy('display_order', 'asc') // Order by display_order
    //             ->get();

    //         $hire_us=User_choice::where('status',1)
    //                             ->orderBy('display_order','asc')
    //                             ->select('id','user_choice_title','user_choice_description','user_choice_button_text','user_choice_button_url','display_order','status')
    //                             ->get();
            
    //         $data=[
    //             'top_banner' => $topBanner,
    //             'brands' => $brands,  // Returning all active brands sorted by display_order
    //             'services'=>$services,
    //             'service_platform'=>$service_platform,
    //             'video' =>$video,
    //             'client'=>$client,
    //             'marketting_house'=> $marketting_house,
    //             'creative_house'=> $creative_house,
    //             'development_house'=> $development_house,
    //             'social_work' => $social_work,
    //             'hire_us'=>$hire_us,

    //             ];

    //             // Return response with top_banner and sorted brands
    //             return response()->json([
    //             'status' => 'success',
    //             'data' => $data
    //         ]);
    // }

    // public function group_service(){

    //         $service_category = Service_category::where('status', 1)
    //         ->orderBy('display_order', 'asc')
    //         ->select('id','service_category_name','display_order','status')
    //         ->get();

    //         $service_item = Service_item::where('status', 1)
    //         ->orderBy('display_order', 'asc')
    //         ->select('id','service_category_id','service_image','service_title','button_text as service_button_text','display_order','status')
    //         ->get();

    //         $group_service_banner = Group_top_banner::where('status', 1)
    //         ->orderBy('display_order', 'asc')
    //         ->select('id','explore_our_service_category_id as service_category_id','explore_our_service_item_id as service_item_id','group_banner_heading','group_banner_subheading','group_banner_button_text','group_banner_button_url','group_banner_img','display_order','status')
    //         ->get();

    //         $brands = Brands::where('status', 1)
    //         ->orderBy('display_order', 'asc')  // Order by display_order
    //         ->select('id', 'brand_name', 'brand_image', 'display_order', 'status')  // Select only needed columns
    //         ->get();

    //         $group_service_category=Group_service_category::where('status',1)
    //         ->orderBy('display_order', 'asc')
    //         ->select('id','explore_our_service_category_id as service_category_id','explore_our_service_item_id as service_item_id','group_service_category_name','display_order','status')
    //         ->get();

    //         $group_service_item=Group_service_item::where('status',1)
    //         ->orderBy('display_order', 'asc')
    //         ->select('id','group_service_category_id','group_service_item_thumbnail','group_service_item_title','group_service_item_description as featureed_description','group_service_item_description2','display_order','status')
    //         ->get();

    //         $group_creator_platform=Group_creator_platform::where('status',1)
    //         ->orderBy('display_order', 'asc')
    //         ->select('id','creator_title','creator_thumbnail','creator_thumbnail_url','display_order','status')
    //         ->get();

    //         $group_success_stories=Group_success_stories::where('status',1)
    //         ->orderBy('display_order', 'asc')
    //         ->select('id','success_stories_title','success_stories_img','success_stories_description','success_stories_url','display_order','status')
    //         ->get();

    //         $data=[

    //             'services_category'=>$service_category,
    //             'service_item'=>$service_item,
    //             'group_service_banner'=>$group_service_banner,
    //             'brands'=>$brands,
    //             'group_service_category'=>$group_service_category,
    //             'group_service_item'=>$group_service_item,
    //             'group_creator_platform'=>$group_creator_platform,
    //             'group_success_stories'=>$group_success_stories,

    //             ];

    //         return response()->json([
    //         'status' => 'success',
    //         'data' => $data
    //     ]);
    // }

}
