<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EventController extends Controller
{
    // HALAMAN PUBLIK: Katalog Event
    public function index()
    {
        $query = Event::with(['category', 'creator'])
            ->withCount('registrations')
            ->whereIn('status', ['published', 'closed']);

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
        if (! in_array($event->status, ['published', 'closed'], true)) {
            abort(404);
        }

        $event->load(['category', 'creator'])->loadCount('registrations');

        $registration = Auth::check()
            ? $event->registrations()->with(['attendance', 'certificate'])->where('user_id', Auth::id())->first()
            : null;

        $isRegistered = $registration !== null;
        $isFull = $event->isFull();
        $registrationTicketUrl = $registration ? route('tickets.show', $registration->ticket_token) : null;
        $registrationQrSvg = $registration
            ? QrCode::format('svg')->size(180)->margin(1)->errorCorrection('H')->generate($registrationTicketUrl)
            : null;

        return view('events.public.show', compact(
            'event',
            'isRegistered',
            'isFull',
            'registration',
            'registrationTicketUrl',
            'registrationQrSvg'
        ));
    }

    // DASHBOARD ADMIN/PANITIA: List Event
    public function adminIndex()
    {
        $query = Event::with(['category', 'creator'])->withCount('registrations');

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
        $data['slug'] = $this->generateUniqueSlug($data['title']);
        $data['created_by'] = Auth::id();

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
        $data['slug'] = $this->generateUniqueSlug($data['title'], $event->id);

        if ($request->hasFile('poster')) {
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

        if ($event->poster && Storage::disk('public')->exists($event->poster)) {
            Storage::disk('public')->delete($event->poster);
        }

        $event->delete();

        return redirect()->route('events.admin.index')->with('success', 'Event berhasil dihapus!');
    }

    protected function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 2;

        while (
            Event::query()
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
