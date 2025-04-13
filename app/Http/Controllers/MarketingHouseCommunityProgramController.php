<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\MarketingHouseItem;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\MarketingHouseCommunityProgramCategory;

class MarketingHouseCommunityProgramController extends Controller
{
    public function index($item_id=null) {
        $data = MarketingHouseCommunityProgramCategory::with('marketingHouseItem')->get();
        // print_r($data);
        // die;
        return view('new_marketing_house.community_program.show', compact('data','item_id'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            // Get sorting details
            $sortColumnIndex = $request->get('order')[0]['column'];
            $sortDirection = $request->get('order')[0]['dir'];

            // Define column mapping
            $columns = [
                'marketing_house_community_program.id',
                'marketing_house_items.title',
                'marketing_house_community_program.community_program_category_name',
                'marketing_house_community_program.community_program_category_description',
                'marketing_house_community_program.display_order',
                'marketing_house_community_program.status',
            ];

            $sortColumn = $columns[$sortColumnIndex] ?? 'marketing_house_community_program.id';

            // Join query to fetch data
            // $data = DB::table('marketing_house_community_program')
            $data = MarketingHouseCommunityProgramCategory::with(['continuity_item'])
                ->join('marketing_house_items', 'marketing_house_community_program.marketing_house_item_id', '=', 'marketing_house_items.id')
                // Join with marketing_house_categories through marketing_house_items
                ->join('marketing_house_categories', 'marketing_house_items.marketing_house_category_id', '=', 'marketing_house_categories.id')
                // ->join('marketing_house_content_created_categories', 'marketing_house_community_program.marketing_house_content_created_category_id', '=', 'marketing_house_content_created_categories.id')
                ->select(
                    'marketing_house_community_program.id',
                    'marketing_house_items.id as item_id', // Include item_id here for using preview button
                    'marketing_house_items.title AS item_title',
                    'marketing_house_community_program.community_program_category_name',
                    'marketing_house_community_program.community_program_category_description',
                    'marketing_house_community_program.display_order',
                    'marketing_house_community_program.status',
                    'marketing_house_items.status as item_status', // fetching status from items table
                    'marketing_house_categories.status as category_status' // fetching status from categories table
                );
                  // Check if item_id is passed in the request and filter based on it
                  if ($request->has('item_id') && $request->item_id != null) {
                    $data->where('marketing_house_item_id',  $request->input('item_id')); // Filter by item_id
                }
                $data->orderBy($sortColumn, $sortDirection);

            return DataTables::of($data)
                ->addIndexColumn()
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
                        <a href="' . route('community_program_category.show', ['id' => $row->id, 'item_id' => request('item_id')]) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                            <i class="fas fa-edit"></i>
                        </a>
                         '.$previewButton.'
                        <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('community_program_category.destroy', ['id' => $row->id, 'item_id' => request('item_id')]) . '\');">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                ';
                })
                ->addColumn('navigate', function ($row) {
                    $continuity_itemCount = $row->continuity_item()->count();

                    // Render the action buttons
                    return '
                        <div class="d-flex flex-column">
                             <a href="' . route('community_program_category_item.index', ['item_id' => $row->id]) . '" class="mb-1 btn btn-primary btn-sm text-nowrap">
                               Continuity Items( ' . $continuity_itemCount . ' )
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['status', 'action','navigate'])
                ->make(true);
        }
    }
    public function add($item_id=null) {
        $items = MarketingHouseCommunityProgramCategory::all();
        $market_items_names = MarketingHouseItem::all();
        return view('new_marketing_house.community_program.add', compact('items','market_items_names','item_id'));
    }

    public function store(Request $request) {

        $item_id = $request->item_id;
        // Create a new community program
        $community_program = new MarketingHouseCommunityProgramCategory();
        $community_program->marketing_house_item_id = $request->marketing_house_item_id;
        $community_program->community_program_category_name = $request->community_program_category_name;
        $community_program->community_program_category_description = $request->community_program_category_description ?? '';
        $community_program->display_order = $request->display_order ?? 0;
        // If status is expected as an integer (1 for 'active', 0 for 'inactive')
        $community_program->status = $request->status ?? 0;
        $community_program->user_id = auth()->id(); // Store the authenticated user's ID
        $community_program->save();

        return redirect()->route('community_program_category.index',['item_id'=>$item_id]);
    }

    public function show($id,$item_id = null) {
        $data = MarketingHouseCommunityProgramCategory::find($id);
        $market_items_names = MarketingHouseItem::all();
        return view('new_marketing_house.community_program.edit', compact('data','market_items_names','item_id'));
    }

    public function update(Request $request){
        $item_id = $request->item_id;
        // Find the community program by ID
        $userId = auth()->id();
        $community_program = MarketingHouseCommunityProgramCategory::find($request->id);

        // Update the model with the new data
        $community_program->marketing_house_item_id = $request->marketing_house_item_id;
        $community_program->community_program_category_name = $request->community_program_category_name;
        $community_program->community_program_category_description = $request->community_program_category_description ?? '';
        $community_program->display_order = $request->display_order ?? 0;
        // If status is expected as an integer (1 for 'active', 0 for 'inactive')
        $community_program->status = $request->status ?? 0;
        $community_program->user_id = $userId; // Store the authenticated user's ID
        $community_program->save();

        return redirect()->route('community_program_category.index',['item_id'=>$item_id]);
    }

    public function destroy($id,$item_id = null)
    {
        $community_program_category = MarketingHouseCommunityProgramCategory::findOrFail($id);
        $community_program_category->delete();

        return redirect()->route('community_program_category.index',['item_id'=>$item_id]);
    }
}
