<?php

namespace Cunningsoft\CodeMetricsBundle\Services;

class PdependParser extends AbstractMetricsParser
{
    /**
     * @return string
     */
    protected function getMetricsFilename()
    {
        return 'pdepend.xml';
    }
}
