<?php

namespace Cunningsoft\CodeMetricsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="code__metrics_method", uniqueConstraints={@ORM\UniqueConstraint(name="method", columns={"name", "class_id"})})
 */
class MetricMethod implements MetricInterface
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var MetricClass
     *
     * @ORM\ManyToOne(targetEntity="MetricClass", inversedBy="methods", fetch="EAGER")
     */
    private $class;

    /**
     * @var int
     */
    private $startLine;

    /**
     * @var int
     */
    private $endLine;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $ccn;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $ccn2;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $cloc;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $eloc;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $lloc;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $loc;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $ncloc;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $npath;

    private function ensureIsInitialized()
    {
        if (null == $this->startLine || null === $this->endLine) {
            $method = new \ReflectionMethod($this->getClass()->getQualifiedName(), $this->getName());

            $this->startLine = $method->getStartLine();
            $this->endLine = $method->getEndLine();
        }
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getQualifiedName()
    {
        return $this->class->getQualifiedName() . '::' . $this->name;
    }

    /**
     * @return MetricClass
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param MetricClass $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return int
     */
    public function getCcn()
    {
        return $this->ccn;
    }

    /**
     * @param int $ccn
     */
    public function setCcn($ccn)
    {
        $this->ccn = $ccn;
    }

    /**
     * @return int
     */
    public function getCcn2()
    {
        return $this->ccn2;
    }

    /**
     * @param int $ccn2
     */
    public function setCcn2($ccn2)
    {
        $this->ccn2 = $ccn2;
    }

    /**
     * @return int
     */
    public function getCloc()
    {
        return $this->cloc;
    }

    /**
     * @param int $cloc
     */
    public function setCloc($cloc)
    {
        $this->cloc = $cloc;
    }

    /**
     * @return int
     */
    public function getEloc()
    {
        return $this->eloc;
    }

    /**
     * @param int $eloc
     */
    public function setEloc($eloc)
    {
        $this->eloc = $eloc;
    }

    /**
     * @return int
     */
    public function getLloc()
    {
        return $this->lloc;
    }

    /**
     * @param int $lloc
     */
    public function setLloc($lloc)
    {
        $this->lloc = $lloc;
    }

    /**
     * @return int
     */
    public function getLoc()
    {
        return $this->loc;
    }

    /**
     * @param int $loc
     */
    public function setLoc($loc)
    {
        $this->loc = $loc;
    }

    /**
     * @return int
     */
    public function getNcloc()
    {
        return $this->ncloc;
    }

    /**
     * @param int $ncloc
     */
    public function setNcloc($ncloc)
    {
        $this->ncloc = $ncloc;
    }

    /**
     * @return int
     */
    public function getNpath()
    {
        return $this->npath;
    }

    /**
     * @param int $npath
     */
    public function setNpath($npath)
    {
        $this->npath = $npath;
    }

    /**
     * @return int
     */
    public function getQuality()
    {
        return $this->getQualityCcn() + $this->getQualityEloc() + $this->getQualityNpath();
    }

    /**
     * @return string
     */
    public function getScore()
    {
        if ($this->getQuality() == 0) {
            return 'A';
        } elseif ($this->getQuality() <= 3) {
            return 'B';
        } elseif ($this->getQuality() <= 6) {
            return 'C';
        } elseif ($this->getQuality() <= 9) {
            return 'D';
        } elseif ($this->getQuality() <= 12) {
            return 'E';
        } else {
            return 'F';
        }
    }

    /**
     * @return int
     */
    private function getQualityCcn()
    {
        if ($this->ccn <= 4) {
            return 0;
        } elseif ($this->ccn <= 7) {
            return 2;
        } elseif ($this->ccn <= 10) {
            return 4;
        } else {
            return 6;
        }
    }

    /**
     * @return int
     */
    private function getQualityEloc()
    {
        if ($this->eloc < 7) {
            return 0;
        } elseif ($this->eloc < 14) {
            return 1;
        } elseif ($this->eloc < 21) {
            return 2;
        } else {
            return 3;
        }
    }

    /**
     * @return int
     */
    private function getQualityNpath()
    {
        if ($this->npath < 20) {
            return 0;
        } elseif ($this->npath < 50) {
            return 2;
        } elseif ($this->npath < 100) {
            return 4;
        } else {
            return 6;
        }
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->class->getFilename();
    }

    /**
     * @return string
     */
    public function getSource()
    {
        $lines = explode("\n", file_get_contents($this->getFilename()));
        $methodLines = array();
        for ($i = $this->getStartLine() - 1; $i <= $this->getEndLine() - 1; $i++) {
            $methodLines[] = $i . ' ' . $lines[$i];
        }

        return implode("\n", $methodLines);
    }

    /**
     * @return int
     */
    public function getStartLine()
    {
        $this->ensureIsInitialized();

        return $this->startLine;
    }

    /**
     * @return int
     */
    public function getEndLine()
    {
        $this->ensureIsInitialized();

        return $this->endLine;
    }

    /**
     * @return Violation[]
     */
    public function getViolations()
    {
        $violations = array();
        foreach ($this->getClass()->getFile()->getViolations() as $v) {
            if ($this->getStartLine() <= $v->getBeginLine() && $this->getEndLine() >= $v->getEndLine()) {
                $violations[] = $v;
            }
        }

        return $violations;
    }
}
