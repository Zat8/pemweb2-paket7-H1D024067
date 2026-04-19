<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventCategory;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    // HALAMAN PUBLIK: Katalog Event
    public function index()
    {
        $query = Event::with(['category', 'creator'])->where('status', 'published');
        
        // Search & Filter
        if (request('search')) {
            $query->where('title', 'like', '%'.request('search').'%');
        }
        if (request('category')) {
            $query->where('event_category_id', request('category'));
        }
        if (request('date')) {
            $query->whereDate('event_date', request('date'));
        }
        
        $events = $query->orderBy('event_date', 'asc')->paginate(10)->withQueryString();
        $categories = EventCategory::all();
        
        return view('events.public.index', compact('events', 'categories'));
    }

    // DETAIL EVENT PUBLIK
    public function show(Event $event)
    {
        if ($event->status !== 'published') {
            abort(404);
        }
        $event->load(['category', 'creator', 'registrations']);
        $isRegistered = Auth::check() ? $event->registrations()->where('user_id', Auth::id())->exists() : false;
        $isFull = $event->registrations()->count() >= $event->quota;
        
        return view('events.public.show', compact('event', 'isRegistered', 'isFull'));
    }

    // DASHBOARD ADMIN/PANITIA: List Event
    public function adminIndex()
    {
        $query = Event::with(['category', 'creator']);
        
        // Filter hanya event yang dibuat oleh panitia (bukan admin)
        if (Auth::user()->role === 'panitia') {
            $query->where('created_by', Auth::id());
        }
        
        if (request('search')) {
            $query->where('title', 'like', '%'.request('search').'%');
        }
        if (request('status')) {
            $query->where('status', request('status'));
        }
        
        $events = $query->latest()->paginate(10)->withQueryString();
        return view('events.admin.index', compact('events'));
    }

    // CREATE: Form Tambah Event
    public function create()
    {
        $categories = EventCategory::all();
        return view('events.admin.create', compact('categories'));
    }

    // STORE: Simpan Event Baru
    public function store(StoreEventRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']);
        $data['created_by'] = Auth::id();
        
        // Handle Upload Poster
        if ($request->hasFile('poster')) {
            $path = $request->file('poster')->store('posters', 'public');
            $data['poster'] = $path;
        }
        
        Event::create($data);
        return redirect()->route('events.admin.index')->with('success', 'Event berhasil dibuat!');
    }

    // EDIT: Form Edit Event
    public function edit(Event $event)
    {
        // Hanya creator atau admin yang bisa edit
        if (Auth::user()->role !== 'admin' && $event->created_by !== Auth::id()) {
            abort(403);
        }
        $categories = EventCategory::all();
        return view('events.admin.edit', compact('event', 'categories'));
    }

    // UPDATE: Update Event
    public function update(UpdateEventRequest $request, Event $event)
    {
        if (Auth::user()->role !== 'admin' && $event->created_by !== Auth::id()) {
            abort(403);
        }
        
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']);
        
        // Handle Upload Poster Baru
        if ($request->hasFile('poster')) {
            // Hapus poster lama jika ada
            if ($event->poster && Storage::disk('public')->exists($event->poster)) {
                Storage::disk('public')->delete($event->poster);
            }
            $path = $request->file('poster')->store('posters', 'public');
            $data['poster'] = $path;
        }
        
        $event->update($data);
        return redirect()->route('events.admin.index')->with('success', 'Event berhasil diperbarui!');
    }

    // DELETE: Hapus Event
    public function destroy(Event $event)
    {
        if (Auth::user()->role !== 'admin' && $event->created_by !== Auth::id()) {
            abort(403);
        }
        
        // Hapus file poster jika ada
        if ($event->poster && Storage::disk('public')->exists($event->poster)) {
            Storage::disk('public')->delete($event->poster);
        }
        
        $event->delete();
        return redirect()->route('events.admin.index')->with('success', 'Event berhasil dihapus!');
    }
}