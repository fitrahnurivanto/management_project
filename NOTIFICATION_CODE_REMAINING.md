# NOTIFICATION SYSTEM - REMAINING CODE

## File yang Perlu Dilengkapi

### 1. PaymentReceivedNotification.php
Location: `app/Notifications/PaymentReceivedNotification.php`

```php
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

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

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

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'client_name' => $this->order->client->name,
            'paid_amount' => $this->order->paid_amount,
            'remaining_amount' => $this->order->remaining_amount,
        ];
    }
}
```

### 2. ProjectStatusChangedNotification.php
Location: `app/Notifications/ProjectStatusChangedNotification.php`

```php
<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\\Contracts\\Queue\\ShouldQueue;
use Illuminate\\Notifications\\Messages\\MailMessage;
use Illuminate\\Notifications\\Notification;

class ProjectStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $project;
    public $oldStatus;
    public $newStatus;

    public function __construct(Project $project, $oldStatus, $newStatus)
    {
        $this->project = $project;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

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

        return (new MailMessage)
            ->subject("{$emoji} Status Project Berubah: {$this->project->project_name}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Status project **{$this->project->project_name}** telah berubah.")
            ->line("**Perubahan Status:**")
            ->line("• Dari: " . strtoupper($this->oldStatus))
            ->line("• Ke: " . strtoupper($this->newStatus))
            ->line("**Detail Project:**")
            ->line("• Kode: {$this->project->project_code}")
            ->line("• Client: {$this->project->client->name}")
            ->action('Lihat Project', route('admin.projects.show', $this->project->id))
            ->salutation('Salam, Management System');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'project_id' => $this->project->id,
            'project_name' => $this->project->project_name,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ];
    }
}
```

### 3. CheckProjectDeadlines Command
Location: `app/Console/Commands/CheckProjectDeadlines.php`

```php
<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectDeadlineNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckProjectDeadlines extends Command
{
    protected $signature = 'notifications:check-deadlines';
    protected $description = 'Check project deadlines and send notifications (H-3 and H-1)';

    public function handle()
    {
        $this->info('Checking project deadlines...');

        $now = Carbon::now();
        $deadlineDays = [3, 1]; // H-3 and H-1

        foreach ($deadlineDays as $days) {
            $targetDate = $now->copy()->addDays($days)->format('Y-m-d');
            
            $projects = Project::whereDate('end_date', $targetDate)
                ->whereIn('status', ['in_progress', 'on_hold'])
                ->with(['client', 'order.items.service'])
                ->get();

            foreach ($projects as $project) {
                $this->info("Found project: {$project->project_name} (H-{$days})");

                // Get PIC user
                $picUser = User::where('name', $project->pic_internal)->first();
                
                if ($picUser) {
                    $picUser->notify(new ProjectDeadlineNotification($project, $days));
                    $this->info(" - Notified PIC: {$picUser->name}");
                }

                // Notify all admins
                $admins = User::whereIn('role', ['super_admin', 'agency_admin', 'academy_admin'])->get();
                foreach ($admins as $admin) {
                    $admin->notify(new ProjectDeadlineNotification($project, $days));
                }
                $this->info(" - Notified {$admins->count()} admins");
            }
        }

        $this->info('Deadline check completed!');
        return 0;
    }
}
```

### 4. Update Kernel.php for Scheduler
Location: `app/Console/Kernel.php`

Add to `schedule()` method:

```php
protected function schedule(Schedule $schedule): void
{
    // Check project deadlines every day at 8 AM
    $schedule->command('notifications:check-deadlines')
             ->dailyAt('08:00')
             ->timezone('Asia/Jakarta');
}
```

### 5. Update User Model
Location: `app/Models/User.php`

Add to class:

```php
use Illuminate\Notifications\Notifiable;

// ... dalam class User

/**
 * Get user notifications
 */
public function notifications()
{
    return $this->hasMany(\App\Models\Notification::class);
}

/**
 * Get notification settings
 */
public function notificationSettings()
{
    return $this->hasMany(\App\Models\NotificationSetting::class);
}

/**
 * Get unread notifications count
 */
public function unreadNotificationsCount()
{
    return $this->notifications()->unread()->count();
}
```

### 6. Create NotificationService Helper
Location: `app/Services/NotificationService.php`

```php
<?php

namespace App\Services;

use App\Models\Notification as NotificationModel;
use App\Models\NotificationSetting;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Send notification to user(s)
     */
    public static function send($users, $notification, $type)
    {
        if (!is_array($users) && !$users instanceof \Illuminate\Support\Collection) {
            $users = [$users];
        }

        foreach ($users as $user) {
            // Check if user has email enabled for this notification type
            $emailEnabled = NotificationSetting::isEnabled($user->id, $type, 'email');
            $inAppEnabled = NotificationSetting::isEnabled($user->id, $type, 'in_app');

            if ($emailEnabled || $inAppEnabled) {
                $user->notify($notification);
            }
        }
    }

    /**
     * Create in-app notification
     */
    public static function create($userId, $type, $title, $message, $data = [], $actionUrl = null)
    {
        return NotificationModel::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'action_url' => $actionUrl,
        ]);
    }

    /**
     * Mark notification as read
     */
    public static function markAsRead($notificationId)
    {
        $notification = NotificationModel::find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    /**
     * Mark all user notifications as read
     */
    public static function markAllAsRead($userId)
    {
        NotificationModel::where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Get user's unread notifications
     */
    public static function getUnread($userId, $limit = 10)
    {
        return NotificationModel::where('user_id', $userId)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
```

### 7. .env Configuration (PLACEHOLDER)

Add to `.env`:

```env
# Email Configuration (Setup later dengan Gmail App Password)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password_16_digit
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# Queue Configuration (For notification jobs)
QUEUE_CONNECTION=database
```

### 8. Test Email Command (Optional)
Create: `app/Console/Commands/TestEmail.php`

```bash
php artisan make:command TestEmail
```

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    protected $signature = 'test:email {email}';
    protected $description = 'Test email configuration';

    public function handle()
    {
        $email = $this->argument('email');
        
        try {
            Mail::raw('Test email dari Laravel Management System. Jika email ini masuk, konfigurasi SMTP sudah benar!', function($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email - Laravel Management System');
            });
            
            $this->info("Email berhasil dikirim ke {$email}!");
            $this->info("Cek inbox (atau spam folder).");
        } catch (\Exception $e) {
            $this->error("Gagal kirim email: " . $e->getMessage());
        }
    }
}
```

---

## NEXT STEPS:

1. ✅ Copy kode di atas ke file yang sesuai
2. ⏳ Setup Gmail App Password (ikuti NOTIFICATION_SETUP.md)
3. ⏳ Update .env dengan email credentials
4. ⏳ Test email: `php artisan test:email youremail@gmail.com`
5. ⏳ Test deadline checker: `php artisan notifications:check-deadlines`
6. ⏳ Setup Windows Task Scheduler untuk production

Sistem notifikasi sudah 90% selesai! Tinggal setup email dan testing.
