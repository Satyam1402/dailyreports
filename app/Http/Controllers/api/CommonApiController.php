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
use App\Models\Book_call;
use App\Models\Banner_title_template;
use App\Models\Author_template;

class CommonApiController extends Controller
 {

    public function brand(){

        $brands = Brands::where('status', 1)
        ->orderBy('display_order', 'asc')  // Order by display_order
        ->select('id', 'brand_name',
        DB::raw("CONCAT(
            CASE 
                WHEN brand_image IS NULL OR brand_image = '' THEN '' 
                ELSE '" . env('AWS_URL') . "/' 
            END, brand_image) AS brand_image"), 'display_order', 'status')  // Select only needed columns
        ->get();

        

        $data=[

            'brands'=>$brands,
            ];

            // Return response with top_banner and sorted brands
            return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function Content_service(){

        $creator_platform = Group_creator_platform::where( 'status', 1 )
        ->orderBy( 'display_order', 'asc' )
        ->select( 'id', 'creator_title',
        DB::raw("CONCAT(
            CASE 
                WHEN creator_thumbnail IS NULL OR creator_thumbnail = '' THEN '' 
                ELSE '" . env('AWS_URL') . "/' 
            END, creator_thumbnail) AS creator_thumbnail"), 'creator_thumbnail_url',
            'display_order', 'status' )
        ->get();

        $data=[
            'contnet_creator_platform'=>$creator_platform,
            ];

            // Return response with top_banner and sorted brands
            return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
    
    public function success_stories(){

        $success_stories = Group_success_stories::where( 'status', 1 )
        ->orderBy( 'display_order', 'asc' )
        ->select( 'id', 'success_stories_title',
        DB::raw("CONCAT(
            CASE 
                WHEN success_stories_img IS NULL OR success_stories_img = '' THEN '' 
                ELSE '" . env('AWS_URL') . "/' 
            END, success_stories_img) AS success_stories_img"), 'success_stories_description', 'success_stories_url', 'display_order', 'status' )
        ->get();

        $data=[

            'success_stories'=>$success_stories,
            ];

            // Return response with top_banner and sorted brands
            return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function book_call(){

        $Book_call = Book_call::where( 'status', 1 )
        ->orderBy( 'display_order', 'asc' )
        ->select( 'id',
        DB::raw("CONCAT(
            CASE 
                WHEN book_image IS NULL OR book_image = '' THEN '' 
                ELSE '" . env('AWS_URL') . "/' 
            END, book_image) AS book_image"), 'book_heading', 'book_title1', 'book_description1', 'book_title2', 'book_description2', 'book_button_text', 'book_button_url', 'display_order', 'status' )
        ->get();

        $data=[

            'book_call'=>$Book_call,
            ];

            // Return response with top_banner and sorted brands
            return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function hire_us(){

        $hire_us=User_choice::where('status',1)
        ->orderBy('display_order','asc')
        ->select('id',
        DB::raw("CONCAT(
            CASE 
                WHEN user_choice_image IS NULL OR user_choice_image = '' THEN '' 
                ELSE '" . env('AWS_URL') . "/' 
            END, user_choice_image) AS user_choice_image"),
            'user_choice_title','user_choice_description','user_choice_button_text','display_order','status')
        ->get();

        $data=[

            'hire_us'=>$hire_us,
            ];

            // Return response with top_banner and sorted brands
            return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function banner_title(){

        $banner_title=Banner_title_template::where('status',1)
        ->orderBy('display_order','asc')
        ->select('id','banner_title','banner_description','banner_total_video','banner_short_text',
        DB::raw("CONCAT(
            CASE 
                WHEN banner_bg_img IS NULL OR banner_bg_img = '' THEN '' 
                ELSE '" . env('AWS_URL') . "/' 
            END, banner_bg_img) AS banner_bg_img"),'display_order','status')
        ->get();

        $data=[

            'banner_title_template'=>$banner_title,
            ];

            // Return response with top_banner and sorted brands
            return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function author(){

        $author_template=Author_template::where('status',1)
        ->orderBy('display_order','asc')
        ->select('id',
        DB::raw("CONCAT(
            CASE 
                WHEN author_image IS NULL OR author_image = '' THEN '' 
                ELSE '" . env('AWS_URL') . "/' 
            END, author_image) AS author_image"),'author_description','click_here_text','click_here_url','author_name','author_url','founder_text','founder_url','cto_text','cto_url','display_order','status')
        ->get();

        $data=[

            'author_template'=>$author_template,
            ];

            // Return response with top_banner and sorted brands
            return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }




 }