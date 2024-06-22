<?php

require __DIR__ . '/vendor/autoload.php';

class MeetSlash {

    private $text;

    public function __construct($text = null) {
        $this->text = !empty($text) ? trim($text) : null;
    }

    public function generateMeetUrl() {
        $url = null;
        if(trim(strtolower($this->text)) == 'standup') {
            return 'https://meet.google.com/tqj-xpmg-zec';
        }

        // Get the API client and construct the service object.
        $client = $this->getGoogleClient();
        $service = new \Google_Service_Calendar($client);

        $now = new \DateTime();
        
        $event = new \Google_Service_Calendar_Event([
            'end' => [
                'date' => $now->format('Y-m-d')
            ],
            'start' => [
                'date' => $now->format('Y-m-d')
            ],
            'conferenceData' => [
                'createRequest' => [
                    'conferenceSolutionKey' => [
                        'type' => 'hangoutsMeet'
                    ],
                    'requestId' => md5(time() . rand(0, 100000))
                ],
            ]
        ]);

        $event = $service->events->insert('primary', $event, [
            'conferenceDataVersion' => 1,
        ]);
        $conferenceData = $event->getConferenceData();
        $entryPoints = $conferenceData->getEntryPoints();
        /**
         * @var $entryPoint \Google_Service_Calendar_EntryPoint
         */
        foreach($entryPoints as $entryPoint) {
            $type = $entryPoint->getEntryPointType();
            if($type == 'video') {
                $url = $entryPoint->getUri();
            }
        }

        $service->events->delete('primary', $event->getId());
        
        return $url;
    }

    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     */
    function getGoogleClient()
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/google-cred.json');
        $client = new Google_Client();
        $client->setApplicationName('Google Calendar API - Coalmarch Slack');
        $client->setScopes([
            Google_Service_Calendar::CALENDAR,
            Google_Service_Calendar::CALENDAR_EVENTS
        ]);
        $client->setAccessType('offline');
        $client->useApplicationDefaultCredentials();
        $client->setSubject('conference_room@coalmarch.com');

        return $client;
    }
}