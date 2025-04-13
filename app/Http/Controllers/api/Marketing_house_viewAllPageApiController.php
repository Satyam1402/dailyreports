<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\MarketingHouseCategory;
use App\Models\MarketingHouseItem;
use App\Models\Marketing_house_project;
use App\Models\All_button_priority;

class Marketing_house_viewAllPageApiController extends Controller {

    public function Marketing_filter_data() {

        $marketingCategoryData = MarketingHouseCategory::where( 'status', 1 )

        ->orderBy( 'display_order', 'asc' )
        ->select( 'id', 'category_name',
        // DB::raw( "CONCAT(
        //     CASE 
        //         WHEN marketing_house_icon IS NULL OR marketing_house_icon = '' THEN '' 
        //         ELSE '" . env( 'AWS_URL' ) . "/' 
        //     END, marketing_house_icon) AS marketing_house_icon" ),
        'display_order', 'status' )
        ->get();

        $marketingYearData = MarketingHouseItem::where('status', 1)
        
    ->select('year') // Only select the 'year' column to avoid duplicates from other columns
    ->orderBy('year', 'desc') // Order by year in descending order
    ->groupBy('year') // Group by year to get distinct years
    ->get();

        $marketing_house_projects = Marketing_house_project::where( 'status', 1 )
        ->orderBy( 'display_order', 'asc' )
        ->select( 'id', 'banner_title_template_id', 'book_call_template_id', 'display_order', 'status' )
        ->first();

        $data = [

            'marketingCategory'=> $marketingCategoryData,
            'marketingYear'=> $marketingYearData,
            'marketing_house_projects'=>$marketing_house_projects,
        ];

        // Return response with top_banner and sorted brands
        return response()->json( [
            'status' => 'success',
            'data' => $data
        ] );
    }

    // public function Marketing_house_item() {
    //     $marketingItemData = MarketingHouseItem::where( 'status', 1 )
    //     ->select(
    //                     'id', 'marketing_house_category_id', 'title',
    //                     DB::raw( "CONCAT(
    //                         CASE 
    //                             WHEN poster_image IS NULL OR poster_image = '' THEN '' 
    //                             ELSE '" . env( 'AWS_URL' ) . "/' 
    //                         END, poster_image) AS poster_image" ),
    //                     'year',
    //                     //  'author_template_id', 'book_call_template_id', 'client', 'genre',
    //                     // 'cast', 'directors', 'description', 'client_requirement_text',
    //                     // 'client_requirement_1', 'client_requirement_2', 'client_requirement_3',
    //                     // 'client_requirement_4', 'client_requirement_5', 'client_requirement_6', 'ideas_strategy_planning_title', 'ideas_strategy_planning_description',
    //                     // DB::raw( "CONCAT(
    //                     //     CASE 
    //                     //         WHEN ideas_strategy_planning_image IS NULL OR ideas_strategy_planning_image = '' THEN '' 
    //                     //         ELSE '" . env( 'AWS_URL' ) . "/' 
    //                     //     END, ideas_strategy_planning_image) AS ideas_strategy_planning_image" ),
    //                     'display_order', 'status'
    // )
    //     // ->select( 'id', 'year', 'display_order', 'status' )  // Only select the 'year' column to avoid duplicate values for other fields
    //     ->orderBy( 'display_order', 'asc' )  // Order by year in descending order
    //     ->get();

    //     $data = [
    //         'marketingItemData'=> $marketingItemData,
    // ];

    //     // Return response with top_banner and sorted brands
    //     return response()->json( [
    //         'status' => 'success',
    //         'data' => $data
    // ] );
    // }

    public function Marketing_house_item( Request $request ) {
        // Get filters and pagination values from the request
        $limit = $request->input( 'limit' );
        // Default limit is 10
        $offset = $request->input( 'offset' ,0);
    
        // Default offset is 0
        $year = $request->input( 'year', null );
        // Filter by year ( optional )
        $categoryId = $request->input( 'category_id', null );
        $title = $request->input( 'title', null );
        // Filter by category_id ( optional )

        // Start building the query
        $query = MarketingHouseItem::where( 'status', 1 )
        ->select(
            'id', 'marketing_house_category_id', 'title',
            DB::raw( "CONCAT(
                            CASE 
                                WHEN poster_image IS NULL OR poster_image = '' THEN '' 
                                ELSE '" . env( 'AWS_URL' ) . "/' 
                            END, poster_image) AS poster_image" ),
            'year',
            'display_order', 'status'
        )
        ->orderBy( 'display_order', 'asc' );
        // Default ordering by display_order
        $totalCount = $query->count();
        // Apply filters if provided
        if ( $year ) {
            $query->where( 'year', $year );
        }

        if ( $categoryId ) {
            $query->where( 'marketing_house_category_id', $categoryId );
        }

        if ( $title ) {
            $query->where('title', 'like', '%' . $title . '%');
        }

        // Apply limit and offset for pagination
        if (!empty($limit) && is_numeric($limit)) {
            // Apply limit and offset only if limit is provided and numeric
            $marketingItemData = $query->skip($offset)->take($limit)->get();
        } else {
            // If no limit is provided, get all data
            $marketingItemData = $query->get();
        }

        $data = [
            'marketingItemData' => $marketingItemData,
            'total_marketingItem'=>$totalCount,
        ];

        // Return response with filtered data and pagination
        return response()->json( [
            'status' => 'success',
            'data' => $data,

        ] );
    }

}