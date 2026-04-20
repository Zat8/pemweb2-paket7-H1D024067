<?php

namespace App\Exports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct(protected Event $event) {}

    public function collection(): Collection
    {
        return $this->event->attendances()->with(['registration.user', 'checkedBy'])
            ->get()
            ->map(fn($att) => [
                $att->registration->user->name,
                $att->registration->user->email,
                $att->registration->user->institution ?? '-',
                $att->registration->ticket_token,
                $att->checked_in_at->format('d M Y H:i:s'),
                $att->checkedBy->name ?? 'System',
            ]);
    }

    public function headings(): array
    {
        return ['Nama Peserta', 'Email', 'Institusi', 'Token Tiket', 'Waktu Check-In', 'Diverifikasi Oleh'];
    }
}
