<?php

namespace App\Filament\Resources\ProductsResource\Pages;

use App\Filament\Resources\ProductsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getFooter(): ?View
    {
        $data['totalPrice'] = null;
        if (Auth::user()->isAdmin()) {
            $data['totalPrice'] = $this->getFilteredTableQuery()
                ->select(DB::raw('SUM(price + COALESCE(joki_nominal, 0)) as total_price'))
                ->value('total_price') ?? 0;
        }

        $data['totalNominalJoki'] = $this->getFilteredTableQuery()
            ->select(DB::raw('SUM(COALESCE(joki_nominal, 0)) as total_nominal_joki'))
            ->when(! Auth::user()->isAdmin(), function ($query) {
                $query->where('created_by', Auth::id());
            })
            ->value('total_nominal_joki') ?? 0;

        return view('filament.resources.products-resource.widgets.rekap-total-produk-belum-terjual', $data);
    }
}
