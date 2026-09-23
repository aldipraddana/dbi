
/**
 * pembayaran
 */
document.addEventListener('DOMContentLoaded', function () {
    
    if (document.querySelector('.fi-header-heading') != null && document.querySelector('.fi-header-heading').textContent.includes('Edit') && document.querySelector('.custom-transaction-id-select select') != undefined) {
        setTimeout(() => {
            document.querySelector('.custom-transaction-id-select select').dispatchEvent(new Event('change'))
        }, 500);
    }
});


/**
 * Data Tagihan Wifi
 */
document.querySelectorAll('.payment-date-from input').forEach(function(input) {
    input.addEventListener('change', function(event) {
        event.preventDefault();
        window.location.reload();
    });
});

document.querySelectorAll('.payment-date-until input').forEach(function(input) {
    input.addEventListener('change', function(event) {
        event.preventDefault();
        window.location.reload();
    });
});

document.querySelectorAll('.payment-type-filter select').forEach(function(select) {
    select.addEventListener('change', function(event) {
        event.preventDefault();
        setTimeout(() => {
            window.location.reload();
        }, 100);
    });
});
