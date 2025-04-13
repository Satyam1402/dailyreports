<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarketingHouseItem;
use Illuminate\Support\Facades\DB;
use App\Models\MarketingHouseCategory;
use Yajra\DataTables\Facades\DataTables;
use App\Models\MarketingHouseOtherActivityCategory;
use App\Models\MarketingHouseOtherActivityItem;

class MarketingHouseOtherActivityCategoryController extends Controller
{
    public function index($item_id = null)
    {
        // $other_activity_item = MarketingHouseOtherActivityItem::find($item_id);
        // $other_activity_category_id= $other_activity_item->marketing_house_other_activity_category_id  ?? '';
        // print_r($other_activity_category_id);
        // die();
        $itemdata = MarketingHouseItem::query()->get();
        $data = MarketingHouseOtherActivityCategory::all();
        return view('new_marketing_house.other_activity_category.show', compact('data','itemdata','item_id'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            // Get sorting details
            $sortColumnIndex = $request->get('order')[0]['column'];
            $sortDirection = $request->get('order')[0]['dir'];

            // Define column mapping (don't use aliases in sorting)
            $columns = [
                'marketing_house_other_activity_category.id',
                'marketing_house_categories.category_name',
                'marketing_house_items.title', // (Use the actual column name for sorting)
                'marketing_house_other_activity_category.category_name', // For activity category name
                // 'marketing_house_other_activity_category.description',
                // 'marketing_house_other_activity_category.image',
                'marketing_house_other_activity_category.display_order',
                'marketing_house_other_activity_category.status',
            ];

            // Determine the column to order by, without using aliases
            $sortColumn = $columns[$sortColumnIndex] ?? 'marketing_house_other_activity_category.id';

            // Join query to fetch data
            // $data = DB::table('marketing_house_other_activity_category')
            $data = MarketingHouseOtherActivityCategory::with(['other_activity_item'])
                ->join('marketing_house_categories', 'marketing_house_other_activity_category.marketing_house_category_id', '=', 'marketing_house_categories.id')
                ->join('marketing_house_items', 'marketing_house_other_activity_category.marketing_house_item_id', '=', 'marketing_house_items.id')
                ->select(
                    'marketing_house_other_activity_category.id',
                    'marketing_house_items.id as item_id', // Include item_id here for using preview button
                    'marketing_house_categories.category_name AS category_name_from_categories', // Alias for category name from categories table
                    'marketing_house_items.title AS item_title',
                    'marketing_house_other_activity_category.category_name AS category_name_from_activity', // Alias for category name from activity table
                    'marketing_house_other_activity_category.display_order',
                    'marketing_house_other_activity_category.status',
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
                              <a href="' . route('marketing-house-other-activity-category.show', ['id' => $row->id, 'item_id' => request('item_id')]) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                  <i class="fas fa-edit"></i>
                              </a>
                            '.$previewButton.'
                              <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('marketing-house-other-activity-category.destroy', ['id' => $row->id, 'item_id' => request('item_id')]) . '\');">
                                  <i class="fas fa-trash"></i>
                              </a>
                          </div>
                      ';
                })
                ->addColumn('navigate', function ($row) {
                    $other_activity_itemCount = $row->other_activity_item()->count();

                    // Render the action buttons
                    return '
                        <div class="d-flex flex-column">
                             <a href="' . route('marketing-house-other-activity-item.index', ['item_id' => $row->id]) . '" class="mb-1 btn btn-primary btn-sm text-nowrap">
                               Other Activites Items( ' . $other_activity_itemCount . ' )
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['status', 'action','navigate'])
                ->make(true);
        }
    }

    public function add(Request $request,$item_id = null)
    {
          // Fetch categories and items for the dropdowns
          $categories = MarketingHouseCategory::all();
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
            // Fetch items for the selected category
            $marketing_item_id = MarketingHouseItem::where('marketing_house_category_id', $request->category_id)->get();

            // Return marketing_item_id as JSON response
            return response()->json($marketing_item_id);
        }
        // Return the view and pass the categories and items to it
        return view('new_marketing_house.other_activity_category.add', compact('categories', 'items','item_id','selectedItem','selectedCategoryId'));
    }


    public function store(Request $request)
    {
        $item_id = $request->item_id;
        // print_r( $request->status);
        // die();

        // $validated = $request->validate([
        // ]);

        // Create a new instance of the MarketingHouseOtherActivityCategory model
        $data = new MarketingHouseOtherActivityCategory();

        // Assign values from the request to the model
        $data->marketing_house_category_id = $request->marketing_house_category_id;
        $data->marketing_house_item_id = $request->marketing_house_item_id;
        $data->category_name = $request->category_name ?? '';
        $data->display_order = $request->display_order ?? 0; // Default to 0 if not provided
        $data->status = $request->status ?? 0; // Default to 0 if not provided
        $data->user_id = auth()->id(); // Get the authenticated user's ID

        // Save the model instance to the database
        $data->save();

        // Redirect back with a success message
        return redirect()->route('marketing-house-other-activity-category.index',['item_id'=>$item_id]);

    }


    public function show(Request $request,$id,$item_id = null)
    {
        // Retrieve the specific record by ID
        $data = MarketingHouseOtherActivityCategory::findOrFail($id);
        $categories = MarketingHouseCategory::all(); // Retrieve all categories for the select dropdown

        if ($request->ajax()) {
            $categoryId = $request->get('category_id');

            // Fetch items based on the selected category
            $items = MarketingHouseItem::where('marketing_house_category_id', $categoryId)->get();

            // Return items as JSON response
            return response()->json($items);
        }

        // Pass the record to the edit view along with any necessary data (e.g., categories, items)
        $items = MarketingHouseItem::where('marketing_house_category_id',$data->marketing_house_category_id)->get(); // Retrieve all items for the select dropdown

        return view('new_marketing_house.other_activity_category.edit', compact('data', 'categories', 'items','item_id'));
    }


    public function update(Request $request, $id)
    {
        // Retrieve the specific record by ID
        $data = MarketingHouseOtherActivityCategory::findOrFail($id);
        $item_id = $request->item_id;

        // Update the model with the new data
        $data->marketing_house_category_id = $request->marketing_house_category_id;
        $data->marketing_house_item_id = $request->marketing_house_item_id;
        $data->category_name = $request->category_name ?? '';
        $data->display_order = $request->display_order ?? 0; // Default to 0 if not provided
        $data->status = $request->status ?? 0; // Default to 0 if not
        $data->user_id = auth()->id(); // Set the user_id to the authenticated user's ID

        // Save the updated record
        $data->save();

        // Redirect back with a success message
        return redirect()->route('marketing-house-other-activity-category.index',['item_id'=>$item_id]);

    }


    public function destroy($id,$item_id=null)
    {
        $category = MarketingHouseOtherActivityCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('marketing-house-other-activity-category.index',['item_id'=>$item_id]);
    }
}
