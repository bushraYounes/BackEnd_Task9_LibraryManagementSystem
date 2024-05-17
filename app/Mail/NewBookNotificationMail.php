<?php

namespace App\Mail;

use App\Models\Book;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewBookNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $book;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Book $book)
    {
        $this->user = $user;
        $this->book = $book;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Book Notification Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    // public function content()
    // {
    //     return $this->subject('New Book Notification')
    //                 ->view('emails.new_book_email_notification')
    //                 ->with([
    //                     'user' => $this->user,
    //                     'book' => $this->book,
    //                 ]);

    //     // return new Content(
    //     //     view: 'view.new_book_email_notification',
    //     // );
    // }

    public function build()
    {
        return $this->subject('New Book Notification')
                    ->view('emails.new_book_email_notification')
                    ->with([
                        'user' => $this->user,
                        'book' => $this->book,
                    ]);
    }


    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
