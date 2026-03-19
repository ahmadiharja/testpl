@component('mail::message')

# {{ $productName }} — Task Completed: {{ $history->name }} ({{ $resultText }}) — Report Attached

Dear {{ $recipientName }},

The scheduled task "**{{ $history->name }}**" Task ({{ $resultText }}) has been completed for the display listed below. Please find the detailed PDF report attached.

- **Task name** — {{ $history->name }}
- **Result** — {{ $resultText }}
- **Display** — {{ $displayInfo }}
- **Facility** — {{ $facilityName }}
- **Performed** — {{ $history->getTimeDisplay() }}

<br>
Best regards,<br>
**QUBYX Remote QA Team**<br>
[support@qubyx.com](mailto:support@qubyx.com)
@endcomponent
