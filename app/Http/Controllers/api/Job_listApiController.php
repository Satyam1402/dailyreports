<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Job_list;


class Job_listApiController extends Controller
{
    // public function job_list(){
    //     $job_list = Job_list::where('status',1)
    //     ->orderBy('display_order','asc')
    //     ->select('id','job_title','job_experience','job_type','workplace_type','job_location','job_salary','job_description','display_order','status')
    //     ->get(); 

    //     $data=[
            
    //         'job_list'=>$job_list,

    //         ];

    //         // Return response with top_banner and sorted brands
    //         return response()->json([
    //         'status' => 'success',
    //         'job_list' => $job_list
    //     ]);
        
    // }


    public function job_list(Request $request){
        
        $limit = $request->input('limit', 10); // Default limit is 10
        $offset = $request->input('offset', 0); // Default offset is 0
        // Start with the base query
        $query = Job_list::where('status', 1);
    
        $totalCount = $query->count();

        // Filter by job experience
        if ($request->has('job_experience') && $request->job_experience != '') {
            $query->where('job_experience', $request->job_experience);
        }
    
        // Filter by job type
        if ($request->has('job_type') && $request->job_type != '') {
            $query->where('job_type', $request->job_type);
        }
    
        // Filter by workplace type
        if ($request->has('workplace_type') && $request->workplace_type != '') {
            $query->where('workplace_type', $request->workplace_type);
        }
    
        // Filter by job location
        if ($request->has('job_location') && $request->job_location != '') {
            $query->where('job_location', 'like', '%' . $request->job_location . '%');
        }

        if ($request->has('job_title') && $request->job_title != '') {
            $query->where('job_title', 'like', '%' . $request->job_title . '%');
        }
    
        // Order by display_order (you can also add more ordering logic if necessary)
        $job_list = $query->orderBy('display_order', 'asc')
            ->select('id', 'job_title', 'job_experience', 'job_type', 'workplace_type', 'job_location', 'job_salary', 'job_description', 'display_order', 'status')
            ->skip($offset)
            ->take($limit)
            ->get();

            $data = [
                'job_list' => $job_list,
                'total_count' => $totalCount, // Total count of filtered results
            ];
    
        // Return the filtered data in the response
        return response()->json([
            'status' => 'success',
            // 'total_count' => $totalCount, // Total count of filtered results
            'data' => $data
        ]);
    }
    
}