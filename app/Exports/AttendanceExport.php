<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AttendanceExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $event;
    public function __construct($event) { $this->event = $event; }

    public function collection()
    {
        return $this->event->attendances()->with(['registration.user'])
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

    public function headings(): array {
        return ['Nama Peserta', 'Email', 'Institusi', 'Token Tiket', 'Waktu Check-In', 'Diverifikasi Oleh'];
    }
}