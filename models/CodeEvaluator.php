<?php

class CodeEvaluator
{
    public function evaluateCode($challengeId, $userCode, $tests)
    {
        $testResults = [];
        $passed      = 0;
        $total       = count($tests);

        foreach ($tests as $test) {
            try {
                // Create and execute the test function
                $wrappedCode = 'function runTest($input) {' .
                    'try {' .
                    $userCode .
                    'return $input;' .
                    '} catch (Throwable $e) {' .
                    'return "Error: " . $e->getMessage();' .
                    '}' .
                    '}';

                // Define the function in global scope
                eval($wrappedCode);

                // Run the test with the input
                $output = runTest($test['input']);

                // Handle null output
                if ($output === null) {
                    $output = "Error: Function returned null";
                } else {
                    $output = trim($output);
                }

                // Compare with expected output
                $isCorrect = ($output === $test['expected_output']);
                if ($isCorrect) {
                    $passed++;
                }

                $testResults[] = [
                    'input'    => $test['input'],
                    'expected' => $test['expected_output'],
                    'actual'   => $output,
                    'passed'   => $isCorrect,
                    'hidden'   => $test['is_hidden'],
                ];
            } catch (Exception $e) {
                $testResults[] = [
                    'input'    => $test['input'],
                    'expected' => $test['expected_output'],
                    'actual'   => "Error: " . $e->getMessage(),
                    'passed'   => false,
                    'hidden'   => $test['is_hidde   n'],
                ];
            }
        }

        $score = $total > 0 ? round(($passed / $total) * 100) : 0;

        return [
            'passed'       => $passed === $total,
            'score'        => $score,
            'test_results' => $testResults,
        ];
    }
}
