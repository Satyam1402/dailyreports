<?php

// app/Http/Controllers/MarketingHouseImageController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarketingHouseItem;
use Illuminate\Support\Facades\DB;
use App\Models\MarketingHouseImage;
use App\Models\MarketingHouseCategory;
use Yajra\DataTables\Facades\DataTables;

class MarketingHouseImageController extends Controller
{
    public function index($marketinghouseitem_id = null)
    {
        $itemdata = MarketingHouseItem::query()->get();
        $data = MarketingHouseImage::with(['category', 'item'])->get();
        return view('new_marketing_house.marketing_house_images.marketing_house_image', compact('data', 'marketinghouseitem_id', 'itemdata'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            // Get sorting details
            $sortColumnIndex = $request->get('order')[0]['column'];
            $sortDirection = $request->get('order')[0]['dir'];

            // Define column mapping
            $columns = [
                'marketing_house_images.id', // 0
                'marketing_house_categories.category_name', // 1
                'marketing_house_items.title', // 2
                'marketing_house_images.image', // 3
                'marketing_house_images.display_order', // 4
                'marketing_house_images.status', // 5
            ];

            $sortColumn = $columns[$sortColumnIndex] ?? 'marketing_house_images.id';

            // Join query to fetch data
            $data = DB::table('marketing_house_images')
                ->join('marketing_house_categories', 'marketing_house_images.marketing_house_category_id', '=', 'marketing_house_categories.id')
                ->join('marketing_house_items', 'marketing_house_images.marketing_house_item_id', '=', 'marketing_house_items.id')
                ->select(
                    'marketing_house_images.id',
                    'marketing_house_categories.category_name',
                    'marketing_house_items.id as item_id', // Add item_id here for preview purposes
                    'marketing_house_items.title',
                    'marketing_house_images.image',
                    'marketing_house_images.display_order',
                    'marketing_house_images.status',
                    'marketing_house_items.status as item_status', // fetching status from items table
                    'marketing_house_categories.status as category_status' // fetching status from categories table
                );

            // Check if item_id is passed in the request and filter based on it
            if ($request->has('marketinghouseitem_id') && $request->marketinghouseitem_id != null) {
                $data->where('marketing_house_item_id',  $request->input('marketinghouseitem_id')); // Filter by item_id
            }

            $data->orderBy($sortColumn, $sortDirection);

            return DataTables::of($data)
                ->addIndexColumn()

                // ->addColumn('image', function ($row) {
                //     // Render the image column
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
                    // Render the status column
                    return $row->status == 0
                        ? '<span class="badge bg-danger">Inactive</span>'
                        : '<span class="badge bg-success">Active</span>';
                })
                ->addColumn('action', function ($row) {
                    // Render action buttons
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
                            <a href="' . route('marketing-house-image.show', ['id' => $row->id, 'marketinghouseitem_id' => request('marketinghouseitem_id')]) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                             ' . $previewButton . '
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('marketing-house-image.destroy', ['id' => $row->id, 'marketinghouseitem_id' => request('marketinghouseitem_id')]) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['image', 'status', 'action']) // Specify raw columns for HTML content
                ->make(true);
        }
    }
    public function add(Request $request, $marketinghouseitem_id = null)
    {
        // Load categories and items for the add view
        $categories = MarketingHouseCategory::all();
        // $items = MarketingHouseItem::all();

        // If marketinghouseitem_id is provided, fetch the item to find its category_id
        $selectedItem = null;
        $selectedCategoryId = null;
        if ($marketinghouseitem_id) {
            $selectedItem = MarketingHouseItem::find($marketinghouseitem_id);  // Get the item by item_id
            if ($selectedItem) {
                $selectedCategoryId = $selectedItem->marketing_house_category_id;  // Get the category_id of the item
            }
        }
        // Handle AJAX request for items filtering
        if ($request->ajax()) {

            // Fetch items for the selected category
            $marketing_item_id = MarketingHouseItem::where('marketing_house_category_id', $request->category_id)->get();

            // Return items as JSON response
            return response()->json($marketing_item_id);
        }

        return view('new_marketing_house.marketing_house_images.add_marketing_house_image', compact('categories', 'marketinghouseitem_id','selectedItem','selectedCategoryId'));
    }



    public function store(Request $request)
    {
        $marketinghouseitem_id = $request->marketinghouseitem_id;
        $imagePath = null;
        $imagePath = upload_file_to_s3($request, 'image', 'marketing-item-video-thumbnail');
        $video = upload_file_to_s3($request, 'marketing_item_upload_video_url', 'marketing-item-video');

        // $userId = Auth::id();

        // Manually create and assign properties with defaults
        $data = new MarketingHouseImage();
        $data->marketing_house_category_id = $request->marketing_house_category_id ?? 0;
        $data->marketing_house_item_id = $request->marketing_house_item_id ?? 0;
        $data->image = $imagePath ?? ''; // S3 file path or null
        $data->marketing_item_upload_video_url = $video ?? '';
        $data->marketing_item_video_url = $request->marketing_item_video_url ?? '';
        $data->display_order = $request->display_order ?? 0; // Default to 0 if not provided
        $data->status = $request->status ?? 0; // Default to 'inactive' if not provided
        $data->user_id = auth()->id();
        // print_r($data);
        // die();

        // Save the data to the database
        $data->save();

        // Redirect back with a success message
        return redirect()->route('marketing-house-image.index', ['marketinghouseitem_id' => $marketinghouseitem_id]);
    }

    //  here show function means "Edit"
    public function show(Request $request, $id, $marketinghouseitem_id = null)
    {
        // Find the existing image by ID
        $image = MarketingHouseImage::findOrFail($id);

        // Handle AJAX request for dynamic dropdowns
        if ($request->ajax()) {
            $categoryId = $request->get('category_id');

            // Fetch items based on the selected category
            $items = MarketingHouseItem::where('marketing_house_category_id', $categoryId)->get();

            // Return items as JSON response
            return response()->json($items);
        }

        // Fetch categories and items for the form
        $categories = MarketingHouseCategory::all();
        $items = MarketingHouseItem::where('marketing_house_category_id', $image->marketing_house_category_id)->get(); // Preload items

        // Return the edit view with necessary data
        return view('new_marketing_house.marketing_house_images.edit_marketing_house_image', compact('image', 'categories', 'items', 'marketinghouseitem_id'));
    }



    public function update(Request $request, $id)
    {
        // Find the existing record
        $data = MarketingHouseImage::findOrFail($id);
        $marketinghouseitem_id = $request->marketinghouseitem_id;

        // Handle image upload to S3 if a new file is provided
        $imagePath = $data->image; // Keep the existing image by default
        // if ($request->hasFile('image')) {
        // Upload new file to S3 and get the path
        $imagePath = upload_file_to_s3($request, 'image', 'marketing-item-video-thumbnail');

        // Initialize the video variable
        $video = null;

        // Check if the user uploaded a new video
        if ($request->hasFile('marketing_item_upload_video_url')) {
            // Upload the new video and store it
            $video = upload_file_to_s3($request, 'marketing_item_upload_video_url', 'marketing-item-video');
        } elseif ($request->has('existing_video') && $request->existing_video) {
            // If no new video is uploaded, use the existing video from the hidden input
            $video = $request->existing_video;
        } else {
            // If the user has removed the video (no video uploaded, and no existing video)
            $video = '';  // This will remove the old video
        }

        // Update the record properties
        $data->marketing_house_category_id = $request->marketing_house_category_id ?? $data->marketing_house_category_id;
        $data->marketing_house_item_id = $request->marketing_house_item_id ?? $data->marketing_house_item_id;
        $data->image = $imagePath ?? $data->image ?? ''; // Updated image path or retain existing
        $data->marketing_item_upload_video_url = $video;
        $data->marketing_item_video_url = $request->marketing_item_video_url ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->status = $request->status ?? 0;
        $data->user_id = auth()->id();

        // Save the updated record
        $data->save();

        // Redirect back with a success message
        return redirect()->route('marketing-house-image.index', ['marketinghouseitem_id' => $marketinghouseitem_id]);
    }


    public function destroy(Request $request, $id, $marketinghouseitem_id = null)
    {
        // $id = $request->id;
        $data = MarketingHouseImage::find($id);
        $data->delete();

        // return redirect( 'marketing_house/marketing_house_image' );
        return redirect()->route('marketing-house-image.index', ['marketinghouseitem_id' => $marketinghouseitem_id]);
    }
}
