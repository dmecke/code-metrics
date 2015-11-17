<?php

namespace Cunningsoft\CodeMetricsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="code_metrics_violation")
 */
class Violation
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
     * @var MetricFile
     *
     * @ORM\ManyToOne(targetEntity="MetricFile")
     */
    private $file;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $beginLine;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $endLine;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $rule;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $ruleset;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $priority;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $description;

    /**
     * @return int
     */
    public function getBeginLine()
    {
        return $this->beginLine;
    }

    /**
     * @param int $beginLine
     */
    public function setBeginLine($beginLine)
    {
        $this->beginLine = $beginLine;
    }

    /**
     * @return int
     */
    public function getEndLine()
    {
        return $this->endLine;
    }

    /**
     * @return string
     */
    public function getLines()
    {
        if ($this->beginLine == $this->endLine) {
            return 'line ' . $this->beginLine;
        }

        return 'lines ' . $this->beginLine . ' - ' . $this->endLine;
    }

    /**
     * @param int $endLine
     */
    public function setEndLine($endLine)
    {
        $this->endLine = $endLine;
    }

    /**
     * @return string
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * @param string $rule
     */
    public function setRule($rule)
    {
        $this->rule = $rule;
    }

    /**
     * @return string
     */
    public function getRuleset()
    {
        return $this->ruleset;
    }

    /**
     * @param string $ruleset
     */
    public function setRuleset($ruleset)
    {
        $this->ruleset = $ruleset;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return MetricFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param MetricFile $file
     */
    public function setFile(MetricFile $file)
    {
        $this->file = $file;
    }

    /**
     * @return MetricClass|bool
     */
    public function getClass()
    {
        foreach ($this->file->getClasses() as $class) {
            if ($class->getStartLine() <= $this->beginLine && $class->getEndLine() >= $this->endLine) {
                return $class;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function hasClass()
    {
        return false !== $this->getClass();
    }

    /**
     * @return MetricMethod|bool
     */
    public function getMethod()
    {
        foreach ($this->getClass()->getMethods() as $method) {
            if ($method->getStartLine() <= $this->beginLine && $method->getEndLine() >= $this->endLine) {
                return $method;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function hasMethod()
    {
        return false !== $this->getMethod();
    }
}
