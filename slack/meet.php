<?php

require_once __DIR__ .'/sendMeet.php';

$meet = new MeetSlash($_POST['text']);
$user = "<@{$_POST['user_id']}>";
$url = $meet->generateMeetUrl();
$channel = '<!channel>';
if($_POST['channel_name'] == 'directmessage') {
    $channel = '';
}

header('Content-type: application/json');
$response = [
    'response_type' => 'in_channel',
    'blocks' => [
        [
            'type' => 'section',
            'text' => [
                'type' => 'mrkdwn',
                'text' => trim("$channel $user has started a meeting at the following url: <{$url}>")
            ]
        ],
        [
            'type' => 'actions',
            'elements' => [
                [
                    'type' => 'button',
                    'text' => [
                        'type' => 'plain_text',
                        'text' => 'Join meeting'
                    ],
                    'url' => $url
                ]
            ]
        ]
    ]
];

echo json_encode($response);