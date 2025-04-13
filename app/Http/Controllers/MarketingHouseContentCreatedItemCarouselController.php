<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarketingHouseItem;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\MarketingHouseContentCreatedCategory;
use App\Models\MarketingHouseContentCreatedItemCarousel;

class MarketingHouseContentCreatedItemCarouselController extends Controller
{
    public function index($item_id = null)
    {
        $content_created_ItemCarousel = MarketingHouseContentCreatedCategory::find($item_id);
        $marketing_house_item_id = $content_created_ItemCarousel->marketing_house_item_id  ?? '';
        // print_r($marketing_house_item_id);
        // die();
        $carousels = MarketingHouseContentCreatedItemCarousel::with('category', 'user', 'marketing_item')->get();
        return view('new_marketing_house.created_item_carousels.show', compact('carousels', 'item_id', 'marketing_house_item_id'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            // Get sorting details
            $sortColumnIndex = $request->get('order')[0]['column'];
            $sortDirection = $request->get('order')[0]['dir'];

            // Define column mapping
            $columns = [
                'marketing_house_content_created_item_carousels.id',
                'marketing_house_items.title',
                'marketing_house_content_created_categories.category_name',
                // 'marketing_house_content_created_item_carousels.carousel_order',
                'marketing_house_content_created_item_carousels.image',
                'marketing_house_content_created_item_carousels.display_order',
                'marketing_house_content_created_item_carousels.status',
            ];

            $sortColumn = $columns[$sortColumnIndex] ?? 'marketing_house_content_created_item_carousels.id';

            // Join query to fetch data
            $data = DB::table('marketing_house_content_created_item_carousels')
                ->join('marketing_house_items', 'marketing_house_content_created_item_carousels.marketing_house_item_id', '=', 'marketing_house_items.id')
                // Join with marketing_house_categories through marketing_house_items
                ->join('marketing_house_categories', 'marketing_house_items.marketing_house_category_id', '=', 'marketing_house_categories.id')
                ->join('marketing_house_content_created_categories', 'marketing_house_content_created_item_carousels.marketing_house_content_created_category_id', '=', 'marketing_house_content_created_categories.id')
                ->select(
                    'marketing_house_content_created_item_carousels.id',
                    'marketing_house_items.id as item_id', // Include item_id here for using preview button
                    'marketing_house_items.title AS item_title',
                    'marketing_house_content_created_categories.category_name',
                    // 'marketing_house_content_created_item_carousels.carousel_order',
                    'marketing_house_content_created_item_carousels.image',
                    'marketing_house_content_created_item_carousels.display_order',
                    'marketing_house_content_created_item_carousels.status',
                    'marketing_house_items.status as item_status', // fetching status from items table
                    'marketing_house_categories.status as category_status' // fetching status from categories table
                );
            // Check if item_id is passed in the request and filter based on it
            if ($request->has('item_id') && $request->item_id != null) {
                $data->where('marketing_house_content_created_category_id',  $request->input('item_id')); // Filter by item_id
            }
            $data->orderBy($sortColumn, $sortDirection);

            return DataTables::of($data)
                ->addIndexColumn()
                // ->addColumn('carousel_order', function ($row) {
                //     // Replace underscores with spaces and capitalize the first letter
                //     return ucwords(str_replace('_', ' ', $row->carousel_order));
                // })
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
                    $previewButton = '';
                    // Check if the status of all three entities (image, item, category) is active (1)
                    if ($row->status == 1 && $row->item_status == 1 && $row->category_status == 1) {
                        // If all statuses are 1, display the preview button
                        $previewButton = '<a href="https://d1r5jv57b9z1uu.cloudfront.net/Web-Series-Individual/' . $row->item_id . '" target="_blank" class="mb-1 btn btn-info btn-sm mr-2">
                                        <i class="fas fa-eye"></i>
                                      </a>';
                    }
                    // Render action buttons
                    return '
                    <div class="d-flex">
                        <a href="' . route('carousels.show', ['id' => $row->id, 'item_id' => request('item_id')]) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                            <i class="fas fa-edit"></i>
                        </a>
                        ' . $previewButton . '
                        <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('carousels.destroy', ['id' => $row->id, 'item_id' => request('item_id')]) . '\');">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                ';
                })
                ->rawColumns(['image', 'status', 'action']) // Specify raw columns for HTML content
                ->make(true);
        }
    }

    public function add(Request $request, $item_id = null)
    {
        $market_items_names = MarketingHouseItem::all();
        // $categories = MarketingHouseContentCreatedCategory::all();

        // If item_id is provided, fetch the item to find its category_id
        $selectedMarketingHouseItemId = null;
        $selectedContentCreatedCategoryId = null;
        if ($item_id) {
            $selectedContentCreatedCategoryId = MarketingHouseContentCreatedCategory::find($item_id);  // Get the item by item_id
            //  print_r($selectedContentCreatedCategoryId);
            //  die;
            if ($selectedContentCreatedCategoryId) {
                $selectedMarketingHouseItemId = $selectedContentCreatedCategoryId->marketing_house_item_id;  // Get the category_id of the item
                //  print_r($item_id);
                //  die;
            }
        }
        if ($request->ajax()) {
            $ContentCreatedCategory = MarketingHouseContentCreatedCategory::where('marketing_house_item_id', $request->category_id)->get();
            return response()->json($ContentCreatedCategory);
        }
        return view('new_marketing_house.created_item_carousels.add', compact('market_items_names', 'item_id','selectedMarketingHouseItemId','selectedContentCreatedCategoryId'));
    }



    public function store(Request $request)
    {
        $item_id = $request->item_id;
        // Upload the image to S3 or the public disk
        $imagePath = upload_file_to_s3($request, 'image', 'marketing-house-content-carousels');

        // Create the new carousel item object using the 'new' keyword
        $carouselItem = new MarketingHouseContentCreatedItemCarousel();

        // Assign the values to the new object
        $carouselItem->marketing_house_item_id = $request->marketing_house_item_id;
        $carouselItem->marketing_house_content_created_category_id = $request->marketing_house_content_created_category_id;
        $carouselItem->carousel_order = $request->carousel_order ?? '';
        $carouselItem->image = $imagePath ?? ''; // Store the path of the image (S3 or public)
        $carouselItem->display_order = $request->display_order ?? 0; // Default to null if no display order is provided

        // If status is expected as an integer (1 for 'active', 0 for 'inactive')
        $carouselItem->status = $request->status ?? 0;

        $carouselItem->user_id = auth()->id(); // Store the authenticated user's ID

        // Save the object in the database
        $carouselItem->save();

        // Redirect to the index page with a success message
        return redirect()->route('carousels.index', ['item_id' => $item_id]);
    }

    public function show($id, Request $request, $item_id = null)
    {
        $market_items_names = MarketingHouseItem::all();
        $categories = MarketingHouseContentCreatedCategory::all();


        if ($request->ajax()) {
            $ContentCreatedCategory = MarketingHouseContentCreatedCategory::where('marketing_house_item_id', $request->category_id)->get();
            return response()->json($ContentCreatedCategory);
        }

        // Fetch the item to be edited
        $carouselItem = MarketingHouseContentCreatedItemCarousel::findOrFail($id);

        // Return the edit view with the item and categories
        return view('new_marketing_house.created_item_carousels.edit', compact('carouselItem', 'market_items_names', 'categories', 'item_id'));
    }



    public function update(Request $request, $id)
    {
        $item_id = $request->item_id;
        // Find the existing carousel item
        $carouselItem = MarketingHouseContentCreatedItemCarousel::findOrFail($id);

        // Validate the incoming request (optional, adjust validation rules as needed)
        // $request->validate([
        // ]);

        // Handle the image upload if a new image is provided
        // if ($request->hasFile('image')) {
        // Upload the new image to S3 or the public disk
        $imagePath = upload_file_to_s3($request, 'image', 'marketing-house-content-carousels');
        // $carouselItem->image = $imagePath;
        // }

        // Update the fields of the carousel item
        $carouselItem->marketing_house_item_id = $request->marketing_house_item_id;
        $carouselItem->marketing_house_content_created_category_id = $request->marketing_house_content_created_category_id;
        $carouselItem->carousel_order = $request->carousel_order ?? '';
        $carouselItem->image = $imagePath ?? $carouselItem->image;
        $carouselItem->display_order = $request->display_order ?? 0; // Keep old value if not updated
        $carouselItem->status = $request->status ?? 0; // Update status to 1 for 'active' and 0 for 'inactive'
        $carouselItem->user_id = auth()->id(); // Update user id (if needed, can be skipped)

        // Save the updated carousel item to the database
        $carouselItem->save();

        // Redirect to the index page with a success message
        return redirect()->route('carousels.index', ['item_id' => $item_id]);
    }


    public function destroy($id, $item_id = null)
    {
        $carousel = MarketingHouseContentCreatedItemCarousel::findOrFail($id);
        $carousel->delete();
        return redirect()->route('carousels.index', ['item_id' => $item_id]);
    }
}
