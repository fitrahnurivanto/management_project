<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectDeadlineNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $project;
    public $daysUntilDeadline;

    public function __construct(Project $project, $daysUntilDeadline)
    {
        $this->project = $project;
        $this->daysUntilDeadline = $daysUntilDeadline;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $urgencyLabel = $this->daysUntilDeadline <= 1 ? '⚠️ URGENT' : '⏰ REMINDER';
        $dayText = $this->daysUntilDeadline == 1 ? 'BESOK' : "H-{$this->daysUntilDeadline}";
        
        return (new MailMessage)
            ->subject("{$urgencyLabel}: Deadline Project {$this->project->project_name} - {$dayText}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Project **{$this->project->project_name}** akan mencapai deadline dalam **{$this->daysUntilDeadline} hari**!")
            ->line("**Detail Project:**")
            ->line("• Kode Project: {$this->project->project_code}")
            ->line("• Client: {$this->project->client->name}")
            ->line("• Deadline: " . $this->project->end_date->format('d F Y'))
            ->line("• Status: " . strtoupper($this->project->status))
            ->line("• Budget: Rp " . number_format($this->project->budget, 0, ',', '.'))
            ->action('Lihat Project', route('admin.projects.show', $this->project->id))
            ->line('Segera selesaikan project ini sebelum deadline!')
            ->salutation('Salam, Management System');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'project_id' => $this->project->id,
            'project_name' => $this->project->project_name,
            'project_code' => $this->project->project_code,
            'deadline' => $this->project->end_date,
            'days_until_deadline' => $this->daysUntilDeadline,
            'status' => $this->project->status,
        ];
    }
}
