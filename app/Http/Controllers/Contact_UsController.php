<?php

namespace App\Http\Controllers;
use App\Models\Contact_us;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\Facades\DataTables;

use Illuminate\Http\Request;

class Contact_UsController extends Controller
{
    public function index(){
        $data = Contact_us::query()->get();
        return view('contact_us.index', compact('data'));
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
                'id','first_name','email','phone_no','company_name','media_budget','created_at','message'
            ]; // value depend on datatable field not in table

            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table

            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            $data = Contact_us::select('*')->orderBy($sortColumn, $sortDirection);

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
                // ->editColumn('banner_video_url', function ($row) {
                //     return '<a href="'. $row->banner_video_url. '" target="_blank">Click</a>';
                // })
                ->editColumn('created_at', function ($row) {
                    return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
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
                ->rawColumns(['newsletter'])
                ->make(true);
        }
    }
}
