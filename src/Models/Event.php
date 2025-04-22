<?php

namespace OutlookCalendarSDK\Models;

use OutlookCalendarSDK\Models\Attendee;

class Event
{
    private $subject;
    private $body;
    private $start;
    private $end;
    private $location;
    private $attendees = [];
    private $isOnlineMeeting = false;
    private $onlineMeetingProvider = 'teamsForBusiness';

    public function __construct(
        string $subject,
        \DateTime $start,
        \DateTime $end,
        string $body = '',
        string $location = ''
    ) {
        $this->subject = $subject;
        $this->start = $start;
        $this->end = $end;
        $this->body = $body;
        $this->location = $location;
    }

    public function addAttendee(Attendee $attendee): self
    {
        $this->attendees[] = $attendee;
        return $this;
    }

    public function setOnlineMeeting(bool $isOnlineMeeting, string $provider = 'teamsForBusiness'): self
    {
        $this->isOnlineMeeting = $isOnlineMeeting;
        $this->onlineMeetingProvider = $provider;
        return $this;
    }

    public function toArray(): array
    {
        $event = [
            'subject' => $this->subject,
            'body' => [
                'contentType' => 'HTML',
                'content' => $this->body,
            ],
            'start' => [
                'dateTime' => $this->start->format('Y-m-d\TH:i:s'),
                'timeZone' => 'UTC',
            ],
            'end' => [
                'dateTime' => $this->end->format('Y-m-d\TH:i:s'),
                'timeZone' => 'UTC',
            ],
            'location' => [
                'displayName' => $this->location,
            ],
            'isOnlineMeeting' => $this->isOnlineMeeting,
            'onlineMeetingProvider' => $this->onlineMeetingProvider,
        ];

        if (!empty($this->attendees)) {
            $event['attendees'] = array_map(function (Attendee $attendee) {
                return $attendee->toArray();
            }, $this->attendees);
        }

        return $event;
    }
}
