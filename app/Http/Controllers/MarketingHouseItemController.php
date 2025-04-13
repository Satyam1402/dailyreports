<?php

namespace App\Http\Controllers;

use App\Models\Book_call;
use Illuminate\Http\Request;
use App\Models\Author_template;
use App\Models\MarketingHouseItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\MarketingHouseCategory;
use Yajra\DataTables\Facades\DataTables;

class MarketingHouseItemController extends Controller
{

    public function index() {

        // echo "hello world";
        // die();
        $categorydata = MarketingHouseCategory::query()->get();
        // $data = Marketting_house_item::query()->get();
        $data = MarketingHouseItem::query()->with('category')->get();
        $authordata = Author_template::query()->get();
        $bookcalldata = Book_call::query()->get();
        // print_r( $data );
        // die;
        return view( 'new_marketing_house.marketing_house_items.marketing_house_item', compact( 'data','categorydata' ) );
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            // Get sorting details
            $sortColumnIndex = $request->get('order')[0]['column'];
            $sortDirection = $request->get('order')[0]['dir'];

            // Define the column mapping for sorting
            $columns = [
                'marketing_house_items.id',
                'marketing_house_categories.category_name',
                // 'marketing_house_categories.status',
                'marketing_house_items.title',
                'marketing_house_items.poster_image',
                'marketing_house_items.status',
                // 'marketing_house_items.display_at_home',
                'marketing_house_items.display_order',
            ];

            // Ensure sorting column exists in the array
            $sortColumn = $columns[$sortColumnIndex] ?? 'marketing_house_items.id';

            // Fetch the data using a join query with DB facade
            $data = MarketingHouseItem::with(['images','pre_launch_activity','other_activity_category','content_created_category','continuity_category'])
                ->join('marketing_house_categories', 'marketing_house_items.marketing_house_category_id', '=', 'marketing_house_categories.id')
                ->select(
                    'marketing_house_items.id',
                    'marketing_house_categories.category_name',
                    'marketing_house_categories.status as category_status', // Fetch category status
                    'marketing_house_items.title',
                    'marketing_house_items.poster_image',
                    'marketing_house_items.status',
                    // 'marketing_house_items.display_at_home',
                    'marketing_house_items.display_order',
                );

                // Apply category filter if selected
                if ($request->has('category_id') && $request->get('category_id') != '') {
                    $category_id = $request->get('category_id');
                    $data->where('marketing_house_items.marketing_house_category_id', $category_id);
                }

                if ($request->has('status') && $request->get('status') != '') {
                    $status = $request->get('status');
                    $data->where('marketing_house_items.status', $status);
                }

                $data->orderBy($sortColumn, $sortDirection)->get();
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
                ->addColumn('poster_image', function ($row) {
                    $imgPath = $row->poster_image ?? '';
                    $baseUrl = env('AWS_URL'); // Get the base URL from the .env file
                    $imgUrl = $baseUrl . '/' . $imgPath; // Concatenate the base URL with the stored path
                    return '<img src="' . $imgUrl . '" alt="No Image Available" width="70" height="70">';
                })
                ->addColumn('status', function ($row) {
                    // Render the status badge
                    return $row->status == 0
                        ? '<span class="badge bg-danger">Inactive</span>'
                        : '<span class="badge bg-success">Active</span>';
                })
                ->addColumn('action', function ($row) {
                    // Render the action buttons
                    return '
                        <div class="d-flex">
                            <a href="' . route('marketing-house-item.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('marketing-house-item.destroy', $row->id) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->addColumn('action', function ($row) {
                    $previewButton = '';
                    if ($row->status == 1 && $row->category_status == 1) { // Check both conditions
                        $previewButton = '<a href="https://d1r5jv57b9z1uu.cloudfront.net/Web-Series-Individual/' . $row->id . '" target="_blank" class="mb-1 btn btn-info btn-sm mr-2">
                                            <i class="fas fa-eye"></i>
                                          </a>';
                    }
                   return '
                        <div class="d-flex">
                            <a href="' . route('marketing-house-item.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            '.$previewButton.'
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('marketing-house-item.destroy', $row->id) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })

                ->addColumn('navigate', function ($row) {
                    $imageCount = $row->images()->count();
                    $pre_launch_activityCount = $row->pre_launch_activity()->count();
                    $other_activity_categoryCount = $row->other_activity_category()->count();
                    $content_created_categoryCount = $row->content_created_category()->count();
                    $continuity_categoryCount = $row->continuity_category()->count();

                    // Render the action buttons
                    return '
                        <div class="d-flex flex-column">

                             <a href="' . route('marketing-house-image.index', ['marketinghouseitem_id' => $row->id]) . '" class="mb-1 btn btn-primary btn-sm text-nowrap">
                               Marketing Item Thumbnail( ' . $imageCount . ' )
                            </a>
                               <a href="' . route('marketing-house-pre-launch-activity.index', ['item_id' => $row->id]) . '" class="mb-1 btn btn-primary btn-sm text-nowrap">
                               Prelaunch Activities( ' . $pre_launch_activityCount . ' )
                            </a>
                                <a href="' . route('marketing-house-other-activity-category.index', ['item_id' => $row->id]) . '" class="mb-1 btn btn-primary btn-sm text-nowrap">
                               Other Activities Category( ' . $other_activity_categoryCount . ' )
                            </a>
                            <a href="' . route('marketing-house-content-created-category.index', ['item_id' => $row->id]) . '" class="mb-1 btn btn-primary btn-sm text-nowrap">
                               Content Category( ' . $content_created_categoryCount . ' )
                            </a>
                              <a href="' . route('community_program_category.index', ['item_id' => $row->id]) . '" class="mb-1 btn btn-primary btn-sm text-nowrap">
                               Continuity Category( ' . $continuity_categoryCount . ' )
                            </a>

                        </div>
                    ';
                })
                ->rawColumns(['poster_image', 'status', 'action','navigate']) // Mark columns with raw HTML
                ->make(true);
        }
    }

    public function add() {

        $categorydata = MarketingHouseCategory::query()->get();
        $authordata = Author_template::query()->get();
        $bookcalldata = Book_call::query()->get();
        return view( 'new_marketing_house.marketing_house_items.add_marketing_house',compact('categorydata','authordata','bookcalldata') );
    }

    public function store(Request $request)
    {
            $poster_image = upload_file_to_s3($request, 'poster_image', 'poster-image');

            $ideas_strategy_planning_image = upload_file_to_s3($request, 'ideas_strategy_planning_image', 'ideas_strategy_planning_image');

        //  print_r($poster_image);
        //  die();

        // Manually create a new instance and assign properties
        $userId = Auth::id();
        $data = new MarketingHouseItem();
        $data->marketing_house_category_id = $request->marketing_house_category_id ?? 0;
        $data->title = $request->title ?? '';
        $data->poster_image = $poster_image ?? ''; // Storing uploaded poster image
        $data->year = $request->year ?? '0000';
        $data->author_template_id = $request->author_template_id ?? '';
        $data->book_call_template_id = $request->book_call_template_id ?? '';
        $data->client = $request->client ?? '';
        $data->genre = $request->genre ?? '';
        $data->cast = $request->cast ?? '';
        $data->directors = $request->directors ?? '';
        $data->description = $request->description ?? '';
        $data->client_requirement_text = $request->client_requirement_text ?? '';
        $data->client_requirement_1 = $request->client_requirement_1 ?? '';
        $data->client_requirement_2 = $request->client_requirement_2 ?? '';
        $data->client_requirement_3 = $request->client_requirement_3 ?? '';
        $data->client_requirement_4 = $request->client_requirement_4 ?? '';
        $data->client_requirement_5 = $request->client_requirement_5 ?? '';
        $data->client_requirement_6 = $request->client_requirement_6 ?? '';

        $data->ideas_strategy_planning_title = $request->ideas_strategy_planning_title ?? '';
        $data->ideas_strategy_planning_description = $request->ideas_strategy_planning_description ?? '';
        $data->ideas_strategy_planning_image = $ideas_strategy_planning_image ?? '';
        // $data->client_requirement_desc = $request->client_requirement_desc ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status = $request->status ?? 0;
        $data->display_at_home = $request->display_at_home ?? 0;


        // Save the data to the database
        $data->save();

        // Redirect back to the index page with success message
        return redirect()->route('marketing-house-item.index');
    }

    public function show( Request $request, $id)
    {
        // Find the Marketing House Item by its ID
        $marketingHouseItem = MarketingHouseItem::findOrFail($id);

        // Pass the MarketingHouseItem and categories to the view
        $categorydata = MarketingHouseCategory::all(); // Assuming you have a MarketingHouseCategory model
        $authordata = Author_template::query()->get();
        $bookcalldata = Book_call::query()->get();

        return view('new_marketing_house.marketing_house_items.edit_marketing_house', compact('marketingHouseItem', 'categorydata','authordata','bookcalldata'));
    }

    public function update(Request $request, $id)
    {
        // Find the Marketing House Item by its ID
        $marketingHouseItem = MarketingHouseItem::findOrFail($id);

        // Handle the file upload (if a new file is uploaded)
        $poster_image = $marketingHouseItem->poster_image; // Default to the current image if no new one is uploaded
        if ($request->hasFile('poster_image')) {
            $poster_image = upload_file_to_s3($request, 'poster_image', 'poster-image');
        }

        // Handle the file upload (if a new file is uploaded)
        $ideas_strategy_planning_image = $marketingHouseItem->ideas_strategy_planning_image; // Default to the current image if no new one is uploaded
        if ($request->hasFile('ideas_strategy_planning_image')) {
            $ideas_strategy_planning_image = upload_file_to_s3($request, 'ideas_strategy_planning_image', 'ideas_strategy_planning_image');
        }

        // Update the fields
        $userId = Auth::id();
        $marketingHouseItem->marketing_house_category_id = $request->marketing_house_category_id ?? 0;
        $marketingHouseItem->title = $request->title ?? '';
        $marketingHouseItem->poster_image = $poster_image;
        $marketingHouseItem->year = $request->year ?? 0000;
        $marketingHouseItem->author_template_id = $request->author_template_id ?? '';
        $marketingHouseItem->book_call_template_id = $request->book_call_template_id ?? '';
        $marketingHouseItem->client = $request->client ?? '';
        $marketingHouseItem->genre = $request->genre ?? '';
        $marketingHouseItem->cast = $request->cast ?? '';
        $marketingHouseItem->directors = $request->directors ?? '';
        $marketingHouseItem->description = $request->description ?? '';
        $marketingHouseItem->client_requirement_text = $request->client_requirement_text ?? '';
        $marketingHouseItem->client_requirement_1 = $request->client_requirement_1 ?? '';
        $marketingHouseItem->client_requirement_2 = $request->client_requirement_2 ?? '';
        $marketingHouseItem->client_requirement_3 = $request->client_requirement_3 ?? '';
        $marketingHouseItem->client_requirement_4 = $request->client_requirement_4 ?? '';
        $marketingHouseItem->client_requirement_5 = $request->client_requirement_5 ?? '';
        $marketingHouseItem->client_requirement_6 = $request->client_requirement_6 ?? '';
        $marketingHouseItem->ideas_strategy_planning_title = $request->ideas_strategy_planning_title ?? '';
        $marketingHouseItem->ideas_strategy_planning_description = $request->ideas_strategy_planning_description ?? '';
        $marketingHouseItem->ideas_strategy_planning_image = $ideas_strategy_planning_image ?? '';

        // $marketingHouseItem->client_requirement_desc = $request->client_requirement_desc ?? '';
        $marketingHouseItem->display_order = $request->display_order ?? 0;
        $marketingHouseItem->status = $request->status ?? 0;
        $marketingHouseItem->user_id = $userId;
        $marketingHouseItem->display_at_home = $request->display_at_home ?? 0;

        // Save the updated data
        $marketingHouseItem->save();

        // Redirect with a success message
        return redirect()->route('marketing-house-item.index');
    }

    public function destroy( Request $request, $id ) {
        // $id = $request->id;
        $data = MarketingHouseItem::find( $id );
        $data->delete();

        return redirect()->route('marketing-house-item.index');
        // return redirect( 'marketing_house/marketing_house_item' );
        // return response()->json( [ 'data' => $data ] );
    }
}
