<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $project;
    public $oldStatus;
    public $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Project $project, $oldStatus, $newStatus)
    {
        $this->project = $project;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
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
        $statusEmoji = [
            'completed' => '✅',
            'in_progress' => '⚙️',
            'on_hold' => '⏸️',
            'cancelled' => '❌',
            'pending' => '⏳'
        ];

        $emoji = $statusEmoji[$this->newStatus] ?? '🔄';
        $clientName = $this->project->client ? $this->project->client->name : 'N/A';

        return (new MailMessage)
            ->subject("{$emoji} Status Project Berubah: {$this->project->project_name}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Status project **{$this->project->project_name}** telah berubah.")
            ->line("**Perubahan Status:**")
            ->line("• Dari: " . strtoupper($this->oldStatus))
            ->line("• Ke: " . strtoupper($this->newStatus))
            ->line("**Detail Project:**")
            ->line("• Kode: {$this->project->project_code}")
            ->line("• Client: {$clientName}")
            ->action('Lihat Project', route('admin.projects.show', $this->project->id))
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
            'project_id' => $this->project->id,
            'project_name' => $this->project->project_name,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
