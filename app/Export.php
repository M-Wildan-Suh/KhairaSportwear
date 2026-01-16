<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransaksiExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $transactions;
    
    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }
    
    public function collection()
    {
        return $this->transactions;
    }
    
    public function headings(): array
    {
        return [
            'Kode Transaksi',
            'Tanggal',
            'Customer',
            'Telepon',
            'Items',
            'Subtotal',
            'Diskon',
            'Pajak',
            'Total',
            'Status',
            'Metode Bayar',
            'Kasir',
        ];
    }
    
    public function map($transaction): array
    {
        return [
            $transaction->kode_transaksi,
            $transaction->created_at->format('d/m/Y H:i'),
            $transaction->customer_name,
            $transaction->customer_phone,
            $transaction->items_count,
            $transaction->subtotal,
            $transaction->diskon,
            $transaction->pajak ?? 0,
            $transaction->total_bayar,
            ucfirst($transaction->status),
            ucfirst(str_replace('_', ' ', $transaction->metode_bayar)),
            $transaction->user->name ?? '-',
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            // Style baris header
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5']
                ]
            ],
        ];
    }
}