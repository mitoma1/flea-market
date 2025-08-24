<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Trade;

class TradeCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $trade;

    /**
     * Create a new message instance.
     */
    public function __construct(Trade $trade)
    {
        $this->trade = $trade;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('取引が完了しました')
            ->markdown('emails.trades.completed');
    }
}
