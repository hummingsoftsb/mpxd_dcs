<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Shape\Drawing;
use PhpOffice\PhpPresentation\Shape\MemoryDrawing;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Border;
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

require_once __DIR__ . '/../third_party/phpppt/src/PhpPresentation/Autoloader.php';
Autoloader::register();

require_once __DIR__ . '/../third_party/phpppt/src/Common/Autoloader.php';
\PhpOffice\Common\Autoloader::register();

$objPHPPresentation = new PhpPresentation();

class Phpppt {

    var $objPHPPresentation;
    var $currentSlide;
    //var $writers = array('PowerPoint2007' => 'pptx', 'ODPresentation' => 'odp');
    var $writers = array('PowerPoint2007' => 'pptx');

    function __construct() {
        $this->objPHPPresentation = new PhpPresentation();
        $this->currentSlide = $this->objPHPPresentation->getActiveSlide();
    }

    function write($phpPresentation, $filename, $writers) {
        $result = '';

        // Write documents
        foreach ($writers as $writer => $extension) {
            $result .= date('H:i:s') . " Write to {$writer} format";
            if (!is_null($extension)) {
                $xmlWriter = IOFactory::createWriter($phpPresentation, $writer);
                $xmlWriter->save(__DIR__ . "/{$filename}.{$extension}");
                rename(__DIR__ . "/{$filename}.{$extension}","./journalimage/{$filename}.{$extension}");
            } else {
                $result .= ' ... NOT DONE!';
            }
            $result .= EOL;
        }

        //$result = $this->getEndingNotes($writers);

        return 'journalimage/'.$filename.'.pptx';
    }

    function getEndingNotes($writers) {
        $result = '';

        // Do not show execution time for index
		/*
        if (!IS_INDEX) {
            $result .= date('H:i:s') . " Done writing file(s)" . EOL;
            $result .= date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MB" . EOL;
        }*/

        // Return
        if (CLI) {
            $result .= 'The results are stored in the "results" subdirectory.' . EOL;
        } else {
            //if (!IS_INDEX) {
                $types = array_values($writers);
                $result .= '<p>&nbsp;</p>';
                $result .= '<p>Results: ';
                foreach ($types as $type) {
                    if (!is_null($type)) {
                        $resultFile = './journalimage/mpxdimage.' . $type;
                        if (file_exists($resultFile)) {
                            $result .= "<a href='{$resultFile}' class='btn btn-primary'>{$type}</a> ";
                        }
                    }
                }
                $result .= '</p>';
            //}
        }

        //return $result;
		//return 'journalimage/mpxdimage.'.$type;
    }

    function generateTitle($project, $date) {
        $shape = $this->currentSlide->createRichTextShape()
                ->setHeight(30)
                ->setWidth(700)
                ->setOffsetX(10)
                ->setOffsetY(0);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $textRun = $shape->createTextRun(strtoupper($project) . ' PHOTO’S AS AT ' . strtoupper($date));
        $textRun->getFont()->setBold(true)
                ->setSize(16)
                ->setColor(new Color('000000'));
    }

    function generatepicture($pic, $description, $offsetx, $offsety, $textoffsetx, $textoffsety) {
        $shape = new Drawing();
        $shape->setName('')
                ->setDescription('')
                ->setPath($pic)
                //->setHeight(255)
				//->setWidth(338)
                ->setOffsetX($offsetx)
                ->setOffsetY($offsety)->getBorder()
        ->setLineStyle(Border::LINE_SINGLE)
        ->setLineWidth(1)
        ->getColor()->setARGB('000000');
        $this->currentSlide->addShape($shape);

        $shape = $this->currentSlide->createRichTextShape()
                ->setHeight(15)
                ->setWidth(350)
                ->setOffsetX($textoffsetx)
                ->setOffsetY($textoffsety);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $textRun = $shape->createTextRun($description);
        $textRun->getFont()->setBold(false)
                ->setSize(11)
                ->setColor(new Color('000000'));
    }

	function generatelogo() {
        $shape = new Drawing();
        $shape->setName('')
                ->setDescription('')
                ->setPath('./files/logo.png')
                ->setHeight(32)
				//->setWidth(400)
                ->setOffsetX(0)
                ->setOffsetY(0)->getBorder();
        //->setLineStyle(Border::LINE_SINGLE)
        //->setLineWidth(1)
        //->getColor()->setARGB('000000');
        $this->currentSlide->addShape($shape);
    }
	
	function generateFooter($date,$pageno) {
        $shape = $this->currentSlide->createRichTextShape()
                ->setHeight(15)
                ->setWidth(200)
                ->setOffsetX('-5')
                ->setOffsetY(937);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $textRun = $shape->createTextRun($date);
        $textRun->getFont()->setBold(false)
                ->setSize(11)
                ->setColor(new Color('848484'));
				
		$shape = $this->currentSlide->createRichTextShape()
                ->setHeight(15)
                ->setWidth(200)
                ->setOffsetX(300)
                ->setOffsetY(937);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $textRun = $shape->createTextRun("Project Director's Office");
        $textRun->getFont()->setBold(false)
                ->setSize(11)
                ->setColor(new Color('848484'));
				
		$shape = $this->currentSlide->createRichTextShape()
                ->setHeight(15)
                ->setWidth(50)
                ->setOffsetX(675)
                ->setOffsetY(937);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $textRun = $shape->createTextRun($pageno);
        $textRun->getFont()->setBold(false)
                ->setSize(11)
                ->setColor(new Color('848484'));
    }
	
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
	
	function removefirstslide(){
		$this->objPHPPresentation->removeSlideByIndex(0);
	}
	
	function newslide(){
		$this->currentSlide = $this->objPHPPresentation->createSlide();
	}
	
	function gowrite($baseurl){
		//$fileloc = $this->write($this->objPHPPresentation, 'mpxdimage'.date('dmYHis'), $this->writers);
		$fileloc = $this->write($this->objPHPPresentation, 'mpxdimage_'.date('dmY'), $this->writers);
		//echo $baseurl.$fileloc;
		header("Location: ".$baseurl.$fileloc);
	}

    function t() {
        return $this->currentSlide;
    }

}
