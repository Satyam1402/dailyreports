<?php

namespace App\Http\Controllers;
use App\Models\BlogItem;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use App\Models\BlogSubCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class BlogSubCategoryController extends Controller
{
    public function index()
    {
        // echo "hello world";
        // exit;
        $data = BlogSubCategory::query()->get();
        return view('blogs.blogSubCategory.index', compact('data'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            // Get sorting details
            $sortColumnIndex = $request->get('order')[0]['column'];
            $sortDirection = $request->get('order')[0]['dir'];

            // Define the column mapping for sorting
            $columns = [
                'blog_sub_categories.id',
                'blog_categories.blog_category_name ',
                'blog_sub_categories.blog_sub_category_name',
                'blog_sub_categories.status',
                'blog_sub_categories.display_order',
            ];

            // Ensure sorting column exists in the array
            $sortColumn = $columns[$sortColumnIndex] ?? 'blog_sub_categories.id';

            // Fetch the data using a join query with DB facade
            $data = DB::table('blog_sub_categories')
                ->join('blog_categories', 'blog_sub_categories.blog_category_id', '=', 'blog_categories.id')
                ->select(
                    'blog_sub_categories.id',
                    'blog_categories.blog_category_name',
                    'blog_categories.status as category_status', // Fetch category status
                    'blog_sub_categories.blog_sub_category_name',
                    'blog_sub_categories.display_order',
                    'blog_sub_categories.status',
                );

                // Apply category filter if selected
                // if ($request->has('category_id') && $request->get('category_id') != '') {
                //     $category_id = $request->get('category_id');
                //     $data->where('blog_sub_categories.marketing_house_category_id', $category_id);
                // }

                if ($request->has('status') && $request->get('status') != '') {
                    $status = $request->get('status');
                    $data->where('blog_sub_categories.status', $status);
                }

                $data->orderBy($sortColumn, $sortDirection);
            // Return the data to the DataTable
            return DataTables::of($data)
                ->addIndexColumn() // Adds the DT_RowIndex column for numbering
                // ->addColumn('poster_image', function ($row) {
                //     // Render the poster image
                //     $poster_image = $row->poster_image ?? '';
                //     return $poster_image
                //         ? '<img src="' . asset($poster_image) . '" alt="Poster Image" width="70" height="70">'
                //         : 'No Image';
                // })
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
                    // if ($row->status == 1 && $row->category_status == 1) { // Check both conditions
                    //     $previewButton = '<a href="https://d1r5jv57b9z1uu.cloudfront.net/" target="_blank" class="mb-1 btn btn-info btn-sm mr-2">
                    //                         <i class="fas fa-eye"></i>
                    //                       </a>';
                    // }
                   return '
                        <div class="d-flex">
                            <a href="' . route('blog_sub_category.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            '.$previewButton.'
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('blog_sub_category.destroy', $row->id) . '\');">
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
                ->rawColumns(['poster_image', 'status', 'action','navigate']) // Mark columns with raw HTML
                ->make(true);
        }
    }

    public function add()
    {
        $blogCategorydata = BlogCategory::query()->get();
        return view('blogs.blogSubCategory.add',compact('blogCategorydata'));
    }

    public function store(Request $request)
    {
        $userId = Auth::user()->id;
        $slug = $this->generateSlug($request->blog_sub_category_name);

        // Create a new BlogCategory instance
        $data = new BlogSubCategory;
        $data->blog_category_id = $request->blog_category_id ?? 0;
        $data->blog_sub_category_name = $request->blog_sub_category_name ?? '';
        $data->blog_sub_category_slug = $slug ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status = $request->status ?? 0;
        $data->save();

        return redirect()->route('blog_sub_category.index');
    }


    public function show($id)
    {
        $blogCategorydata = BlogCategory::query()->get();
        $blogSubCategorydata = BlogSubCategory::findOrFail($id);
        // print_r($category);
        // die();
        return view('blogs.blogSubCategory.edit', compact('blogCategorydata','blogSubCategorydata'));
    }

    public function update(Request $request)
    {
        // Get the ID from the request
        $id = $request->id;

        // Find the category to update
        $data = BlogSubCategory::find($id);
        $slug = $this->generateSlug($request->blog_sub_category_name, $id);


        // Get the user ID (from the authenticated user)
        $userId = Auth::user()->id;

        // Prepare the data for updating
        $update = [
            'blog_category_id' => $request->blog_category_id ?? '',
            'blog_sub_category_name' => $request->blog_sub_category_name ?? '',
            'blog_sub_category_slug' => $slug ?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status' => $request->status ?? 0,
        ];

        // Update the category
        $data->update($update);

        // Redirect to the desired page after updating
        return redirect()->route('blog_sub_category.index');
    }



    public function destroy($id)
    {
        $data = BlogSubCategory::findOrFail($id);
        $data->delete();

        return redirect()->route('blog_sub_category.index');

    }

    public function generateSlug($slugName, $categoryId = null)
    {
        $slug = strtolower($slugName); 
        $slug = preg_replace('/\s+/', '-', $slug); // Replace spaces with hyphens
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug); // Remove any non-alphanumeric characters (except dash)

        // Check if the slug already exists, but exclude the current category (if editing)
        $originalSlug = $slug;
        $i = 1;
        while (BlogSubCategory::where('blog_sub_category_slug', $slug)
                    ->where('id', '!=', $categoryId) // Exclude the current category record
                    ->exists()) {
            $slug = $originalSlug . '-' . $i;
            $i++;
        }

        return $slug;
    }
}
