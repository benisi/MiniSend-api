<?php

namespace Tests\Unit;

use App\Helpers\MessageParser;
use Tests\TestCase;

class MessageParserTest extends TestCase
{
    public function test_can_parse_message_with_variables()
    {
        $message = 'Hello {$firstName} {$lastName}';
        $variables = [
            [
                "var" => "firstName",
                "value" => "Benjamin"
            ],
            [
                "var" => "lastName",
                "value" => "Isidahomen"
            ]
        ];

        $output = MessageParser::substituteValues($message, $variables);
        $this->assertEquals($output, 'Hello Benjamin Isidahomen');
    }
}
