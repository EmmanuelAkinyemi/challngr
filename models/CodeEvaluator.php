<?php
class CodeEvaluator
{
    public function evaluateCode($challengeId, $userCode, $tests)
    {
        $testResults = [];
        $passed = 0;
        $total = count($tests);

        foreach ($tests as $test) {
            try {
                // Create a temporary file with the user's code
                $tempFile = tempnam(sys_get_temp_dir(), 'challenge_');
                if ($tempFile === false) {
                    throw new Exception("Failed to create temporary file");
                }

                // Add error reporting to the code
                $wrappedCode = "<?php
                error_reporting(E_ALL);
                ini_set('display_errors', 1);
                
                function runTest(\$input) {
                    try {
                        $userCode
                    } catch (Throwable \$e) {
                        return 'Error: ' . \$e->getMessage();
                    }
                }
                
                echo runTest(\$argv[1]);
                ?>";

                // Write the code to the temp file
                if (file_put_contents($tempFile, $wrappedCode) === false) {
                    throw new Exception("Failed to write code to temporary file");
                }

                // Prepare the input
                $input = escapeshellarg($test['input']);

                // Execute the code with the test input and capture both stdout and stderr
                $command = "php $tempFile $input 2>&1";
                $output = shell_exec($command);
                
                // Handle null output (command failed)
                if ($output === null) {
                    $output = "Error: Failed to execute code. Command: $command";
                } else {
                    $output = trim($output);
                }

                // Clean up
                unlink($tempFile);

                // Compare with expected output
                $isCorrect = ($output === $test['expected_output']);
                if ($isCorrect)
                    $passed++;

                $testResults[] = [
                    'input' => $test['input'],
                    'expected' => $test['expected_output'],
                    'actual' => $output,
                    'passed' => $isCorrect,
                    'hidden' => $test['is_hidden']
                ];
            } catch (Exception $e) {
                $testResults[] = [
                    'input' => $test['input'],
                    'expected' => $test['expected_output'],
                    'actual' => "Error: " . $e->getMessage(),
                    'passed' => false,
                    'hidden' => $test['is_hidden']
                ];
            } finally {
                // Make sure we clean up the temp file even if an error occurs
                if (isset($tempFile) && file_exists($tempFile)) {
                    @unlink($tempFile);
                }
            }
        }

        $score = $total > 0 ? round(($passed / $total) * 100) : 0;

        return [
            'passed' => $passed === $total,
            'score' => $score,
            'test_results' => $testResults
        ];
    }
}