<script>
    window.open("{{ route('bukti.kas.masuk', ['id' => $id]) }}", "_blank");
    window.location.href = "{{ route('filament.admin.resources.transaction-payments.index') }}";
</script>