<?php

namespace App\Http\Controllers;
use App\Models\BlogItem;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use App\Models\BlogSubCategory;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class BlogCategoryController extends Controller
{
    public function index()
    {
        // echo "hello world";
        // exit;
        $data = BlogCategory::query()->get();
        return view('blogs.blogCategory.index', compact('data'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {

            $sortColumnIndex = $request->get('order')[0]['column']; // value depend on datatable field not in table
            // echo 'working';
            // echo $sortColumnIndex;

            $sortDirection = $request->get('order')[0]['dir'];

            // Map column index to actual column names (you can adjust this as per your columns)
            $columns = [
                'id','blog_category_name','display_order',
                'status'
            ]; // value depend on datatable field not in table

            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table

            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            $data = BlogCategory::select('*')->orderBy($sortColumn, $sortDirection);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->status == 0) {
                        return '<span class="badge bg-danger">Inactive</span>';
                    } else {
                        return '<span class="badge bg-success">Active</span>';
                    }
                })
                // ->addColumn('banner_video_thumbnail', function ($row) {
                //     $imgUrl = $row->banner_video_thumbnail ?? '';
                //     return '<img src="' . $imgUrl . '" alt="Profile Image" width="70" height="70">';
                // })
                // ->editColumn('banner_video_url', function ($row) {
                //     return '<a href="'. $row->banner_video_url. '" target="_blank">Click</a>';
                // })
                // ->editColumn('created_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
                // })
                // ->editColumn('updated_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->updated_at)->format('d/m/Y');
                // })
                ->addColumn('action', function ($row) {
                    $previewButton = '';
                    // Check if status is 1, and display the preview button if true
                    // if ($row->status == 1) {
                    //     $previewButton = '<a href="https://d1r5jv57b9z1uu.cloudfront.net/" target="_blank" class="mb-1 btn btn-info btn-sm mr-2">
                    //             <i class="fas fa-eye"></i>
                    //         </a>';
                    // }
                    return '
                        <div class="d-flex">
                            <a href="' . route('blog.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                             '.$previewButton.'
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('blog.destroy', $row->id) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['status','action'])
                ->make(true);
        }
    }

    public function add()
    {
        return view('blogs.blogCategory.add');
    }

    public function store(Request $request)
    {
        $userId = Auth::user()->id;

        // Create a new BlogCategory instance
        $data = new BlogCategory;
        $data->blog_category_name = $request->blog_category_name ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status = $request->status ?? 0;
        $data->save();

        return redirect()->route('blog.index');
    }


    public function show($id)
    {
        // echo "hello world";
        // die();
        $data = BlogCategory::findOrFail($id);
        // print_r($category);
        // die();
        return view('blogs.blogCategory.edit', compact('data'));
    }

    public function update(Request $request)
    {
        // Get the ID from the request
        $id = $request->id;

        // Find the category to update
        $data = BlogCategory::find($id);

        // Get the user ID (from the authenticated user)
        $userId = Auth::user()->id;

        // Prepare the data for updating
        $update = [
            'blog_category_name' => $request->blog_category_name ?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status' => $request->status ?? 0,
        ];

        // Update the category
        $data->update($update);

        // Redirect to the desired page after updating
        return redirect()->route('blog.index');
    }



    public function destroy($id)
    {
        $data = BlogCategory::findOrFail($id);
        $data->delete();

        // return redirect('marketing/marketing_house_category');
        return redirect()->route('blog.index');

    }
}
