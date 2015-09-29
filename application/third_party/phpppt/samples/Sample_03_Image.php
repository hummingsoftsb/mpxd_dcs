<?php
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Shape\Drawing;
use PhpOffice\PhpPresentation\Shape\MemoryDrawing;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Autoloader;
use PhpOffice\PhpPresentation\Settings;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Slide;
use PhpOffice\PhpPresentation\AbstractShape;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Shape\RichText\BreakElement;
use PhpOffice\PhpPresentation\Shape\RichText\TextElement;
use PhpOffice\PhpPresentation\Style\Bullet;

//error_reporting(E_ALL);
define('CLI', (PHP_SAPI == 'cli') ? true : false);
define('EOL', CLI ? PHP_EOL : '<br />');
define('SCRIPT_FILENAME', basename($_SERVER['SCRIPT_FILENAME'], '.php'));
define('IS_INDEX', SCRIPT_FILENAME == 'index');

require_once __DIR__ . '/../src/PhpPresentation/Autoloader.php';
Autoloader::register();

require_once  __DIR__ . '/../src/Common/Autoloader.php';
\PhpOffice\Common\Autoloader::register();

// Set writers
$writers = array('PowerPoint2007' => 'pptx', 'ODPresentation' => 'odp');


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

// Create new PHPPresentation object
$objPHPPresentation = new PhpPresentation();

// Create slide
$currentSlide = $objPHPPresentation->getActiveSlide();

function generateTitle($project,$date){
	global $currentSlide;
	$shape = $currentSlide->createRichTextShape()
      ->setHeight(30)
      ->setWidth(900)
      ->setOffsetX(10)
      ->setOffsetY(0);
	$shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
	$textRun = $shape->createTextRun($project.' AS AT '.$date);
	$textRun->getFont()->setBold(true)
					   ->setSize(16)
					   ->setColor( new Color( 'FFE06B20' ) );
}

function generatepicture($pic,$description,$offsetx,$offsety,$textoffsetx,$textoffsety){
	global $currentSlide;
	$shape = new Drawing();
	$shape->setName('')
		  ->setDescription('')
		  ->setPath($pic)
		  ->setHeight(280)
		  ->setOffsetX($offsetx)
		  ->setOffsetY($offsety);
	$currentSlide->addShape($shape);

	$shape = $currentSlide->createRichTextShape()
		  ->setHeight(15)
		  ->setWidth(400)
		  ->setOffsetX($textoffsetx)
		  ->setOffsetY($textoffsety);
	$shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
	$textRun = $shape->createTextRun($description);
	$textRun->getFont()->setBold(false)
					   ->setSize(12)
					   ->setColor( new Color( '000000' ) );
}

generateTitle('V4 PROJECT PROGRESS','11TH JULY 2014');
generatepicture('./resources/map-thumb.jpg','Stressing work for LG1 in progress at span TT33R – TT32R (near KGPA)',100,30,40,310); //pic1
generatepicture('./resources/map-thumb.jpg','Stressing work for LG1 in progress at span TT33R – TT32R (near KGPA)',580,30,520,310); //pic2
generatepicture('./resources/map-thumb.jpg','Stressing work for LG1 in progress at span TT33R – TT32R (near KGPA)',100,370,40,650); //pic3
generatepicture('./resources/map-thumb.jpg','Stressing work for LG1 in progress at span TT33R – TT32R (near KGPA)',580,370,520,650); //pic4
// Save file
echo write($objPHPPresentation, basename(__FILE__, '.php'), $writers);