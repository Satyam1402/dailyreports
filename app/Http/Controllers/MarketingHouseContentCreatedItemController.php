<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarketingHouseItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Models\MarketingHouseContentCreatedItem;
use App\Models\MarketingHouseContentCreatedCategory;

class MarketingHouseContentCreatedItemController extends Controller
{
    public function index($item_id=null)
    {
        $content_created_item = MarketingHouseContentCreatedCategory::find($item_id);
        $marketing_house_item_id= $content_created_item->marketing_house_item_id  ?? '';
        // print_r($marketing_house_item_id);
        // die();
        $data = MarketingHouseContentCreatedItem::with('category', 'marketing_item')->get();
        return view('new_marketing_house.content_created_item.show', compact('data','item_id','marketing_house_item_id'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            // Get sorting details
            $sortColumnIndex = $request->get('order')[0]['column'];
            $sortDirection = $request->get('order')[0]['dir'];

            // Define column mapping
            $columns = [
                'marketing_house_content_created_items.id',
                'marketing_house_items.title',
                'marketing_house_content_created_categories.category_name',
                'marketing_house_content_created_items.image',
                // 'marketing_house_content_created_items.url',
                'marketing_house_content_created_items.display_order',
                'marketing_house_content_created_items.status',
            ];

            $sortColumn = $columns[$sortColumnIndex] ?? 'marketing_house_content_created_items.id';

            // Join query to fetch data
            $data = DB::table('marketing_house_content_created_items')
                // Join with marketing_house_items to access item-related data
                ->join('marketing_house_items', 'marketing_house_content_created_items.marketing_house_item_id', '=', 'marketing_house_items.id')
                 // Join with marketing_house_categories through marketing_house_items
                ->join('marketing_house_categories', 'marketing_house_items.marketing_house_category_id', '=', 'marketing_house_categories.id')
                ->join('marketing_house_content_created_categories', 'marketing_house_content_created_items.marketing_house_content_created_category_id', '=', 'marketing_house_content_created_categories.id')
                ->select(
                    'marketing_house_content_created_items.id',
                    'marketing_house_items.id as item_id', // Include item_id here for using preview button
                    'marketing_house_items.title AS item_title',
                    'marketing_house_content_created_categories.category_name',
                    'marketing_house_content_created_items.image',
                    // 'marketing_house_content_created_items.url',
                    'marketing_house_content_created_items.display_order',
                    'marketing_house_content_created_items.status',
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
                // ->addColumn('url', function ($row) {
                //     // Check if the URL is a video (YouTube, Vimeo, MP4, etc.)
                //     $url = $row->url;

                //     // Check if it's a video link
                //     if (strpos($url, 'youtube.com') !== false || strpos($url, 'vimeo.com') !== false || preg_match('/\.(mp4|avi|mov)$/', $url)) {
                //         return '<a href="' . $url . '" target="_blank">Play Video</a>';
                //     }
                //     return '<a href="' . $url . '" target="_blank">' . $url . '</a>';
                // })
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
                        <a href="' . route('marketing-house-content-created-item.show', ['id' => $row->id, 'item_id' => request('item_id')]) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                            <i class="fas fa-edit"></i>
                        </a>
                        '.$previewButton.'
                        <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('marketing-house-content-created-item.destroy', ['id' => $row->id, 'item_id' => request('item_id')]) . '\');">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                ';
                })
                ->rawColumns(['image','status','action'])
                ->make(true);
        }
    }

    public function add(Request $request,$item_id=null)
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
                //  print_r($selectedMarketingHouseItemId);
                //  die;
             }

         }

        if ($request->ajax()) {
            $ContentCreatedCategory = MarketingHouseContentCreatedCategory::where('marketing_house_item_id', $request->category_id)->get();
            return response()->json($ContentCreatedCategory);
        }
        return view('new_marketing_house.content_created_item.add', compact('market_items_names','item_id','selectedMarketingHouseItemId','selectedContentCreatedCategoryId'));
    }

    public function store(Request $request)
    {
        $item_id = $request->item_id;
        // $request->validate([
        // ]);

        // Upload the image to S3 using the provided function
        $imagePath = upload_file_to_s3($request, 'image', 'marketing-house-content-items');

        // Save the record in the database
        MarketingHouseContentCreatedItem::create([
            'marketing_house_item_id' => $request->marketing_house_item_id,
            'marketing_house_content_created_category_id' => $request->marketing_house_content_created_category_id,
            'image' => $imagePath,
            'url' => $request->url ?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => auth()->id(),
            'status' => $request->status ?? 0,
        ]);

        // Redirect to the index page with a success message
        return redirect()->route('marketing-house-content-created-item.index',['item_id'=>$item_id]);
    }


    public function show($id, Request $request,$item_id = null)
    {
        $market_items_names = MarketingHouseItem::all();

        if ($request->ajax()) {
            // Handle the AJAX request to fetch categories for the selected house item
            $ContentCreatedCategory = MarketingHouseContentCreatedCategory::where('marketing_house_item_id', $request->category_id)->get();
            return response()->json($ContentCreatedCategory);
        }

        // Fetch the item to be edited
        $item = MarketingHouseContentCreatedItem::findOrFail($id);

        // Fetch all available categories for the dropdown
        $categories = MarketingHouseContentCreatedCategory::where('marketing_house_item_id', $item->marketing_house_item_id)->orderBy('category_name')->get();

        // Return the edit view with the item and categories
        return view('new_marketing_house.content_created_item.edit', compact('item', 'categories', 'market_items_names','item_id'));
    }



    public function update(Request $request, $id)
    {
        $item_id = $request->item_id;
        // Find the item by ID
        $item = MarketingHouseContentCreatedItem::findOrFail($id);

        // Upload the image to S3 if a new image is provided
        if ($request->hasFile('image')) {
            $imagePath = upload_file_to_s3($request, 'image', 'marketing-house-content-items');
            $item->image = $imagePath; // Update the image path
        }

        // Update the other fields
        $item->update([
            'marketing_house_item_id' => $request->marketing_house_item_id,
            'marketing_house_content_created_category_id' => $request->marketing_house_content_created_category_id,
            'url' => $request->url ?? $item->url,
            'display_order' => $request->display_order ?? 0,
            'user_id' => auth()->id(),
            'status' => $request->status ?? 0,
        ]);

        // Redirect with a success message
        return redirect()->route('marketing-house-content-created-item.index',['item_id'=>$item_id]);
    }



    public function destroy($id,$item_id = null)
    {
        $item = MarketingHouseContentCreatedItem::findOrFail($id);
        $item->delete();

        return redirect()->route('marketing-house-content-created-item.index',['item_id'=>$item_id]);
    }
}
