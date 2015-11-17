<?php

namespace Cunningsoft\CodeMetricsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="code_metrics_class", uniqueConstraints={@ORM\UniqueConstraint(name="class", columns={"namespace", "name"})})
 */
class MetricClass implements MetricInterface
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
     */
    private $startLine;

    /**
     * @var int
     */
    private $endLine;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $namespace;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var MetricMethod[]
     *
     * @ORM\OneToMany(targetEntity="MetricMethod", mappedBy="class", cascade={"ALL"})
     */
    private $methods;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $ca;

    /**
     * @var int
     */
    private $cbo;

    /**
     * @var int
     */
    private $ce;

    /**
     * @var int
     */
    private $cis;

    /**
     * @var int
     */
    private $cloc;

    /**
     * @var float
     */
    private $cr;

    /**
     * @var int
     */
    private $csz;

    /**
     * @var int
     */
    private $dit;

    /**
     * @var int
     */
    private $eloc;

    /**
     * @var int
     */
    private $impl;

    /**
     * @var int
     */
    private $lloc;

    /**
     * @var int
     */
    private $loc;

    /**
     * @var int
     */
    private $ncloc;

    /**
     * @var int
     */
    private $noam;

    /**
     * @var int
     */
    private $nocc;

    /**
     * @var int
     */
    private $nom;

    /**
     * @var int
     */
    private $noom;

    /**
     * @var int
     */
    private $npm;

    /**
     * @var float
     */
    private $rcr;

    /**
     * @var int
     */
    private $vars;

    /**
     * @var int
     */
    private $varsi;

    /**
     * @var int
     */
    private $varsnp;

    /**
     * @var int
     */
    private $wmc;

    /**
     * @var int
     */
    private $wmci;

    /**
     * @var int
     */
    private $wmcnp;

    private function ensureIsInitialized()
    {
        if (null == $this->startLine || null === $this->endLine) {
            $class = new \ReflectionClass($this->getQualifiedName());

            $this->startLine = $class->getStartLine();
            $this->endLine = $class->getEndLine();
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
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
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
        return $this->namespace . '\\' . $this->name;
    }

    /**
     * @return MetricMethod[]
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @return int
     */
    public function getCa()
    {
        return $this->ca;
    }

    /**
     * @param int $ca
     */
    public function setCa($ca)
    {
        $this->ca = $ca;
    }

    /**
     * @return int
     */
    public function getQuality()
    {
        $quality = 0;
        foreach ($this->methods as $method) {
            $quality = max($quality, $method->getQuality());
        }

        return $quality;
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
     * @return string
     */
    public function getSource()
    {
        $lines = explode("\n", file_get_contents($this->getFilename()));
        $classLines = array();
        for ($i = $this->getStartLine() - 1; $i <= $this->getEndLine() - 1; $i++) {
            $classLines[] = $i . ' ' . $lines[$i];
        }

        return implode("\n", $classLines);
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
     * @return string
     */
    public function getFilename()
    {
        return $this->file->getFilename();
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
        foreach ($this->file->getViolations() as $v) {
            if ($this->getStartLine() <= $v->getBeginLine() && $this->getEndLine() >= $v->getEndLine()) {
                $violations[] = $v;
            }
        }

        return $violations;
    }

    /**
     * @return int
     */
    public function getCbo()
    {
        return $this->cbo;
    }

    /**
     * @param int $cbo
     */
    public function setCbo($cbo)
    {
        $this->cbo = $cbo;
    }

    /**
     * @return int
     */
    public function getCe()
    {
        return $this->ce;
    }

    /**
     * @param int $ce
     */
    public function setCe($ce)
    {
        $this->ce = $ce;
    }

    /**
     * @return int
     */
    public function getCis()
    {
        return $this->cis;
    }

    /**
     * @param int $cis
     */
    public function setCis($cis)
    {
        $this->cis = $cis;
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
     * @return float
     */
    public function getCr()
    {
        return $this->cr;
    }

    /**
     * @param float $cr
     */
    public function setCr($cr)
    {
        $this->cr = $cr;
    }

    /**
     * @return int
     */
    public function getCsz()
    {
        return $this->csz;
    }

    /**
     * @param int $csz
     */
    public function setCsz($csz)
    {
        $this->csz = $csz;
    }

    /**
     * @return int
     */
    public function getDit()
    {
        return $this->dit;
    }

    /**
     * @param int $dit
     */
    public function setDit($dit)
    {
        $this->dit = $dit;
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
    public function getImpl()
    {
        return $this->impl;
    }

    /**
     * @param int $impl
     */
    public function setImpl($impl)
    {
        $this->impl = $impl;
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
    public function getNoam()
    {
        return $this->noam;
    }

    /**
     * @param int $noam
     */
    public function setNoam($noam)
    {
        $this->noam = $noam;
    }

    /**
     * @return int
     */
    public function getNocc()
    {
        return $this->nocc;
    }

    /**
     * @param int $nocc
     */
    public function setNocc($nocc)
    {
        $this->nocc = $nocc;
    }

    /**
     * @return int
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param int $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * @return int
     */
    public function getNoom()
    {
        return $this->noom;
    }

    /**
     * @param int $noom
     */
    public function setNoom($noom)
    {
        $this->noom = $noom;
    }

    /**
     * @return int
     */
    public function getNpm()
    {
        return $this->npm;
    }

    /**
     * @param int $npm
     */
    public function setNpm($npm)
    {
        $this->npm = $npm;
    }

    /**
     * @return float
     */
    public function getRcr()
    {
        return $this->rcr;
    }

    /**
     * @param float $rcr
     */
    public function setRcr($rcr)
    {
        $this->rcr = $rcr;
    }

    /**
     * @return int
     */
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * @param int $vars
     */
    public function setVars($vars)
    {
        $this->vars = $vars;
    }

    /**
     * @return int
     */
    public function getVarsi()
    {
        return $this->varsi;
    }

    /**
     * @param int $varsi
     */
    public function setVarsi($varsi)
    {
        $this->varsi = $varsi;
    }

    /**
     * @return int
     */
    public function getVarsnp()
    {
        return $this->varsnp;
    }

    /**
     * @param int $varsnp
     */
    public function setVarsnp($varsnp)
    {
        $this->varsnp = $varsnp;
    }

    /**
     * @return int
     */
    public function getWmc()
    {
        return $this->wmc;
    }

    /**
     * @param int $wmc
     */
    public function setWmc($wmc)
    {
        $this->wmc = $wmc;
    }

    /**
     * @return int
     */
    public function getWmci()
    {
        return $this->wmci;
    }

    /**
     * @param int $wmci
     */
    public function setWmci($wmci)
    {
        $this->wmci = $wmci;
    }

    /**
     * @return int
     */
    public function getWmcnp()
    {
        return $this->wmcnp;
    }

    /**
     * @param int $wmcnp
     */
    public function setWmcnp($wmcnp)
    {
        $this->wmcnp = $wmcnp;
    }
}
