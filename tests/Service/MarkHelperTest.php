<?php

namespace App\Tests\Service;

use App\Entity\Mark;
use App\Service\MarkHelper;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class MarkHelperTest extends TestCase
{
    private $markRepository;
    private $serializer;
    private $helper;

    public function setUp()
    {
        $this->markRepository = $this->getMockBuilder('App\Repository\MarkRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $this->serializer = $this->getMockBuilder('Symfony\Component\Serializer\SerializerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->helper = new MarkHelper($this->markRepository, $this->serializer);
    }

    public function testGetMarksByStudent()
    {
        $studentId = 10;
        $student = $this->getMockBuilder('App\Entity\Student')
            ->getMock();

        $mark1 = (new Mark())->setId(1)
        ->setLesson('Maths')
        ->setValue(11.5)
        ->setStudent($student);

        $mark2 = (new Mark())->setId(2)
            ->setLesson('Histoire')
            ->setValue(16.5)
            ->setStudent($student);

        $marks = new ArrayCollection([$mark1, $mark2]);
        $student->method('getMarks')
            ->willReturn($marks);
        $student->method('getId')
            ->willReturn($studentId);

        $marksArrayExpected = [
            ['student_id' => $studentId,
            'lesson' => $mark1->getLesson(),
            'value' => $mark1->getValue()],
            ['student_id' => $studentId,
             'lesson' => $mark2->getLesson(),
             'value' => $mark2->getValue()]
        ];

        $result = $this->helper->getMarksByStudent($student);

        $this->assertEquals($marksArrayExpected, $result);
    }

    public function testGetAverageByClass()
    {
        $maxime = $this->getMockBuilder('App\Entity\Student')
            ->getMock();
        $david = $this->getMockBuilder('App\Entity\Student')
            ->getMock();

        $mark1 = (new Mark())->setId(1)
            ->setLesson('Maths')
            ->setValue(10.5)
            ->setStudent($maxime);

        $mark2 = (new Mark())->setId(2)
            ->setLesson('Histoire')
            ->setValue(20.0)
            ->setStudent($maxime);

        $mark3 = (new Mark())->setId(3)
            ->setLesson('Maths')
            ->setValue(16.5)
            ->setStudent($david);

        $mark4 = (new Mark())->setId(2)
            ->setLesson('Histoire')
            ->setValue(10.0)
            ->setStudent($david);

        $marks = new ArrayCollection([$mark1, $mark2, $mark3, $mark4]);

        $this->markRepository->method('findAll')
            ->willReturn($marks);

        $result = $this->helper->getAverageByClass();

        $this->assertEquals(14.25, $result);
    }

    public function testGetMarkFromFormRequest()
    {
        $data = [
            'value' => '10',
            'student' => 'maxime'
        ];

        $maxime = $this->getMockBuilder('App\Entity\Student')
            ->getMock();

        $mark = (new Mark())->setId(1)
            ->setLesson('Maths')
            ->setValue(10.5)
            ->setStudent($maxime);

        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $this->serializer->method('getRequest')
            ->with([json_encode($data),Mark::class,'json'])
            ->willReturn($mark);

        $this->helper->getMarkFromFormRequest($request);
        $this->assertTrue(true);
    }
}
