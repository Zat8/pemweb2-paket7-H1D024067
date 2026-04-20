<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventCategoryRequest;
use App\Http\Requests\UpdateEventCategoryRequest;
use App\Models\EventCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EventCategoryController extends Controller
{
    public function index(): View
    {
        $categories = EventCategory::withCount('events')->latest()->get();

        return view('event-categories.index', [
            'categories' => $categories,
            'editingCategory' => null,
        ]);
    }

    public function create(): View
    {
        return $this->index();
    }

    public function edit(EventCategory $eventCategory): View
    {
        $categories = EventCategory::withCount('events')->latest()->get();

        return view('event-categories.index', [
            'categories' => $categories,
            'editingCategory' => $eventCategory,
        ]);
    }

    public function store(StoreEventCategoryRequest $request): RedirectResponse
    {
        EventCategory::create($request->validated());

        return redirect()->route('event-categories.index')->with('success', 'Kategori event berhasil ditambahkan.');
    }

    public function update(UpdateEventCategoryRequest $request, EventCategory $eventCategory): RedirectResponse
    {
        $eventCategory->update($request->validated());

        return redirect()->route('event-categories.index')->with('success', 'Kategori event berhasil diperbarui.');
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
