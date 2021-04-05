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

    public static function getRequestDataWithAttachment()
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
            "text" => 'Benjamin is saying hi',
            "attachments" => [
                [
                    "filename" => 'test',
                    "content" => 'iVBORw0KGgoAAAANSUhEUgAAADgAAABiCAYAAAAFkxCPAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAUYSURBVHgB3ZzRUdwwFEWvtOQ/JWw6SCrAHWQJ5DukgGSGClICSSgg8J0MLB04FUAqgBIogJUiee3NWjZeS77PZnJmFjAztt9dWe9Z0rUBMovF6Us8IxSIOHFzvTe7s1C3CjY3WF0vf53kmBCqwMPDs4VV9ir8vxOcK2MvjFnly+XJPUaEKvDd4fcf7ojH6D7jvfuZO7HXWMELfoAgbIF37ojzmH2K1oW9Nsq17s+TW5ChCVy8P32t7ewGQxBoXQ0STlyGoVjX+hbHWs2usIfXIEAT6C61t+DxwMq+FIG+9rl+lIGEhf0NEhSBs9leBiLKqCVIUAS6b5x5ecLXS5Bg9cEMLFwmZd4MDBboy0Ns7dtBDiKDBVLKwxbGri5AZLBAcnkA++Z8kEB+eVA5yAwSSC8P7p4UZAYJpJcHxSsPFUP7YAYWvjwIjCaSBfrR+3MuDxXpLfiCXB6Movc/T7JAZdUHMFk95hAgXSC5PEhNXewhgcXRaQYiYXnwcztWqTljKiNJoLJ6wZzNaSkPmRM397/drSDevf9+j8SpjKQwD4/OblwNpEwp+PJw+fPzq2qzmlvt2iVmoiq6BX0ANHFr8tqWz862e4ey/7e17m041Iq/RHsEEENYHtbZOeIEtqjFfqLq2Ks5ODpzrWt+G5jc37hHC5wBb4n6GuVBDbw61q2rMo3Zl4Ojb9fRZcJa3u1ZWB7K7ExbvFHu+FEC+QHY4PJ02ZmIT0JRAukBYFXLgFrpfbAob96jBNID2Bq9S2Xn3gJHKQ9EquzcvwXJAbiaVZu99tkZTMrs3FsgO4BwclcqO/cWKBDAfbXNz85mc3X0EsgOANb82d7kZ2eTV3/3EsgOwCpTW1yRzM69BFIDCNb+pLPzToHsABprf8LZeXcLkgMI1/6ks/NOgdqAeXlKl4fGeLBPH8xAIgxAIDs3lr47x4NlAHMIBaChMxAJs/P6HB24QSMzuzUCsJDLzhWdAiWtIT47j+HMeFKguDVEODtXPClQ2hoinZ0353lqhxGsIRlItJWHiq4+mEEoALozo6U8VLQKlA6A7cxoKw+bc7X+UziAMY17rQIlrSFjG/caAqWtIWMb9xoC+QHYmnNpbONeQ+D/Uh4q2vpgBhaBc5CdnZWyO1d+awKlnYNTGPe0aADB2t8Uxj0tGcD22t9Uxr2aQElryFTGvY1AaWvIVMa9jUDX/6jOpTZrCFhEGPf0GAFM6esuBMo7B7lzOzHGvXULCjsHpzTuFQKlFh8rpjTuFQJHsIbQiPV1a2lryAjZuRONR9y6e7oDa92wxhYPKFIDUErxEkyCr7vhNiyf5Mz8bVt030lwDkYe/9wd/2PMLo21ifIb8p+vxTPxM+/d1AsFtd+jlOS1LWHjXh86F1/KZLEsP+t66YL2ab+tdcMApI17fYhyG5aD1/PyU2TItftX7xcuwdVjrX9IZue+JFmaK8rxmP8UwyFZ52DaYz+0h5TDb1fCOYgEaAIbBxZwDiIBEYHixr0IZFpQ2BoSg4hAaWtIDCICJY17sdAFSjoHU6ALlHQOpkAXKGncS4EqUNy4lwC3BUeyhsRAFTiWNSQGdh/MQKLP2l8fBo0mtpE27qXCe6cTdAYiXdaQGIjvdHpe5aGCJ1DZC9bLNpjvdCI+avyPxlRGLAYfLy8/n4OAiMBtqokq7VePbTFRtfM+1TyuXrFeeyQuMGRX6/rycPXr0xuQoJWJvgQTVc3WJZWHZ4lv3UI0kb/VYvYmggsN4QAAAABJRU5ErkJggg=='
                ]
            ]
        ];
    }

    public static function getRequestDataWithInvalidAttachment()
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
            "text" => 'Benjamin is saying hi',
            "attachments" => [
                [
                    "filename" => 'test',
                    "content" => 'invalid stuff'
                ]
            ]
        ];
    }
}
