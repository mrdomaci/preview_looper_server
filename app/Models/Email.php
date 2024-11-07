<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EmailStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Mail;

class Email extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipient',
        'subject',
        'body',
        'status',
        'client_service_id',
    ];

    public function getId(): int
    {
        return $this->getAttribute('id');
    }

    /**
     * @return array<string>
     */
    public function getRecipient(): array
    {
        return explode(',', $this->getAttribute('recipient'));
    }

    public function getSubject(): string
    {
        return $this->getAttribute('subject');
    }

    public function getBody(): string
    {
        return $this->getAttribute('body');
    }

    public function getStatus(): EmailStatusEnum
    {
        return EmailStatusEnum::fromCase($this->getAttribute('status'));
    }

    public function setStatus(EmailStatusEnum $status): self
    {
        return $this->setAttribute('status', $status->name);
    }

    public function getClientServiceId(): int
    {
        return $this->getAttribute('client_service_id');
    }

    public function clientService(): BelongsTo
    {
        return $this->belongsTo(ClientService::class);
    }

    public function send(): bool
    {
        $response = Mail::html($this->getBody(), function ($message) {
            $message->to($this->getRecipient())
                    ->subject($this->getSubject());
        });
        if ($response === null) {
            return false;
        }
        $this->setStatus(EmailStatusEnum::SENT);
        $this->save();
        return true;
    }
}
