<?php

namespace App\Http\Controllers;

use App\Models\EventCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventCategoryController extends Controller
{
    public function index(): View
    {
        $categories = EventCategory::withCount('events')->latest()->get();

        return view('event-categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:event_categories,name'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Kategori ini sudah ada.',
        ]);

        EventCategory::create($validated);

        return redirect()->route('event-categories.index')->with('success', 'Kategori event berhasil ditambahkan.');
    }

    public function destroy(EventCategory $eventCategory): RedirectResponse
    {
        if ($eventCategory->events()->exists()) {
            return redirect()->route('event-categories.index')
                ->with('error', 'Kategori tidak bisa dihapus karena masih dipakai oleh event.');
        }

        $eventCategory->delete();

        return redirect()->route('event-categories.index')->with('success', 'Kategori event berhasil dihapus.');
    }
}
