<?php
namespace App\Filament\Widgets;

use App\Models\Agenda;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\AgendaResource;
use App\Filament\Resources\Schemas\Forms\AgendaForms;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Saade\FilamentFullcalendar\Data\EventData;
use Saade\FilamentFullCalendar\Actions;
use Filament\Forms;
use Illuminate\Support\Carbon;
use Filament\Notifications\Notification;

class CalendarWidget extends FullCalendarWidget
{
    public Model|string|null $model = Agenda::class;

    protected function modalActions(): array
    {
        return [
            // Form modal saat klik tanggal pada calender
            Actions\CreateAction::make()
                ->mountUsing(
                    function (Forms\Form $form, array $arguments) {
                        $form->fill([
                            'title' => $arguments['agenda']['title'] ?? '',
                            'description' => $arguments['agenda']['description'] ?? '',
                            'is_all_day' => $arguments['agenda']['is_all_day'] ?? false,
                            'start_at' => $arguments['agenda']['start'] ?? now(),
                            'end_at' => $arguments['agenda']['end'] ?? now()->addHour(),
                            'location' => $arguments['agenda']['location'] ?? '',
                            'is_published' => $arguments['agenda']['is_published'] ?? false,
                            'is_online' => $arguments['agenda']['is_online'] ?? false,
                            'online_link' => $arguments['agenda']['online_link'] ?? '',
                            'category_id' => $arguments['agenda']['category_id'] ?? null,
                        ]);
                    }
                ),
            Actions\DeleteAction::make(),
        ];
    }

    // ──────────────────────────────────────────────────────────────────────
    // FETCH EVENTS
    // ──────────────────────────────────────────────────────────────────────

    public function fetchEvents(array $fetchInfo): array
    {
        return Agenda::query()
            ->isPublished()
            ->where('start_at', '>=', $fetchInfo['start'])
            ->where('end_at', '<=', $fetchInfo['end'])
            ->with('category')
            ->orderBy('start_at')
            ->get()
            ->map(
                function (Agenda $agenda) {
                    return [
                        'id' => $agenda->id,
                        'title' => $agenda->title,
                        'start' => $agenda->start_at->toIso8601String(),
                        'end' => $agenda->end_at->toIso8601String(),
                        'allDay' => $agenda->is_all_day,
                        'url' => AgendaResource::getUrl('view', ['record' => $agenda]),
                        'backgroundColor' => $this->resolveEventColor($agenda),
                        'borderColor' => $this->resolveEventColor($agenda),
                        'textColor' => '#ffffff',
                        'extendedProps' => [
                            'description' => $agenda->description,
                            'location' => $agenda->location,
                            'is_online' => $agenda->is_online,
                            'online_link' => $agenda->online_link,
                            'is_all_day' => $agenda->is_all_day,
                            'status' => $agenda->status,
                            'category' => $agenda->category?->name,
                            'category_color' => $agenda->category?->color,
                        ],
                    ];

                }

            )
            ->toArray();
    }

    // ──────────────────────────────────────────────────────────────────────
    // HElPER
    // ──────────────────────────────────────────────────────────────────────

    private function resolveEventColor(Agenda $agenda): string
    {
        // Gunakan warna kategori jika tersedia
        if ($agenda->category?->color) {
            return $agenda->category->color;
        }

        // Fallback berdasarkan status waktu
        if ($agenda->isOngoing()) {
            return '#16a34a'; // green-600 — sedang berlangsung
        }

        if ($agenda->isUpcoming()) {
            return '#2563eb'; // blue-600  — akan datang
        }
        return '#9ca3af'; // gray-400  — sudah selesai
    }


    // ──────────────────────────────────────────────────────────────────────
    // DRAG & DROP — eventDrop
    // ──────────────────────────────────────────────────────────────────────

    public function onEventDrop(
        array $event,
        array $oldEvent,
        array $relatedEvents,
        array $delta,
        array|null $oldResource,
        array|null $newResource,
    ): bool {

        $agenda = Agenda::find($event['id']);

        if (!$agenda) {
            return false;
        }

        $oldStart = Carbon::parse($oldEvent['start']);
        $newStart = Carbon::parse($event['start']);

        // Hitung selisih waktu antara posisi lama dan baru (dalam detik)
        $deltaSeconds = $oldStart->diffInSeconds($newStart, false);

        // Geser start_at & end_at sebesar delta yang sama
        // sehingga durasi agenda tidak berubah
        $newStartAt = Carbon::parse($agenda->start_at)->addSeconds($deltaSeconds);
        $newEndAt = Carbon::parse($agenda->end_at)->addSeconds($deltaSeconds);

        // Tangani peralihan allDay → timed atau sebaliknya
        // Jika event di-drop ke slot all-day, jam di-strip
        $isAllDay = $event['allDay'] ?? $agenda->is_all_day;

        $agenda->update([
            'start_at' => $newStartAt,
            'end_at' => $newEndAt,
            'is_all_day' => $isAllDay,
        ]);

        Notification::make()
            ->title('Agenda diperbarui')
            ->body("\"{$agenda->title}\" dipindahkan ke " . $newStartAt->translatedFormat('d M Y') . '.')
            ->success()
            ->send();

        // Dispatch event untuk refresh UI calendar dengan data terbaru
        $this->dispatch('filament-fullcalendar--refresh');

        return true;
    }

    // ──────────────────────────────────────────────────────────────────────
    // RESIZE — onEventResize
    // ──────────────────────────────────────────────────────────────────────

    public function onEventResize(
        array $event, 
        array $oldEvent, 
        array $relatedEvents, 
        array $startDelta, 
        array $endDelta
    ): bool {
        $agenda = Agenda::find($event['id']);

        if (!$agenda) {
            return false;
        }

        $newStart = Carbon::parse($event['start']);
        $newEnd = Carbon::parse($event['end']);

        // Validasi: end tidak boleh sama atau sebelum start
        if ($newEnd->lessThanOrEqualTo($newStart)) {
            Notification::make()
                ->title('Gagal memperbarui')
                ->body('Waktu selesai tidak boleh sebelum waktu mulai.')
                ->danger()
                ->send();

            // false → kalender otomatis revert resize ke ukuran sebelumnya
            return false;
        }

        $agenda->update([
            'start_at' => $newStart,
            'end_at' => $newEnd,
        ]);

        $duration = $newStart->diffForHumans($newEnd, [
            'parts' => 2,
            'syntax' => Carbon::DIFF_ABSOLUTE,
        ]);

        Notification::make()
            ->title('Durasi agenda diperbarui')
            ->body("\"{$agenda->title}\" sekarang berdurasi {$duration}.")
            ->success()
            ->send();

        // Dispatch event untuk refresh UI calendar dengan data terbaru
        $this->dispatch('filament-fullcalendar--refresh');

        return true;
    }


    // Config untuk full calendar
    public function config(): array
    {
        return [
            'firstDay' => 1,
            'initialView' => 'dayGridMonth',

            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay,listMonth',
            ],

            'buttonText' => [
                'today' => 'Hari Ini',
                'month' => 'Bulan',
                'week' => 'Minggu',
                'day' => 'Hari',
                'list' => 'Daftar',
            ],

            'eventStartEditable' => true, // Enable drag-and-drop untuk event
            'eventDurationEditable' => true, // Enable resizing untuk event
            'eventResizableFromStart' => true, // Resizing dari awal event
            'snapDuration' => '00:15:00', // Snap ke interval 15 menit saat drag/resizing

            'dayMaxEvents' => 3, // Tampilkan maks 3 event per hari, sisanya "+N more"
            'nowIndicator' => true, // Garis "sekarang" di view minggu/hari
            'navLinks' => true, // Klik tanggal → pindah ke view hari

            'eventTimeFormat' => [
                'hour' => '2-digit',
                'minute' => '2-digit',
                'hour12' => false,
            ],
        ];
    }


    // Form Schema
    public function getFormSchema(): array
    {
        return (AgendaForms::class)::schema();
    }

}