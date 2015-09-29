<?php
/**
 * Header file
*/
use PhpOffice\PhpPresentation\Autoloader;
use PhpOffice\PhpPresentation\Settings;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Slide;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\AbstractShape;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\Shape\Drawing;
use PhpOffice\PhpPresentation\Shape\MemoryDrawing;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Shape\RichText\BreakElement;
use PhpOffice\PhpPresentation\Shape\RichText\TextElement;
use PhpOffice\PhpPresentation\Style\Bullet;

error_reporting(E_ALL);
define('CLI', (PHP_SAPI == 'cli') ? true : false);
define('EOL', CLI ? PHP_EOL : '<br />');
define('SCRIPT_FILENAME', basename($_SERVER['SCRIPT_FILENAME'], '.php'));
define('IS_INDEX', SCRIPT_FILENAME == 'index');

require_once __DIR__ . '/../src/PhpPresentation/Autoloader.php';
Autoloader::register();

require_once  __DIR__ . '/../src/Common/Autoloader.php';
\PhpOffice\Common\Autoloader::register();

//require_once __DIR__ . '/../vendor/autoload.php';

// Set writers
$writers = array('PowerPoint2007' => 'pptx', 'ODPresentation' => 'odp');

// Return to the caller script when runs by CLI
if (CLI) {
    return;
}

// Set titles and names
$pageHeading = str_replace('_', ' ', SCRIPT_FILENAME);
$pageTitle = IS_INDEX ? 'Welcome to ' : "{$pageHeading} - ";
$pageTitle .= 'PHPPresentation';
$pageHeading = IS_INDEX ? '' : "<h1>{$pageHeading}</h1>";

// Populate samples
$files = '';
if ($handle = opendir('.')) {
    while (false !== ($file = readdir($handle))) {
        if (preg_match('/^Sample_\d+_/', $file)) {
            $name = str_replace('_', ' ', preg_replace('/(Sample_|\.php)/', '', $file));
            $files .= "<li><a href='{$file}'>{$name}</a></li>";
        }
    }
    closedir($handle);
}

/**
 * Write documents
 *
 * @param \PhpOffice\PhpPresentation\PhpPresentation $phpPresentation
 * @param string $filename
 * @param array $writers
 */
function write($phpPresentation, $filename, $writers)
{
    $result = '';
    
    // Write documents
    foreach ($writers as $writer => $extension) {
        $result .= date('H:i:s') . " Write to {$writer} format";
        if (!is_null($extension)) {
            $xmlWriter = IOFactory::createWriter($phpPresentation, $writer);
            $xmlWriter->save(__DIR__ . "/{$filename}.{$extension}");
            rename(__DIR__ . "/{$filename}.{$extension}", __DIR__ . "/results/{$filename}.{$extension}");
        } else {
            $result .= ' ... NOT DONE!';
        }
        $result .= EOL;
    }

    $result .= getEndingNotes($writers);

    return $result;
}

/**
 * Get ending notes
 *
 * @param array $writers
 */
function getEndingNotes($writers)
{
    $result = '';

    // Do not show execution time for index
    if (!IS_INDEX) {
        $result .= date('H:i:s') . " Done writing file(s)" . EOL;
        $result .= date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MB" . EOL;
    }

    // Return
    if (CLI) {
        $result .= 'The results are stored in the "results" subdirectory.' . EOL;
    } else {
        if (!IS_INDEX) {
            $types = array_values($writers);
            $result .= '<p>&nbsp;</p>';
            $result .= '<p>Results: ';
            foreach ($types as $type) {
                if (!is_null($type)) {
                    $resultFile = 'results/' . SCRIPT_FILENAME . '.' . $type;
                    if (file_exists($resultFile)) {
                        $result .= "<a href='{$resultFile}' class='btn btn-primary'>{$type}</a> ";
                    }
                }
            }
            $result .= '</p>';
        }
    }

    return $result;
}

/**
 * Creates a templated slide
 *
 * @param PHPPresentation $objPHPPresentation
 * @return \PhpOffice\PhpPresentation\Slide
 */
function createTemplatedSlide(PhpOffice\PhpPresentation\PhpPresentation $objPHPPresentation)
{
    // Create slide
    $slide = $objPHPPresentation->createSlide();
    
    // Add logo
    $shape = $slide->createDrawingShape();
    $shape->setName('PHPPresentation logo')
        ->setDescription('PHPPresentation logo')
        ->setPath('./resources/phppowerpoint_logo.gif')
        ->setHeight(36)
        ->setOffsetX(10)
        ->setOffsetY(10);
    $shape->getShadow()->setVisible(true)
        ->setDirection(45)
        ->setDistance(10);

    // Return slide
    return $slide;
}

class PhpPptTree {
    protected $oPhpPresentation;
    protected $htmlOutput;

    public function __construct(PhpPresentation $oPHPPpt)
    {
        $this->oPhpPresentation = $oPHPPpt;
    }

    public function display()
    {
        $this->append('<div class="container-fluid pptTree">');
        $this->append('<div class="row">');
        $this->append('<div class="collapse in col-md-6">');
        $this->append('<div class="tree">');
        $this->append('<ul>');
        $this->displayPhpPresentation($this->oPhpPresentation);
        $this->append('</ul>');
        $this->append('</div>');
        $this->append('</div>');
        $this->append('<div class="col-md-6">');
        $this->displayPhpPresentationInfo($this->oPhpPresentation);
        $this->append('</div>');
        $this->append('</div>');
        $this->append('</div>');

        return $this->htmlOutput;
    }

    protected function append($sHTML)
    {
        $this->htmlOutput .= $sHTML;
    }

    protected function displayPhpPresentation(PhpPresentation $oPHPPpt)
    {
        $this->append('<li><span><i class="fa fa-folder-open"></i> PhpPresentation</span>');
        $this->append('<ul>');
        $this->append('<li><span class="shape" id="divPhpPresentation"><i class="fa fa-info-circle"></i> Info "PhpPresentation"</span></li>');
        foreach ($oPHPPpt->getAllSlides() as $oSlide) {
            $this->append('<li><span><i class="fa fa-minus-square"></i> Slide</span>');
            $this->append('<ul>');
            $this->append('<li><span class="shape" id="div'.$oSlide->getHashCode().'"><i class="fa fa-info-circle"></i> Info "Slide"</span></li>');
            foreach ($oSlide->getShapeCollection() as $oShape) {
                if($oShape instanceof Group) {
                    $this->append('<li><span><i class="fa fa-minus-square"></i> Shape "Group"</span>');
                    $this->append('<ul>');
                    // $this->append('<li><span class="shape" id="div'.$oShape->getHashCode().'"><i class="fa fa-info-circle"></i> Info "Group"</span></li>');
                    foreach ($oShape->getShapeCollection() as $oShapeChild) {
                        $this->displayShape($oShapeChild);
                    }
                    $this->append('</ul>');
                    $this->append('</li>');
                } else {
                    $this->displayShape($oShape);
                }
            }
            $this->append('</ul>');
            $this->append('</li>');
        }
        $this->append('</ul>');
        $this->append('</li>');
    }

    protected function displayShape(AbstractShape $shape)
    {
        if($shape instanceof MemoryDrawing) {
            $this->append('<li><span class="shape" id="div'.$shape->getHashCode().'">Shape "MemoryDrawing"</span></li>');
        } elseif($shape instanceof Drawing) {
            $this->append('<li><span class="shape" id="div'.$shape->getHashCode().'">Shape "Drawing"</span></li>');
        } elseif($shape instanceof RichText) {
            $this->append('<li><span class="shape" id="div'.$shape->getHashCode().'">Shape "RichText"</span></li>');
        } else {
            var_export($shape);
        }
    }

    protected function displayPhpPresentationInfo(PhpPresentation $oPHPPpt)
    {
        $this->append('<div class="infoBlk" id="divPhpPresentationInfo">');
        $this->append('<dl>');
        $this->append('<dt>Number of slides</dt><dd>'.$oPHPPpt->getSlideCount().'</dd>');
        $this->append('<dt>Document Layout Height</dt><dd>'.$oPHPPpt->getLayout()->getCY(DocumentLayout::UNIT_MILLIMETER).' mm</dd>');
        $this->append('<dt>Document Layout Width</dt><dd>'.$oPHPPpt->getLayout()->getCX(DocumentLayout::UNIT_MILLIMETER).' mm</dd>');
        $this->append('<dt>Properties : Category</dt><dd>'.$oPHPPpt->getProperties()->getCategory().'</dd>');
        $this->append('<dt>Properties : Company</dt><dd>'.$oPHPPpt->getProperties()->getCompany().'</dd>');
        $this->append('<dt>Properties : Created</dt><dd>'.$oPHPPpt->getProperties()->getCreated().'</dd>');
        $this->append('<dt>Properties : Creator</dt><dd>'.$oPHPPpt->getProperties()->getCreator().'</dd>');
        $this->append('<dt>Properties : Description</dt><dd>'.$oPHPPpt->getProperties()->getDescription().'</dd>');
        $this->append('<dt>Properties : Keywords</dt><dd>'.$oPHPPpt->getProperties()->getKeywords().'</dd>');
        $this->append('<dt>Properties : Last Modified By</dt><dd>'.$oPHPPpt->getProperties()->getLastModifiedBy().'</dd>');
        $this->append('<dt>Properties : Modified</dt><dd>'.$oPHPPpt->getProperties()->getModified().'</dd>');
        $this->append('<dt>Properties : Subject</dt><dd>'.$oPHPPpt->getProperties()->getSubject().'</dd>');
        $this->append('<dt>Properties : Title</dt><dd>'.$oPHPPpt->getProperties()->getTitle().'</dd>');
        $this->append('</dl>');
        $this->append('</div>');

        foreach ($oPHPPpt->getAllSlides() as $oSlide) {
            $this->append('<div class="infoBlk" id="div'.$oSlide->getHashCode().'Info">');
            $this->append('<dl>');
            $this->append('<dt>HashCode</dt><dd>'.$oSlide->getHashCode().'</dd>');
            $this->append('<dt>Slide Layout</dt><dd>Layout::'.$this->getConstantName('\PhpOffice\PhpPresentation\Slide\Layout', $oSlide->getSlideLayout()).'</dd>');
            
            $this->append('<dt>Offset X</dt><dd>'.$oSlide->getOffsetX().'</dd>');
            $this->append('<dt>Offset Y</dt><dd>'.$oSlide->getOffsetY().'</dd>');
            $this->append('<dt>Extent X</dt><dd>'.$oSlide->getExtentX().'</dd>');
            $this->append('<dt>Extent Y</dt><dd>'.$oSlide->getExtentY().'</dd>');
            $this->append('</dl>');
            $this->append('</div>');

            foreach ($oSlide->getShapeCollection() as $oShape) {
                if($oShape instanceof Group) {
                    foreach ($oShape->getShapeCollection() as $oShapeChild) {
                        $this->displayShapeInfo($oShapeChild);
                    }
                } else {
                    $this->displayShapeInfo($oShape);
                }
            }
        }
    }

    protected function displayShapeInfo(AbstractShape $oShape)
    {
        $this->append('<div class="infoBlk" id="div'.$oShape->getHashCode().'Info">');
        $this->append('<dl>');
        $this->append('<dt>HashCode</dt><dd>'.$oShape->getHashCode().'</dd>');
        $this->append('<dt>Offset X</dt><dd>'.$oShape->getOffsetX().'</dd>');
        $this->append('<dt>Offset Y</dt><dd>'.$oShape->getOffsetY().'</dd>');
        $this->append('<dt>Height</dt><dd>'.$oShape->getHeight().'</dd>');
        $this->append('<dt>Width</dt><dd>'.$oShape->getWidth().'</dd>');
        $this->append('<dt>Rotation</dt><dd>'.$oShape->getRotation().'°</dd>');
        $this->append('<dt>Hyperlink</dt><dd>'.ucfirst(var_export($oShape->hasHyperlink(), true)).'</dd>');
        $this->append('<dt>Fill</dt><dd>@Todo</dd>');
        $this->append('<dt>Border</dt><dd>@Todo</dd>');
        if($oShape instanceof MemoryDrawing) {
            $this->append('<dt>Name</dt><dd>'.$oShape->getName().'</dd>');
            $this->append('<dt>Description</dt><dd>'.$oShape->getDescription().'</dd>');
            ob_start();
            call_user_func($oShape->getRenderingFunction(), $oShape->getImageResource());
            $sShapeImgContents = ob_get_contents();
            ob_end_clean();
            $this->append('<dt>Mime-Type</dt><dd>'.$oShape->getMimeType().'</dd>');
            $this->append('<dt>Image</dt><dd><img src="data:'.$oShape->getMimeType().';base64,'.base64_encode($sShapeImgContents).'"></dd>');
        } elseif($oShape instanceof Drawing) {
            $this->append('<dt>Name</dt><dd>'.$oShape->getName().'</dd>');
            $this->append('<dt>Description</dt><dd>'.$oShape->getDescription().'</dd>');
        } elseif($oShape instanceof RichText) {
            $this->append('<dt># of paragraphs</dt><dd>'.count($oShape->getParagraphs()).'</dd>');
            $this->append('<dt>Inset (T / R / B / L)</dt><dd>'.$oShape->getInsetTop().'px / '.$oShape->getInsetRight().'px / '.$oShape->getInsetBottom().'px / '.$oShape->getInsetLeft().'px</dd>');
            $this->append('<dt>Text</dt>');
            $this->append('<dd>');
            foreach ($oShape->getParagraphs() as $oParagraph) {
                $this->append('Paragraph<dl>');
                $this->append('<dt>Alignment Horizontal</dt><dd> Alignment::'.$this->getConstantName('\PhpOffice\PhpPresentation\Style\Alignment', $oParagraph->getAlignment()->getHorizontal()).'</dd>');
                $this->append('<dt>Alignment Vertical</dt><dd> Alignment::'.$this->getConstantName('\PhpOffice\PhpPresentation\Style\Alignment', $oParagraph->getAlignment()->getVertical()).'</dd>');
                $this->append('<dt>Alignment Margin (L / R)</dt><dd>'.$oParagraph->getAlignment()->getMarginLeft().' px / '.$oParagraph->getAlignment()->getMarginRight().'px</dd>');
                $this->append('<dt>Alignment Indent</dt><dd>'.$oParagraph->getAlignment()->getIndent().' px</dd>');
                $this->append('<dt>Alignment Level</dt><dd>'.$oParagraph->getAlignment()->getLevel().'</dd>');
                $this->append('<dt>Bullet Style</dt><dd> Bullet::'.$this->getConstantName('\PhpOffice\PhpPresentation\Style\Bullet', $oParagraph->getBulletStyle()->getBulletType()).'</dd>');
                $this->append('<dt>Bullet Font</dt><dd>'.$oParagraph->getBulletStyle()->getBulletFont().'</dd>');
                if ($oParagraph->getBulletStyle()->getBulletType() == Bullet::TYPE_BULLET) {
                    $this->append('<dt>Bullet Char</dt><dd>'.$oParagraph->getBulletStyle()->getBulletChar().'</dd>');
                }
                if ($oParagraph->getBulletStyle()->getBulletType() == Bullet::TYPE_NUMERIC) {
                    $this->append('<dt>Bullet Start At</dt><dd>'.$oParagraph->getBulletStyle()->getBulletNumericStartAt().'</dd>');
                    $this->append('<dt>Bullet Style</dt><dd>'.$oParagraph->getBulletStyle()->getBulletNumericStyle().'</dd>');
                }
                $this->append('<dt>RichText</dt><dd><dl>');
                foreach ($oParagraph->getRichTextElements() as $oRichText) {
                    if($oRichText instanceof BreakElement) {
                        $this->append('<dt><i>Break</i></dt>');
                    } else {
                        if ($oRichText instanceof TextElement) {
                           $this->append('<dt><i>TextElement</i></dt>');
                        } else {
                           $this->append('<dt><i>Run</i></dt>');
                        }
                        $this->append('<dd>'.$oRichText->getText());
                        $this->append('<dl>');
                        $this->append('<dt>Font Name</dt><dd>'.$oRichText->getFont()->getName().'</dd>');
                        $this->append('<dt>Font Size</dt><dd>'.$oRichText->getFont()->getSize().'</dd>');
                        $this->append('<dt>Font Color</dt><dd>#'.$oRichText->getFont()->getColor()->getARGB().'</dd>');
                        $this->append('<dt>Font Transform</dt><dd>');
                            $this->append('<abbr title="Bold">Bold</abbr> : '.($oRichText->getFont()->isBold() ? 'Y' : 'N').' - ');
                            $this->append('<abbr title="Italic">Italic</abbr> : '.($oRichText->getFont()->isItalic() ? 'Y' : 'N').' - ');
                            $this->append('<abbr title="Underline">Underline</abbr> : Underline::'.$this->getConstantName('\PhpOffice\PhpPresentation\Style\Font', $oRichText->getFont()->getUnderline()).' - ');
                            $this->append('<abbr title="Strikethrough">Strikethrough</abbr> : '.($oRichText->getFont()->isStrikethrough() ? 'Y' : 'N').' - ');
                            $this->append('<abbr title="SubScript">SubScript</abbr> : '.($oRichText->getFont()->isSubScript() ? 'Y' : 'N').' - ');
                            $this->append('<abbr title="SuperScript">SuperScript</abbr> : '.($oRichText->getFont()->isSuperScript() ? 'Y' : 'N'));
                        $this->append('</dd>');
                        if ($oRichText instanceof TextElement) {
                            if ($oRichText->hasHyperlink()) {
                                $this->append('<dt>Hyperlink URL</dt><dd>'.$oRichText->getHyperlink()->getUrl().'</dd>');
                                $this->append('<dt>Hyperlink Tooltip</dt><dd>'.$oRichText->getHyperlink()->getTooltip().'</dd>');
                            }
                        }
                        $this->append('</dl>');
                        $this->append('</dd>');
                    }
                }
                $this->append('</dl></dd></dl>');
            }
            $this->append('</dd>');
        } else {
            // Add another shape
        }
        $this->append('</dl>');
        $this->append('</div>');
    }
    
    protected function getConstantName($class, $search, $startWith = '') {
        $fooClass = new ReflectionClass($class);
        $constants = $fooClass->getConstants();
        $constName = null;
        foreach ($constants as $key => $value ) {
            if ($value == $search) {
                if (empty($startWith) || (!empty($startWith) && strpos($key, $startWith) === 0)) {
                    $constName = $key;
                }
                break;
            }
        }
        return $constName;
    }
}
?>
<title><?php echo $pageTitle; ?></title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
<link rel="stylesheet" href="bootstrap/css/font-awesome.min.css" />
<link rel="stylesheet" href="bootstrap/css/phppresentation.css" />
</head>
<body>
<div class="container">
<div class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="./">PHPPresentation</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="dropdown active">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-code fa-lg"></i>&nbsp;Samples<strong class="caret"></strong></a>
                    <ul class="dropdown-menu"><?php echo $files; ?></ul>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="https://github.com/PHPOffice/PHPPresentation"><i class="fa fa-github fa-lg" title="GitHub"></i>&nbsp;</a></li>
                <li><a href="http://phppresentation.readthedocs.org/en/develop/"><i class="fa fa-book fa-lg" title="Docs"></i>&nbsp;</a></li>
                <li><a href="http://twitter.com/PHPOffice"><i class="fa fa-twitter fa-lg" title="Twitter"></i>&nbsp;</a></li>
            </ul>
        </div>
    </div>
</div>
<?php echo $pageHeading; ?>