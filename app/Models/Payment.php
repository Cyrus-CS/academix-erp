<?php
// app/Models/Payment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'student_id', 'fee_type_id', 'created_by',
        'amount_paid', 'receipt_number', 'status',
        'paid_at', 'note',
    ];

    protected $casts = [
        'paid_at' => 'date',
        'amount_paid' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            if (empty($payment->receipt_number)) {
                $payment->receipt_number = static::generateReceipt();
            }
        });
    }

    public static function generateReceipt(): string
    {
        $prefix = 'REC-' . now()->format('Ymd') . '-';
        $last = static::whereDate('created_at', today())
                       ->max('receipt_number');
        $number = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public static function paymentsMonth(){
        return static::whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->sum('amount_paid');
    }

    public static function paymentToday()
    {
        return static::whereDate('paid_at', today())->sum('amount_paid');
    }

     /**
     * Vérifier si le paiement est complet
     * @return bool
     */
    public function isPaid() : bool{
        return $this->status === 'paid';
    }

    /**
     * Vérifier si le paiement est partial
     * @return bool
     */
    public function isPartial() : bool{
        return $this->status === 'partial';
    }

    public function isPending() : bool{
        return $this->status === 'pending';
    }

    // ----------------------- RELATIONS -------------------
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function feeType(): BelongsTo
    {
        return $this->belongsTo(FeeType::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    
    // ---------------------------- SCOPES -------------------------------
    public function scopePaid(Builder $query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending(Builder $query)
    {
        return $query->where('status', 'pending');
    }
}