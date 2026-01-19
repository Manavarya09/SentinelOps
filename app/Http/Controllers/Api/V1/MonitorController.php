<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Monitor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitorController extends Controller
{
    public function index()
    {
        $monitors = Auth::user()->organization->monitors()
            ->with(['checks' => function ($query) {
                $query->latest()->limit(10);
            }])
            ->get();

        return response()->json($monitors);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'method' => 'in:GET,POST,PUT,PATCH,DELETE',
            'headers' => 'nullable|array',
            'body' => 'nullable|array',
            'interval' => 'integer|min:1|max:1440',
            'timeout' => 'integer|min:1|max:300',
            'retries' => 'integer|min:0|max:10',
            'failure_threshold' => 'integer|min:1|max:10',
            'is_active' => 'boolean',
            'check_ssl' => 'boolean',
            'response_time_threshold' => 'nullable|integer|min:1',
        ]);

        $monitor = Auth::user()->organization->monitors()->create($validated);

        return response()->json($monitor, 201);
    }

    public function show(Monitor $monitor)
    {
        $this->authorize('view', $monitor);

        $monitor->load(['checks' => function ($query) {
            $query->latest()->limit(50);
        }]);

        return response()->json($monitor);
    }

    public function update(Request $request, Monitor $monitor)
    {
        $this->authorize('update', $monitor);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'url' => 'url',
            'method' => 'in:GET,POST,PUT,PATCH,DELETE',
            'headers' => 'nullable|array',
            'body' => 'nullable|array',
            'interval' => 'integer|min:1|max:1440',
            'timeout' => 'integer|min:1|max:300',
            'retries' => 'integer|min:0|max:10',
            'failure_threshold' => 'integer|min:1|max:10',
            'is_active' => 'boolean',
            'check_ssl' => 'boolean',
            'response_time_threshold' => 'nullable|integer|min:1',
        ]);

        $monitor->update($validated);

        return response()->json($monitor);
    }

    public function destroy(Monitor $monitor)
    {
        $this->authorize('delete', $monitor);

        $monitor->delete();

        return response()->json(['message' => 'Monitor deleted']);
    }
}
