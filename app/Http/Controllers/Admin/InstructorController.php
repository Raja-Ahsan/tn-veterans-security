<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use Illuminate\Http\Request;

class InstructorController extends Controller
{
    public function index()
    {
        $instructors = Instructor::orderBy('name')->get();

        return view('admin.instructors.index', compact('instructors'));
    }

    public function create()
    {
        return view('admin.instructors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'bio' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        Instructor::create($validated);

        return redirect()->route('admin.instructors.index')
            ->with('success', 'Instructor created successfully.');
    }

    public function edit(Instructor $instructor)
    {
        return view('admin.instructors.edit', compact('instructor'));
    }

    public function update(Request $request, Instructor $instructor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'bio' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $instructor->update($validated);

        return redirect()->route('admin.instructors.index')
            ->with('success', 'Instructor updated successfully.');
    }

    public function destroy(Instructor $instructor)
    {
        $instructor->delete();

        return redirect()->route('admin.instructors.index')
            ->with('success', 'Instructor deleted successfully.');
    }
}
