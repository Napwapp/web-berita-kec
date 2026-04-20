import "./bootstrap";
import Alpine from "alpinejs";
import "preline";
import { Calendar } from '@fullcalendar/core'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction'

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar')

    if (calendarEl) {
        const calendar = new Calendar(calendarEl, {
            plugins: [
                dayGridPlugin,
                timeGridPlugin,
                listPlugin,
                interactionPlugin
            ],

            initialView: 'dayGridMonth',
            height: 'auto',

            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },

            // Text tombol dalam bahasa Indonesia
            buttonText: {
                today: 'Hari Ini',
                month: 'Bulan',
                week: 'Minggu',
                day: 'Hari',
                list: 'List'
            },    

            events: [
                {
                    title: 'Rapat Koordinasi Kecamatan',
                    start: '2026-04-22T09:00:00',
                    end: '2026-04-22T11:22:00'
                },
                {
                    title: 'Kerja Bakti Lingkungan',
                    start: '2026-04-24',
                    allDay: true
                },
                {
                    title: 'Lomba 17-an',
                    start: '2026-04-26T08:00:00',
                    end: '2026-04-26T15:00:00'
                },
                {
                    title: 'Pelatihan UMKM',
                    start: '2026-04-28T13:00:00'
                }
            ],

            // klik event (nanti bisa ke detail page)
            eventClick: function(info) {
                alert('Event: ' + info.event.title)
            },

            // Event Dateclick
            dateClick: function(info) {
                console.log('Tanggal dipilih:', info.dateStr)
            }
        })
        calendar.render()
    }
})

window.Alpine = Alpine;

Alpine.start();
