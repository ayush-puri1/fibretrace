<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Sent when an admin rejects a user's GSTIN with a reason.
 */
class UserRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public User   $user;
    public string $reason;

    public function __construct(User $user, string $reason)
    {
        $this->user   = $user;
        $this->reason = $reason;
    }

    public function build(): self
    {
        return $this
            ->subject('FibreTrace Registration Update')
            ->view('emails.user-rejected')
            ->with([
                'userName'    => $this->user->name,
                'companyName' => $this->user->company_name,
                'reason'      => $this->reason,
            ]);
    }
}
