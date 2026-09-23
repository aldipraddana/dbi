<div class="area-transaction-bill-logs-table">
    <p class="text-gray-500">Tidak ada riwayat tagihan untuk transaksi ini.</p>
</div>


<script type="text/javascript">

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelector('.custom-transaction-id-select select')?.addEventListener('change', function () {
            setTimeout(() => {
                const selectedValue = this.value;
                
                fetch(`{{ url('bill-history') }}/${selectedValue}`)
                    .then(response => response.text())
                    .then(data => {
                        document.querySelector('.area-transaction-bill-logs-table').innerHTML = data;
                    })
                    .catch(error => {
                        console.error('Error fetching bill history:', error);
                    });
            }, 2000);
        });
    });

</script>