{{-- Blade view for LaporanKeuangan Filament page --}}
<x-filament-panels::page>
    {{ $this->form }}
</x-filament-panels::page>

<script>
    document.addEventListener('download-file', (event) => {
        const url = event.detail.url;
        const link = document.createElement('a');
        link.href = url;
        link.download = '';
        link.target = '_blank';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    document.addEventListener('open-print-view', (event) => {
        const url = event.detail.url;
        window.open(url, '_blank');
    });
</script>
