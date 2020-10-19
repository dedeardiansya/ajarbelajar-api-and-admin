<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MinitutorPostPublishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $post = null, $type = '';

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($post, $type)
    {
        $this->post = $post;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $type = ($this->type) == 'article' ? 'Artikel' : 'Playlist';
        return (new MailMessage)
                    ->subject('[AjarBelajar] Yeay, kontenmu terbit!')
                    ->line("Halo, MiniTutor yang baik!")
                    ->line("Terima kasih sudah memberikan kontribusi yang konkret terhadap pendidikan gratis Indonesia! Konten kamu yang berjudul '{$this->post->title}' sudah terbit lho!")
                    ->action('Lihat ' . $type, config('frontend.url') . '/' . $this->type . 's/' . $this->post->slug);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $type = ($this->type) == 'article' ? 'Artikel' : 'Playlist';
        return [
            'post_id' => $this->post->id,
            'type' => $this->type,
            'message' => $type . " anda yang berjudul '" . $this->post->title . "' telah dipublikasikan."
        ];
    }
}
