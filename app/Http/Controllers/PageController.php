<?php

namespace App\Http\Controllers;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

use App\Models\Page;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class PageController extends Controller
{

    public function index()
    {
        $data = Page::query()->get();
        // print_r($data);
        // die;
        return view('pages.page',compact('data'));
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
                'id','page_name','page_slug','page_title','page_description','display_order','status'
            ]; // value depend on datatable field not in table

            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table

            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            $data = Page::select('*')->orderBy($sortColumn, $sortDirection);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->status == 0) {
                        return '<span class="badge bg-danger">Inactive</span>';
                    } else {
                        return '<span class="badge bg-success">Active</span>';
                    }
                })
                // ->addColumn('client_img', function ($row) {
                //     $imgUrl = $row->client_img ?? '';
                //     return '<img src="' . $imgUrl . '" alt="Profile Image" width="70" height="70">';
                // })
                // ->editColumn('client_description', function ($row) {
                //     return '<a href="'. $row->client_description. '" target="_blank">Click</a>';
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
                            <a href="' . route('page.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('page.destroy', $row->id) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['status','action'])
                ->make(true);
        }
    }

    public function add(){
        return view('pages.add_page');
    }

    public function store( Request $request )
    {

        $request->validate( [
            // 'title'=>'required',
        ], [
            // 'name.required' => 'Name cannot be empty',
        ] );

        // $image = upload_file_to_s3($request, 'client_img', 'client-image');

        $userId=Auth::user()->id;
        $slug = $this->generateSlug($request->page_title);

        $data = new Page;
        $data->page_name = $request->page_name ?? '';
        $data->page_slug = $slug ?? '';
        $data->page_title = $request->page_title ?? '';
        $data->page_description = $request->page_description ?? '';
        $data->page_meta_keyword = $request->meta_keyword ?? '';
        $data->page_meta_description = $request->meta_description ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status = $request->status ?? 0;
        $data->save();

        return redirect()->route('page.index');
    }

    public function show(Request $request,$id)
    {
            // $id = $request->id;
            $data = Page::find($id);
            return view('pages.edit_page',compact('data'));
    }

    public function update(Request $request)
    {
        $id = $request->id;

        // $image = upload_file_to_s3($request, 'client_img', 'client-image');

        $data = Page::find($id);

        $slug = $this->generateSlug($request->page_title, $id);

        $userId=Auth::user()->id;

        $update = [

            'page_name' => $request->page_name ?? '',
            'page_slug' => $slug ?? '',
            'page_title' => $request->page_title ?? '',
            'page_description' => $request->page_description ?? '',
            'page_meta_keyword' => $request->meta_keyword ?? '',
            'page_meta_description' => $request->meta_description ?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status'=> $request->status ?? 0,

        ];
        // print_r($update);
        // die;

        $data->update($update);
        // $data->save();

        return redirect()->route('page.index');
    }

    public function destroy(Request $request,$id)
    {
        // $id = $request->id;
        $data = Page::find($id);
        $data->delete();

        return redirect()->route('page.index');
    }

    public function generateSlug($slugName, $categoryId = null)
    {
        $slug = strtolower($slugName); 
        $slug = preg_replace('/\s+/', '-', $slug); // Replace spaces with hyphens
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug); // Remove any non-alphanumeric characters (except dash)

        // Check if the slug already exists, but exclude the current category (if editing)
        $originalSlug = $slug;
        $i = 1;
        while (Page::where('page_slug', $slug)
                    ->where('id', '!=', $categoryId) // Exclude the current category record
                    ->exists()) {
            $slug = $originalSlug . '-' . $i;
            $i++;
        }

        return $slug;
    }




}
