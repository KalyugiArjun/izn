<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OfficerInspectionNote;
use App\Models\TrafficInspectorInspectionNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;


class InspectionNotesController extends Controller
{

public function officerIndex(Request $request): JsonResponse
{
    $perPage = min((int) $request->get('per_page', 10), 50);
    $search = trim($request->get('search', ''));
    $isLatest = $request->get('latest', 'false') === 'true';
    
    // Get bookmarked IDs from request
    $bookmarkedIds = $request->get('bookmarked_ids', []);
    
    // If it's a string, convert to array
    if (is_string($bookmarkedIds)) {
        $bookmarkedIds = array_filter(explode(',', $bookmarkedIds));
    }
    
    // Convert to integers
    $bookmarkedIds = array_map('intval', $bookmarkedIds);

    $query = officerInspectionNote::select([
        'id',
        'type_of_inspection',
        'place_of_inspection',
        'date_of_inspection',
        'inspection_note_pdf',
        'status',
    ]);

    // Apply search filter if provided
    if ($search !== '') {
        $query->where(function ($q) use ($search) {
            $q->where('date_of_inspection', 'LIKE', "%{$search}%")
              ->orWhere('type_of_inspection', 'LIKE', "%{$search}%");
        });
    }

    // Apply latest filter (last 7 days)
    if ($isLatest) {
        $query->where('date_of_inspection', '>=', now()->subDays(7)->startOfDay());
    }

    // Apply bookmarked IDs filter
    if (!empty($bookmarkedIds)) {
        $query->whereIn('id', $bookmarkedIds);
    }

    $circulars = $query
        ->orderBy('date_of_inspection', 'desc')
        ->orderBy('id', 'desc')
        ->paginate($perPage);

    // Add bookmarked status to each item
    $items = $circulars->items();
    foreach ($items as $item) {
        $item->is_bookmarked = in_array($item->id, $bookmarkedIds);
    }

    return response()->json([
        'success' => true,
        'message' => 'Inspection Notes fetched successfully.',
        'data' => $items,
        'pagination' => [
            'current_page' => $circulars->currentPage(),
            'per_page' => $circulars->perPage(),
            'total' => $circulars->total(),
            'last_page' => $circulars->lastPage(),
            'has_more' => $circulars->hasMorePages(),
        ],
        'filters' => [
            'bookmarked_ids' => $bookmarkedIds,
            'search' => $search,
            'latest' => $isLatest
        ]
    ], 200);
}


public function trafficIndex(Request $request): JsonResponse
{
    $perPage = min((int) $request->get('per_page', 10), 50);
    $search = trim($request->get('search', ''));
    $isLatest = $request->get('latest', 'false') === 'true';
    
    // Get bookmarked IDs from request
    $bookmarkedIds = $request->get('bookmarked_ids', []);
    
    // If it's a string, convert to array
    if (is_string($bookmarkedIds)) {
        $bookmarkedIds = array_filter(explode(',', $bookmarkedIds));
    }
    
    // Convert to integers
    $bookmarkedIds = array_map('intval', $bookmarkedIds);

    $query = TrafficInspectorInspectionNote::select([
        'id',
        'type_of_inspection',
        'place_of_inspection',
        'date_of_inspection',
        'inspection_note_pdf',
        'status',
    ]);

    // Apply search filter if provided
    if ($search !== '') {
        $query->where(function ($q) use ($search) {
            $q->where('date_of_inspection', 'LIKE', "%{$search}%")
              ->orWhere('type_of_inspection', 'LIKE', "%{$search}%");
        });
    }

    // Apply latest filter (last 7 days)
    if ($isLatest) {
        $query->where('date_of_inspection', '>=', now()->subDays(7)->startOfDay());
    }

    // Apply bookmarked IDs filter
    if (!empty($bookmarkedIds)) {
        $query->whereIn('id', $bookmarkedIds);
    }

    $circulars = $query
        ->orderBy('date_of_inspection', 'desc')
        ->orderBy('id', 'desc')
        ->paginate($perPage);

    // Add bookmarked status to each item
    $items = $circulars->items();
    foreach ($items as $item) {
        $item->is_bookmarked = in_array($item->id, $bookmarkedIds);
    }

    return response()->json([
        'success' => true,
        'message' => 'Inspection Notes fetched successfully.',
        'data' => $items,
        'pagination' => [
            'current_page' => $circulars->currentPage(),
            'per_page' => $circulars->perPage(),
            'total' => $circulars->total(),
            'last_page' => $circulars->lastPage(),
            'has_more' => $circulars->hasMorePages(),
        ],
        'filters' => [
            'bookmarked_ids' => $bookmarkedIds,
            'search' => $search,
            'latest' => $isLatest
        ]
    ], 200);
}

// public function trafficIndex(Request $request): JsonResponse
// {
//     $perPage = min((int) $request->get('per_page', 10), 50);

//     $circulars = TrafficInspectorInspectionNote::select([
//         'id',
//         'type_of_inspection',
//         'place_of_inspection',
//         'date_of_inspection',
//         'inspection_note_pdf',
//         'status',
//     ])
//     ->orderBy('id', 'desc')
//     ->paginate($perPage);

//     return response()->json([
//         'success' => true,
//         'message' => 'Officer Inspection Note fetched successfully.',
//         'data' => $circulars->items(),
//         'pagination' => [
//             'current_page' => $circulars->currentPage(),
//             'per_page' => $circulars->perPage(),
//             'total' => $circulars->total(),
//             'last_page' => $circulars->lastPage(),
//             'has_more' => $circulars->hasMorePages(),
//         ],
//     ], 200);
// }

}