<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


// use App\Models\Top_banner;
// use App\Models\Brands;
// use App\Models\Service_category;
// use App\Models\Service_item;
// use App\Models\Service_platform;
// use App\Models\Video;
// use App\Models\Client;
// use App\Models\Marketting_house_category;
// use App\Models\Creative_house_category;
// use App\Models\Development_house_category;
// use App\Models\Social_work_category;
// use App\Models\User_choice;



// use App\Models\Group_top_banner;
// use App\Models\Group_service_category;
// use App\Models\Group_service_item;
// use App\Models\Group_creator_platform;
// use App\Models\Group_success_stories;

use App\Models\Creative_house_category;
use App\Models\Creative_house_item;
use App\Models\Creative_house_project;
use App\Models\All_button_priority;


class Creative_house_ApiController extends Controller
{
    public function index(){

        // $all_button_priority = All_button_priority::select('dropdown_no', 'creative_house_item_id')->get();
        // $all_button_priority_creative_house = All_button_priority::select('dropdown_no', 'creative_house_item_id')
        //                         ->where('type', 'creative_house')                        
        //                         ->get()
        //                         ->keyBy('dropdown_no');


        // Get all button priorities for 'marketing_house' type
        $all_button_priority_marketing_house = All_button_priority::select( 'dropdown_no', 'creative_house_item_id' )
        ->where( 'type', 'creative_house' )
        ->get()
        ->keyBy( 'dropdown_no' );

        // print_r($all_button_priority_marketing_house->toArray())
        // ;
        // die;
        // Initialize an empty array to store the detailed data
        $formatted_data = [];

        // Loop through the items in the $all_button_priority_marketing_house collection
        foreach ( $all_button_priority_marketing_house as $priority ) {
            // Use marketing_house_item_id to fetch detailed information
            $item_details = Creative_house_item::find($priority->creative_house_item_id );

            // If item_details exists, process the data
            if ( $item_details ) {
                // Store the item data in the formatted_data array
                $formatted_data[] = [
                    // 'dropdown_no' => $priority->dropdown_no,
                    // 'marketing_house_item_id' => $priority->marketing_house_item_id,
                    'id' => $item_details->id, // Assuming you only want one item per category
                    'creative_house_video_title'=>$item_details->creative_house_video_title??'',	
                    'creative_house_thumbnail' => $item_details->creative_house_thumbnail ? env('AWS_URL') . '/' . $item_details->creative_house_thumbnail : '',  // Add poster image with the full URL if it exists
                    'creative_house_upload_video_url' => $item_details->creative_house_upload_video_url	 ? env('AWS_URL') . '/' . $item_details->creative_house_upload_video_url	 : '' , // Add poster image with the full URL if it exists
                    'creative_house_video_url	' => $item_details->creative_house_video_url ?? '',  // Add poster image with the full URL if it exists
                ];
            }
        }


        $creative_house = Creative_house_category::with(['items' => function ($query) {
            $query->where('status', 1) // Filter active services at the items level
                  ->select('id', 'creative_house_category_id', 'author_template_id', 'book_call_template_id', 
                DB::raw("CONCAT(
                    CASE 
                        WHEN creative_house_thumbnail IS NULL OR creative_house_thumbnail = '' THEN '' 
                        ELSE '" . env('AWS_URL') . "/' 
                    END, creative_house_thumbnail) AS creative_house_thumbnail"),
                DB::raw("CONCAT(
                    CASE 
                        WHEN creative_house_upload_video_url IS NULL OR creative_house_upload_video_url = '' THEN '' 
                        ELSE '" . env('AWS_URL') . "/' 
                    END, creative_house_upload_video_url) AS creative_house_upload_video_url"),
                     'creative_house_video_url', 'creative_house_video_title', 
                DB::raw("CONCAT(
                    CASE 
                        WHEN requirement_title IS NULL OR requirement_title = '' THEN '' 
                        ELSE '" . env('AWS_URL') . "/' 
                    END, requirement_title) AS requirement_title_logo"),
                     'requirement_description', 'display_order', 'status')
                  ->with([
                      'creative_house_approach' => function ($approachQuery) {
                          $approachQuery->where('status', 1) // Apply status filter for creative_house_approach
                                        ->select('id', 'creative_house_item_id',
                                        DB::raw("CONCAT(
                                            CASE 
                                                WHEN approach_thumbnail IS NULL OR approach_thumbnail = '' THEN '' 
                                                ELSE '" . env('AWS_URL') . "/' 
                                            END, approach_thumbnail) AS approach_thumbnail"),
                                        DB::raw("CONCAT(
                                            CASE 
                                                WHEN approach_upload_video_url IS NULL OR approach_upload_video_url = '' THEN '' 
                                                ELSE '" . env('AWS_URL') . "/' 
                                            END, approach_upload_video_url) AS approach_upload_video_url"),
                                            'approach_video_url','approach_heading','approach_description','display_order', 'status')
                                        ->orderBy('display_order', 'asc'); // Order by display_order
                      },
                      'creative_house_final_output' => function ($outputQuery) {
                          $outputQuery->where('status', 1) // Apply status filter for creative_house_final_output
                                      ->select('id', 'creative_house_item_id', 'final_output_title',
                                    DB::raw("CONCAT(
                                        CASE 
                                            WHEN final_output_thumbnail IS NULL OR final_output_thumbnail = '' THEN '' 
                                            ELSE '" . env('AWS_URL') . "/' 
                                        END, final_output_thumbnail) AS final_output_thumbnail"),
                                    DB::raw("CONCAT(
                                        CASE 
                                            WHEN final_output_upload_video_url IS NULL OR final_output_upload_video_url = '' THEN '' 
                                            ELSE '" . env('AWS_URL') . "/' 
                                        END, final_output_upload_video_url) AS final_output_upload_video_url"),
                                    'final_output_video_url', 'status')
                                      ->orderBy('display_order', 'asc'); // Order by display_order
                      }
                  ])
                  ->orderBy('display_order', 'asc'); // Order by display_order for items
        }])->select('id', 'creative_house_category_name',
                    DB::raw("CONCAT(
                        CASE 
                            WHEN creative_house_icon IS NULL OR creative_house_icon = '' THEN '' 
                            ELSE '" . env('AWS_URL') . "/' 
                        END, creative_house_icon) AS creative_house_icon"), 
                    'display_order', 'status')
          ->where('status', 1) // Filter active categories
          ->orderBy('display_order', 'asc') // Order by display_order for categories
          ->get();
        

        $creative_house_projects = Creative_house_project::where('status',1)
                                    ->orderBy('display_order','asc')
                                    ->select('id','banner_title_template_id','book_call_template_id','display_order','status')
                                    ->first();
        
        $data=[
            'all_button_priority_creative_house'=>$formatted_data,
            'creative_house'=> $creative_house,
            'creative_house_projects'=>$creative_house_projects,

            ];



            // Return response with top_banner and sorted brands
            return response()->json([
            'status' => 'success',
            'data' => $data
        ]);

        //         // Example data population (add your actual data logic here)
        // $data = [
        //     'top_banner' => $topBanner,  // Assuming $topBanner contains the top banner data
        //     'brands' => $brands          // Assuming $brands contains the list of brands
        // ];

        // // Now return the response with $data
        // return response()->json([
        //     'status' => 'success',
        //     'data' => $data // Return the populated $data array
        // ]);
    }



}
