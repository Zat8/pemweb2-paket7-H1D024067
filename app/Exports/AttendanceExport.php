<?php

namespace App\Exports;

use App\Models\Event;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct(protected Event $event) {}

    public function collection(): Collection
    {
        return $this->event->registrations()->with(['user', 'attendance.checkedBy'])
            ->get()
            ->map(fn($registration) => [
                $registration->user->name,
                $registration->user->email,
                $registration->user->institution ?? '-',
                $registration->ticket_token,
                $registration->attendance?->checked_in_at?->format('d M Y H:i:s') ?? '-',
                $registration->attendance?->checkedBy?->name ?? '-',
            ]);
    }

    public function headings(): array
    {
        return ['Nama Peserta', 'Email', 'Institusi', 'Token Tiket', 'Waktu Check-In', 'Diverifikasi Oleh'];
    }
}
