<?php

namespace Tests\Mock;

class EmailRequestData
{
    public static function getRequestDataWithOutVariables()
    {
        return [
            "from" => [
                "email" => "bisidahomen@gmail.com",
                "name" => "Benjamin Isidahomen"
            ],
            "to" => [
                [
                    "email" => "test@doe.com",
                    "name" => "John doe"
                ],
                [
                    "email" => "mark@doe.com",
                    "name" => "Mark doe"
                ],
            ],
            "subject" => 'Hi from Benjamin',
            "text" => 'Benjamin is saying hi'
        ];
    }

    public static function getRequestDataWithVariables()
    {
        return [
            "from" => [
                "email" => "bisidahomen@gmail.com",
                "name" => "Benjamin Isidahomen"
            ],
            "to" => [
                [
                    "email" => "test@doe.com",
                    "name" => "John doe"
                ],
                [
                    "email" => "mark@doe.com",
                    "name" => "Mark doe"
                ],
            ],
            "subject" => 'Hi from {$name}',
            "text" => '{$name} is saying hi',
            "variables" => [
                [
                    "email" => "test@doe.com",
                    "substitutions" => [
                        [
                            "var" => "name",
                            "value" => "Benjamin"
                        ]
                    ]
                ]
            ]
        ];
    }

    public static function getRequestDataWithVariableAndHtml()
    {
        return [
            "from" => [
                "email" => "bisidahomen@gmail.com",
                "name" => "Benjamin Isidahomen"
            ],
            "to" => [
                [
                    "email" => "test@doe.com",
                    "name" => "John doe"
                ],
                [
                    "email" => "mark@doe.com",
                    "name" => "Mark doe"
                ],
            ],
            "subject" => 'Hi from {$name}',
            "html" => '<h1>{$name} is saying hi</h1>
                <p>testing html with {$name}</p>
            ',
            "variables" => [
                [
                    "email" => "test@doe.com",
                    "substitutions" => [
                        [
                            "var" => "name",
                            "value" => "Benjamin"
                        ]
                    ]
                ]
            ]
        ];
    }
}
