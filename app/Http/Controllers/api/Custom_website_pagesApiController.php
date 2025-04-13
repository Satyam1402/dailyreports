<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Page;

class Custom_website_pagesApiController extends Controller
 {
     public function custom_pages(){

        $custom_pages = Page::where('status', 1)
        ->orderBy('display_order', 'asc')  // Order by display_order
        ->select('id', 'page_name', 'page_slug','page_title','page_description','page_meta_keyword','page_meta_description', 'display_order', 'status')  // Select only needed columns
        ->get();

        $data=[

            'custom_pages'=>$custom_pages,
            ];

            // Return response with top_banner and sorted brands
            return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
     }
 }