<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("💰 Pembayaran Diterima: Order #{$this->order->order_number}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Pembayaran untuk order **#{$this->order->order_number}** telah diterima!")
            ->line("**Detail Pembayaran:**")
            ->line("• Client: {$this->order->client->name}")
            ->line("• Total Order: Rp " . number_format($this->order->total_amount, 0, ',', '.'))
            ->line("• Dibayar: Rp " . number_format($this->order->paid_amount, 0, ',', '.'))
            ->line("• Sisa: Rp " . number_format($this->order->remaining_amount, 0, ',', '.'))
            ->line("• Status: " . strtoupper($this->order->payment_status))
            ->action('Lihat Order', route('admin.orders.index'))
            ->salutation('Salam, Management System');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'client_name' => $this->order->client->name,
            'paid_amount' => $this->order->paid_amount,
            'remaining_amount' => $this->order->remaining_amount,
