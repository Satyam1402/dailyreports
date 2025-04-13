<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarketingHouseItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\MarketingHouseCategory;
use Yajra\DataTables\Facades\DataTables;
use App\Models\MarketingHouseContentCreatedCategory;

class MarketingHouseContentCreatedCategoryController extends Controller
{
    public function index($item_id = null)
    {
        $data = MarketingHouseContentCreatedCategory::with(['marketingHouseCategory', 'marketingHouseItem'])->get();
        return view('new_marketing_house.content_created_category.show', compact('data', 'item_id'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            // Get sorting details
            $sortColumnIndex = $request->get('order')[0]['column'];
            $sortDirection = $request->get('order')[0]['dir'];

            // Define column mapping (don't use aliases in sorting)
            $columns = [
                'marketing_house_content_created_categories.id', // 0
                'marketing_house_categories.category_name', // 1
                'marketing_house_items.title', // 2 (Use the actual column name for sorting)
                'marketing_house_content_created_categories.category_name', // 3
                'marketing_house_content_created_categories.display_order', // 6
                'marketing_house_content_created_categories.status', // 7
            ];

            // Determine the column to order by, without using aliases
            $sortColumn = $columns[$sortColumnIndex] ?? 'marketing_house_content_created_categories.id';

            // Join query to fetch data
            // $data = DB::table('marketing_house_content_created_categories')
            $data = MarketingHouseContentCreatedCategory::with(['content_created_item','content_created_carousal'])
                ->join('marketing_house_categories', 'marketing_house_content_created_categories.marketing_house_category_id', '=', 'marketing_house_categories.id')
                ->join('marketing_house_items', 'marketing_house_content_created_categories.marketing_house_item_id', '=', 'marketing_house_items.id')
                ->select(
                    'marketing_house_content_created_categories.id',
                    'marketing_house_items.id as item_id', // Include item_id here for using preview button
                    'marketing_house_categories.category_name AS category_name_from_categories', // Alias for category name from categories table
                    'marketing_house_items.title AS item_title', // Alias for marketing_house_items.title
                    'marketing_house_content_created_categories.category_name AS content_created_category_name',
                    'marketing_house_content_created_categories.navigate_to', // fetch categories_id data from navigate_to column
                    'marketing_house_content_created_categories.display_order',
                    'marketing_house_content_created_categories.status',
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
                              <a href="' . route('marketing-house-content-created-category.show', ['id' => $row->id, 'item_id' => request('item_id')]) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                  <i class="fas fa-edit"></i>
                              </a>
                               ' . $previewButton . '
                              <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('marketing-house-content-created-category.destroy', ['id' => $row->id, 'item_id' => request('item_id')]) . '\');">
                                  <i class="fas fa-trash"></i>
                              </a>
                          </div>
                      ';
                })
                // ->addColumn('navigate', function ($row) {
                //     // Render the action buttons

                //     return '
                //         <div class="d-flex flex-column">
                //              <a href="' . route('marketing-house-content-created-item.index', ['item_id' => $row->id]) . '" class="mb-1 btn btn-primary btn-sm text-nowrap">
                //                Content Items
                //             </a>
                //              <a href="' . route('carousels.index', ['item_id' => $row->id]) . '" class="mb-1 btn btn-primary btn-sm text-nowrap">
                //                Content Items Carousels
                //             </a>
                //         </div>
                //     ';
                // })
                ->addColumn('navigate', function ($row) {
                    $content_created_itemCount = $row->content_created_item()->count();
                    $content_created_carousalCount = $row->content_created_carousal()->count();

                    // Check if the 'navigate_to' column is set to '1' for Content Items or '2' for Content Items Carousels
                    if ($row->navigate_to == 1) {
                        // Render the "Content Items" button if 'navigate_to' is 1
                        return '
                            <div class="d-flex flex-column">
                                <a href="' . route('marketing-house-content-created-item.index', ['item_id' => $row->id]) . '" class="mb-1 btn btn-primary btn-sm text-nowrap">
                                    Content Items( ' . $content_created_itemCount . ' )
                                </a>
                            </div>
                        ';
                    } elseif ($row->navigate_to == 2) {
                        // Render the "Content Items Carousels" button if 'navigate_to' is 2
                        return '
                            <div class="d-flex flex-column">
                                <a href="' . route('carousels.index', ['item_id' => $row->id]) . '" class="mb-1 btn btn-primary btn-sm text-nowrap">
                                    Content Items Carousels( ' . $content_created_carousalCount . ' )
                                </a>
                            </div>
                        ';
                    }

                    // If no condition is met, return nothing or an alternative
                    return '';
                })
                ->rawColumns(['image', 'status', 'action', 'navigate'])
                ->make(true);
        }
    }

    public function add(Request $request, $item_id = null)
    {
        $categories = MarketingHouseCategory::all();
        $items = MarketingHouseItem::all();

        // If item_id is provided, fetch the item to find its category_id
        $selectedMarketingHouseItemId = null;
        $selectedMarketingHouseCategoryId = null;
        if ($item_id) {
            $selectedMarketingHouseItemId = MarketingHouseItem::find($item_id);  // Get the item by item_id
            if ($selectedMarketingHouseItemId) {
                $selectedMarketingHouseCategoryId = $selectedMarketingHouseItemId->marketing_house_category_id;  // Get the category_id of the item
            }
        }
        if ($request->ajax()) {
            // Fetch items for the selected category
            $marketing_house_item_id = MarketingHouseItem::where('marketing_house_category_id', $request->category_id)->get();

            // Return items as JSON response
            return response()->json($marketing_house_item_id);
        }

        return view('new_marketing_house.content_created_category.add', compact('categories', 'items', 'item_id','selectedMarketingHouseItemId','selectedMarketingHouseCategoryId'));
    }


    public function store(Request $request)
    {
        $item_id = $request->item_id;
        // Get the currently authenticated user's ID
        $userId = Auth::id();

        // Validate the incoming request
        // $request->validate([
        // ]);

        // Create the new category record
        $data = new MarketingHouseContentCreatedCategory();
        $data->marketing_house_category_id = $request->marketing_house_category_id ?? 0;
        $data->marketing_house_item_id = $request->marketing_house_item_id ?? 0;
        $data->category_name = $request->category_name ?? '';
        $data->navigate_to = $request->navigate_to ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->status = $request->status ?? 0;
        $data->user_id = $userId;

        // Save the data to the database
        $data->save();

        // Redirect to the index page with a success message
        return redirect()->route('marketing-house-content-created-category.index', ['item_id' => $item_id]);
    }


    public function show(Request $request, $id, $item_id = null)
    {
        $category = MarketingHouseContentCreatedCategory::findOrFail($id);
        $categories = MarketingHouseCategory::all();

        if ($request->ajax()) {
            $categoryId = $request->get('category_id');

            // Fetch items based on the selected category
            $items = MarketingHouseItem::where('marketing_house_category_id', $categoryId)->get();

            // Return items as JSON response
            return response()->json($items);
        }

        $items = MarketingHouseItem::where('marketing_house_category_id', $category->marketing_house_category_id)->get(); // Retrieve all items for the select dropdown
        return view('new_marketing_house.content_created_category.edit', compact('category', 'categories', 'items', 'item_id'));
    }

    public function update(Request $request, $id)
    {
        $item_id = $request->item_id;
        // $request->validate([
        // ]);

        $userId = auth()->id(); // Get the currently authenticated user's ID

        $data = MarketingHouseContentCreatedCategory::findOrFail($id);

        $data->marketing_house_category_id = $request->marketing_house_category_id ?? 0;
        $data->marketing_house_item_id = $request->marketing_house_item_id ?? 0;
        $data->category_name = $request->category_name ?? '';
        $data->navigate_to = $request->navigate_to ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->status = $request->status ?? 0;
        $data->user_id = $userId;

        $data->save();

        return redirect()->route('marketing-house-content-created-category.index', ['item_id' => $item_id]);
    }

    public function destroy($id, $item_id = null)
    {
        $category = MarketingHouseContentCreatedCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('marketing-house-content-created-category.index', ['item_id' => $item_id]);
    }
}
