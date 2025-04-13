<?php

namespace App\Http\Controllers;
use App\Models\BlogItem;
use App\Models\BlogCategory;
use App\Models\BlogSubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class BlogItemController extends Controller
{
    public function index()
    {
        $data = BlogItem::query()->get();
        return view('blogs.blogItems.index', compact('data'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            // Get sorting details
            $sortColumnIndex = $request->get('order')[0]['column'];
            $sortDirection = $request->get('order')[0]['dir'];

            // Define the column mapping for sorting
            $columns = [
                'blog_items.id',
                'blog_categories.blog_category_name ',
                'blog_sub_categories.blog_sub_category_name',
                'blog_items.main_image',
                'blog_items.blog_title',
                'blog_items.blog_description',
                'blog_items.status',
                'blog_items.display_order',
            ];

            // Ensure sorting column exists in the array
            $sortColumn = $columns[$sortColumnIndex] ?? 'blog_items.id';

            // Fetch the data using a join query with DB facade
            $data = DB::table('blog_items')
                ->join('blog_categories', 'blog_items.blog_category_id', '=', 'blog_categories.id')
                ->join('blog_sub_categories', 'blog_items.blog_sub_category_id', '=', 'blog_sub_categories.id')
                ->select(
                    'blog_items.id',
                    'blog_categories.blog_category_name',
                    'blog_categories.status as category_status', // Fetch category status
                    'blog_sub_categories.blog_sub_category_name',
                    'blog_sub_categories.status as subcategory_status',
                    'blog_items.main_image',
                    'blog_items.blog_slug',
                    'blog_items.blog_title',
                    'blog_items.blog_description',
                    'blog_items.status',
                    'blog_items.display_order',
                );

                // Apply category filter if selected
                // if ($request->has('category_id') && $request->get('category_id') != '') {
                //     $category_id = $request->get('category_id');
                //     $data->where('blog_sub_categories.marketing_house_category_id', $category_id);
                // }

                // if ($request->has('status') && $request->get('status') != '') {
                //     $status = $request->get('status');
                //     $data->where('blog_sub_categories.status', $status);
                // }

                $data->orderBy($sortColumn, $sortDirection);
            // Return the data to the DataTable
            return DataTables::of($data)
                ->addIndexColumn() // Adds the DT_RowIndex column for numbering
                // ->addColumn('main_image', function ($row) {
                //     // Render the poster image
                //     $main_image = $row->main_image ?? '';
                //     return $main_image
                //         ? '<img src="' . asset($main_image) . '" alt="MainImage" width="70" height="70">'
                //         : 'No Image';
                // })
                ->addColumn('main_image', function ($row) {
                    $imgPath = $row->main_image ?? '';
                    $baseUrl = env('AWS_URL'); // Get the base URL from the .env file
                    $imgUrl = $baseUrl . '/' . $imgPath; // Concatenate the base URL with the stored path
                    return '<img src="' . $imgUrl . '" alt="No Image Available" width="70" height="70">';
                })
                ->addColumn('status', function ($row) {
                    // Render the status badge
                    return $row->status == 0
                        ? '<span class="badge bg-danger">Inactive</span>'
                        : '<span class="badge bg-success">Active</span>';
                })
                // ->addColumn('action', function ($row) {
                //     // Render the action buttons
                //     return '
                //         <div class="d-flex">
                //             <a href="' . route('blog_sub_category.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                //                 <i class="fas fa-edit"></i>
                //             </a>
                //             <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('blog_sub_category.destroy', $row->id) . '\');">
                //                 <i class="fas fa-trash"></i>
                //             </a>
                //         </div>
                //     ';
                // })
                ->addColumn('action', function ($row) {
                    $previewButton = '';
                    // if ($row->status == 1 && $row->category_status == 1 && $row->subcategory_status == 1) { // Check both conditions
                    //     $previewButton = '<a href="https://d1r5jv57b9z1uu.cloudfront.net/" target="_blank" class="mb-1 btn btn-info btn-sm mr-2">
                    //                         <i class="fas fa-eye"></i>
                    //                       </a>';
                    // }
                   return '
                        <div class="d-flex">
                            <a href="' . route('blog_items.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            '.$previewButton.'
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('blog_items.destroy', $row->id) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })

                // ->addColumn('navigate', function ($row) {
                //     // Render the action buttons
                //     return '
                //         <div class="d-flex flex-column">

                //              <a href="' . route('marketing-house-image.index', ['marketinghouseitem_id' => $row->id]) . '" class="mb-1 btn btn-primary btn-sm text-nowrap">
                //                Marketing Item Thumbnail
                //             </a>
                //                <a href="' . route('marketing-house-pre-launch-activity.index', ['item_id' => $row->id]) . '" class="mb-1 btn btn-primary btn-sm text-nowrap">
                //                Prelaunch Activities
                //             </a>
                //                 <a href="' . route('marketing-house-other-activity-category.index', ['item_id' => $row->id]) . '" class="mb-1 btn btn-primary btn-sm text-nowrap">
                //                Other Activities Category
                //             </a>
                //             <a href="' . route('marketing-house-content-created-category.index', ['item_id' => $row->id]) . '" class="mb-1 btn btn-primary btn-sm text-nowrap">
                //                Content Category
                //             </a>
                //               <a href="' . route('community_program_category.index', ['item_id' => $row->id]) . '" class="mb-1 btn btn-primary btn-sm text-nowrap">
                //                Continuity Category
                //             </a>

                //         </div>
                //     ';
                // })
                ->rawColumns(['main_image', 'status', 'action']) // Mark columns with raw HTML
                ->make(true);
        }
    }

    public function add(Request $request)
    {
        $blogCategorydata = BlogCategory::query()->get();
        // $blogSubCategorydata = BlogSubCategory::query()->get();
        // Check if this is an AJAX request ( filtering service items by category )
        if ( $request->ajax() ) {
            // Fetch service items based on the selected category from the AJAX request
            $blogSubCategorydata = BlogSubCategory::where( 'blog_category_id', $request->category_id )->get();

            // Return the filtered service items as a JSON response
            return response()->json( $blogSubCategorydata );
        }
        
        return view('blogs.blogItems.add',compact('blogCategorydata'));
    }

    public function store(Request $request)
    {
        $blog_main_image = upload_file_to_s3($request, 'main_image', 'Blog-Main-Image');
        $userId = Auth::user()->id;
        // $slug = $this->generateSlug($request->blog_title);
        $blogSubCategorySlugData = BlogSubCategory::find($request->blog_sub_category_id );
        // print_r($blogSubCategorySlugData->blog_sub_category_slug);
        // die;

        // Create a new BlogCategory instance
        $data = new BlogItem;
        $data->blog_category_id = $request->blog_category_id ?? 0;
        $data->blog_sub_category_id = $request->blog_sub_category_id ?? 0;
        $data->main_image = $blog_main_image ?? '';
        $data->blog_slug = $blogSubCategorySlugData->blog_sub_category_slug ?? '';
        $data->blog_title = $request->blog_title ?? '';
        $data->blog_description = $request->blog_description ?? '';
        $data->blog_meta_keyword = $request->meta_keyword ?? '';
        $data->blog_meta_description = $request->meta_description ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status = $request->status ?? 0;
        $data->save();

        return redirect()->route('blog_items.index');
    }


    public function show(Request $request,$id)
    {
        $blogCategorydata = BlogCategory::query()->get();
        $blogSubCategorydata = BlogSubCategory::query()->get();
        $blogItems = BlogItem::findOrFail($id);
        $selectedCategory=$blogItems->blog_category_id;
        $selectedSubCategory=$blogItems->blog_sub_category_id;
        // print_r($category);
        // die();

        if ( $request->ajax() ) {
            // If it's an AJAX request, return the filtered service items for the selected category
            $categoryId = $request->get('category_id');
            
            // Fetch the service items based on the category selected
            $filteredItems = BlogSubCategory::where('blog_category_id', $categoryId)->get();

            // Return the filtered items as JSON
            return response()->json($filteredItems);
        }

        return view('blogs.blogItems.edit', compact('blogCategorydata','blogSubCategorydata','blogItems','selectedCategory','selectedSubCategory'));
    }

    public function update(Request $request)
    {
        // Get the ID from the request
        $id = $request->id;

        $blogMainImage = upload_file_to_s3($request, 'main_image', 'Blog-main-Image');
        // Find the category to update
        $data = BlogItem::find($id);

        // $slug = $this->generateSlug($request->blog_title, $id);
        $blogSubCategorySlugData = BlogSubCategory::find($request->blog_sub_category_id );
        // print_r($blogSubCategorySlugData->blog_sub_category_slug);
        // die;
        // Get the user ID (from the authenticated user)
        $userId = Auth::user()->id;

        // Prepare the data for updating
        $update = [
            'blog_category_id' => $request->blog_category_id ?? 0,
            'blog_sub_category_id' => $request->blog_sub_category_id ?? 0,
            'main_image' => $blogMainImage ?? $data->main_image ?? '',
            // 'blog_slug' => $slug ?? '',
            'blog_slug'  => $blogSubCategorySlugData->blog_sub_category_slug ?? '',
            'blog_title' => $request->blog_title ?? '',
            'blog_description' => $request->blog_description ?? '',
            'blog_meta_keyword' => $request->meta_keyword ?? '',
            'blog_meta_description' => $request->meta_description ?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status' => $request->status ?? 0,
        ];

        // Update the category
        $data->update($update);

        // Redirect to the desired page after updating
        return redirect()->route('blog_items.index');
    }



    public function destroy($id)
    {
        $data = BlogItem::findOrFail($id);
        $data->delete();

        return redirect()->route('blog_items.index');

    }

    public function generateSlug($slugName, $categoryId = null)
    {
        $slug = strtolower($slugName); 
        $slug = preg_replace('/\s+/', '-', $slug); // Replace spaces with hyphens
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug); // Remove any non-alphanumeric characters (except dash)

        // Check if the slug already exists, but exclude the current category (if editing)
        $originalSlug = $slug;
        $i = 1;
        while (BlogItem::where('blog_slug', $slug)
                    ->where('id', '!=', $categoryId) // Exclude the current category record
                    ->exists()) {
            $slug = $originalSlug . '-' . $i;
            $i++;
        }

        return $slug;
    }
}
