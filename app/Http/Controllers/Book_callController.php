<?php

namespace App\Http\Controllers;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

use App\Models\Creative_house_category;
use App\Models\Creative_house_item;
use App\Models\Book_call;
use Illuminate\Support\Facades\Auth;

class Book_callController extends Controller
{

    public function index() {
        $data = Book_call::query()->get();
        // print_r( $data );
        // die;
        return view( 'book_call.index', compact( 'data') );
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
                'id','book_name','book_image','book_heading','book_title1','book_description1','book_title2','book_description2','book_button_text','display_order','status'
            ]; // value depend on datatable field not in table
    
            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table
            
            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            $data = Book_call::select('*')->orderBy($sortColumn, $sortDirection);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->status == 0) {
                        return '<span class="badge bg-danger">Inactive</span>';
                    } else {
                        return '<span class="badge bg-success">Active</span>';
                    }
                })
                // ->addColumn('book_image', function ($row) {
                //     $imgUrl = $row->book_image ?? '';
                //     return '<img src="' . $imgUrl . '" alt="Profile Image" width="70" height="70">';
                // })
                ->addColumn('book_image', function ($row) {
                    $imgPath = $row->book_image ?? '';
                    $baseUrl = env('AWS_URL'); // Get the base URL from the .env file
                    $imgUrl = $baseUrl . '/' . $imgPath; // Concatenate the base URL with the stored path
                    return '<img src="' . $imgUrl . '" alt="No Image Available" width="70" height="70">';
                })
                // ->editColumn('website_url', function ($row) {
                //     return '<a href="'. $row->website_url. '" target="_blank">Click</a>';
                // })
                // ->editColumn('created_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
                // })
                // ->editColumn('updated_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->updated_at)->format('d/m/Y');
                // })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-flex">
                            <a href="' . route('book-call.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('book-call.destroy', $row->id) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['status','action','book_image'])
                ->make(true);
        }
    }

    public function add() {

        return view( 'book_call.add');
    }

    public function store( Request $request )  {


        $request->validate( [
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',

        ] );

        $image = upload_file_to_s3($request, 'book_image', 'book-a-call');


        $userId = Auth::user()->id;

        $data = new Book_call;
        $data->book_name = $request->book_name ?? '';
        $data->book_image = $image ?? '';
        $data->book_heading = $request->book_heading?? '';
        $data->book_title1 = $request->book_title1?? '';
        $data->book_title2 = $request->book_title2?? '';
        $data->book_description1 = $request->book_description1?? '';
        $data->book_description2 = $request->book_description2?? '';
        $data->book_button_text = $request->book_button_text?? '';
        $data->book_button_url = $request->book_button_url?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status = $request->status ?? 0;
        $data->save();

        return redirect( 'template/book_call' );
    }

    public function show( Request $request, $id ) {
        // $id = $request->id;
        $data = Book_call::find($id);
        // print_r( $data->toArray() );
        // die;
        return view( 'book_call.edit', compact( 'data') );
    }

    public function update( Request $request ) {
        $id = $request->id;

        $image = upload_file_to_s3($request, 'book_image', 'book-a-call');
   
        $data = Book_call::find( $id );

        $userId = Auth::user()->id;

        $update = [

            'book_name' => $request->book_name ?? '',
            'book_image' => $image ?? $data->book_image ?? '',
            'book_heading' => $request->book_heading?? '',
            'book_title1' => $request->book_title1?? '',
            'book_title2' => $request->book_title2?? '',
            'book_description1' => $request->book_description1?? '',
            'book_description2' => $request->book_description2?? '',
            'book_button_text' => $request->book_button_text?? '',
            'book_button_url' => $request->book_button_url?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status'=> $request->status ?? 0,

        ];
        // print_r( $update );
        // die;

        $data->update( $update );
        // $data->save();

        return redirect( 'template/book_call' );
    }

    public function destroy( Request $request, $id ) {
        // $id = $request->id;
        $data = Book_call::find( $id );
        $data->delete();

        return redirect( 'template/book_call' );
        // return response()->json( [ 'data' => $data ] );
    }

}
