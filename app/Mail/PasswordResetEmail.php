<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class PasswordResetEmail extends Mailable
{
  use Queueable, SerializesModels;

  /**
   * Create a new message instance.
   */
  protected $token;

  public function __construct(string $token)
  {
    $this->token = $token;
  }

  /**
   * Get the message envelope.
   */
  public function envelope(): Envelope
  {
    return new Envelope(
      subject: 'Password Reset Email',
      replyTo: [
        new Address('no-reply@stareducationplus.org.uk', 'Password Reset | Star Education Plus'),
      ],
    );
  }

  /**
   * Get the message content definition.
   */
  public function content(): Content
  {
    return new Content(
      view: 'index',
      with: [
        'token' => $this->token,
      ],
    );
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
