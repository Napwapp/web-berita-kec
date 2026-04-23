# 📚 Source Code References - FilamentFullCalendar

## Sumber Resmi dari GitHub: saade/filament-fullcalendar

### 1. Event Dispatch Listener (JavaScript)

**File:** `resources/js/components/filament-fullcalendar.js` (lines 143-154)

```javascript
window.addEventListener('filament-fullcalendar--refresh', () =>
    this.calendar.refetchEvents(),
)

window.addEventListener('filament-fullcalendar--today', () =>
    this.calendar.today(),
)

window.addEventListener('filament-fullcalendar--view', (event) =>
    this.calendar.changeView(event.detail.view),
)

window.addEventListener('filament-fullcalendar--goto', (event) =>
    this.calendar.gotoDate(event.detail.date),
)
```

**Kesimpulan:** Event listener `filament-fullcalendar--refresh` sudah tersedia dan akan memanggil `refetchEvents()`.

---

### 2. RefreshRecords Method

**File:** `src/Widgets/Concerns/InteractsWithEvents.php` (lines 102-107)

```php
public function refreshRecords(): void
{
    $this->dispatch('filament-fullcalendar--refresh');
}
```

**Kesimpulan:** Method ini sudah ada dan cukup dipanggil setelah update database.

---

### 3. Action Classes yang Menggunakan refreshRecords()

#### EditAction.php
**File:** `src/Actions/EditAction.php`

```php
class EditAction extends BaseEditAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->model(
            fn (FullCalendarWidget $livewire) => $livewire->getModel()
        );

        $this->record(
            fn (FullCalendarWidget $livewire) => $livewire->getRecord()
        );

        $this->schema(
            fn (FullCalendarWidget $livewire) => $livewire->getFormSchema()
        );

        $this->after(
            fn (FullCalendarWidget $livewire) => $livewire->refreshRecords()  // ← Here!
        );

        $this->cancelParentActions();
    }
}
```

#### CreateAction.php
**File:** `src/Actions/CreateAction.php`

```php
class CreateAction extends BaseCreateAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->model(
            fn (FullCalendarWidget $livewire) => $livewire->getModel()
        );

        $this->schema(
            fn (FullCalendarWidget $livewire) => $livewire->getFormSchema()
        );

        $this->after(
            fn (FullCalendarWidget $livewire) => $livewire->refreshRecords()  // ← Here!
        );

        $this->cancelParentActions();
    }
}
```

#### DeleteAction.php
**File:** `src/Actions/DeleteAction.php`

```php
class DeleteAction extends BaseDeleteAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->model(
            fn (FullCalendarWidget $livewire) => $livewire->getModel()
        );

        $this->record(
            fn (FullCalendarWidget $livewire) => $livewire->getRecord()
        );

        $this->after(
            function (FullCalendarWidget $livewire) {
                $livewire->record = null;
                $livewire->refreshRecords();  // ← Here!
            }
        );

        $this->cancelParentActions();
    }
}
```

**Kesimpulan:** Semua action class memanggil `refreshRecords()` setelah aksi selesai untuk sync UI dengan database.

---

### 4. Event Drop Handler di JavaScript

**File:** `resources/js/components/filament-fullcalendar.js` (lines 48-78)

```javascript
eventDrop: async ({
    event,
    oldEvent,
    relatedEvents,
    delta,
    oldResource,
    newResource,
    revert,
}) => {
    const shouldRevert = await this.$wire.onEventDrop(
        event,
        oldEvent,
        relatedEvents,
        delta,
        oldResource,
        newResource,
    )

    if (typeof shouldRevert === 'boolean' && shouldRevert) {
        revert()  // Revert drop jika return true
    }
},
```

**Kesimpulan:** Ketika drag-drop selesai, JavaScript memanggil `onEventDrop()` di PHP dan cek return value (boolean).

---

### 5. InteractsWithEvents Trait

**File:** `src/Widgets/Concerns/InteractsWithEvents.php`

```php
<?php

namespace Saade\FilamentFullCalendar\Widgets\Concerns;

use Carbon\Carbon;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

trait InteractsWithEvents
{
    /**
     * Triggered when the user clicks an event.
     */
    public function onEventClick(array $event): void
    {
        if ($this->getModel()) {
            $this->record = $this->resolveRecord($event['id']);
        }

        $this->mountAction('view', [
            'type' => 'click',
            'event' => $event,
        ]);
    }

    /**
     * Triggered when dragging stops and the event has moved to a different day/time.
     * @return bool Whether to revert the drop action.
     */
    public function onEventDrop(array $event, array $oldEvent, array $relatedEvents, array $delta, ?array $oldResource, ?array $newResource): bool
    {
        if ($this->getModel()) {
            $this->record = $this->resolveRecord($event['id']);
        }

        $this->mountAction('edit', [
            'type' => 'drop',
            'event' => $event,
            'oldEvent' => $oldEvent,
            'relatedEvents' => $relatedEvents,
            'delta' => $delta,
            'oldResource' => $oldResource,
            'newResource' => $newResource,
        ]);

        return false;
    }

    /**
     * Triggered when resizing stops and the event has changed in duration.
     * @return bool Whether to revert the resize action.
     */
    public function onEventResize(array $event, array $oldEvent, array $relatedEvents, array $startDelta, array $endDelta): bool
    {
        if ($this->getModel()) {
            $this->record = $this->resolveRecord($event['id']);
        }

        $this->mountAction('edit', [
            'type' => 'resize',
            'event' => $event,
            'oldEvent' => $oldEvent,
            'relatedEvents' => $relatedEvents,
            'startDelta' => $startDelta,
            'endDelta' => $endDelta,
        ]);

        return false;
    }

    // ... more methods

    public function refreshRecords(): void
    {
        $this->dispatch('filament-fullcalendar--refresh');
    }

    protected function calculateTimezoneOffset(string $start, ?string $end, bool $allDay): array
    {
        // ... implementation
    }
}
```

**Kesimpulan:** 
- `onEventDrop()` dan `onEventResize()` bisa di-override untuk custom logic
- Default implementation memanggil `mountAction('edit')` 
- Method `refreshRecords()` cukup dipanggil untuk trigger refresh

---

### 6. FullCalendarWidget Class

**File:** `src/Widgets/FullCalendarWidget.php` (lines 0-68)

```php
<?php

namespace Saade\FilamentFullCalendar\Widgets;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Concerns\InteractsWithHeaderActions;
use Filament\Widgets\Widget;
use Saade\FilamentFullCalendar\Actions;

class FullCalendarWidget extends Widget implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;
    use Concerns\InteractsWithEvents;  // ← Trait yang punya refreshRecords()
    use Concerns\InteractsWithRecords;
    use InteractsWithHeaderActions;
    use InteractsWithFormActions;
    use Concerns\InteractsWithRawJS;
    use Concerns\CanBeConfigured;
    use Concerns\IsBackwardCompatible;

    protected string $view = 'filament-fullcalendar::fullcalendar';

    protected int | string | array $columnSpan = 'full';

    protected function headerActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function modalActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * FullCalendar will call this function whenever it needs new event data.
     * This is triggered when the user clicks prev/next or switches views.
     */
    public function fetchEvents(array $info): array
    {
        return [];
    }

    public function getFormSchema(): array
    {
        return [];
    }
}
```

**Kesimpulan:** `FullCalendarWidget` sudah punya trait `InteractsWithEvents` yang menyediakan semua method dan `refreshRecords()`.

---

## Changelog Reference

**CHANGELOG.md - v1.6.0 (2022-09-25)**
```
- feat: refresh calendar events by @saade in https://github.com/saade/filament-fullcalendar/pull/13
```

**Kesimpulan:** Fitur refresh sudah ada sejak versi 1.6.0 dan terus dikembangkan.

---

## Summary: Mengapa Solusi Ini Bekerja?

1. ✅ **Event listener sudah ada** di JavaScript (line 143)
2. ✅ **Method refreshRecords() sudah ada** di PHP trait (line 107)
3. ✅ **Dispatch method ada di Livewire** default
4. ✅ **Action classes sudah menggunakan pattern ini** (EditAction, CreateAction, DeleteAction)
5. ✅ **Hanya perlu dipanggil di onEventDrop() dan onEventResize()**

---

## Video Proof dari Source

Jika melihat flow di source code:

```
User drag event
    ↓
eventDrop handler (JavaScript) ← Line 48
    ↓
this.$wire.onEventDrop() ← Call method di PHP
    ↓
onEventDrop() di CalendarWidget (custom override)
    ↓
$this->dispatch('filament-fullcalendar--refresh') ← NEW LINE
    ↓
return true
    ↓
JavaScript listener (Line 143)
    ↓
this.calendar.refetchEvents()
    ↓
this.$wire.fetchEvents() ← Call method di PHP
    ↓
fetchEvents() di CalendarWidget
    ↓
Query database dan return array
    ↓
FullCalendar re-render dengan data baru
```

---

## Verification Links

Repository: https://github.com/saade/filament-fullcalendar
Branch: 3.x

File References:
- `src/Widgets/Concerns/InteractsWithEvents.php` ← Main trait
- `resources/js/components/filament-fullcalendar.js` ← JavaScript handler
- `src/Actions/EditAction.php` ← Usage example
- `src/Actions/CreateAction.php` ← Usage example
- `src/Actions/DeleteAction.php` ← Usage example

---

**Kesimpulan:** Semua infrastructure sudah ada dan siap digunakan. Hanya tinggal memanggil `$this->dispatch('filament-fullcalendar--refresh')` setelah update database di `onEventDrop()` dan `onEventResize()`.
