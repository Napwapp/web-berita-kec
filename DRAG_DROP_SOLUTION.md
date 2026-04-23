# Solusi FullCalendar Drag-Drop: Event Tetap di Posisi Baru

## Ringkasan Masalah
Setelah drag-drop event dan database berhasil update, UI FullCalendar kembali ke posisi lama (hanya benar setelah refresh halaman).

---

## Jawaban untuk Setiap Pertanyaan

### 1. **Apakah ada method untuk update/refresh event di UI setelah drag-drop?**

✅ **YA, ada!** Method: `$this->dispatch('filament-fullcalendar--refresh')`

**Cara kerja:**
```php
// Di CalendarWidget.php, dalam method onEventDrop()
public function onEventDrop(...): bool
{
    $agenda = Agenda::find($event['id']);
    $agenda->update([...]);
    
    // Trigger refresh event
    $this->dispatch('filament-fullcalendar--refresh');
    
    return true;
}
```

**Kode di JavaScript yang mendengarkan:**
```js
// Di filament-fullcalendar.js (line 143)
window.addEventListener('filament-fullcalendar--refresh', () =>
    this.calendar.refetchEvents()
)
```

---

### 2. **Bagaimana cara membuat FullCalendar tetap menampilkan event di posisi baru tanpa perlu refresh?**

**Solusi:** Dispatch event ke browser untuk memanggil `refetchEvents()`:

```php
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
    
    // UPDATE DATABASE
    $newStartAt = Carbon::parse($agenda->start_at)->addSeconds($deltaSeconds);
    $newEndAt = Carbon::parse($agenda->end_at)->addSeconds($deltaSeconds);
    
    $agenda->update([
        'start_at' => $newStartAt,
        'end_at' => $newEndAt,
        'is_all_day' => $event['allDay'] ?? $agenda->is_all_day,
    ]);
    
    // ⭐ REFRESH UI CALENDAR
    $this->dispatch('filament-fullcalendar--refresh');
    
    return true;
}
```

**Cara kerjanya:**
1. Database update → sukses ✓
2. PHP dispatch event → `filament-fullcalendar--refresh` ✓
3. JavaScript listener → tangkap event dan jalankan `calendar.refetchEvents()` ✓
4. `refetchEvents()` → panggil `fetchEvents()` di PHP ✓
5. PHP return data event terbaru dari database ✓
6. UI FullCalendar update dengan data terbaru ✓

---

### 3. **Apakah ada event listener atau dispatch yang diperlukan?**

✅ **YA, event listener sudah ada di source code!**

**Event listeners yang tersedia di `filament-fullcalendar.js` (line 143-154):**

```js
// Refresh semua events
window.addEventListener('filament-fullcalendar--refresh', () =>
    this.calendar.refetchEvents()
)

// Navigasi ke hari ini
window.addEventListener('filament-fullcalendar--today', () =>
    this.calendar.today()
)

// Ubah view (month, week, day, dll)
window.addEventListener('filament-fullcalendar--view', (event) =>
    this.calendar.changeView(event.detail.view)
)

// Pergi ke tanggal tertentu
window.addEventListener('filament-fullcalendar--goto', (event) =>
    this.calendar.gotoDate(event.detail.date)
)
```

**Cara dispatch di PHP:**
```php
// Di trait InteractsWithEvents.php (line 107)
public function refreshRecords(): void
{
    $this->dispatch('filament-fullcalendar--refresh');
}
```

---

### 4. **Apakah perlu mengembalikan data event yang updated dari method onEventDrop()?**

❌ **TIDAK perlu!**

`onEventDrop()` hanya return boolean:
- **`return true`** = Jangan revert perubahan (keep posisi baru)
- **`return false`** = Revert ke posisi lama

Setelah `return true`, method `dispatch('filament-fullcalendar--refresh')` akan otomatis fetch data terbaru via method `fetchEvents()`.

---

### 5. **Bagaimana contoh implementasi yang benar agar event tetap di posisi baru?**

#### **BEFORE (Masalah):**
```php
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
    $deltaSeconds = $oldStart->diffInSeconds($newStart, false);
    
    $newStartAt = Carbon::parse($agenda->start_at)->addSeconds($deltaSeconds);
    $newEndAt = Carbon::parse($agenda->end_at)->addSeconds($deltaSeconds);
    
    $agenda->update([
        'start_at' => $newStartAt,
        'end_at' => $newEndAt,
        'is_all_day' => $event['allDay'] ?? $agenda->is_all_day,
    ]);
    
    Notification::make()
        ->title('Agenda diperbarui')
        ->success()
        ->send();
    
    return true; // ❌ Posisi baru ditampilkan, tapi kalau refresh, kembali ke posisi lama
}
```

#### **AFTER (Benar):**
```php
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
    $deltaSeconds = $oldStart->diffInSeconds($newStart, false);
    
    $newStartAt = Carbon::parse($agenda->start_at)->addSeconds($deltaSeconds);
    $newEndAt = Carbon::parse($agenda->end_at)->addSeconds($deltaSeconds);
    
    $agenda->update([
        'start_at' => $newStartAt,
        'end_at' => $newEndAt,
        'is_all_day' => $event['allDay'] ?? $agenda->is_all_day,
    ]);
    
    Notification::make()
        ->title('Agenda diperbarui')
        ->body("\"{$agenda->title}\" dipindahkan ke " . $newStartAt->translatedFormat('d M Y') . '.')
        ->success()
        ->send();
    
    // ⭐ TAMBAHAN: Dispatch event untuk refresh UI
    $this->dispatch('filament-fullcalendar--refresh');
    
    return true; // ✅ UI akan refresh dan menampilkan posisi baru dengan benar
}
```

#### **Untuk onEventResize juga:**
```php
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
    
    if ($newEnd->lessThanOrEqualTo($newStart)) {
        Notification::make()
            ->title('Gagal memperbarui')
            ->body('Waktu selesai tidak boleh sebelum waktu mulai.')
            ->danger()
            ->send();
        
        return false;
    }
    
    $agenda->update([
        'start_at' => $newStart,
        'end_at' => $newEnd,
    ]);
    
    Notification::make()
        ->title('Durasi agenda diperbarui')
        ->success()
        ->send();
    
    // ⭐ Dispatch event untuk refresh UI
    $this->dispatch('filament-fullcalendar--refresh');
    
    return true;
}
```

---

## Flow Diagram

```
DRAG-DROP EVENT
    ↓
onEventDrop() di PHP
    ↓
Update Database ✓
    ↓
$this->dispatch('filament-fullcalendar--refresh')
    ↓
JavaScript listener mendengarkan event
    ↓
calendar.refetchEvents()
    ↓
Panggil fetchEvents() di PHP
    ↓
Ambil data terbaru dari Database
    ↓
Return array event terbaru
    ↓
FullCalendar render event di posisi baru ✓
```

---

## Sumber Kode Resmi

### Dari GitHub Saade/FilamentFullCalendar:

**File:** `src/Widgets/Concerns/InteractsWithEvents.php` (line 102-107)
```php
public function refreshRecords(): void
{
    $this->dispatch('filament-fullcalendar--refresh');
}
```

**File:** `resources/js/components/filament-fullcalendar.js` (line 143-154)
```js
window.addEventListener('filament-fullcalendar--refresh', () =>
    this.calendar.refetchEvents()
)
```

**File:** `src/Actions/EditAction.php` (line 26)
```php
$this->after(
    fn (FullCalendarWidget $livewire) => $livewire->refreshRecords()
);
```

Ini membuktikan bahwa setiap action (Create, Edit, Delete) memanggil `refreshRecords()` untuk sync UI dengan database.

---

## Testing

Untuk memastikan solusi berfungsi:

1. ✅ Drag event ke posisi baru
2. ✅ Database terupdate → check database
3. ✅ Notification muncul
4. ✅ Event tetap di posisi baru (UI sync dengan database)
5. ✅ Kalau refresh halaman, event masih di posisi baru

---

## Catatan Penting

- Method `onEventDrop()` dan `onEventResize()` hanya perlu return `true` atau `false` untuk revert/keep
- Data event yang di-update **tidak perlu di-return** dari method, karena akan di-fetch via `fetchEvents()`
- Dispatch event sudah diimplementasikan di trait `InteractsWithEvents`, jadi Anda hanya perlu memanggilnya setelah database update
- Parent class sudah mempunyai listener untuk event ini, jadi tidak perlu setup tambahan

---

## Kesimpulan

✅ **Solusi:** Tambahkan satu baris di `onEventDrop()` dan `onEventResize()`:
```php
$this->dispatch('filament-fullcalendar--refresh');
```

Selesai! Event akan tetap di posisi baru dan UI selalu sync dengan database.
