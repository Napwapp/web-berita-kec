<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agenda;
use App\Models\CategoryAgenda;
use Carbon\Carbon;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua agenda published
        $all = Agenda::query()
            ->where('status', 'published')
            ->with('category')
            ->orderBy('start_at')
            ->get();
        
        $upcoming = $all->filter(fn(Agenda $a) => $a->isUpcoming())->values();
        $ongoing = $all->filter(fn(Agenda $a) => $a->isOngoing())->values();
        $past = $all->filter(fn(Agenda $a) => $a->isPast())
            ->sortByDesc('end_at')->values();

        // Statistik header
        $agendaBulanIni = $all->filter(function ($a) {
            return Carbon::parse($a->start_at)->isSameMonth(now())
                || Carbon::parse($a->end_at)->isSameMonth(now());
        })->count();

        $agendaHariIni = $all->filter(function ($a) {
            return Carbon::parse($a->start_at)->startOfDay()->lte(now())
                && Carbon::parse($a->end_at)->endOfDay()->gte(now());
        })->count();

        // Kategori dengan jumlah agenda published
        $categories = CategoryAgenda::withCount([
            'agendas' => fn($q) => $q->where('status', 'published'),
        ])
            ->having('agendas_count', '>', 0)
            ->orderByDesc('agendas_count')
            ->get();

        // Filter dari query parameter
        $activeTab = $request->get('tab', 'all'); // <── DEFAULT "Semua"
        $search = $request->get('search', '');
        $categoryId = $request->get('category', null);

        // Tentukan koleksi berdasarkan tab
        $list = match ($activeTab) {
            'ongoing' => $ongoing,
            'past' => $past,
            'all' => $all,
            default => $upcoming,
        };

        // Filter search (judul, deskripsi, lokasi)
        if ($search) {
            $q = strtolower(trim($search));
            $list = $list->filter(
                fn($a) =>
                stripos($a->title, $q) !== false ||
                stripos($a->description ?? '', $q) !== false ||
                stripos($a->location ?? '', $q) !== false
            )->values();
        }

        // Filter kategori
        if ($categoryId) {
            $list = $list->filter(fn($a) => $a->category_id == $categoryId)->values();
        }

        return view('agenda.index', compact(
            'all',
            'upcoming',
            'ongoing',
            'past',
            'categories',
            'agendaBulanIni',
            'agendaHariIni',
            'activeTab',
            'search',
            'categoryId',
            'list'
        ));
    }

    public function show(Agenda $agenda)
    {
        if (!$agenda->isPublished()) {
            abort(404);
        }
        return view('agenda.show', compact('agenda'));
    }
}
