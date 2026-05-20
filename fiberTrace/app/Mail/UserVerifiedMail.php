<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Sent when an admin approves a user's GSTIN.
 * Queued via database queue — won't block the request.
 */
class UserVerifiedMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build(): self
    {
        return $this
            ->subject('🎉 Your FibreTrace Account is Verified!')
            ->view('emails.user-verified')
            ->with([
                'userName'    => $this->user->name,
                'companyName' => $this->user->company_name,
                'loginUrl'    => route('login'),
            ]);
    }
}
