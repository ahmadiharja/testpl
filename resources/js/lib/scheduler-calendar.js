import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

function normalizeEvent(event) {
    const data = event.extendedProps?.rawData || {};
    const locationBits = [data.facility, data.workgroup, data.workstation, data.display].filter(Boolean);
    const className = Array.isArray(event.classNames) ? event.classNames[0] : event.classNames;

    return {
        id: event.id,
        title: event.title,
        subtitle: locationBits.join(' / ') || 'Remote calibration task',
        dateLabel: event.start
            ? event.start.toLocaleString()
            : '-',
        badgeLabel: data.isqa ? 'QA Task' : 'Calibration Task',
        badgeClass: className === 'event-red'
            ? 'border-rose-200 bg-rose-50 text-rose-700'
            : className === 'event-green'
                ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                : 'border-sky-200 bg-sky-50 text-sky-700',
        locationLabel: locationBits.join(' / ') || '-',
    };
}

export function createSchedulerCalendar({ element, eventsUrl, onEventClick }) {
    if (!element) {
        return null;
    }

    const calendar = new Calendar(element, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay',
        },
        buttonText: {
            today: 'Today',
            month: 'Month',
            week: 'Week',
            day: 'Day',
        },
        height: 'auto',
        aspectRatio: 1.9,
        dayMaxEventRows: 4,
        fixedWeekCount: false,
        navLinks: true,
        eventDisplay: 'block',
        editable: false,
        selectable: false,
        nowIndicator: true,
        eventTimeFormat: {
            hour: 'numeric',
            minute: '2-digit',
            meridiem: 'short',
        },
        events(info, successCallback, failureCallback) {
            const url = new URL(eventsUrl, window.location.origin);
            url.searchParams.set('start', info.startStr);
            url.searchParams.set('end', info.endStr);

            fetch(url.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
                .then((response) => response.json())
                .then((payload) => {
                    const events = (payload || []).map((item) => ({
                        id: item.id,
                        title: item.title,
                        start: item.start,
                        allDay: !!item.allDay,
                        classNames: Array.isArray(item.className)
                            ? item.className
                            : (item.className ? [item.className] : []),
                        extendedProps: {
                            rawData: item.data || {},
                        },
                    }));
                    successCallback(events);
                })
                .catch(failureCallback);
        },
        eventClick(info) {
            info.jsEvent.preventDefault();
            onEventClick?.(normalizeEvent(info.event));
        },
    });

    calendar.render();
    return calendar;
}
