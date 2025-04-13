<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarketingHouseItem;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\Facades\DataTables;
use App\Models\MarketingHouseCommunityProgramCategory;
use App\Models\MarketingHouseCommunityProgramCategoryItem;

class MarketingHouseCommunityProgramCategoryItemController extends Controller
{
    public function index($item_id = null)
    {
        $community_program_item = MarketingHouseCommunityProgramCategory::find($item_id);
        $marketing_house_item_id = $community_program_item->marketing_house_item_id  ?? '';
        // print_r($marketing_house_item_id);
        // die();
        $data = MarketingHouseCommunityProgramCategoryItem::with('marketingHouseItem', 'communitycategory')->get();
        return view('new_marketing_house.community_program_item.show', compact('data', 'item_id', 'marketing_house_item_id'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            // Get sorting details
            $sortColumnIndex = $request->get('order')[0]['column'];
            $sortDirection = $request->get('order')[0]['dir'];

            // Define column mapping
            $columns = [
                'marketing_house_community_program_item.id',
                'marketing_house_items.title',
                'marketing_house_community_program.community_program_category_name',
                'marketing_house_community_program_item.community_program_item_video_thumbnail',
                // 'marketing_house_community_program_item.community_program_item_video_file',
                // 'marketing_house_community_program_item.community_program_item_video_url ',
                'marketing_house_community_program_item.community_program_item_description',
                'marketing_house_community_program_item.display_order',
                'marketing_house_community_program_item.status',
            ];

            $sortColumn = $columns[$sortColumnIndex] ?? 'marketing_house_community_program_item.id';

            // Join query to fetch data
            $data = DB::table('marketing_house_community_program_item')
                ->join('marketing_house_items', 'marketing_house_community_program_item.marketing_house_item_id', '=', 'marketing_house_items.id')
                  // Join with marketing_house_categories through marketing_house_items
                ->join('marketing_house_categories', 'marketing_house_items.marketing_house_category_id', '=', 'marketing_house_categories.id')
                ->join('marketing_house_community_program', 'marketing_house_community_program_item.community_program_category_id', '=', 'marketing_house_community_program.id')
                ->select(
                    'marketing_house_community_program_item.id',
                    'marketing_house_items.id as item_id', // Include item_id here for using preview button
                    'marketing_house_items.title AS item_title',
                    'marketing_house_community_program.community_program_category_name',
                    'marketing_house_community_program_item.community_program_item_video_thumbnail',
                    // 'marketing_house_community_program_item.community_program_item_video_file',
                    // 'marketing_house_community_program_item.community_program_item_video_url',
                    'marketing_house_community_program_item.community_program_item_description',
                    'marketing_house_community_program_item.display_order',
                    'marketing_house_community_program_item.status',
                    'marketing_house_items.status as item_status', // fetching status from items table
                    'marketing_house_categories.status as category_status' // fetching status from categories table
                );
            // Check if item_id is passed in the request and filter based on it
            if ($request->has('item_id') && $request->item_id != null) {
                $data->where('community_program_category_id',  $request->input('item_id')); // Filter by item_id
            }
            $data->orderBy($sortColumn, $sortDirection);

            return DataTables::of($data)
                ->addIndexColumn()
                // ->addColumn('video_thumbnail', function ($row) {
                //     // Render the image column
                //     return $row->community_program_item_video_thumbnail
                //         ? '<img src="' . asset($row->community_program_item_video_thumbnail) . '" alt="Image" width="70" height="70">'
                //         : 'No Image';
                // })
                ->addColumn('video_thumbnail', function ($row) {
                    $imgPath = $row->community_program_item_video_thumbnail ?? '';
                    $baseUrl = env('AWS_URL'); // Get the base URL from the .env file
                    $imgUrl = $baseUrl . '/' . $imgPath; // Concatenate the base URL with the stored path
                    return '<img src="' . $imgUrl . '" alt="No Image Available" width="70" height="70">';
                })
                // ->addColumn('video_file', function ($row) {
                //     // Check if the URL is a video (YouTube, Vimeo, MP4, etc.)
                //     $video_file = $row->community_program_item_video_file;

                //     // Check if it's a video link
                //     if (strpos($video_file, 'youtube.com') !== false || strpos($video_file, 'vimeo.com') !== false || preg_match('/\.(mp4|avi|mov)$/', $video_file)) {
                //         return '<a href="' . $video_file . '" target="_blank">Play Video</a>';
                //     }
                //     return '<a href="' . $video_file . '" target="_blank">' . $video_file . '</a>';
                // })
                // ->addColumn('video_url', function ($row) {
                //     // Check if the URL is a video (YouTube, Vimeo, MP4, etc.)
                //     $video_url = $row->community_program_item_video_url;

                //     // Check if it's a video link
                //     if (strpos($video_url, 'youtube.com') !== false || strpos($video_url, 'vimeo.com') !== false || preg_match('/\.(mp4|avi|mov)$/', $video_url)) {
                //         return '<a href="' . $video_url . '" target="_blank">Play Video</a>';
                //     }
                //     return '<a href="' . $video_url . '" target="_blank">' . $video_url . '</a>';
                // })
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
                        <a href="' . route('community_program_category_item.show', ['id' => $row->id, 'item_id' => request('item_id')]) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                            <i class="fas fa-edit"></i>
                        </a>
                         '.$previewButton.'
                        <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('community_program_category_item.destroy', ['id' => $row->id, 'item_id' => request('item_id')]) . '\');">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                ';
                })
                ->rawColumns(['video_thumbnail', 'status', 'action'])
                ->make(true);
        }
    }

    public function add(Request $request, $item_id = null)
    {
        $market_items_names = MarketingHouseItem::all();
        // $community_program_category = MarketingHouseCommunityProgramCategory::all();
        $community_program_category_items = MarketingHouseCommunityProgramCategoryItem::all();

        // If item_id is provided, fetch the item to find its category_id
        $selectedCommunityProgramCategoryId = null;
        $selectedMarketingHouseItemId = null;
        if ($item_id) {
            $selectedCommunityProgramCategoryId = MarketingHouseCommunityProgramCategory::find($item_id);  // Get the item by item_id
            //   print_r($selectedCommunityProgramCategoryId);
            //  die;
            if ($selectedCommunityProgramCategoryId) {
                $selectedMarketingHouseItemId = $selectedCommunityProgramCategoryId->marketing_house_item_id;  // Get the marketing_house_item_id of the item
                //   print_r($selectedCommunityProgramCategoryId);
                //   die;
            }
        }
        if ($request->ajax()) {
            $CommunityProgramCategory = MarketingHouseCommunityProgramCategory::where('marketing_house_item_id', $request->category_id)->get();
            return response()->json($CommunityProgramCategory);
        }
        return view('new_marketing_house.community_program_item.add', compact('community_program_category_items', 'market_items_names', 'item_id','selectedCommunityProgramCategoryId','selectedMarketingHouseItemId'));
    }

    public function store(Request $request)
    {
        $item_id = $request->item_id;
        // Validate the incoming request data
        $request->validate([]);

        // Handle file uploads using predefined functions
        $thumbnail = upload_file_to_s3($request, 'community_program_item_video_thumbnail', 'community-category-item-thumbnail');
        $video = upload_file_to_s3($request, 'community_program_item_video_file', 'community-category-item-video');


        // Create a new instance of the model
        $data = new MarketingHouseCommunityProgramCategoryItem();
        $data->marketing_house_item_id = $request->marketing_house_item_id;
        $data->community_program_category_id  = $request->community_program_category_id;
        $data->community_program_item_description = $request->community_program_item_description ?? '';
        $data->community_program_item_video_url = $request->community_program_item_video_url ?? '';
        $data->community_program_item_video_thumbnail = $thumbnail ?? ''; // Path returned from S3 upload
        $data->community_program_item_video_file = $video ?? ''; // Path returned from S3 upload
        $data->display_order = $request->display_order ?? 0; // Set default display order if none provided
        $data->status = $request->status ?? 0; // Set default status if none provided
        $data->user_id = auth()->id(); // Assuming user_id is stored in Laravel's Auth system (if not, replace with your own method)

        // Save the record
        $data->save();

        // Redirect with success message
        return redirect()->route('community_program_category_item.index', ['item_id' => $item_id]);
    }

    public function show($id, Request $request, $item_id = null)
    {
        $market_items_names = MarketingHouseItem::all();
        $community_program_category = MarketingHouseCommunityProgramCategory::all();
        $community_program_item = MarketingHouseCommunityProgramCategoryItem::findOrFail($id);


        if ($request->ajax()) {
            $CommunityProgramCategory = MarketingHouseCommunityProgramCategory::where('marketing_house_item_id', $request->category_id)->get();
            return response()->json($CommunityProgramCategory);
        }

        // Return the edit view with the item and categories
        return view('new_marketing_house.community_program_item.edit', compact('market_items_names', 'community_program_item', 'community_program_category', 'item_id'));
    }


    public function update(Request $request, $id)
    {
        $item_id = $request->item_id;
        // Validate the incoming request data
        $request->validate([]);

        // Handle file uploads using predefined functions
        $thumbnail = upload_file_to_s3($request, 'community_program_item_video_thumbnail', 'community-program-category-item-thumbnail');

        // Initialize the video variable
        $video = null;

        // Check if the user uploaded a new video
        if ($request->hasFile('community_program_item_video_file')) {
            // Upload the new video and store it
            $video = upload_file_to_s3($request, 'community_program_item_video_file', 'marketing-item-video');
        } elseif ($request->has('existing_video') && $request->existing_video) {
            // If no new video is uploaded, use the existing video from the hidden input
            $video = $request->existing_video;
        } else {
            // If the user has removed the video (no video uploaded, and no existing video)
            $video = '';  // This will remove the old video
        }

        // Find the record to update
        $data = MarketingHouseCommunityProgramCategoryItem::findOrFail($id);

        // Update fields
        $data->marketing_house_item_id = $request->marketing_house_item_id;
        $data->community_program_category_id  = $request->community_program_category_id;
        $data->community_program_item_description = $request->community_program_item_description ?? '';
        $data->community_program_item_video_url = $request->community_program_item_video_url ?? '';
        $data->community_program_item_video_thumbnail = $thumbnail ?? $data->community_program_item_video_thumbnail ?? ''; // Path returned from S3 upload
        $data->community_program_item_video_file = $video ?? $data->community_program_item_video_file ?? ''; // Path returned from S3 upload
        $data->display_order = $request->display_order ?? 0;
        $data->status = $request->status ?? 0; // Set default status if none provided
        $data->user_id = auth()->id();

        // Save the record
        $data->save();

        // Redirect with success message
        return redirect()->route('community_program_category_item.index', ['item_id' => $item_id]);
    }

    public function destroy($id, $item_id = null)
    {
        $category_item = MarketingHouseCommunityProgramCategoryItem::find($id);
        $category_item->delete();

        return redirect()->route('community_program_category_item.index', ['item_id' => $item_id]);
    }
}
