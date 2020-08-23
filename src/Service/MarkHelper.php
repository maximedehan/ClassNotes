<?php

namespace App\Service;

use App\Entity\Student;
use App\Repository\MarkRepository;

class MarkHelper
{
    private $markRepository;

    public function __construct(MarkRepository $markRepository)
    {
        $this->markRepository = $markRepository;
    }

    public function getMarksByStudent(Student $student): array
    {
        $marksArray = [];
        $marks = $student->getMarks()->getValues();
        foreach ($marks as $mark) {
            $marksArray[] = [
            'student_id' => $student->getId(),
            'lesson' => $mark->getLesson(),
            'value' => $mark->getValue()
            ];
        }

        return $marksArray;
    }

    public function getAverageByClass(): float
    {
        $marks = $this->markRepository->findAll();
        $average = 0;
        foreach ($marks as $mark) {
            $average = $average + $mark->getValue();
        }

        return round($average/count($marks),2);
    }

    public function getAverageByStudent(Student $student): float
    {
        $marks = $student->getMarks()->getValues();
        $average = 0;
        foreach ($marks as $mark) {
            $marksArray[] = ['lesson' => $mark->getLesson(), 'value' => $mark->getValue()];
            $average = $average + $mark->getValue();
        }

        return round($average/count($marks), 2);
    }
}
