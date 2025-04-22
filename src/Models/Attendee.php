<?php

namespace OutlookCalendarSDK\Models;

class Attendee
{
    private $email;
    private $name;
    private $type;

    public function __construct(string $email, string $name = '', string $type = 'required')
    {
        $this->email = $email;
        $this->name = $name;
        $this->type = $type;
    }

    public function toArray(): array
    {
        return [
            'emailAddress' => [
                'address' => $this->email,
                'name' => $this->name,
            ],
            'type' => $this->type,
        ];
    }
}
