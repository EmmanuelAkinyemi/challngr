<?php
class ChallengeController {
    private $model;
    private $evaluator;

    public function __construct(ChallengeModel $model, CodeEvaluator $evaluator) {
        $this->model = $model;
        $this->evaluator = $evaluator;
    }

    public function getFirstChallengeId() {
        return $this->model->getFirstChallengeId();
    }

    public function showChallenge($id) {
        $challenge = $this->model->getChallengeById($id);
        if (!$challenge) {
            throw new Exception("Challenge not found");
        }
        
        $tests = $this->model->getTestsForChallenge($id);
        return [
            'challenge' => $challenge,
            'tests' => $tests
        ];
    }

    public function submitSolution($challengeId, $userId, $code) {
        $tests = $this->model->getTestsForChallenge($challengeId);
        $result = $this->evaluator->evaluateCode($challengeId, $code, $tests);
        
        $this->model->saveSubmission(
            $challengeId,
            $userId,
            $code,
            $result['passed'],
            $result['test_results'],
            $result['score']
        );
        
        return $result;
    }
}