<?php

namespace App\Http\Controllers;

use App\Models\Free_consultation_category;
use App\Models\Free_consultation_item;

use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Free_consultation_categoryController extends Controller
{
    public function index(){
        $data = Free_consultation_category::with('free_consultation_item')->get();
        // print_r($data->toArray());
        // die;
        return view('free_consultation.free_consultation_category.index', compact('data'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {

            $sortColumnIndex = $request->get('order')[0]['column']; // value depend on datatable field not in table
            // echo 'working';
            // echo $sortColumnIndex;

            $sortDirection = $request->get('order')[0]['dir'];

            // Map column index to actual column names (you can adjust this as per your columns)
            $columns = [
                'id','first_name','last_name','email','company_name','phone_no','schedule_date','schedule_time','schedule_duration','timezone','website_link','instagram_link','facebook_link','x_link','youtube_link','msg','created_at'
            ]; // value depend on datatable field not in table

            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table

            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            $data = Free_consultation_category::select('*')->with('free_consultation_item.group_service_item')->orderBy($sortColumn, $sortDirection);
            return DataTables::of($data)
                ->addIndexColumn()
                // ->addColumn('newsletter', function ($row) {
                //     if ($row->newsletter == 0) {
                //         return '<span class="badge bg-danger">Inactive</span>';
                //     } else {
                //         return '<span class="badge bg-success">Active</span>';
                //     }
                // })
                // ->addColumn('banner_video_thumbnail', function ($row) {
                //     $imgUrl = $row->banner_video_thumbnail ?? '';
                //     return '<img src="' . $imgUrl . '" alt="Profile Image" width="70" height="70">';
                // })
                ->editColumn('website_link', function ($row) {
                    return '<a href="'. $row->website_link. '" target="_blank">Click</a>';
                })
                ->editColumn('instagram_link', function ($row) {
                    return '<a href="'. $row->instagram_link. '" target="_blank">Click</a>';
                })
                ->editColumn('facebook_link', function ($row) {
                    return '<a href="'. $row->facebook_link. '" target="_blank">Click</a>';
                })
                ->editColumn('x_link', function ($row) {
                    return '<a href="'. $row->x_link. '" target="_blank">Click</a>';
                })
                ->editColumn('youtube_link', function ($row) {
                    return '<a href="'. $row->youtube_link. '" target="_blank">Click</a>';
                })
                ->editColumn('created_at', function ($row) {
                    return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
                })
                  // Add the new column for group_service_item_id
            // ->addColumn('group_service_item_id', function ($row) {
            //     // Extract the group_service_item_id values and join them with a comma
            //     $groupServiceItemIds = array_map(function($item) {
            //         return $item['group_service_item_id']; // Extract group_service_item_id
            //     }, $row->free_consultation_item->toArray());

            //     // Join the group_service_item_ids with commas
            //     return implode(', ', $groupServiceItemIds);
            // })
              // Add the new column for group_service_item_id and onetime/recurring status
            //   ->addColumn('group_service_item_id', function ($row) {
            //     // Extract the group_service_item_id, one_time, and recurring values
            //     $groupServiceItems = array_map(function($item) {
            //         $serviceItem = $item['group_service_item_id']; // Extract group_service_item_id

            //         // Check for one_time and recurring values and add the appropriate label
            //         $labels = [];
            //         if ($item['one_time'] == 1) {
            //             $labels[] = 'One Time';
            //         }
            //         if ($item['recurring'] == 1) {
            //             $labels[] = 'Recurring';
            //         }

            //         // Combine the service item with the labels inside parentheses if they exist
            //         if (!empty($labels)) {
            //             $serviceItem .= ' (' . implode(', ', $labels) . ')';
            //         }

            //         return $serviceItem;
            //     }, $row->free_consultation_item->toArray());

            //     // Join the group_service_item_ids with commas
            //     return implode(', ', $groupServiceItems);
            // })
            ->addColumn('group_service_item_id', function ($row) {
                // Extract the group_service_item_title, one_time, and recurring values
                $groupServiceItems = array_map(function($item) {
                    $serviceItemTitle = $item['group_service_item'] ? $item['group_service_item']['group_service_item_title'] : 'Unknown'; // Get title from the related group_service_item

                    // Check for one_time and recurring values and add the appropriate label
                    $labels = [];
                    if ($item['one_time'] == 1) {
                        $labels[] = 'One Time';
                    }
                    if ($item['recurring'] == 1) {
                        $labels[] = 'Recurring';
                    }

                    // Combine the service item with the labels inside parentheses if they exist
                    if (!empty($labels)) {
                        $serviceItemTitle .= ' (' . implode(', ', $labels) . ')';
                    }

                    return $serviceItemTitle;
                }, $row->free_consultation_item->toArray());

                // Join the group_service_item_titles with commas
                return implode(', ', $groupServiceItems);
            })
                // ->editColumn('updated_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->updated_at)->format('d/m/Y');
                // })
                // ->addColumn('action', function ($row) {
                //     return '
                //         <div class="d-flex">
                //             <a href="' . route('marketing-house-category.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                //                 <i class="fas fa-edit"></i>
                //             </a>
                //             <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('marketing-house-category.destroy', $row->id) . '\');">
                //                 <i class="fas fa-trash"></i>
                //             </a>
                //         </div>
                //     ';
                // })
                ->rawColumns(['website_link','instagram_link','facebook_link','x_link','youtube_link','group_service_item_id'])
                ->make(true);
        }
    }
}
