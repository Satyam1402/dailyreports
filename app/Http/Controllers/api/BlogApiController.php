<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


use App\Models\BlogCategory;
use App\Models\BlogSubCategory;
use App\Models\BlogItem;

class BlogApiController extends Controller
 {

    public function blog(){

        $blog = BlogCategory::with(['subCategories' => function ($query) {
            $query->where('status', 1)
                  ->select('id','blog_category_id', 'blog_sub_category_name','blog_sub_category_slug', 'display_order', 'status')
                  ->with([
                      'blogItems' => function ($Blogitem) {
                          $Blogitem->where('status', 1) 
                                        ->select('id','blog_category_id', 'blog_sub_category_id',
                                        DB::raw("CONCAT(
                                            CASE 
                                                WHEN main_image IS NULL OR main_image = '' THEN '' 
                                                ELSE '" . env('AWS_URL') . "/' 
                                            END, main_image) AS main_image"),'blog_slug', 'blog_title', 'blog_description','blog_meta_keyword','blog_meta_description', 'display_order', 'status')
                                        ->orderBy('display_order', 'asc'); 
                      },
                  ])
                  ->orderBy('display_order', 'asc'); 
        }])->where('status', 1)
        ->orderBy('display_order', 'asc')
        ->select('id','blog_category_name', 'display_order', 'status')
        ->get();

        $data=[

            'blog'=>$blog,
            ];

            // Return response with top_banner and sorted brands
            return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }


    public function blog_category(){

        $blog = BlogCategory::with(['subCategories' => function ($query) {
            $query->where('status', 1)
                  ->select('id','blog_category_id', 'blog_sub_category_name','blog_sub_category_slug', 'display_order', 'status')
                  ->orderBy('display_order', 'asc'); 
        }])->where('status', 1)
        ->orderBy('display_order', 'asc')
        ->select('id','blog_category_name', 'display_order', 'status')
        ->get();

        $data=[

            'blog'=>$blog,
            ];

            // Return response with top_banner and sorted brands
            return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    // public function getBlogItemBySlug1($slug)
    // {
    //     // Fetch blog item based on the slug, ordered by display_order
    //     $blogItem = BlogItem::where('blog_slug', $slug)
    //         ->orderBy('display_order', 'asc')
    //         ->select('id','blog_category_id as category_id', 'blog_sub_category_id as sub_category_id', 'main_image as image', 'blog_title as title','blog_slug as slug', 'blog_description as description','blog_meta_keyword as meta_keyword','blog_meta_description as meta_description', 'display_order', 'status')
    //         ->get();
       
    //     $data=[

    //         'blogItem'=>$blogItem,
    //         ];

    //     return response()->json([
    //         'status' => 'success',
    //         'data' => $data
    //     ]);
    // }

    public function getBlogItemBySlug(Request $request, $slug)
{ 
    // Set the default limit and offset
    $limit = $request->input('limit', 10); // Default limit is 10
    $offset = $request->input('offset', 0); // Default offset is 0

    // Fetch blog items based on the slug, ordered by display_order with limit and offset
    $blogItems = BlogItem::where('blog_slug', $slug)
        ->orderBy('display_order', 'asc')
        ->select('id','blog_category_id as category_id', 'blog_sub_category_id as sub_category_id',
        DB::raw("CONCAT(
            CASE 
                WHEN main_image IS NULL OR main_image = '' THEN '' 
                ELSE '" . env('AWS_URL') . "/' 
            END, main_image) AS image"),
         'blog_title as title', 'blog_slug as slug', 'blog_description as description',
          'blog_meta_keyword as meta_keyword', 'blog_meta_description as meta_description', 
          'display_order', 'status')
        ->skip($offset)
        ->take($limit)
        ->get();

        // Combine meta_keywords and meta_descriptions from all blog items
        $metaKeywords = $blogItems->pluck('meta_keyword')->filter()->implode(', ');
        $metaDescriptions = $blogItems->pluck('meta_description')->filter()->implode(' ');

    // Return the paginated response
    $data = [
        'blogItems' => $blogItems,
        'meta_keywords' => $metaKeywords, // Combined meta keywords
        'meta_description' => $metaDescriptions, // Combined meta description
    ];

    return response()->json([
        'status' => 'success',
        'data' => $data
    ]);
}


public function getBlogItemDetailById($id)
{
    // Fetch the blog item by its ID
    $blogItem = BlogItem::where('id', $id)
        ->select(
            'id',
            'blog_category_id as category_id', 
            'blog_sub_category_id as sub_category_id',
            DB::raw("CONCAT(
                CASE 
                    WHEN main_image IS NULL OR main_image = '' THEN '' 
                    ELSE '" . env('AWS_URL') . "/' 
                END, main_image) AS image"), 
            'blog_title as title', 
            'blog_slug as slug', 
            'blog_description as description',
            'blog_meta_keyword as meta_keyword',
            'blog_meta_description as meta_description', 
            'display_order', 
            'status'
        )
        ->first();

    // Check if the blog item exists


    // Optionally, you can also fetch related data like category or subcategory
    // $category = BlogCategory::find($blogItem->category_id);
    // $subCategory = BlogSubCategory::find($blogItem->sub_category_id);

    // Prepare the response data
    $data = [
        'blogItem' => $blogItem,
        // 'category' => $category ? $category->name : null,
        // 'subCategory' => $subCategory ? $subCategory->name : null,
    ];
    if (!$blogItem) {
        return response()->json([
            'status' => 'error',
            'message' => 'Blog item not found',
            'data' => $data
        ], 404);
    }

    return response()->json([
        'status' => 'success',
        'data' => $data
    ]);
}




 }