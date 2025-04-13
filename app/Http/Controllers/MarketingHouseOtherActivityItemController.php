<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarketingHouseItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Models\MarketingHouseOtherActivityItem;
use App\Models\MarketingHouseOtherActivityCategory;

class MarketingHouseOtherActivityItemController extends Controller
{
    // Show all activity items
    public function index($item_id = null)
    {
        $other_activity_item = MarketingHouseOtherActivityCategory::find($item_id);
        $marketing_house_item_id = $other_activity_item->marketing_house_item_id  ?? '';
        // print_r($marketing_house_item_id);
        // die();
        $data = MarketingHouseOtherActivityItem::with(['marketing_item'])->get();
        return view('new_marketing_house.other_activity_item.show', compact('data', 'item_id', 'marketing_house_item_id'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            // Get sorting details
            $sortColumnIndex = $request->get('order')[0]['column'];
            $sortDirection = $request->get('order')[0]['dir'];

            // Define column mapping (don't use aliases in sorting)
            $columns = [
                'marketing_house_other_activity_item.id',
                'marketing_house_items.title',
                'marketing_house_other_activity_category.category_name', // Correctly use the original column name
                'marketing_house_other_activity_item.title',
                'marketing_house_other_activity_item.description',
                'marketing_house_other_activity_item.image1',
                'marketing_house_other_activity_item.image2',
                'marketing_house_other_activity_item.image3',
                'marketing_house_other_activity_item.image4',
                'marketing_house_other_activity_item.display_order',
                'marketing_house_other_activity_item.status',
            ];

            // Determine the column to order by, without using aliases
            $sortColumn = $columns[$sortColumnIndex] ?? 'marketing_house_other_activity_item.id';

            // Join query to fetch data
            $data = DB::table('marketing_house_other_activity_item')
                // Join with marketing_house_items to access item-related data
                ->join('marketing_house_items', 'marketing_house_other_activity_item.marketing_house_item_id', '=', 'marketing_house_items.id')
                // Join with marketing_house_categories through marketing_house_items
                ->join('marketing_house_categories', 'marketing_house_items.marketing_house_category_id', '=', 'marketing_house_categories.id')
                // Join with marketing_house_other_activity_category for category-specific data
                ->join('marketing_house_other_activity_category', 'marketing_house_other_activity_item.marketing_house_other_activity_category_id', '=', 'marketing_house_other_activity_category.id')
                ->select(
                    'marketing_house_other_activity_item.id',
                    'marketing_house_items.title AS item_title',
                    'marketing_house_items.id as item_id', // Include item_id here for using preview button
                    'marketing_house_other_activity_category.category_name AS other_activity_category_name',
                    'marketing_house_other_activity_item.title AS other_activity_item_title',
                    'marketing_house_other_activity_item.description',
                    'marketing_house_other_activity_item.image1',
                    'marketing_house_other_activity_item.image2',
                    'marketing_house_other_activity_item.image3',
                    'marketing_house_other_activity_item.image4',
                    'marketing_house_other_activity_item.display_order',
                    'marketing_house_other_activity_item.status',
                    'marketing_house_items.status as item_status', // fetching status from items table
                    'marketing_house_categories.status as category_status' // fetching status from categories table
                );
            // Check if item_id is passed in the request and filter based on it
            if ($request->has('item_id') && $request->item_id != null) {
                $data->where('marketing_house_other_activity_category_id',  $request->input('item_id')); // Filter by item_id
            }
            $data->orderBy($sortColumn, $sortDirection); // Use the original column names for ordering

            return DataTables::of($data)
                ->addIndexColumn()
                // Add image column if needed
                // ->addColumn('image', function ($row) {
                //     $images = '';

                //     // Check for each image and append to the $images string
                //     if ($row->image1) {
                //         $images .= '<img src="' . asset($row->image1) . '" alt="Image 1" width="70" height="70" style="margin-right: 5px;">';
                //     }
                //     if ($row->image2) {
                //         $images .= '<img src="' . asset($row->image2) . '" alt="Image 2" width="70" height="70" style="margin-right: 5px;">';
                //     }
                //     if ($row->image3) {
                //         $images .= '<img src="' . asset($row->image3) . '" alt="Image 3" width="70" height="70" style="margin-right: 5px;">';
                //     }
                //     if ($row->image4) {
                //         $images .= '<img src="' . asset($row->image4) . '" alt="Image 4" width="70" height="70" style="margin-right: 5px;">';
                //     }

                //     // If no images are found, display 'No Images'
                //     if (empty($images)) {
                //         $images = 'No Images';
                //     }

                //     return $images;
                // })
                ->addColumn('image', function ($row) {
                    $images = '';
                
                    // Get the base URL from the .env file
                    $baseUrl = env('AWS_URL'); 
                
                    // Check for each image and append to the $images string
                    if ($row->image1) {
                        $imgUrl = $baseUrl . '/' . $row->image1;
                        $images .= '<img src="' . $imgUrl . '" alt="First Image Not Found" width="70" height="70" style="margin-right: 5px;">';
                    }
                    if ($row->image2) {
                        $imgUrl = $baseUrl . '/' . $row->image2;
                        $images .= '<img src="' . $imgUrl . '" alt="Second Image Not Found" width="70" height="70" style="margin-right: 5px;">';
                    }
                    if ($row->image3) {
                        $imgUrl = $baseUrl . '/' . $row->image3;
                        $images .= '<img src="' . $imgUrl . '" alt="Third Image Not Found" width="70" height="70" style="margin-right: 5px;">';
                    }
                    if ($row->image4) {
                        $imgUrl = $baseUrl . '/' . $row->image4;
                        $images .= '<img src="' . $imgUrl . '" alt="Fourth Image Not Found" width="70" height="70" style="margin-right: 5px;">';
                    }
                
                    // If no images are found, display 'No Images'
                    if (empty($images)) {
                        $images = 'No Images';
                    }
                
                    return $images;
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
                            <a href="' . route('marketing-house-other-activity-item.show', ['id' => $row->id, 'item_id' => request('item_id')]) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            ' . $previewButton . '
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('marketing-house-other-activity-item.destroy', ['id' => $row->id, 'item_id' => request('item_id')]) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['status', 'action', 'image'])  // Allow HTML in these columns
                ->make(true);
        }
    }

    public function add(Request $request, $item_id = null)
    {

        $market_items_names = MarketingHouseItem::all();

        // If item_id is provided, fetch the item to find its category_id
        $selectedItem = null;
        $selectedMarketingHouseitemId = null;
        if ($item_id) {
            $selectedItem = MarketingHouseOtherActivityCategory::find( $item_id);
            // print_r( $selectedItem)  ;
            // die();
            // Get the item by item_id
            if ($selectedItem) {
                $selectedOtherActivityCategoryId = MarketingHouseOtherActivityCategory::all();
                $selectedMarketingHouseitemId = $selectedItem->marketing_house_item_id;  // Get the category_id of the item
                // where('marketing_house_item_id', $selectedMarketingHouseitemId)->get();

            }
            // print_r($selectedOtherActivityCategoryId);
            // die;
        }

        if ($request->ajax()) {
            $OtherActivitycategories = MarketingHouseOtherActivityCategory::where('marketing_house_item_id', $request->category_id)->get();
            return response()->json($OtherActivitycategories);
        }

        return view('new_marketing_house.other_activity_item.add', compact('market_items_names', 'item_id','selectedItem','selectedMarketingHouseitemId','selectedOtherActivityCategoryId'));
    }

    // Store the new activity item in the database
    public function store(Request $request)
    {
        $item_id = $request->item_id;
        // Handle file uploads and store file paths
        $imagePaths = [];
        for ($i = 1; $i <= 4; $i++) {
            if ($request->hasFile("image$i")) {
                $imagePaths["image$i"] = upload_file_to_s3($request, "image$i", 'marketing-house-other-activity-items');
            }
        }

        // Create and save the new item
        $data = new MarketingHouseOtherActivityItem();
        $data->marketing_house_item_id = $request->marketing_house_item_id;
        $data->marketing_house_other_activity_category_id = $request->marketing_house_other_activity_category_id;
        $data->title = $request->title ?? '';
        $data->description = $request->description ?? '';
        $data->image1 = $imagePaths['image1'] ?? null;
        $data->image2 = $imagePaths['image2'] ?? null;
        $data->image3 = $imagePaths['image3'] ?? null;
        $data->image4 = $imagePaths['image4'] ?? null;
        $data->display_order = $request->display_order ?? 0;
        $data->status = $request->status ?? 0;
        $data->user_id = Auth::id();

        $data->save();

        // Redirect with a success message
        return redirect()->route('marketing-house-other-activity-item.index', ['item_id' => $item_id]);
    }


    // Show the form to edit the existing activity item
    public function show($id, Request $request, $item_id = null)
    {
        $market_items_names = MarketingHouseItem::all();

        if ($request->ajax()) {
            // Handle the AJAX request to fetch categories for the selected house item
            $otherActivityCategories = MarketingHouseOtherActivityCategory::where('marketing_house_item_id', $request->category_id)->get();
            return response()->json($otherActivityCategories);
        }

        // Fetch the item to be edited
        $item = MarketingHouseOtherActivityItem::findOrFail($id);

        // Fetch all available categories for the dropdown
        $categories = MarketingHouseOtherActivityCategory::where('marketing_house_item_id', $item->marketing_house_item_id)->orderBy('category_name')->get();

        // Return the edit view with the item and categories
        return view('new_marketing_house.other_activity_item.edit', compact('item', 'categories', 'market_items_names', 'item_id'));
    }


    // Update the activity item in the database
    public function update(Request $request, $id)
    {
        $data = MarketingHouseOtherActivityItem::findOrFail($id);
        $item_id = $request->item_id;
        $imagePaths = [];
        for ($i = 1; $i <= 4; $i++) {
            if ($request->hasFile("image$i")) {
                $imagePaths["image$i"] = upload_file_to_s3($request, "image$i", 'marketing-house-other-activity-items');
            }
        }

        $data->marketing_house_item_id = $request->marketing_house_item_id;
        $data->marketing_house_other_activity_category_id = $request->marketing_house_other_activity_category_id;
        $data->title = $request->title ?? '';
        $data->description = $request->description ?? '';
        $data->image1 = $imagePaths['image1'] ?? $data->image1;
        $data->image2 = $imagePaths['image2'] ?? $data->image2;
        $data->image3 = $imagePaths['image3'] ?? $data->image3;
        $data->image4 = $imagePaths['image4'] ?? $data->image4;
        $data->display_order = $request->display_order ?? 0;
        $data->status = $request->status ?? 0;
        $data->user_id = Auth::id();
        $data->save();

        return redirect()->route('marketing-house-other-activity-item.index', ['item_id' => $item_id]);
    }



    // Delete an activity item
    public function destroy($id, $item_id = null)
    {
        $item = MarketingHouseOtherActivityItem::findOrFail($id);
        $item->delete();

        return redirect()->route('marketing-house-other-activity-item.index', ['item_id' => $item_id]);
    }
}
