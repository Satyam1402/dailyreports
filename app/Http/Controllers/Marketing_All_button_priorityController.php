<?php

namespace App\Http\Controllers;

use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

use App\Models\MarketingHouseCategory;
use App\Models\MarketingHouseItem;
use App\Models\Creative_house_category;
use App\Models\Creative_house_item;
use App\Models\All_button_priority;
use Illuminate\Support\Facades\Auth;

class Marketing_All_button_priorityController extends Controller
{
    // Show the 12 dropdowns with current selections

    public function index()
    {

        // Get all the saved button priorities and their associated category and creative house values
        $allButtonPriorities = All_button_priority::where('type', 'marketing_house')->get();


        // Create an array to hold the selected category and creative house item values for each priority
        $selectedData = [];

        foreach ($allButtonPriorities as $priority) {
            $selectedData[$priority->dropdown_no] = [
                'marketing_house_item_id' => $priority->marketing_house_item_id,
            ];
        }
        $marketing_categories = MarketingHouseCategory::all();
        $marketing_items = MarketingHouseItem::all();
        $dropdownPriorities = $allButtonPriorities->keyBy('dropdown_no');

        return view('all_button_priority.marketing_house_priority', compact('marketing_categories', 'dropdownPriorities', 'marketing_items', 'selectedData'));
    }

    // Handle the update of all dropdown selections

    public function update(Request $request)
    {
        $validated = $request->validate([
            // 'dropdowns' => 'required|array',
            'dropdowns.*' => 'nullable',
            // 'dropdowns.*' => 'required',  // Ensure each dropdown selection is valid
        ]);

        // Update the values for all 12 dropdowns
        foreach ($validated['dropdowns'] as $dropdownNumber => $marketingHouseId) {
            All_button_priority::updateOrCreate(
                ['dropdown_no' => $dropdownNumber, 'type' => 'marketing_house'],  // Add type = 'marketing_house'
                ['marketing_house_item_id' => $marketingHouseId]
            );
        }

        return response()->json(['status' => 'success', 'message' => 'All selections updated successfully']);
    }

    // public function update1( Request $request )
    // {
    //         $validated = $request->validate( [
    //             'dropdowns.*' => 'nullable',  // Make sure it's nullable if no selection is made
    //         'categories.*' => 'required',  // Ensure category selections are also nullable
    //     ]);


    //         foreach ($validated['dropdowns'] as $dropdownNumber => $creativeHouseId) {
    //             $categoryId = isset($validated['categories'][$dropdownNumber]) ? $validated['categories'][$dropdownNumber] : 100;

    //             // Use updateOrCreate to store category_id and creative_house_item_id for each priority
    //             All_button_priority::updateOrCreate(
    //                 ['dropdown_no' => $dropdownNumber],
    //                 [
    //                     'creative_house_category_id' => $categoryId,  // Save the category ID
    //                     'creative_house_item_id' => $creativeHouseId,
    //                 ]
    //             );
    //         }

    //          return response()->json(['status' => 'success', 'message' => 'All selections updated successfully' ] );
    // }

    // public function getItemsByCategory( $categoryId )
    // {
    //     // Find the category by ID
    //     $category = Creative_house_category::find( $categoryId );

    //         if ( $category ) {
    //             // Fetch items related to the selected category
    //             $items = $category->items;
    //             // This uses the hasMany relationship

    //             // Return the filtered items in JSON format
    //             return response()->json( [
    //                 'items' => $items
    //     ] );
    //         }

    //     // Return an empty array if no category found
    //     return response()->json( [ 'items' => [] ] );
    // }


}
