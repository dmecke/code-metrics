<?php

namespace Cunningsoft\CodeMetricsBundle\Controller;

use Doctrine\ORM\EntityManager;
use Cunningsoft\CodeMetricsBundle\Entity\MetricClass;
use Cunningsoft\CodeMetricsBundle\Entity\MetricInterface;
use Cunningsoft\CodeMetricsBundle\Entity\MetricMethod;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MetricsController extends Controller
{
    /**
     * @return RedirectResponse
     *
     * @Route("/", name="code_metrics_show")
     */
    public function showAction()
    {
        return $this->redirect($this->generateUrl('code_metrics_classes'));
    }

    /**
     * @return array
     *
     * @Route("/classes", name="code_metrics_classes")
     * @Template
     */
    public function classesAction()
    {
        return array(
            'classes' => $this->sortByQuality($this->getEntityManager()->getRepository('CodeMetricsBundle:MetricClass')->findAll()),
        );
    }

    /**
     * @param MetricClass $metricClass
     *
     * @return array
     *
     * @Route("/class/{id}", name="code_metrics_class")
     * @Template
     */
    public function classAction(MetricClass $metricClass)
    {
        return array(
            'class' => $metricClass,
        );
    }

    /**
     * @return array
     *
     * @Route("/methods", name="code_metrics_methods")
     * @Template
     */
    public function methodsAction()
    {
        return array(
            'methods' => $this->sortByQuality($this->getEntityManager()->getRepository('CodeMetricsBundle:MetricMethod')->findAll()),
        );
    }

    /**
     * @param MetricMethod $metricMethod
     *
     * @return array
     *
     * @Route("/method/{id}", name="code_metrics_method")
     * @Template
     */
    public function methodAction(MetricMethod $metricMethod)
    {
        return array(
            'method' => $metricMethod,
            'sourceCode' => file_get_contents($metricMethod->getFilename()),
        );
    }

    /**
     * @return array
     *
     * @Route("/violations", name="code_metrics_violations")
     * @Template
     */
    public function violationsAction()
    {
        return array(
            'violations' => $this->getEntityManager()->getRepository('CodeMetricsBundle:Violation')->findAll(),
        );
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager()
    {
        return $this->get('doctrine.orm.entity_manager');
    }

    /**
     * @param MetricInterface[] $metrics
     *
     * @return MetricInterface[]
     */
    private function sortByQuality($metrics)
    {
        $qualities = array();
        foreach ($metrics as $k => $method) {
            $qualities[$k] = $method->getQuality();
        }
        array_multisort($qualities, SORT_DESC, $metrics);

        return $metrics;
    }
}
