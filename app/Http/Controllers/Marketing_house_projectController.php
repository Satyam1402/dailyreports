<?php

namespace App\Http\Controllers;



use App\Models\Book_call;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Creative_house_item;
use Illuminate\Support\Facades\Auth;

use App\Models\Banner_title_template;
use App\Models\Marketing_house_project;
use Yajra\DataTables\Facades\DataTables;

class Marketing_house_projectController extends Controller
{

    public function index() {
        // $itemdata = Creative_house_item::query()->get();
        $bookcalldata = Book_call::query()->get();
        $data = Marketing_house_project::query()->with(['banner_template','book_call_template'])->get();
        // $data = Creative_house_item::query()->get();
        // print_r( $data );
        // die;
        return view( 'marketing_house_project.index', compact( 'data' ) );
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {

            $smallestDisplayOrder = Marketing_house_project::where('status', 1)->min('display_order');
            // Get sorting details
            $sortColumnIndex = $request->get('order')[0]['column'];
            $sortDirection = $request->get('order')[0]['dir'];

            // Define column mapping
            $columns = [
                'marketing_house_project.id',
                'banner_title_template.banner_name',
                'book_a_call.book_name',
                'marketing_house_project.display_order',
                'marketing_house_project.status',
            ];

            $sortColumn = $columns[$sortColumnIndex] ?? 'marketing_house_project.id';

            // Join query to fetch data
            $data = DB::table('marketing_house_project')
                ->join('banner_title_template', 'marketing_house_project.banner_title_template_id', '=', 'banner_title_template.id')
                ->join('book_a_call', 'marketing_house_project.book_call_template_id', '=', 'book_a_call.id')
                ->select(
                    'marketing_house_project.id',
                    'banner_title_template.banner_name',
                    'book_a_call.book_name',
                    'marketing_house_project.display_order',
                    'marketing_house_project.status'
                )
                ->orderBy($sortColumn, $sortDirection);

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('status', function ($row) {
                    // Render the status column
                    return $row->status == 0
                        ? '<span class="badge bg-danger">Inactive</span>'
                        : '<span class="badge bg-success">Active</span>';
                })
                ->addColumn('action', function ($row) use ($smallestDisplayOrder) {
                    // Render action buttons
                    $previewButton = '';
                
                    if ($row->status == 1 && $row->display_order == $smallestDisplayOrder) {
                        $previewButton = '<a href="https://d1r5jv57b9z1uu.cloudfront.net/View-all-Series" target="_blank" class="mb-1 btn btn-info btn-sm mr-2">
                                <i class="fas fa-eye"></i>
                            </a>';
                    }
                    return '
                    <div class="d-flex">
                        <a href="' . route('marketing-house-project.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                            <i class="fas fa-edit"></i>
                        </a>
                        ' . $previewButton . '
                        <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('marketing-house-project.destroy', $row->id) . '\');">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                ';
                })
                ->rawColumns(['status','action'])
                ->make(true);
        }
    }

    public function add() {
        $bannerdata = Banner_title_template::query()->get();
        $bookcalldata = Book_call::query()->get();

        return view( 'marketing_house_project.add',compact('bannerdata','bookcalldata'));
    }

    public function store( Request $request )  {

        // print_r( $request->creative_house_video_url );
        // die;
        $request->validate( [
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',

        ] );

        $userId = Auth::user()->id;

        $data = new Marketing_house_project;
        $data->banner_title_template_id = $request->banner_title_template_id??0;
        $data->book_call_template_id = $request->book_call_template_id??0;
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status = $request->status ?? 0;
        $data->save();

        return redirect( 'marketing_house/marketing_house_project' );
    }

    public function show( Request $request, $id ) {
        // $id = $request->id;
        // $categorydata = Creative_house_category::query()->get();
        $bannerdata = Banner_title_template::query()->get();
        $bookcalldata = Book_call::query()->get();
        $data = Marketing_house_project::find($id);
        // print_r( $data->toArray() );
        // die;
        return view( 'marketing_house_project.edit', compact( 'data','bannerdata','bookcalldata' ) );
    }

    public function update( Request $request ) {
        $id = $request->id;



        $data = Marketing_house_project::find( $id );

        $userId = Auth::user()->id;

        $update = [


            'banner_title_template_id'=>$request->banner_title_template_id ?? 0,
            'book_call_template_id'=>$request->book_call_template_id ?? 0,
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status'=> $request->status ?? 0,

        ];
        // print_r( $update );
        // die;

        // $data = Admin_post::find( $id );
        $data->update( $update );
        // $data->save();

        return redirect( 'marketing_house/marketing_house_project' );
    }

    public function destroy( Request $request, $id ) {
        // $id = $request->id;
        $data = Marketing_house_project::find( $id );
        $data->delete();

        return redirect( 'marketing_house/marketing_house_project' );
        // return response()->json( [ 'data' => $data ] );
    }

}
