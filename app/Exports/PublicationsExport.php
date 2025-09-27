<?php

namespace App\Exports;

use App\Models\Publication;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PublicationsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Publication::with('user')->get()->map(function ($publication) {
            return [
                'ID' => $publication->id,
                'Title' => $publication->titre,
                'Author' => $publication->user ? $publication->user->first_name . ' ' . $publication->user->last_name : 'Deleted User',
                'Category' => $publication->categorie,
                'Content' => $publication->contenu,
                'Created At' => $publication->created_at->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Author',
            'Category',
            'Content',
            'Created At',
        ];
    }
}