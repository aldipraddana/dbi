<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $name nama status aset
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetStatuses newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetStatuses newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetStatuses query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetStatuses whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetStatuses whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetStatuses whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetStatuses whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAssetStatuses {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $parent_id
 * @property string $asset_number
 * @property string|null $location_code 01..18, diisi ketika master
 * @property string|null $location_detail diisi ketika master
 * @property string|null $location_rt diisi ketika master
 * @property string|null $location_rw diisi ketika master
 * @property string|null $location_padukuhan diisi ketika master
 * @property int|null $size luas (m^2)
 * @property string|null $lot persil (1-9999), diisi ketika master
 * @property string|null $class
 * @property int|null $usage_type_id penggunaan aset (hunian, usaha, dll)
 * @property string|null $status_id status aset (pertanian, perikanan, dll), diisi ketika master
 * @property bool $is_rentalable
 * @property int $rental_price
 * @property string|null $description
 * @property int $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Assets> $fractionAsset
 * @property-read int|null $fraction_asset_count
 * @property-read Assets|null $parent
 * @property-read int $remaining_size
 * @property-read \App\Models\AssetStatuses|null $status
 * @property-read string|null $status_name
 * @property-read \App\Models\User|null $updatedBy
 * @property-read \App\Models\UsageTypes|null $usageType
 * @method static \Database\Factories\AssetFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets rentalable()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets whereAssetNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets whereClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets whereIsRentalable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets whereLocationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets whereLocationDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets whereLocationPadukuhan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets whereLocationRt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets whereLocationRw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets whereLot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets whereRentalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets whereUsageTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assets withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAssets {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name nama jenis pembayaran
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTypes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTypes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTypes query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTypes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTypes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTypes whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTypes whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPaymentTypes {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name Nama penyewa
 * @property string|null $resident_card_number Nomor KTP penyewa
 * @property string|null $family_card_number Nomor KK penyewa
 * @property string|null $tax_id_number Nomor NPWP penyewa
 * @property string|null $phone_number Nomor telepon penyewa, awali dengan +62
 * @property string|null $address Alamat penyewa
 * @property int $created_by ID pengguna yang membuat data penyewa
 * @property int|null $updated_by ID pengguna yang memperbarui data penyewa
 * @property int|null $deleted_by ID pengguna yang menghapus data penyewa
 * @property \Illuminate\Support\Carbon|null $deleted_at Tanggal dan waktu penghapusan data penyewa
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $createdBy
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Database\Factories\TenantFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenants newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenants newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenants onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenants query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenants whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenants whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenants whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenants whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenants whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenants whereFamilyCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenants whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenants whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenants wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenants whereResidentCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenants whereTaxIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenants whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenants whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenants withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenants withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTenants {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $bill_number nomor tagihan
 * @property int $transaction_id referensi ke transaksi
 * @property string $transaction_number nomor transaksi, copy dari tabel transactions
 * @property int $amount jumlah tagihan (Rp.)
 * @property int $arrears_amount jumlah tunggakan (Rp.)
 * @property int $penalty_amount jumlah denda (Rp.)
 * @property int $payment_period_range jangka waktu pembayaran dalam bulan
 * @property string $payment_period_start tanggal mulai periode pembayaran
 * @property string $payment_period_end tanggal akhir periode pembayaran
 * @property string $status status tagihan : Lunas, Belum Lunas
 * @property bool $is_payable
 * @property int $created_by pembuat tagihan
 * @property int|null $updated_by pembaru tagihan
 * @property int|null $deleted_by penghapus tagihan
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $creator
 * @property-read \App\Models\User|null $deleter
 * @property-read string|null $payment_period
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TransactionPayments> $payments
 * @property-read int|null $payments_count
 * @property-read int $total_amount
 * @property-read \App\Models\Transactions $transaction
 * @property-read \App\Models\User|null $updater
 * @method static \Database\Factories\TransactionBillFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionBills newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionBills newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionBills onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionBills query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionBills whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionBills whereArrearsAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionBills whereBillNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionBills whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionBills whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionBills whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionBills whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionBills whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionBills whereIsPayable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionBills wherePaymentPeriodEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionBills wherePaymentPeriodRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionBills wherePaymentPeriodStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionBills wherePenaltyAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionBills whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionBills whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionBills whereTransactionNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionBills whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionBills whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionBills withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionBills withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTransactionBills {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $transaction_id
 * @property int $tenant_id
 * @property string $tenant_name
 * @property string|null $tenant_resident_card_number
 * @property string|null $tenant_family_card_number
 * @property string|null $tenant_tax_id_number
 * @property string|null $tenant_phone_number
 * @property string|null $tenant_address
 * @property int $asset_id
 * @property string $asset_number
 * @property string|null $asset_location_code
 * @property string|null $asset_location_detail
 * @property string|null $asset_location_rt
 * @property string|null $asset_location_rw
 * @property string|null $asset_location_padukuhan
 * @property int|null $asset_size
 * @property int|null $asset_lot
 * @property string $asset_class
 * @property int|null $asset_usage_type_id
 * @property int|null $asset_status_id
 * @property int $asset_rental_price
 * @property string|null $asset_description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AssetStatuses|null $assetStatus
 * @property-read \App\Models\UsageTypes|null $assetUsageType
 * @property-read \App\Models\Transactions $transaction
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereAssetClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereAssetDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereAssetLocationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereAssetLocationDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereAssetLocationPadukuhan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereAssetLocationRt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereAssetLocationRw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereAssetLot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereAssetNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereAssetRentalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereAssetSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereAssetStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereAssetUsageTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereTenantAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereTenantFamilyCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereTenantName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereTenantPhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereTenantResidentCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereTenantTaxIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTransactionLog {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $transaction_id referensi ke transaksi
 * @property int|null $transaction_bill_id referensi ke tagihan
 * @property int $payment_type_id
 * @property string $revenue_type jenis pendapatan
 * @property int $nominal jumlah pembayaran (Rp.)
 * @property int $payment_period_range jangka waktu pembayaran dalam bulan
 * @property string|null $payment_period_start tanggal mulai periode pembayaran
 * @property string|null $payment_period_end tanggal akhir periode pembayaran
 * @property string $payer pembayar, bisa nama atau nomor identitas
 * @property int $created_by pembuat pembayaran
 * @property int|null $updated_by pembaru pembayaran
 * @property int|null $deleted_by penghapus pembayaran
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string|null $payment_period
 * @property-read \App\Models\PaymentTypes $paymentType
 * @property-read \App\Models\TransactionBills|null $transactionBills
 * @property-read \App\Models\Transactions $transactions
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPayments newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPayments newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPayments query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPayments whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPayments whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPayments whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPayments whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPayments whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPayments whereNominal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPayments wherePayer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPayments wherePaymentPeriodEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPayments wherePaymentPeriodRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPayments wherePaymentPeriodStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPayments wherePaymentTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPayments whereRevenueType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPayments whereTransactionBillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPayments whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPayments whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPayments whereUpdatedBy($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTransactionPayments {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $transaction_number komponen dari no. dhks, yang ditampilkan
 * @property int $tenant_id dari frontend, panggil by nama/no. ktp
 * @property int $asset_id data asset tidak boleh diedit ketika sudah digunakan di transaksi
 * @property int $rental_period lama sewa dalam tahun
 * @property string $rental_started_at
 * @property int $rental_price copy dari table asset, dari user read-only
 * @property string|null $rental_finished_at
 * @property string|null $rental_finished_description
 * @property string $payment_period monthly/anually
 * @property string $payment_status paid off/not yet paid off
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int $arrears_amount
 * @property-read string $asset_class
 * @property-read string $asset_description
 * @property-read string $asset_location_code
 * @property-read string $asset_location_detail
 * @property-read string $asset_location_padukuhan
 * @property-read string $asset_location_rt
 * @property-read string $asset_location_rw
 * @property-read string $asset_lot
 * @property-read string $asset_number
 * @property-read string $asset_rental_price
 * @property-read string $asset_size
 * @property-read string $asset_status_id
 * @property-read string $asset_usage_type_id
 * @property-read \App\Models\Assets $assets
 * @property-read \Illuminate\Support\Carbon $bill_period_start
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TransactionBills> $bills
 * @property-read int|null $bills_count
 * @property-read \App\Models\User $creator
 * @property-read \App\Models\User|null $deleter
 * @property-read bool $is_final_status
 * @property-read \App\Models\TransactionLog|null $log
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TransactionPayments> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\Tenants $tenant
 * @property-read string $tenant_address
 * @property-read string $tenant_family_card_number
 * @property-read string $tenant_name
 * @property-read string $tenant_phone_number
 * @property-read string $tenant_resident_card_number
 * @property-read string $tenant_tax_id_number
 * @property-read int $total_rental_price
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TransactionBills> $transactionBills
 * @property-read int|null $transaction_bills_count
 * @property-read \App\Models\User|null $updater
 * @method static \Database\Factories\TransactionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions wherePaymentPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereRentalFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereRentalFinishedDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereRentalPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereRentalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereRentalStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereTransactionNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTransactions {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $code kode penggunaan aset
 * @property string $name nama penggunaan aset
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageTypes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageTypes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageTypes query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageTypes whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageTypes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageTypes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageTypes whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UsageTypes whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUsageTypes {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $employee_card_number
 * @property string|null $resident_card_number
 * @property string|null $phone_number
 * @property string|null $username
 * @property string $password
 * @property int|null $position_id
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read User|null $creator
 * @property-read User|null $deleter
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserMenu> $menus
 * @property-read int|null $menus_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\UserPosition|null $position
 * @property-read User|null $updater
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmployeeCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereResidentCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $Users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMenu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMenu newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMenu query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMenu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMenu whereName($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUserMenu {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPosition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPosition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPosition query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPosition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPosition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPosition whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPosition whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUserPosition {}
}

