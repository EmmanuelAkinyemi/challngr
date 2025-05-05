<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/Quiz.php';

// Create new quiz
$quiz = new Quiz();
$quizId = $quiz->createQuiz(
    'PHP Programming Quiz',
    'Test your knowledge of PHP programming language',
    20 // 20 minute time limit
);

// Define questions and answers
$questions = [
    [
        'question' => 'What does PHP stand for?',
        'options' => [
            'Personal Home Page',
            'PHP: Hypertext Preprocessor',
            'Private Home Page',
            'Personal Hypertext Processor'
        ],
        'correct' => 1
    ],
    [
        'question' => 'Which of the following is the correct way to start a PHP block?',
        'options' => [
            '<?php>',
            '<script php>',
            '<?php',
            '<php'
        ],
        'correct' => 2
    ],
    [
        'question' => 'How do you create a PHP variable?',
        'options' => [
            '$variable_name',
            'var variable_name',
            'variable $name',
            'new Variable()'
        ],
        'correct' => 0
    ],
    [
        'question' => 'Which function is used to output text in PHP?',
        'options' => [
            'print()',
            'echo()',
            'write()',
            'output()'
        ],
        'correct' => 1
    ],
    [
        'question' => 'What is the result of 8 % 3 in PHP?',
        'options' => [
            '2',
            '3',
            '2.666',
            '0'
        ],
        'correct' => 0
    ],
    [
        'question' => 'Which superglobal variable holds information about headers, paths, and script locations?',
        'options' => [
            '$_GET',
            '$_SERVER',
            '$_ENV',
            '$_GLOBALS'
        ],
        'correct' => 1
    ],
    [
        'question' => 'How do you concatenate two strings in PHP?',
        'options' => [
            'str_concat()',
            'concat()',
            'Using the + operator',
            'Using the . operator'
        ],
        'correct' => 3
    ],
    [
        'question' => 'Which function converts a string to lowercase?',
        'options' => [
            'strtolower()',
            'lowercase()',
            'str_to_lower()',
            'toLowerCase()'
        ],
        'correct' => 0
    ],
    [
        'question' => 'What is the correct way to include a file in PHP?',
        'options' => [
            '#include "file.php"',
            'include "file.php"',
            'import "file.php"',
            'require "file.php";'
        ],
        'correct' => 3
    ],
    [
        'question' => 'Which operator is used to check if two values are equal and of the same type?',
        'options' => [
            '==',
            '===',
            '=',
            '!='
        ],
        'correct' => 1
    ],
    [
        'question' => 'What does PDO stand for in PHP?',
        'options' => [
            'PHP Database Object',
            'PHP Data Object',
            'Personal Data Object',
            'Prepared Data Object'
        ],
        'correct' => 1
    ],
    [
        'question' => 'Which function returns the length of a string?',
        'options' => [
            'len()',
            'length()',
            'strlen()',
            'string_length()'
        ],
        'correct' => 2
    ],
    [
        'question' => 'How do you start a session in PHP?',
        'options' => [
            'session_begin()',
            'start_session()',
            'session_start()',
            'begin_session()'
        ],
        'correct' => 2
    ],
    [
        'question' => 'What is the correct way to create an array in PHP?',
        'options' => [
            '$arr = array("a", "b", "c");',
            '$arr = ["a", "b", "c"];',
            '$arr = new Array("a", "b", "c");',
            'Both A and B'
        ],
        'correct' => 3
    ],
    [
        'question' => 'Which function sorts an array in descending order?',
        'options' => [
            'sort()',
            'asort()',
            'rsort()',
            'dsort()'
        ],
        'correct' => 2
    ],
    [
        'question' => 'What does the following code output? <?php echo (int) "5.5foo"; ?>',
        'options' => [
            '5.5',
            '5.5foo',
            '5',
            'Error'
        ],
        'correct' => 2
    ],
    [
        'question' => 'Which function is used to redirect to another page in PHP?',
        'options' => [
            'redirect()',
            'header()',
            'location()',
            'forward()'
        ],
        'correct' => 1
    ],
    [
        'question' => 'What is the correct way to create a constant in PHP?',
        'options' => [
            'define("CONSTANT", "value");',
            'const CONSTANT = "value";',
            'constant("CONSTANT", "value");',
            'Both A and B'
        ],
        'correct' => 3
    ],
    [
        'question' => 'Which function is used to get the current date and time?',
        'options' => [
            'now()',
            'time()',
            'date()',
            'getdate()'
        ],
        'correct' => 2
    ],
    [
        'question' => 'What is the correct way to handle exceptions in PHP?',
        'options' => [
            'try-catch block',
            'onError handler',
            'exception() function',
            'error_handle()'
        ],
        'correct' => 0
    ]
];

// Insert questions and options
foreach ($questions as $questionData) {
    $questionId = $quiz->addQuestion(
        $quizId,
        $questionData['question'],
        'multiple_choice',
        1 // Each question is worth 1 point
    );

    foreach ($questionData['options'] as $index => $optionText) {
        $quiz->addOption(
            $questionId,
            $optionText,
            $index === $questionData['correct'] // Mark correct option
        );
    }
}

echo "Successfully created PHP quiz with 20 questions (Quiz ID: $quizId)\n";
