<?php

namespace Cunningsoft\CodeMetricsBundle\Entity;

interface MetricInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getQualifiedName();

    /**
     * @return int
     */
    public function getQuality();

    /**
     * @return string
     */
    public function getScore();

    /**
     * @return string
     */
    public function getFilename();

    /**
     * @return string
     */
    public function getSource();

    /**
     * @return Violation[]
     */
    public function getViolations();

    /**
     * @return int
     */
    public function getStartLine();

    /**
     * @return int
     */
    public function getEndLine();
}
