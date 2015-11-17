<?php

namespace Cunningsoft\CodeMetricsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="code__metrics_file")
 */
class MetricFile
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
    private $filename;

    /**
     * @var MetricClass[]
     *
     * @ORM\OneToMany(targetEntity="MetricClass", mappedBy="file", cascade={"ALL"})
     */
    private $classes;

    /**
     * @var Violation[]
     *
     * @ORM\OneToMany(targetEntity="Violation", mappedBy="file", cascade={"ALL"})
     */
    private $violations;

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return Violation[]
     */
    public function getViolations()
    {
        return $this->violations;
    }

    /**
     * @return MetricClass[]
     */
    public function getClasses()
    {
        return $this->classes;
    }
}
