<?php
/**
 * Bw/App/Container.php
 *
 * BelwinCore Open Source Edition
 * Copyright (C) 2008 - 2011 Cocos d.o.o.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Cocos d.o.o., Kosovska 31,
 * Beograd, 11000, Serbia, or email info@cocos.com.
 */
class Bw_App_HTMLPagesProcessor

	{
	protected $_db;
	private static $_instance = null;
	protected $parentNode;
	protected $strParent;
	public $lPages = array(
);
	private $ri = 1; // Row counter from beggining
	public $pgi = 1;

 // Page counter
	public $oi = 1000;

 // Line on page    (soert in debugger need 1000)
	public $pageHeader = "";

 // content of page header for Html page
	public $pageFooter = "";

 // content of page footer for Html page
	public $pageStartSettings = 0;

 // number from which page user want report to start
	public $pageOffsetSettings = 0;

 // number of pages that starts from pageStartSettings
	public $_repType = "HTML";

	// /public $BwGetRepHeader="";    //Content of html head tag
	public $RepPageStyle = "";

 // Content of CSS style
	public $_companyName;

	public $_periodName;

	public $_cntname;

 // Cnt name of report
	private $TagStack = array();
	private $CurrentHeaders = array();
	private $CurrentFooters = array();
	public $rrow = array(
);

	//    protected $currentNode;
	public $pgBodyOpen = '<body>';

 // Body Open tag
	public $pgHederOpen = '<head>';

 // Body Open tag
	public $pgheaderStatic = '';

 // Default Static Page Header
	public $pgfooterStatic = '';

 // Default Static Page Footer
	public $pgheaderstack0 = array();
	public $pgheaderstack = array();
	public $pgfooterstack = array();
	public $pgHederClose = '</head>';

 // Head Close tag
	public $pgBodyClose = '</body>';

 // Body Close tag

	public $zi = 0;

 // Zebra indicator
	// Page settings

	public $pageOrientation;

	public $fontSize;

	public $pageFormat;

	static public $_tradename;

	static public $_pgtext;

	static public $_sysdate;

	function __construct()
		{
		}

	public

	//------------------------------------------------------------
	//
	//------------------------------------------------------------
	function BwGetRepHeader($_bwrepuuid)
		{
		$pgrephd = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
		$pgrephd.= '<meta name="_bwrepuuid" content="' . $_bwrepuuid . '"/>';
		$pgrephd.= '<meta name="pgstart" content="' . $this->pgi . '"/>';
		return ($pgrephd);
		}

	// --------------------------------------------------------------
	// Report section
	// --------------------------------------------------------------
	public	function BwGetRepFooter()
		{
		$strft.= '<div>                        ';
		$strft.= '<hr/>';
		$strft.= '</div> ';
		return ($strft);
		}

	// --------------------------------------------------------------
	// BwGetStyleFromReport
	// --------------------------------------------------------------
	public	function BwGetUserStyle()
		{
		$style = "<style>";
		$style.= ".bwtr0 {color: #000; background: #e0e0e0;-webkit-print-color-adjust:exact;}";
		$style.= ".bwtr1 {} ";
		$style.= "</style>";
		return $style;
		}

	// --------------------------------------------------------------
	// BwGetStyleFromReport
	// --------------------------------------------------------------
	public	function BwGetStyleFromReport()
		{
		/*
		$style="";
		if(isset($this->lxmltpl->style) && !empty($this->lxmltpl->style) ){
		$style = trim ((string) $this->lxmltpl->style);
		}

		if (isset($this->tblstyle)) {
		$style.=$this->tblstyle;
		}

		return $style;
		*/
		return Bw_App_RepHTMLPages::$pgStyle;
		}

	// --------------------------------------------------------------
	// BwGetStyle
	// --------------------------------------------------------------
	public	function BwGetStyle()
		{
		$rStyle = '';
		$rStyle = " <style type='text/css'> ";

		// STYLE FOR MEDIA-SCREEN
		//    border-spacing: 0;
		//    border-collapse: collapse;

		$rStyle.= "
            .bwtbtitle {letter-spacing:5}
            .bwrtbl { width: 100%; border-collapse: collapse; font: normal 11px tahoma,arial,verdana,sans-serif;  }
            .logo1{font-size: 24px;padding:24px 0px 10px 10px;margin: 0;}
            .logo2{font-size: 16px;padding: 5px 0px 0px 0px;margin: 0;}
            .logo3{font-size: 16px;padding: 5px 0px 0px 0px;margin: 0;}

            .pgl{ float: left; width: 40%;  }
            .pgc{ display: inline-block; width: 20%; }
            .pgr{ float: right; width: 40%; text-align: right; }


            .num { text-align: right; width:100px; }
            .bwsifra { text-align: center; width:100px; }
            .bwblank { background:#ffffff; -webkit-print-color-adjust:exact;   font-family: tahoma,arial,verdana,sans-serif;    font-size: 9px; }
            .bwtr0 {color: #000; background: #f0f0f0;-webkit-print-color-adjust:exact;}
            .bwtr1 {} 
            .bwtg1 {color: #000; background: #efefef; font-weight: bold;-webkit-print-color-adjust:exact;} 
            .bwtg2 {color: #000; background: #e8e8e8; font-weight: bold;-webkit-print-color-adjust:exact;} 
            .bwtg3 {color: #000; background: #e0e0e0; font-weight: bold;-webkit-print-color-adjust:exact;} 
            .bwtg4 {color: #000; background: #dddddd; font-weight: bold;-webkit-print-color-adjust:exact;} 
            .p__tr {color: #000; background: #e8e8e8;-webkit-print-color-adjust:exact;} 
            .p__ts {color: #000; background: #e8e8e8;-webkit-print-color-adjust:exact;} 
            .bwts {color: #000; background: #e8e8e8;-webkit-print-color-adjust:exact;} 
            .bwtt {color: #000; background: #b8b8b8;-webkit-print-color-adjust:exact;} 
            .bwgt {color: #000; background: #aaaaaa;-webkit-print-color-adjust:exact;} 
            .rfooter { background:#f0f0f0; -webkit-print-color-adjust:exact;   font-family: tahoma,arial,verdana,sans-serif;    font-size: 9px; } 
            .rtl { font-family: tahoma,arial,verdana,sans-serif; font-size: 18px; display: table; margin: 5 auto;} 
            .rst { font-family: tahoma,arial,verdana,sans-serif; font-size: 14px;  margin: 5 auto; } 
            h1{font-size: 24px;padding:24px 0px 10px 10px;margin: 0;}
            h2{font-size: 16px;padding: 5px 0px 0px 0px;margin: 0;}
            h3{font-size: 14px;padding: 5px 0px 0px 0px;margin: 0;}
            h4{font-size: 14px;padding: 5px 0px 0px 0px;margin: 0;}
            h5{font-size: 14px;padding: 5px 0px 0px 0px;margin: 0;}
            .Bw_cntname{font-size: 24px;padding:12px 0px 5px 5px;margin: 0;}
            .rfooter { background:#f0f0f0;  -webkit-print-color-adjust:exact;  font-family: tahoma,arial,verdana,sans-serif;    font-size: 9px; } 
            .rftl {  font-family: tahoma,arial,verdana,sans-serif;    font-size: 10px;  float: left; } 
            .rftc {  font-family: tahoma,arial,verdana,sans-serif;    font-size: 10px;  float: left; } 
            .rftr {  font-family: tahoma,arial,verdana,sans-serif;    font-size: 10px;  float: right;}   
            .bwrtbl tr td{text-overflow: ellipsis; white-space: nowrap; overflow: hidden;padding: 0 0.2em;   }
            .bwrtbl tr th{ /*text-overflow: ellipsis; white-space: nowrap;*/ overflow: hidden;padding: 0 0.2em;background:#e0e0e0; -webkit-print-color-adjust:exact;   }
            .bwreptitle { text-align: center; width:100px; }
            </style> ";
		return ($rStyle);
		}

	public	function BwGetPrintStyle0()
		{
		$rStyle = " <style> 
            @media print{
            body{
            min-width:19cm;
            max-width: 19cm;
            }

            #BwRTblFooter{
            page-break-after: always;
            }
            table{
            page-break-after: always;
            }
            @page{
            margin-left: 1.5cm;
            margin-right: 0.5cm;
            margin-top: 0.5cm;
            margin-bottom: 0cm;
            /*size: A4 landscape;
            */
            size: A4 portrait;
            }
            #logoDrugiOktobar{display:none;} 
            #nazivDrugiOktobar1{display:none;}
            #nazivDrugiOktobar2{display:none;}
*/
            }";
		$rStyle.= "   </style>  ";
		$rStyle.= $this->BwGetUserStyle();
		return ($rStyle);
		}

	//------------------------------------------------------------
	//
	//------------------------------------------------------------
	public	function BwGetPrintStyle() { }


	
	//------------------------------------------------------------
	//
	//------------------------------------------------------------
	public	function AddStringToPage($pstr, $lineoffset, $rr)
		{
		/*
		if ( ( $pstr == '</table>'  )  && ($this->oi ==1000 or $this->oi ==1000 ) ){           //Fix, Uggly
		return;
		}    */
		$row = $rr;
		$lTbHeader = true;
		$lTbfooter = true;
		$zi = $this->zi;
		extract($this->rrow);
		$_cntname = $this->_cntname;
		$_companyName = $this->_companyName;
		$_periodName = $this->_periodName;
		$_pg = & $this->pgi;
		$str = $pstr;
		$str = str_replace(array(
			"\""
		) , "'", $str);
		eval("\$str1 = \"$str\";");
		if (!isset($this->lPages[$this->pgi]))
			{
			$this->lPages[$this->pgi] = null;
			}

		if (!isset($this->lPages[$this->pgi][$this->oi]))
			{
			$this->lPages[$this->pgi][$this->oi] = null;
			}

		$this->lPages[$this->pgi][$this->oi].= $str1;
		if ($this->oi > 1003)
			{
			wm($this->oi);
			}

		$this->oi = $this->oi + $lineoffset;
		$LineLimitPerPage = 63;
		if ($this->oi - 1000 > $LineLimitPerPage)
			{
			$lTbHeader = true;
			if ($this->pgi == 1)
				{
				}

			if ($this->pgi >= 7)
				{
				}

			if (!in_array($pstr, array(
				'</td>',
				'</tr>',
				'</table>'
			)))
				{
				return ($this->MakeRepPage($lTbHeader, $lTbfooter));
				}
			}

		return false;
		}

	//------------------------------------------------------------
	//
	//------------------------------------------------------------
	public	function MakeRepPage($_bwrepuuid, $pTbHeader, $pTbfooter)
		{

		// while (@ob_end_flush()); // lose radi u mozili i u IE-u . to jest onda ne radi output

		$_companyname = Bw_App_RepHTMLPages::$companyName;
		$_periodname = Bw_App_RepHTMLPages::$periodName;
		$_reportname = Bw_App_RepHTMLPages::$cntname;
		$_lastline = '<br/>';
		self::$_tradename = 'COCOS Belwin Belkonto: ' . Bw_App_RepHTMLPages::$cntappl . ' ' . Bw_App_RepHTMLPages::$cntcode;
		self::$_pgtext = Bw_App_RepHTMLPages::$cntname . ' - strana: ' . $this->pgi;
		self::$_sysdate = 'Г… tampano: ' . date_format(date_create() , 'Y-m-d H:i:s');
		$this->RepPageStyle = $this->BwGetStyle();
		$this->RepPageStyle.= $this->BwGetStyleFromReport();
		$this->RepPageStyle.= $this->BwGetUserStyle();
		if ($this->_repType == "HTML")
			{
			$this->RepPageStyle.= $this->BwGetPrintStyle();
			}

		$strPage = '<html>' . $this->pgHederOpen;
		$strPage.= $this->BwGetRepHeader($_bwrepuuid);
		$strPage.= $this->RepPageStyle;

		//            $strPage .='</head><body>';

		$strPage.= $this->pgHederClose;
		$strPage.= $this->pgBodyOpen;
		$strPage.= $this->pgheaderStatic;
		foreach($this->pgheaderstack0 as $lstr)
			{
			$strPage.= $lstr;
			}

		$this->pgheaderstack0 = $this->pgheaderstack;
		foreach($this->lPages[$this->pgi] as $key => $value)
			{
			$strPage.= $value;
			}

		foreach($this->pgfooterstack as $lstr)
			{
			$strPage.= $lstr;
			}

		$strPage.= $this->pgfooterStatic;
		$strPage.= $this->pgBodyClose;
		$strPage.= '</html>';
		if ($this->pgi >= Bw_App_RepHTMLPages::$pgRepStart && $this->pgi <= Bw_App_RepHTMLPages::$pgRepEnd)
			{
			if ($this->_repType == "HTML")
				{
				echo $strPage;
				@ob_flush(); //using '@' because we do not want to get notice if we do not have anything in buffer
				@flush();
				}
			elseif ($this->_repType == "PDF")
				{
				$this->printPDFPage($strPage);
				}
			elseif ($this->_repType == "XLS")
				{
				echo $strPage;
				}
			elseif ($this->_repType == "DOC")
				{
				echo $strPage;
				}
			elseif ($this->_repType == "TXT")
				{
				}
			}

		$this->WriteRepPage($strPage);
		$this->oi = 1000;
		$this->pgi++;

		if ($this->pgi >= Bw_App_RepHTMLPages::$pgRepEnd)
			{
			return (true);
			}
		}

	//------------------------------------------------------------
	//
	//------------------------------------------------------------
	public	function printPDFPage($htmlPage)
		{
		require_once ('./Bw/Library/tcpdf/lang/eng.php');

		require_once ('./Bw/Library/tcpdf/config/tcpdf_config.php');

		require_once ('./Bw/Library/tcpdf/tcpdf.php');

		// only odf settings

		if (isset($_REQUEST['ORIENTATION']) && !empty($_REQUEST['ORIENTATION']))
			{
			$this->pageOrientation = $_REQUEST['ORIENTATION'];
			}
		  else
			{
			$this->pageOrientation = PDF_PAGE_ORIENTATION;
			}

		if (isset($_REQUEST['_repformat']) && !empty($_REQUEST['_repformat']))
			{
			$this->pageFormat = $_REQUEST['_repformat'];
			}
		  else
			{
			$this->pageFormat = PDF_PAGE_FORMAT;
			}

		if (isset($_REQUEST['_repfontsize']) && !empty($_REQUEST['_repfontsize']))
			{
			$this->fontSize = $_REQUEST['_repfontsize'];
			}
		  else
			{
			$this->fontSize = 10;
			}

		$pdf = new TCPDF($this->pageOrientation, PDF_UNIT, $this->pageFormat, true, 'UTF-8', false);
		$preferences = array(
			'HideToolbar' => false,
			'HideMenubar' => false,
			'HideWindowUI' => false,
			'FitWindow' => true,
			'CenterWindow' => true,
			'DisplayDocTitle' => true,
			'NonFullScreenPageMode' => 'UseNone',
			'ViewArea' => 'BleedBox',
			'ViewClip' => 'BleedBox',
			'PrintArea' => 'CropBox',
			'PrintClip' => 'CropBox',
			'PrintScaling' => 'AppDefault',
			'Duplex' => 'DuplexFlipLongEdge',
			'PickTrayByPDFSize' => true,
			'PrintPageRange' => array() ,
			'NumCopies' => 2
		);
		$pdf->setViewerPreferences($preferences);
		$pdf->SetFont('freeserif', '', $this->fontSize);
		$pdf->AddPage();
		$pdf->writeHTML($htmlPage, true, false, true, false, '');

		// reset pointer to the last page

		$pdf->lastPage();

		// kreiranje fajla

		$pg = $this->pgi;
		$_bwrepuuid = $_REQUEST['_bwrepuuid'];
		$lfile = './tempdir/' . $_bwrepuuid . 'pg' . $pg . '.pdf';
		$embed = 'tempdir/' . $_bwrepuuid . 'pg' . $pg . '.pdf';

		// proveravam da li fajl postoji kako bih isao overwite

		if (file_exists($lfile)) unlink($lfile);
		$pdf->Output($lfile, 'F');
		echo '<embed width="100%" height="100%" name="plugin" src="./' . $embed . '" type="application/pdf">';
		}

	//------------------------------------------------------------
	//
	//------------------------------------------------------------
	public	function WriteRepPage($strPage)
		{
		$pg = $this->pgi;
		$_bwrepuuid = $_REQUEST['_bwrepuuid'];
		$cache = Zend_Registry::get('cache');

		// $cache->clean();

		$idcache = md5('./tempdir/' . $_bwrepuuid . 'pg' . $pg . '.html');
		if ($cache->getOption('caching') === false)
			{
			$lfile = './tempdir/' . $_bwrepuuid . 'pg' . $pg . '.html';
			$fh = fopen($lfile, 'w');
			fwrite($fh, $strPage);
			fclose($fh);
			}
		  else
			{
			if (!($cache->test($idcache)))
				{ // Ako ne postoji neka ga sacuva
				$cache->save($strPage, $idcache);
				}
			  else
				{
				}
			}
		}

	//------------------------------------------------------------
	//
	//------------------------------------------------------------
	public	function CreateBarcode($value)
		{
		extract($this->rrow);
		$str0 = (string)$value['barcodeValue'];
		$str = str_replace(array(
			"\""
		) , "'", $str);
		eval("\$str1 = \"$str0\";");
		return "src='data:image/png;base64," . $this->Barcode($str1) . "'";
		}

	//------------------------------------------------------------
	//
	//------------------------------------------------------------
	public	function Barcode($code)
		{

		// Only the text to draw is required

		$barcodeOptions = array(
			'text' => $code
		);

		// No required options

		$rendererOptions = array();
		$resource = Zend_Barcode::draw('code39', 'image', $barcodeOptions, array());
		ob_start();
		imagepng($resource);
		$data = ob_get_clean();
		return base64_encode($data);
		}
	}

?>