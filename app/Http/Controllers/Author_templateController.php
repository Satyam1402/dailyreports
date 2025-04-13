<?php

namespace App\Http\Controllers;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

use App\Models\Creative_house_category;
use App\Models\Creative_house_item;
use App\Models\Author_template;
use Illuminate\Support\Facades\Auth;

class Author_templateController extends Controller
{

    public function index() {
        $data = Author_template::query()->get();
        // print_r( $data );
        // die;
        return view( 'author_template.index', compact( 'data') );
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
                'id','template_name','author_image','author_description','click_here_text','click_here_url','author_name','author_url','founder_text','founder_url','cto_text','cto_url','display_order','status'
            ]; // value depend on datatable field not in table
    
            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table
            
            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            $data = Author_template::select('*')->orderBy($sortColumn, $sortDirection);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->status == 0) {
                        return '<span class="badge bg-danger">Inactive</span>';
                    } else {
                        return '<span class="badge bg-success">Active</span>';
                    }
                })
                // ->addColumn('author_image', function ($row) {
                //     $imgUrl = $row->author_image ?? '';
                //     return '<img src="' . $imgUrl . '" alt="Profile Image" width="70" height="70">';
                // })
                ->addColumn('author_image', function ($row) {
                    $imgPath = $row->author_image ?? '';
                    $baseUrl = env('AWS_URL'); // Get the base URL from the .env file
                    $imgUrl = $baseUrl . '/' . $imgPath; // Concatenate the base URL with the stored path
                    return '<img src="' . $imgUrl . '" alt="No Image Available" width="70" height="70">';
                })
                ->editColumn('author_url', function ($row) {
                    return '<a href="'. $row->author_url. '" target="_blank">Click</a>';
                })
                // ->editColumn('created_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
                // })
                // ->editColumn('updated_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->updated_at)->format('d/m/Y');
                // })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-flex">
                            <a href="' . route('author-template.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('author-template.destroy', $row->id) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['status','action','author_image','author_url'])
                ->make(true);
        }
    }

    public function add() {

        return view( 'author_template.add');
    }

    public function store( Request $request )  {


        $request->validate( [
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',

        ] );

        $image = upload_file_to_s3($request, 'author_image', 'author-image');


        $userId = Auth::user()->id;

        $data = new Author_template;
        $data->template_name = $request->template_name ?? '';
        $data->author_image = $image ?? '';
        $data->author_description = $request->author_description?? '';
        $data->click_here_text = $request->click_here_text?? '';
        $data->click_here_url = $request->click_here_url?? '';
        $data->author_name = $request->author_name?? '';
        $data->author_url = $request->author_url?? '';
        $data->founder_text = $request->founder_text?? '';
        $data->founder_url = $request->founder_url?? '';
        $data->cto_text = $request->cto_text?? '';
        $data->cto_url = $request->cto_url?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status = $request->status ?? 0;
        $data->save();

        return redirect( 'template/author' );
    }

    public function show( Request $request, $id ) {
        // $id = $request->id;
        $data = Author_template::find($id);
        // print_r( $data->toArray() );
        // die;
        return view( 'author_template.edit', compact( 'data') );
    }

    public function update( Request $request ) {
        $id = $request->id;

        $image = upload_file_to_s3($request, 'author_image', 'author-image');
   
        $data = Author_template::find( $id );

        $userId = Auth::user()->id;

        $update = [

            'template_name' => $request->template_name ?? '',
            'author_image' => $image ?? $data->author_image ?? '',
            'author_description' => $request->author_description?? '',
            'click_here_text' => $request->click_here_text?? '',
            'click_here_url' => $request->click_here_url?? '',
            'author_name' => $request->author_name?? '',
            'author_url' => $request->author_url?? '',
            'founder_text' => $request->founder_text?? '',
            'founder_url' => $request->founder_url?? '',
            'cto_text' => $request->cto_text?? '',
            'cto_url' => $request->cto_url?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status'=> $request->status ?? 0,

        ];
        // print_r( $update );
        // die;

        $data->update( $update );
        // $data->save();

        return redirect( 'template/author' );
    }

    public function destroy( Request $request, $id ) {
        // $id = $request->id;
        $data = Author_template::find( $id );
        $data->delete();

        return redirect( 'template/author' );
        // return response()->json( [ 'data' => $data ] );
    }

}
