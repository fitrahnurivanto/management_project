<?php

namespace App\Presenters;

use App\Models\Order;
use Illuminate\Support\Str;

class OrderPresenter
{
    public function __construct(private Order $order) {}

    /**
     * Get client name
     */
    public function getClientName(): string
    {
        return $this->order->client->name 
            ?: ($this->order->client->user->name ?? 'Client');
    }

    /**
     * Get client phone in international format (62xxx)
     */
    public function getClientPhone(): string
    {
        $phone = $this->order->client->phone 
            ?: ($this->order->client->contact_phone ?? '');
        
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        
        // Convert 08xxx to 628xxx
        if (substr($cleanPhone, 0, 1) === '0') {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }
        
        return $cleanPhone;
    }

    /**
     * Get WhatsApp message based on order type
     */
    public function getWhatsAppMessage(): string
    {
        $clientName = $this->getClientName();
        $message = "Halo " . urlencode($clientName);
        
        if ($this->order->order_type === 'registration') {
            $message .= match($this->order->registration_type) {
                'magang' => ", terima kasih sudah mendaftar Magang",
                'sertifikasi' => ", terima kasih sudah mendaftar Sertifikasi BNSP",
                default => ", mengenai pendaftaran"
            };
        } else {
            $message .= ", mengenai order";
        }
        
        return $message . " (No: {$this->order->order_number}). Tim kami akan menghubungi Anda untuk informasi lebih lanjut.";
    }

    /**
     * Get full WhatsApp link
     */
    public function getWhatsAppLink(): string
    {
        $phone = $this->getClientPhone();
        $message = $this->getWhatsAppMessage();
        
        return "https://wa.me/{$phone}?text={$message}";
    }

    /**
     * Get client email
     */
    public function getClientEmail(): string
    {
        return $this->order->client->email 
            ?: ($this->order->client->user->email ?? '-');
    }

    /**
     * Get client phone display
     */
    public function getClientPhoneDisplay(): string
    {
        return $this->order->client->phone 
            ?: ($this->order->client->contact_phone ?? '-');
    }

    /**
     * Get company name if exists
     */
    public function getCompanyName(): ?string
    {
        return $this->order->client->company_name;
    }

    /**
     * Check if order is registration type
     */
    public function isRegistration(): bool
    {
        return $this->order->order_type === 'registration';
    }

    /**
     * Check if order needs review
     */
    public function needsReview(): bool
    {
        return $this->order->payment_status === 'pending_review';
    }

    /**
     * Check if order is paid
     */
    public function isPaid(): bool
    {
        return $this->order->payment_status === 'paid';
    }

    /**
     * Check if order is installment with remaining amount
     */
    public function hasRemainingInstallment(): bool
    {
        return $this->order->payment_type === 'installment' 
            && $this->order->remaining_amount > 0;
    }

    /**
     * Get payment status badge class and text
     */
    public function getPaymentStatusBadge(): array
    {
        return match($this->order->payment_status) {
            'pending_review' => [
                'class' => 'bg-yellow-100 text-yellow-800',
                'icon' => 'fa-clock',
                'text' => 'Perlu Review'
            ],
            'paid' => [
                'class' => 'bg-green-100 text-green-800',
                'icon' => 'fa-check-circle',
                'text' => 'Terkonfirmasi'
            ],
            'refunded' => [
                'class' => 'bg-purple-100 text-purple-800',
                'icon' => 'fa-undo',
                'text' => 'Refund'
            ],
            'rejected' => [
                'class' => 'bg-red-100 text-red-800',
                'icon' => 'fa-times-circle',
                'text' => 'Ditolak'
            ],
            default => [
                'class' => 'bg-gray-100 text-gray-800',
                'icon' => 'fa-circle',
                'text' => ucfirst($this->order->payment_status)
            ]
        };
    }

    /**
     * Get payment type badge
     */
    public function getPaymentTypeBadge(): array
    {
        if ($this->order->payment_type === 'installment') {
            return [
                'class' => 'bg-blue-100 text-blue-800',
                'icon' => 'fa-wallet',
                'text' => 'DP'
            ];
        }
        
        return [
            'class' => 'bg-green-100 text-green-800',
            'icon' => 'fa-money-bill-wave',
            'text' => 'Lunas'
        ];
    }

    /**
     * Get registration type badge
     */
    public function getRegistrationBadge(): ?array
    {
        if (!$this->isRegistration()) {
            return null;
        }

        return match($this->order->registration_type) {
            'magang' => [
                'class' => 'bg-indigo-100 text-indigo-800',
                'icon' => 'fa-user-graduate',
                'text' => 'Pendaftaran Magang'
            ],
            'sertifikasi' => [
                'class' => 'bg-purple-100 text-purple-800',
                'icon' => 'fa-certificate',
                'text' => 'Pendaftaran Sertifikasi BNSP'
            ],
            default => null
        };
    }

    /**
     * Format currency
     */
    public function formatCurrency(int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Get order date formatted
     */
    public function getOrderDate(): string
    {
        $date = $this->order->order_date 
            ? \Carbon\Carbon::parse($this->order->order_date)
            : $this->order->created_at;
        
        return $date->format('d M Y H:i');
    }

    /**
     * Get truncated notes
     */
    public function getTruncatedNotes(int $limit = 50): ?string
    {
        if (!$this->order->payment_notes) {
            return null;
        }

        if ($this->order->payment_notes === $this->order->notes) {
            return null;
        }

        return Str::limit($this->order->payment_notes, $limit);
    }
}
