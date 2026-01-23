<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $clientName = $this->project->client ? $this->project->client->name : 'N/A';
        $deadline = $this->project->end_date ? $this->project->end_date->format('d F Y') : 'Belum ditentukan';
        
        return (new MailMessage)
            ->subject("🎯 Project Baru: {$this->project->project_name}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Anda ditugaskan ke project baru: **{$this->project->project_name}**")
            ->line("**Detail Project:**")
            ->line("• Kode: {$this->project->project_code}")
            ->line("• Client: {$clientName}")
            ->line("• Status: " . strtoupper($this->project->status))
            ->line("• Budget: Rp " . number_format($this->project->budget, 0, ',', '.'))
            ->line("• Deadline: " . $deadline)
            ->action('Lihat Project', route('admin.projects.show', $this->project->id))
            ->line('Silakan koordinasi dengan tim untuk memulai project ini.')
            ->salutation('Salam, Management System');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'project_id' => $this->project->id,
            'project_name' => $this->project->project_name,
            'project_code' => $this->project->project_code,
            'client_name' => $this->project->client ? $this->project->client->name : 'N/A',
            'deadline' => $this->project->end_date,
        ];
    }
}
