<?php

namespace App\Exports;

use App\Models\MeetingMemo;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MemoExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = MeetingMemo::with(['creator:id,name', 'checker:id,name', 'verifier:id,name', 'approver:id,name', 'company:id,name']);

        if (!empty($this->filters['company_id'])) {
            $query->where('company_id', $this->filters['company_id']);
        }

        if (!empty($this->filters['status']) && $this->filters['status'] !== 'all') {
            if ($this->filters['status'] === 'pending') {
                $query->whereIn('status', ['pending_checker', 'pending_verifier', 'pending_approver']);
            } else {
                $query->where('status', $this->filters['status']);
            }
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('meeting_date', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('meeting_date', '<=', $this->filters['date_to']);
        }

        if (!empty($this->filters['created_by'])) {
            $query->where('created_by', $this->filters['created_by']);
        }

        if (!empty($this->filters['memocreators'])) {
            $query->where('created_by', $this->filters['memocreators']);
        }

        if (!empty($this->filters['memoverifers'])) {
            // Usually managers check/verify. We can filter by both checker_id or verifier_id, or just verifier_id.
            $query->where(function($q) {
                $q->where('checker_id', $this->filters['memoverifers'])
                  ->orWhere('verifier_id', $this->filters['memoverifers']);
            });
        }

        if (!empty($this->filters['memoapprovers'])) {
            $query->where('approver_id', $this->filters['memoapprovers']);
        }

        return $query->latest();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Company',
            'Title',
            'Created By',
            'Memo Date',
            'Status',
            'Checked By',
            'Verified By',
            'Approved By',
            'Created At',
        ];
    }

    public function map($memo): array
    {
        $statusLabel = str_replace('_', ' ', ucfirst($memo->status));

        return [
            $memo->id,
            $memo->company->name ?? '-',
            $memo->title,
            $memo->creator->name ?? '-',
            $memo->meeting_date ? $memo->meeting_date->format('Y-m-d') : '-',
            $statusLabel,
            $memo->checker->name ?? '-',
            $memo->verifier->name ?? '-',
            $memo->approver->name ?? '-',
            $memo->created_at->format('Y-m-d H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
            ],
        ];
    }
}
