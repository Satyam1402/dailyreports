<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Contact_us;

class Contact_usApiController extends Controller
 {

    public function index( Request $request ) {

        try {
            // Validate the request data
            $validatedData = $request->validate( [
                'first_name' => 'required|string|max:255',
                // 'last_name' => 'nullable',
                'phone_no' => 'required|string|max:255',
                'company_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'media_budget' => 'required|string', // Optional
                'message' => 'required|string', // Optional
                // 'newsletter' => 'required',
            ] );

            // Store the data
            $submission = Contact_us::create( [
                'first_name' => $validatedData[ 'first_name' ],
                // 'last_name' => $validatedData[ 'last_name' ],
                'phone_no' => $validatedData[ 'phone_no' ],
                'company_name' => $validatedData[ 'company_name' ],
                'email' => $validatedData[ 'email' ],
                'media_budget' => $request->input( 'media_budget', null ), // Optional field
                'message' => $request->input( 'message', null ), // Optional field
                // 'newsletter' => $validatedData[ 'newsletter' ] ?? false,
            ] );

            // Return a JSON response
            return response()->json( [
                'success' => true,
                'data' => $submission,
            ], 201 );

        } catch ( ValidationException $e ) {
            return response()->json( [
                'success' => false,
                'errors' => $e->errors(),
            ], 422 );
        }
    }

}