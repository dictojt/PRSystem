<?php

namespace App\Http\Controllers;

use App\Models\PrsRequest;
use App\Models\RequestAction;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Create request form
     */
    public function createRequest()
    {
        return view('user.create-request');
    }

    /**
     * Store new request(s). Each item gets its own request_id.
     */
    public function storeRequest(Request $request)
    {
        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s.,\-]+$/'],
            'items.*.description' => ['nullable', 'string', 'max:500', 'regex:/^[a-zA-Z0-9\s.,\-]+$/'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1', 'max:9999'],
        ], [
            'items.required' => 'Please add at least one item.',
            'items.*.item_name.required' => 'Item name is required.',
            'items.*.item_name.regex' => 'Item name may only contain letters, numbers, spaces, dots, commas, and hyphens.',
            'items.*.description.regex' => 'Description may only contain letters, numbers, spaces, dots, commas, and hyphens.',
        ]);

        $userId = auth()->id();
        if (! $userId) {
            return redirect()->route('user.requests.create')
                ->withInput()
                ->with('error', 'You must be logged in to create a request.');
        }

        $items = array_values(array_filter($request->input('items', []), function ($item) {
            return ! empty(trim($item['item_name'] ?? ''));
        }));

        if (empty($items)) {
            return redirect()->route('user.requests.create')
                ->withInput()
                ->with('error', 'Please add at least one item with a name.');
        }

        $createdIds = [];
        foreach ($items as $item) {
            $requestId = PrsRequest::generateRequestId();
            $quantity = isset($item['quantity']) && (int) $item['quantity'] >= 1
                ? (int) $item['quantity']
                : 1;

            $prsRequest = PrsRequest::create([
                'user_id' => $userId,
                'request_id' => $requestId,
                'item_name' => trim($item['item_name']),
                'description' => isset($item['description']) ? trim($item['description']) : null,
                'quantity' => $quantity,
                'status' => 'Pending',
            ]);

            RequestAction::create([
                'request_id' => $prsRequest->id,
                'description' => 'Send to approver',
                'due_date' => now()->addDays(2),
                'status' => 'pending',
            ]);

            $createdIds[] = $requestId;
        }

        $route = auth()->check() ? 'user.dashboard' : 'user.guest';
        $message = count($createdIds) === 1
            ? "Request created successfully. Request ID: {$createdIds[0]}"
            : 'Requests created successfully. Request IDs: ' . implode(', ', $createdIds);

        return redirect()
            ->route($route)
            ->with('success', $message);
    }

    /**
     * View the user's requested items (list of their requests)
     * Query: ?status=all|pending|completed
     */
    public function viewRequests(Request $request)
    {
        $userId = auth()->id();
        $requests = [];
        $filter = $request->query('status', 'all');
        if (! in_array($filter, ['all', 'pending', 'completed'], true)) {
            $filter = 'all';
        }

        if ($userId) {
            $query = PrsRequest::where('user_id', $userId);
            $requests = $query
                ->orderByDesc('created_at')
                ->get()
                ->map(fn ($r) => [
                    'request_id' => $r->request_id,
                    'item_name' => $r->item_name,
                    'quantity' => $r->quantity ?? 1,
                    'description' => $r->description,
                    'status' => $r->status,
                    'created_at' => $r->created_at->format('M d, Y'),
                    'rejection_reason' => $r->rejection_reason,
                ])
                ->toArray();
        }

        return view('user.view-requests', compact('requests', 'filter'));
    }

    /**
     * View reports
     */
    public function reports()
    {
        $userId = auth()->id();
        if (! $userId) {
            $reports = [
                ['name' => 'Requests Summary', 'period' => 'This Month', 'value' => '0 requests'],
                ['name' => 'Completed', 'period' => 'This Month', 'value' => '0'],
                ['name' => 'Pending', 'period' => 'Current', 'value' => '0'],
            ];
            return view('user.reports', compact('reports'));
        }

        $thisMonthCount = PrsRequest::where('user_id', $userId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $completedCount = PrsRequest::where('user_id', $userId)
            ->where('status', 'Approved')
            ->whereMonth('approved_at', now()->month)
            ->whereYear('approved_at', now()->year)
            ->count();

        $pendingCount = PrsRequest::where('user_id', $userId)
            ->where('status', 'Pending')
            ->count();

        $reports = [
            ['name' => 'Requests Summary', 'period' => 'This Month', 'value' => $thisMonthCount . ' requests'],
            ['name' => 'Completed', 'period' => 'This Month', 'value' => (string) $completedCount],
            ['name' => 'Pending', 'period' => 'Current', 'value' => (string) $pendingCount],
        ];

        return view('user.reports', compact('reports'));
    }

    /**
     * Support page
     */
    public function support()
    {
        return view('user.support');
    }
}
