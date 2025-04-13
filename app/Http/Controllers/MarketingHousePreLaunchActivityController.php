<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarketingHouseItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\User;
use App\Models\MarketingHouseCategory;
use Yajra\DataTables\Facades\DataTables;
use App\Models\MarketingHousePreLaunchActivity;

class MarketingHousePreLaunchActivityController extends Controller
{
    public function index($item_id = null)
    {
        // print_r($item_id);
        // die;
        $itemdata = MarketingHouseItem::query()->get();
        $prelaunchdata = MarketingHousePreLaunchActivity::query()->with('item')->get();
        // $data = Creative_house_item::query()->get();
        // print_r( $data );
        // die;
        return view('new_marketing_house.pre_launch_activities.show', compact('prelaunchdata', 'itemdata', 'item_id'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            // Get sorting details
            $sortColumnIndex = $request->get('order')[0]['column'];
            $sortDirection = $request->get('order')[0]['dir'];

            // Define column mapping (don't use aliases in sorting)
            $columns = [
                'marketing_house_pre_launch_activities.id', // 0
                'marketing_house_categories.category_name', // 1
                'marketing_house_items.title', // 2 (Use the actual column name for sorting)
                'marketing_house_pre_launch_activities.title', // 3
                'marketing_house_pre_launch_activities.description', // 4
                'marketing_house_pre_launch_activities.image', // 5
                'marketing_house_pre_launch_activities.display_order', // 6
                'marketing_house_pre_launch_activities.status', // 7
            ];

            // Determine the column to order by, without using aliases
            $sortColumn = $columns[$sortColumnIndex] ?? 'marketing_house_pre_launch_activities.id';

            // Join query to fetch data
            $data = DB::table('marketing_house_pre_launch_activities')
                ->join('marketing_house_categories', 'marketing_house_pre_launch_activities.marketing_house_category_id', '=', 'marketing_house_categories.id')
                ->join('marketing_house_items', 'marketing_house_pre_launch_activities.marketing_house_item_id', '=', 'marketing_house_items.id')
                ->select(
                    'marketing_house_pre_launch_activities.id',
                    'marketing_house_categories.category_name',
                    'marketing_house_items.id as item_id', // Include item_id here using preview button
                    'marketing_house_items.title AS item_title', // Alias for marketing_house_items.title
                    'marketing_house_pre_launch_activities.title AS activity_title', // Alias for marketing_house_pre_launch_activities.title
                    'marketing_house_pre_launch_activities.description',
                    'marketing_house_pre_launch_activities.image',
                    'marketing_house_pre_launch_activities.display_order',
                    'marketing_house_pre_launch_activities.status',
                    'marketing_house_items.status as item_status', // fetching status from items table
                    'marketing_house_categories.status as category_status' // fetching status from categories table
                );
            // Check if item_id is passed in the request and filter based on it
            if ($request->has('item_id') && $request->item_id != null) {
                $data->where('marketing_house_item_id',  $request->input('item_id')); // Filter by item_id
            }

            $data->orderBy($sortColumn, $sortDirection); // Use the original column names for ordering

            return DataTables::of($data)
                ->addIndexColumn()
                // ->addColumn('image', function ($row) {
                //     return $row->image
                //         ? '<img src="' . asset($row->image) . '" alt="Image" width="70" height="70">'
                //         : 'No Image';
                // })
                ->addColumn('image', function ($row) {
                    $imgPath = $row->image ?? '';
                    $baseUrl = env('AWS_URL'); // Get the base URL from the .env file
                    $imgUrl = $baseUrl . '/' . $imgPath; // Concatenate the base URL with the stored path
                    return '<img src="' . $imgUrl . '" alt="No Image Available" width="70" height="70">';
                })
                ->addColumn('status', function ($row) {
                    return $row->status == 0
                        ? '<span class="badge bg-danger">Inactive</span>'
                        : '<span class="badge bg-success">Active</span>';
                })
                ->addColumn('action', function ($row) {
                    $previewButton = '';
                    // Check if the status of all three entities (image, item, category) is active (1)
                    if ($row->status == 1 && $row->item_status == 1 && $row->category_status == 1) {
                        // If all statuses are 1, display the preview button
                        $previewButton = '<a href="https://d1r5jv57b9z1uu.cloudfront.net/Web-Series-Individual/' . $row->item_id . '" target="_blank" class="mb-1 btn btn-info btn-sm mr-2">
                                        <i class="fas fa-eye"></i>
                                      </a>';
                    }
                    return '
                          <div class="d-flex">
                              <a href="' . route('marketing-house-pre-launch-activity.show', ['id' => $row->id, 'item_id' => request('item_id')]) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                  <i class="fas fa-edit"></i>
                              </a>
                            ' . $previewButton . '
                              <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('marketing-house-pre-launch-activity.destroy', ['id' => $row->id, 'item_id' => request('item_id')]) . '\');">
                                  <i class="fas fa-trash"></i>
                              </a>
                          </div>
                      ';
                })
                ->rawColumns(['image', 'status', 'action'])
                ->make(true);
        }
        if (!$request->ajax()) {
            return response()->json(['error' => 'Not an AJAX request'], 400);
        }
    }

    public function add(Request $request, $item_id = null)
    {

        $categories = MarketingHouseCategory::all(); // Fetch all categories
        $items = MarketingHouseItem::all();

        // If item_id is provided, fetch the item to find its category_id
        $selectedItem = null;
        $selectedCategoryId = null;
        if ($item_id) {
            $selectedItem = MarketingHouseItem::find($item_id);  // Get the item by item_id
            if ($selectedItem) {
                $selectedCategoryId = $selectedItem->marketing_house_category_id;  // Get the category_id of the item
            }
        }

        if ($request->ajax()) {
            $marketing_item_id = MarketingHouseItem::where('marketing_house_category_id', $request->category_id)->get();
            return response()->json($marketing_item_id);
        }


        return view('new_marketing_house.pre_launch_activities.add', compact('categories', 'items', 'item_id','selectedItem','selectedCategoryId'));
    }



    public function store(Request $request)
    {
        $item_id = $request->item_id;
        $imagePath = null;
        $imagePath = upload_file_to_s3($request, 'image', 'marketing-house-pre-launch-activities');

        // Create a new instance of MarketingHousePreLaunchActivity and assign properties
        $data = new MarketingHousePreLaunchActivity();
        $data->marketing_house_category_id = $request->marketing_house_category_id ?? 0;
        $data->marketing_house_item_id = $request->marketing_house_item_id ?? 0;
        $data->title = $request->title ?? '';
        $data->description = $request->description ?? '';
        $data->image = $imagePath ?? ''; // S3 file path or null
        $data->display_order = $request->display_order ?? 0; // Default to 0 if not provided
        $data->status = $request->status ?? 0; // Default to 'inactive' if not provided
        $data->user_id = auth()->id(); // Assign authenticated user's ID

        // Save the data to the database
        $data->save();

        // Redirect back with a success message
        return redirect()->route('marketing-house-pre-launch-activity.index', ['item_id' => $item_id]);
    }


    public function show(Request $request, $id, $item_id = null)
    {
        // Find the pre-launch activity by ID
        $data = MarketingHousePreLaunchActivity::findOrFail($id);
        $categories = MarketingHouseCategory::all();

        // Handle AJAX request for dynamic dropdowns
        if ($request->ajax()) {
            $categoryId = $request->get('category_id');

            // Fetch items based on the selected category
            $items = MarketingHouseItem::where('marketing_house_category_id', $categoryId)->get();

            // Return items as JSON response
            return response()->json($items);
        }
        // Fetch related data for dropdowns (categories, items, users)
        $items = MarketingHouseItem::where('marketing_house_category_id', $data->marketing_house_category_id)->get();
        $users = User::all();

        // Return the edit view with the data
        return view('new_marketing_house.pre_launch_activities.edit', compact('data', 'categories', 'items', 'users', 'item_id'));
    }

    public function update(Request $request, $id)
    {
        // Find the pre-launch activity by ID
        $data = MarketingHousePreLaunchActivity::findOrFail($id);
        $item_id = $request->item_id;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Upload new image to S3
            $imagePath = upload_file_to_s3($request, 'image', 'marketing-house-pre-launch-activities');
            $data->image = $imagePath; // Update the image path in the database
        }

        // Update other fields
        $data->marketing_house_category_id = $request->marketing_house_category_id ?? $data->marketing_house_category_id;
        $data->marketing_house_item_id = $request->marketing_house_item_id ?? $data->marketing_house_item_id;
        $data->title = $request->title ?? $data->title;
        $data->description = $request->description ?? $data->description;
        $data->display_order = $request->display_order ?? 0;
        $data->status = $request->status ?? 0;
        $data->user_id = auth()->id(); // Update with the current user's ID

        // Save the changes to the database
        $data->save();

        // Redirect back with a success message
        return redirect()->route('marketing-house-pre-launch-activity.index', ['item_id' => $item_id]);
    }


    public function destroy(Request $request, $id, $item_id = null)
    {
        // $id = $request->id;
        $data = MarketingHousePreLaunchActivity::find($id);
        $data->delete();

        // return redirect('marketing_house/marketing_house_pre_launch_activity',['item_id'=>$item_id]);
        return redirect()->route('marketing-house-pre-launch-activity.index', ['item_id' => $item_id]);
    }
}
