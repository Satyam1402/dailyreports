<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;

use App\Models\Free_consultation_category;
use App\Models\Free_consultation_item;


class Free_consultationApiController extends Controller
 {

    public function index(Request $request)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'schedule_date' => 'required',
            'schedule_time' => 'required',
            'schedule_duration' => 'required',
            'timezone' => 'required|string',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable',
            'company_name' => 'nullable|string|max:255',
            'phone_no' => 'required',
            'email' => 'required|email',
            'website_link' => 'nullable',
            'instagram_link' => 'nullable',
            'facebook_link' => 'nullable',
            'x_link' => 'nullable',
            'youtube_link' => 'nullable',
            'msg' => 'nullable|string|max:500',
            // 'free_consultaion_item' => 'required|array',
            // 'free_consultaion_item.*.group_service_category_id' => 'required|integer',
            // 'free_consultaion_item.*.group_service_item_id' => 'required|integer',
            // 'free_consultaion_item.*.one_time' => 'required',
            // 'free_consultaion_item.*.recurring' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        // Use a database transaction to ensure atomicity
        DB::beginTransaction();

        try {
            // Create Order
            $order = Free_consultation_category::create([
                'schedule_date' => $request->schedule_date,
                'schedule_time' => $request->schedule_time,
                'schedule_duration' => $request->schedule_duration,
                'timezone' => $request->timezone,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name??'',
                'company_name' => $request->company_name ?? '',
                'phone_no' => $request->phone_no,
                'email' => $request->email,
                'website_link' => $request->website_link ?? '',
                'instagram_link' => $request->instagram_link ?? '',
                'facebook_link' => $request->facebook_link ?? '',
                'x_link' => $request->x_link ?? '',
                'youtube_link' => $request->youtube_link ?? '',
                'msg' => $request->msg ?? '',
            ]);

            if (!empty($request->free_consultaion_item) && is_array($request->free_consultaion_item)) {
            // Create associated OrderItems
            foreach ($request->free_consultaion_item as $item) {
                Free_consultation_item::create([
                    'free_consultation_category_id' => $order->id,  // Link to the created order
                    'group_service_category_id' => $item['group_service_category_id'],
                    'group_service_item_id' => $item['group_service_item_id'],
                    'one_time' => $item['one_time'],
                    'recurring' => $item['recurring'],
                ]);
            }
        }
         // Eager load the Free_consultation_item records along with the order
         $orderWithItems = $order->load('free_consultation_item');

            // Commit the transaction
            DB::commit();

            return response()->json([
                'message' => 'Data created successfully!',
                'order' => $orderWithItems
            ], 201);

        } catch (Exception $e) {
            // Rollback in case of error
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

 }