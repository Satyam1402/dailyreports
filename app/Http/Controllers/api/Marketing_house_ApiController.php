<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\MarketingHouseCategory;
use App\Models\MarketingHouseItem;
use App\Models\Marketing_house_project;
use App\Models\MarketingHouseContentCreatedItemCarousel;
use App\Models\All_button_priority;

class Marketing_house_ApiController extends Controller {

    public function index() {


        // Get all button priorities for 'marketing_house' type
        $all_button_priority_marketing_house = All_button_priority::select( 'dropdown_no', 'marketing_house_item_id' )
        ->where( 'type', 'marketing_house' )
        ->get()
        ->keyBy( 'dropdown_no' );

        // Initialize an empty array to store the detailed data
        $formatted_data = [];

        // Loop through the items in the $all_button_priority_marketing_house collection
        foreach ( $all_button_priority_marketing_house as $priority ) {
            // Use marketing_house_item_id to fetch detailed information
            $item_details = MarketingHouseItem::find($priority->marketing_house_item_id );

            // If item_details exists, process the data
            if ( $item_details ) {
                // Store the item data in the formatted_data array
                $formatted_data[] = [
                    // 'dropdown_no' => $priority->dropdown_no,
                    // 'marketing_house_item_id' => $priority->marketing_house_item_id,
                    'id' => $item_details->id, // Assuming you only want one item per category
                    'poster_image' => $item_details->poster_image ? env('AWS_URL') . '/' . $item_details->poster_image : null  // Add poster image with the full URL if it exists
                ];
            }
        }

        // Return the formatted data as an API response
        // return response()->json( [
        //     'status' => 'success',
        //     'data' => $formatted_data
        // ] );

        $marketing_house = MarketingHouseCategory::where( 'status', 1 )
        ->with( [
            'items' => function ( $query ) {
                $query->select(
                    'id', 'marketing_house_category_id', 'title',
                    DB::raw( "CONCAT(
                        CASE 
                            WHEN poster_image IS NULL OR poster_image = '' THEN '' 
                            ELSE '" . env( 'AWS_URL' ) . "/' 
                        END, poster_image) AS poster_image" ),
                    'year', 'author_template_id', 'book_call_template_id', 'client', 'genre',
                    'cast', 'directors', 'description', 'client_requirement_text',
                    'client_requirement_1', 'client_requirement_2', 'client_requirement_3',
                    'client_requirement_4', 'client_requirement_5', 'client_requirement_6', 'ideas_strategy_planning_title', 'ideas_strategy_planning_description',
                    DB::raw( "CONCAT(
                        CASE 
                            WHEN ideas_strategy_planning_image IS NULL OR ideas_strategy_planning_image = '' THEN '' 
                            ELSE '" . env( 'AWS_URL' ) . "/' 
                        END, ideas_strategy_planning_image) AS ideas_strategy_planning_image" ),
                    'display_order', 'status'
        )
                ->where( 'status', 1 )
                ->orderBy( 'display_order', 'asc' )
                ->with( [
                    'images' => function ( $query ) {
                        $query->select( 'id', 'marketing_house_item_id',
                        DB::raw( "CONCAT(
                            CASE 
                                WHEN image IS NULL OR image = '' THEN '' 
                                ELSE '" . env( 'AWS_URL' ) . "/' 
                            END, image) AS image" ),
                            DB::raw( "CONCAT(
                                CASE 
                                    WHEN marketing_item_upload_video_url IS NULL OR marketing_item_upload_video_url = '' THEN '' 
                                    ELSE '" . env( 'AWS_URL' ) . "/' 
                                END, marketing_item_upload_video_url) AS marketing_item_upload_video_url" ),
                            'marketing_item_video_url',
                        'display_order', 'status' )
                        ->where( 'status', 1 )
                        ->orderBy( 'display_order', 'asc' );

                    }
                    ,
                    // Adding the pre_launch_activity relation
                    'pre_launch_activity' => function ( $query ) {
                        $query->select( 'id', 'marketing_house_item_id', 'title', 'description',
                        DB::raw( "CONCAT(
                            CASE 
                                WHEN image IS NULL OR image = '' THEN '' 
                                ELSE '" . env( 'AWS_URL' ) . "/' 
                            END, image) AS image" ),
                            'display_order', 'status' ) // Select only necessary columns
                        ->where( 'status', 1 )
                        ->orderBy( 'display_order', 'asc' );
                        // Adjust the ordering if necessary
                    }
                    ,
                    // Adding the other_activity relation
                    'other_activity_category' => function ( $query ) {
                        $query->select( 'id', 'marketing_house_item_id', 'category_name', 'display_order', 'status' ) // Select necessary columns
                        ->where( 'status', 1 )
                        ->orderBy( 'display_order', 'asc' ) // Adjust the order by if needed
                        ->with( [
                            'other_activity_item' => function ( $query ) {
                                $query->select( 'id', 'marketing_house_item_id', 'marketing_house_other_activity_category_id', 'title', 'description',
                                DB::raw( "CONCAT(
                                    CASE 
                                        WHEN image1 IS NULL OR image1 = '' THEN '' 
                                        ELSE '" . env( 'AWS_URL' ) . "/' 
                                    END, image1) AS image1" ),
                                DB::raw( "CONCAT(
                                    CASE 
                                        WHEN image2 IS NULL OR image2 = '' THEN '' 
                                        ELSE '" . env( 'AWS_URL' ) . "/' 
                                    END, image2) AS image2" ),
                                DB::raw( "CONCAT(
                                    CASE 
                                        WHEN image3 IS NULL OR image3 = '' THEN '' 
                                        ELSE '" . env( 'AWS_URL' ) . "/' 
                                    END, image3) AS image3" ),
                                DB::raw( "CONCAT(
                                    CASE 
                                        WHEN image4 IS NULL OR image4 = '' THEN '' 
                                        ELSE '" . env( 'AWS_URL' ) . "/' 
                                    END, image4) AS image4" ),
                                'display_order', 'status' )
                                ->where( 'status', 1 )
                                ->orderBy( 'display_order', 'asc' );
                                // Adjust order by as needed
                            }
                            ,
        ] );
                    }
                    ,
                    // Adding the content_created_category relation
                    'content_created_category' => function ( $query ) {
                        $query->select( 'id', 'marketing_house_item_id', 'category_name', 'display_order', 'status' )
                        ->where( 'status', 1 )
                        ->orderBy( 'display_order', 'asc' )
                        ->with( [
                            'content_created_item' => function ( $query ) {
                                $query->select( 'id', 'marketing_house_item_id', 'marketing_house_content_created_category_id',
                                DB::raw( "CONCAT(
                                    CASE 
                                        WHEN image IS NULL OR image = '' THEN '' 
                                        ELSE '" . env( 'AWS_URL' ) . "/' 
                                    END, image) AS image" ),
                                'url', 'display_order', 'status' )
                                ->where( 'status', 1 )
                                ->orderBy( 'display_order', 'asc' );
                                // Adjust order by as needed
                            }
                            ,
                            'content_created_carousal' => function ( $query ) {
                                $query->select( 'id', 'marketing_house_item_id', 'marketing_house_content_created_category_id', 'carousel_order',
                                DB::raw( "CONCAT(
                                    CASE 
                                        WHEN image IS NULL OR image = '' THEN '' 
                                        ELSE '" . env( 'AWS_URL' ) . "/' 
                                    END, image) AS image" ),
                                'display_order', 'status' )
                                ->where( 'status', 1 )
                                ->orderBy( 'display_order', 'asc' );
                                // Adjust ordering if necessary
                            }
        ] );
                        // Adjust ordering if necessary
                    }
                    ,
                    // Adding the continuity_category relation
                    'continuity_category' => function ( $query ) {
                        $query->select( 'id', 'marketing_house_item_id', 'community_program_category_name', 'community_program_category_description', 'display_order', 'status' )
                        ->where( 'status', 1 )
                        ->orderBy( 'display_order', 'asc' )
                        ->with( [
                            'continuity_item' => function ( $query ) {
                                $query->select( 'id', 'community_program_category_id',
                                DB::raw( "CONCAT(
                                    CASE 
                                        WHEN community_program_item_video_thumbnail IS NULL OR community_program_item_video_thumbnail = '' THEN '' 
                                        ELSE '" . env( 'AWS_URL' ) . "/' 
                                    END, community_program_item_video_thumbnail) AS community_program_item_video_thumbnail" ),
                                DB::raw( "CONCAT(
                                    CASE 
                                        WHEN community_program_item_video_file IS NULL OR community_program_item_video_file = '' THEN '' 
                                        ELSE '" . env( 'AWS_URL' ) . "/' 
                                    END, community_program_item_video_file) AS community_program_item_video_file" ),
                                'community_program_item_video_url', 'community_program_item_description', 'display_order', 'status' )
                                ->where( 'status', 1 )
                                ->orderBy( 'display_order', 'asc' );
                                // Adjust ordering if necessary
                            }
        ] );
                        // Adjust ordering if necessary
                    }
        ] );
            }
        ] )
        ->orderBy( 'display_order', 'asc' )
        ->select( 'id', 'category_name',
        DB::raw( "CONCAT(
            CASE 
                WHEN marketing_house_icon IS NULL OR marketing_house_icon = '' THEN '' 
                ELSE '" . env( 'AWS_URL' ) . "/' 
            END, marketing_house_icon) AS marketing_house_icon" ),
             'display_order', 'status' )
        ->get();

        $marketing_house_projects = Marketing_house_project::where( 'status', 1 )
        ->orderBy( 'display_order', 'asc' )
        ->select( 'id', 'banner_title_template_id', 'book_call_template_id', 'display_order', 'status' )
        ->first();

        $data = [
            'all_button_priority_marketing_house'=>$formatted_data,
            'marketing_house'=> $marketing_house,
            'marketing_house_projects'=>$marketing_house_projects,
        ];

        // Return response with top_banner and sorted brands
        return response()->json( [
            'status' => 'success',
            'data' => $data
        ] );
    }

    // public function marketing_content_created_carousal(Request $request)
    // {
    //     $carousalData=MarketingHouseContentCreatedItem::where()-get();
    // }
    public function marketing_content_created_carousal(Request $request)
{
    // Extracting parameters from the request
    $limit = $request->get('limit', 10); // Default limit is 10 if not provided
    $offset = $request->get('offset', 0); // Default offset is 0 if not provided
    $carouselOrderSlug = $request->get('carousel_order', null); // Slug for carousel_order
    $marketingItemSlug = $request->get('marketing_item_id', null); // Slug for marketing_house_item_id

    // Starting the query
    $query = MarketingHouseContentCreatedItemCarousel::query();

    // Filtering by slug for carousel_order if provided
    if ($carouselOrderSlug) {
        $query->where('carousel_order', $carouselOrderSlug);
    }

    // Filtering by slug for marketing_house_item_id if provided
    if ($marketingItemSlug) {
        $query->where('marketing_house_item_id', $marketingItemSlug);
    }

    // Apply limit and offset
    $carousalData = $query->select(
            'id',
            'marketing_house_item_id',
            'marketing_house_content_created_category_id',
            DB::raw("CONCAT(
                CASE 
                    WHEN image IS NULL OR image = '' THEN '' 
                    ELSE '" . env('AWS_URL') . "/' 
                END, image) AS image"
            ),
            'carousel_order',
            'display_order',
            'status'
        )
        ->where('status', 1)
        ->orderBy('display_order', 'asc')
        ->limit($limit)  // Adding limit
        ->offset($offset) // Adding offset
        ->get();

    $data = [
        'carousalData'=>$carousalData,
     
    ];

    // Return response with top_banner and sorted brands
    return response()->json( [
        'status' => 'success',
        'data' => $data
    ] );
}

}
