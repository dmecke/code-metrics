<?php

namespace Cunningsoft\CodeMetricsBundle\Command;

use Doctrine\ORM\EntityManager;
use Cunningsoft\CodeMetricsBundle\Entity\MetricClass;
use Cunningsoft\CodeMetricsBundle\Entity\MetricFile;
use Cunningsoft\CodeMetricsBundle\Entity\MetricMethod;
use Cunningsoft\CodeMetricsBundle\Entity\Violation;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class AnalyzeCommand extends ContainerAwareCommand
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var MetricClass[]
     */
    private $classes;

    /**
     * @var MetricFile[]
     */
    private $files;

    protected function configure()
    {
        $this->setName('codemetrics:analyze');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->runProcess($this->getContainer()->getParameter('codemetrics.pdepend_bin') . ' --summary-xml=' . $this->getContainer()->getParameter('kernel.cache_dir') . '/metrics/pdepend.xml src/Cunningsoft/CodeMetricsBundle');
        $this->runProcess($this->getContainer()->getParameter('codemetrics.phpmd_bin') . ' src/Cunningsoft/CodeMetricsBundle xml codesize,controversial,design,unusedcode,naming --reportfile ' . $this->getContainer()->getParameter('kernel.cache_dir') . '/metrics/phpmd.xml');
        $this->cleanUp();
        $this->persistResults();
    }

    /**
     * @param string$command
     *
     * @throws \RuntimeException
     */
    private function runProcess($command)
    {
        $process = new Process($command);
        $process->setTimeout(3600);
        $process->run(function($type, $buffer) {
            $this->output->write($buffer);
        });
    }

    private function cleanUp()
    {
        $classes = $this->getEntityManager()->getRepository('CodeMetricsBundle:MetricFile')->findAll();
        foreach ($classes as $class) {
            $this->getEntityManager()->remove($class);
        }
        $this->getEntityManager()->flush();
    }

    private function persistResults()
    {
        $this->persistPdependResults();
        $this->persistPhpMdResults();
        $this->getEntityManager()->flush();
    }

    private function persistPdependResults()
    {
        /** @var \DOMElement[] $nodes */
        $nodes = $this->getContainer()->get('code_metrics_pdepend_parser')->getNodes('method');
        foreach ($nodes as $node) {
            $metricFile = $this->buildFile($node->parentNode->getElementsByTagName('file')->item(0));
            $metricClass = $this->buildClass($node->parentNode, $metricFile);

            $metricMethod = new MetricMethod();
            $metricMethod->setClass($metricClass);
            $metricMethod->setName($node->getAttribute('name'));
            $metricMethod->setCcn($node->getAttribute('ccn'));
            $metricMethod->setCcn2($node->getAttribute('ccn2'));
            $metricMethod->setCloc($node->getAttribute('cloc'));
            $metricMethod->setEloc($node->getAttribute('eloc'));
            $metricMethod->setLloc($node->getAttribute('lloc'));
            $metricMethod->setLoc($node->getAttribute('loc'));
            $metricMethod->setNcloc($node->getAttribute('ncloc'));
            $metricMethod->setNpath($node->getAttribute('npath'));
            $this->getEntityManager()->persist($metricMethod);
        }
    }

    private function persistPhpMdResults()
    {
        /** @var \DOMElement[] $nodes */
        $nodes = $this->getContainer()->get('code_metrics_phpmd_parser')->getNodes('violation');
        foreach ($nodes as $node) {
            $metricFile = $this->buildFile($node->parentNode);

            $violation = new Violation();
            $violation->setFile($metricFile);
            $violation->setBeginLine($node->getAttribute('beginline'));
            $violation->setEndLine($node->getAttribute('endline'));
            $violation->setRule($node->getAttribute('rule'));
            $violation->setRuleset($node->getAttribute('ruleset'));
            $violation->setPriority($node->getAttribute('priority'));
            $violation->setDescription(trim($node->nodeValue));
            $this->getEntityManager()->persist($violation);
        }
    }

    /**
     * @param \DOMElement $fileNode
     *
     * @return MetricFile
     */
    private function buildFile(\DOMElement $fileNode)
    {
        $id = $fileNode->getAttribute('name');

        if (!isset($this->files[$id])) {
            $metricFile = new MetricFile();
            $metricFile->setFilename($fileNode->getAttribute('name'));
            $this->getEntityManager()->persist($metricFile);

            $this->files[$id] = $metricFile;
        }

        return $this->files[$id];
    }

    /**
     * @param \DOMElement $classNode
     * @param MetricFile $metricFile
     *
     * @return MetricClass
     */
    private function buildClass(\DOMElement $classNode, MetricFile $metricFile)
    {
        $id = $classNode->parentNode->getAttribute('name') . '::' . $classNode->getAttribute('name');

        if (!isset($this->classes[$id])) {
            $metricClass = new MetricClass();
            $metricClass->setFile($metricFile);
            $metricClass->setNamespace($classNode->parentNode->getAttribute('name'));
            $metricClass->setName($classNode->getAttribute('name'));
            $metricClass->setCa($classNode->getAttribute('ca'));
            $metricClass->setCa($classNode->getAttribute('cbo'));
            $metricClass->setCa($classNode->getAttribute('ce'));
            $metricClass->setCa($classNode->getAttribute('cis'));
            $metricClass->setCa($classNode->getAttribute('cloc'));
            $metricClass->setCa($classNode->getAttribute('cr'));
            $metricClass->setCa($classNode->getAttribute('csz'));
            $metricClass->setCa($classNode->getAttribute('dit'));
            $metricClass->setCa($classNode->getAttribute('eloc'));
            $metricClass->setCa($classNode->getAttribute('impl'));
            $metricClass->setCa($classNode->getAttribute('lloc'));
            $metricClass->setCa($classNode->getAttribute('loc'));
            $metricClass->setCa($classNode->getAttribute('ncloc'));
            $metricClass->setCa($classNode->getAttribute('noam'));
            $metricClass->setCa($classNode->getAttribute('nocc'));
            $metricClass->setCa($classNode->getAttribute('nom'));
            $metricClass->setCa($classNode->getAttribute('noom'));
            $metricClass->setCa($classNode->getAttribute('npm'));
            $metricClass->setCa($classNode->getAttribute('rcr'));
            $metricClass->setCa($classNode->getAttribute('vars'));
            $metricClass->setCa($classNode->getAttribute('varsi'));
            $metricClass->setCa($classNode->getAttribute('varsnp'));
            $metricClass->setCa($classNode->getAttribute('wmc'));
            $metricClass->setCa($classNode->getAttribute('wmci'));
            $metricClass->setCa($classNode->getAttribute('wmcnp'));
            $this->getEntityManager()->persist($metricClass);

            $this->classes[$id] = $metricClass;
        }

        return $this->classes[$id];
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }
}
