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
/*

This Class Make HTML Pages
*/
class Bw_App_RepHTMLPages

	{
	protected $_db;
	private static $_instance = null;

	// Company, Period, Container name...

	public static $companyName;

	public static $periodName;

	public static $cntname;

	// Show rep page spectar

	public static $pgRepOffset;

	public static $pgRepStart;

	public static $pgRepEnd;

	// Show rep page spectar

	public static $pgRepCaheOffset;

	public static $pgRepCacheStart;

	public static $pgRepCacheEnd;

	public static $pgHeader;

 // Static page header

	public static $pgFooter;

 // Static page footer

	public static $pgStyle;

	public static $cntappl;

	public static $cntcode;

	public static $cnttype;

	function __construct()
		{
		}

	// Initialise report
	// _bwrepuuid UUID uniquere report requist indetifieesr
	// General purpuse propertes
	// xmltemplate
	// -------------------------------------------

	public

	function RepHTMLPageInit($_accountid, $__db, $packageName, $_cntappl, $_cntcode, $_cnttype, $_cntrow, $_paramsData)
		{
		wm('BwGetRepPage-startL' . $_cntappl . '-' . $_cntcode . '-' . $_cnttype);
		self::$cntappl = $_cntappl;
		self::$cntcode = $_cntcode;
		self::$cnttype = $_cntcode;
		if (!empty($_REQUEST['_bwrepuuid']))
			{
			$this->_bwrepuuid = $_REQUEST['_bwrepuuid'];
			}
		  else
			{
			$this->_bwrepuuid = uniqid($_accountid->acccode . $packageName, true);
			}

		$this->_companyName = strtoupper(Bw_Company::getCompanyName($_accountid->acccompany));
		$this->_periodName = strtoupper(Bw_Period::getPeriodName(Bw_Company::getActivePeriod($_accountid->acccompany))); // Treba da uzme godinu korisnika za firmu !!!
		$this->_cntname = ($_cntrow['cntname']); // $this->BwGetRepTplBody();
		$tplbody = $_cntrow['tplbody']; // $this->BwGetRepTplBody();

		// $this->lxmltpl = new SimpleXMLElement($tplbody);

		$this->lxmltpl = simplexml_load_string($tplbody);
		Bw_App_RepHTMLPages::$pgStyle = $this->lxmltpl->style->asXML();
		Bw_App_RepHTMLPages::$pgHeader = $this->lxmltpl->repbody->pgheader->children()->asXml();
		Bw_App_RepHTMLPages::$pgFooter = $this->lxmltpl->repbody->pgfooter->children()->asXML();
		if ($this->lxmltpl === false)
			{

			// $this->lxmltpl->tlbody = "<DIV> Failed loading report tamplate!<DIV>";

			}

		if (!empty($_REQUEST['_pgstart']))
			{
			$pgstart = (int)$_REQUEST['_pgstart'];
			}
		  else
			{
			$pgstart = 1;
			}

		$this->pgstart = $pgstart;

		// $this->pgstart = $pgstart;

		if (!empty($_REQUEST['_pgoffset']))
			{
			$pgoffset = (int)$_REQUEST['_pgoffset'];
			}
		  else
			{
			$pgoffset = 1;
			}

		$this->pgoffset = $pgoffset;
		if (!empty($_REQUEST['pgsize']))
			{
			$pgsize = (int)$_REQUEST['pgsize'];
			}
		  else
			{
			$pgsize = 63;
			}

		$this->pgsize = $pgsize;
		$this->pgcurrent = $pgstart;
		$paramsData0 = array(
			'pgstart' => $pgstart,
			'pgoffset' => $pgoffset
		);

		// Search section

		if (!empty($_REQUEST['search']))
			{
			$search = $_REQUEST['search'];
			}
		  else
			{
			$search = '';
			}

		//    if ( $cnfg['adapter'] == 'Oracle' ) {

		$pp = array_change_key_case($paramsData0);

		// $pp = array_merge($paramsData , $pp);
		// xyz

		$pp = array_merge(array(
			"search" => $search
		) , $pp);
		$pp = array_merge(array(
			"_cntappl" => $_cntappl
		) , $pp);
		$pp = array_merge(array(
			"_cntcode" => $_cntcode
		) , $pp);
		$pp = array_merge(array(
			"_cnttype" => $_cnttype
		) , $pp);

		// xyz

		$pp = array_merge(array(
			"tplbody" => $tplbody
		) , $pp);

		// wm( '-----------------------------------' );
		// wm( json_encode( $tProcName ) );
		// wm( json_encode( $_cntrow['cntprocname'] ) );
		// wm( '-----------------------------------' );

		$pp = array_merge(array(
			"search" => $search
		) , $pp);
		$pp = array_merge(array(
			"cntappl" => $_cntappl
		) , $pp);
		$pp = array_merge(array(
			"cntcode" => $_cntcode
		) , $pp);
		$pp = array_merge(array(
			"cnttype" => $_cnttype
		) , $pp);
		$this->pp = $pp;
		/*
		if (isset($this->pp['context'])){
		$locale = Zend_Registry::get( 'locale' );
		$langi  = $locale->getLanguage();

		// $tplbody = $this->BwGetRepTpl($__db,  $_cntappl, $_cntcode, $langi, $pp['context']) ;

		}

		*/
		$this->lfooter = $_cntappl . ' ' . $_cntcode;

		//  }

		return (true);
		}

	function Setup()
		{
		}

	// -Refactoring

	public

	function readRepPage_ZendCashe($start, $end, $_bwrepuuid)
		{

		// $start and $end are the numbers od starting and ending pages
		// provera da li svi fajlovi koji se traze postoje. Ako neki ne postoji onda ce da vratri false

		$cache = Zend_Registry::get('cache');
		$i = $start;

		// Why we are using $end+1 , answer you can find in function readRepPage_LocalStorageCache
		// because these function have the same problem

		while ($i <= $end + 1)
			{
			$idcache = md5('./tempdir/' . $_bwrepuuid . 'pg' . $i . '.html');
			$data = $cache->load($idcache);
			if ($data === false)
				{
				return false;
				}

			$i++;
			}

		$i = $_pgstart;
		while ($i <= $end)
			{
			$idcache = md5('./tempdir/' . $_bwrepuuid . 'pg' . $i . '.html');
			$data = $cache->load($idcache);
			echo $data;
			$i++;
			}

		return true;
		}

	// -Refactoring

	public

	function readRepPage_LocalStorageCache($_pgstart, $_pgend, $_bwrepuuid)
		{

		// provera da li svi fajlovi koji se traze postoje. Ako neki ne postoji onda ce da vratri false

		$i = $_pgstart;

		// Idemo do jedne vise stranice nego sto korisnik trazi i proveravamo da li postoji

		/*
		Zasto ?
		Problem je da baza moze dati manjak potrebnih infomacija u prethodnom reportu za trazenu poslednju stranicu.
		Ako proverimo da postoji sledeca stranica onda smo otklonili svaku sumnju da li je baza dala dovoljan broj infomacija
		za trazenu poslednju stranicu. Ako ta stranica ne postoji onda pusticemo opet report i necemo prikazivati kesiranje.
		U bazi u paketima uvek zahtevamo 5 stranica vise od  poslednje trazene.
		Problem je mogao biti resen da baza izracuna tacno koliko treba da da stranica i tako ne bi imali problema, ali tako se
		vracamo na stare probleme a ovako je mnogo efikasnije i bolje. Posto ce ovo ovde jos da se menja ostavio sam tekst.
		Ko ne razume neka pita Milosa.
		*/
		while ($i <= $_pgend + 1)
			{
			$lfile = __DIR__ . '/../../tempdir/' . $_bwrepuuid . 'pg' . $i . '.html';
			if (!file_exists($lfile))
				{
				return false;
				}

			$i++;
			}

		$i = $_pgstart;
		while ($i <= $_pgend)
			{
			$lfile = __DIR__ . '/../../tempdir/' . $_bwrepuuid . 'pg' . $i . '.html';

			// echo ($lfile);
			// Ako postoji samo da se prikaze

			$fh = fopen($lfile, 'r');
			if ($fh)
				{
				$theData = fread($fh, filesize($lfile) + 1);
				echo ($theData);
				$theData = '';
				fclose();
				}
			  else
				{
				break;
				}

			$i++;
			}

		return true;
		}

	// Preprae report header

	public

	function BwGetRepHeader($acc, $packag)
		{
		$pgrephd = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
		$pgrephd.= '<meta name="_bwrepuuid" content="' . $this->_bwrepuuid . '"/>';
		$pgrephd.= '<meta name="pgstart" content="' . $this->pgstart . '"/>';
		return ($pgrephd);
		}

	// --------------------------------------------------------------
	// BwGetStyleFromReport
	// --------------------------------------------------------------

	public

	function BwGetUserStyle()
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

	public

	function BwGetStyleFromReport()
		{
		$style = "";
		if (isset($this->lxmltpl->style) && !empty($this->lxmltpl->style))
			{
			$style = trim((string)$this->lxmltpl->style);
			}

		if (isset($this->tblstyle))
			{
			$style.= $this->tblstyle;
			}

		return $style;
		}

	// --------------------------------------------------------------
	// BwGetStyle
	// --------------------------------------------------------------

	public

	function BwGetStyle()
		{
		$rStyle = '';
		$rStyle = " <style type='text/css'> ";

		// STYLE FOR MEDIA-SCREEN

		$rStyle.= "
            body{
            max-width:31cm;
            }

            .bwrtbl { max-width: 100%; min-width: 100%; table-layout:fixed; font: normal 11px tahoma,arial,verdana,sans-serif;margin-bottom:80px;  }



            .logo1{font-size: 24px;padding:24px 0px 10px 10px;margin: 0;}
            .logo2{font-size: 16px;padding: 5px 0px 0px 0px;margin: 0;}
            .logo3{font-size: 16px;padding: 5px 0px 0px 0px;margin: 0;}
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


            ";

		// GET STYLE FROM REPORT PAGE

		$rStyle.= $this->BwGetStyleFromReport();

		// STYLE FOR MEDIA-PRINT

		$rStyle.= "
            @media print{
            body{
            min-width:19cm;
            max-width: 19cm;
            }

            #BwRTblFooter{
            page-break-after: always;
            }
            table{
            /*page-break-after: always;*/
            }
            #strPrt{
            page-break-after: always;
            }
            @page{
            margin-left: 1.5cm;
            margin-right: 0.5cm;
            margin-top: 0.5cm;
            margin-bottom: 0.5cm;
            /*size: A4 landscape;
            */
            size: A4 portrait;
            }
            #logoDrugiOktobar{display:none;} 
            #nazivDrugiOktobar1{display:none;}
            #nazivDrugiOktobar2{display:none;}

            }";
		$rStyle.= "   </style>  ";
		$rStyle.= $this->BwGetUserStyle();
		return ($rStyle);
		}

	function MakeHtmlReportPages($_accountid, $__db, $packageName, $_cntappl, $_cntcode, $_cnttype, $_cntrow, $_paramsData, $_repType)
		{

		// echo ' MakeHtmlReportPages';

		static $pgRepOffset;
		static $pgRepStart;
		static $pgRepEnd;
		static $pgRepCaheOffset;
		static $pgRepCacheStart;
		static $pgRepCacheEnd;
		$lTbHeader = true;
		$lTbfooter = true;
		$format = $_repType; //!!!
		$_pg = 0; //Page

		// ------------------

		static $lHTMLPages = array();

		// static $lrow = array( "foo" => "bar", "bar" => "foo");

		static $lrow = array();
		$this->accountid = $_accountid;
		$this->db = $__db;
		$this->packageName = $packageName;
		$this->_ox = '';

		// -----------------------
		// Ovde pita da li vec postoji to sto zelis da prikazes ( npr: dao je vec bio prvu stranu a sada hoce n-tu n )
		// Ako postoji, prikaze i vrati se nazad
		// Ako ne postoji
		// Proveri token da li je to vec zadat report, ako nije ide noramalni i pravi ga
		// Ako je to aktuelni report - samo moze da ceka da se napravi ta straa. S vremena na vreme ce proverita da li to stojai //return;

		$_bwrepuuid = $_REQUEST['_bwrepuuid']; //

		// Page report show spectar

		if ($format == "HTML")
			{
			if ($this->readRepPage_ZendCashe($pgRepStart, $pgRepEnd, $_bwrepuuid))
				{
				return;
				}
			  else
				{
				if ($this->readRepPage_LocalStorageCache($pgRepStart, $pgRepEnd, $_bwrepuuid))
					{
					return;
					}
				}
			}
		  else
			{
			if ($format == "PDF")
				{

				// daj da vidimo da li imamo kes za pdf...

				}
			}

		// ------------------

		$this->RepHTMLPageInit($_accountid, $__db, $packageName, $_cntappl, $_cntcode, $_cnttype, $_cntrow, $_paramsData);

		// -------------------

		$_companyName = $this->_companyName;
		$_periodName = $this->_periodName;

		// require "PageCreation_HTML_Process.php";
		// require "XML_Tag.php";
		// /$xmlRepTemplate = new Bw_App_XMLTreeViewHTMLReport();

		$lHTMLPages = new Bw_App_HTMLPagesProcessor();

		// /  $xmlRepTemplate->init($this->lxmltpl->body,null,"XML");
		// $lHTMLPages->GetTemplatePageHeader($this->lxmltpl->pgheader);
		// $lHTMLPages->GetTemplatePageFooter($this->lxmltpl->pgfooter);

		$lHTMLPages->_cntname = $this->_cntname;
		$lHTMLPages->_companyName = $this->_companyName;
		$lHTMLPages->_periodName = $this->_periodName;
		$lHTMLPages->_repType = $_repType;
		/*
		$this->pgRepOffset = $this->pgRepOffset;
		$this->pgRepStart = $pgRepStart;
		$this->pgRepEnd = $this->pgRepEnd;
		$this->pgRepCaheOffset = pgRepCaheOffset;
		$this->pgRepCacheStart = $this->pgRepCacheStart;
		$this->pgRepCacheEnd = $this->pgRepCacheEnd;
		*/

		//  $lHTMLPages->GetCurrentHeadersFooters($this->lxmltpl);
		// $xmlRepTemplate->htmlPage = $this->pgstart;//pocinjenam od trazene pocetne strane. Ako zelimo da krecemo iz pocetka i da pravimo za sve onda cemo morati da to ukinemo ovde.
		//   $lHTMLPages->BwGetRepHeader =  $this->BwGetRepHeader($_accountid, $packageName);

		$lHTMLPages->BwGetStyle = $this->BwGetStyle();
		$_companyname = $this->_companyName;
		$_periodname = $this->_periodName;
		$_reportname = $this->_cntname;

		// Report Page Header Stack

		$lpgHeader0 = '<div><div class="pgl">$_companyname</div><div class="pgc"></div><div class="pgr">$_periodname</div></div>';
		$lpgHeader = Bw_App_RepHTMLPages::$pgHeader;

		//            $str = ($lpgHeader =='') ? $lpgHeader0 : $lpgHeader;

		$str = str_replace(array(
			"\""
		) , "'", $str);
		eval("\$str1 = \"$str\";");
		$lHTMLPages->pgheaderStatic = $str1;

		// Report Page footer Stack

		$lpgFooter0 = '';
		$lpgFooter = Bw_App_RepHTMLPages::$pgFooter;
		$str = ($lpgFooter == '') ? $lpgFooter0 : $lpgFooter;
		$str = str_replace(array(
			"\""
		) , "'", $str);
		eval("\$str1 = \"$str\";");
		$lHTMLPages->pgfooterStatic = $str1;

		// ======================================================================

		$this->rootNode = new Bw_App_RepNode($this->_bwrepuuid);

		// ======================================================================

		$row = array();
		$lrow = array();

		// $this->rootNode->RepNode2Html($lHTMLPages, $this->rootNode, $lrow, $this->lxmltpl->reptitle->body, $this->accountid, $this->db, $this->packageName, $this->pp,$_companyName,$_periodName);
		// $this->pgi++;

		$this->oi = 1000;
		$tagname = '';

		// /            $this->rootNode->RepNode2Html($lHTMLPages, $this->rootNode, $lrow, $this->lxmltpl->repbody->body, $this->accountid, $this->db, $this->packageName, $this->pp,$_companyName,$_periodName);

		$rv0 = $this->rootNode->RepNode2Html($tagname, $lHTMLPages, $this->rootNode, $lrow, $this->lxmltpl->repbody, $this->accountid, $this->db, $this->packageName, $this->pp, $_companyName, $_periodName);

		// $this->pgi++;
		//  $this->oi = 1000;
		//    $this->rootNode->RepNode2Html($lHTMLPages, $this->rootNode, $lrow, $this->lxmltpl->repfooter->body, $this->accountid, $this->db, $this->packageName, $this->pp,$_companyName,$_periodName);
		// Ako nije gotov treba da se aktivra
		// kako znam da nije gotov

		if (!$rv0)
			{
			$lHTMLPages->MakeRepPage($this->_bwrepuuid, $lTbHeader, $lTbfooter);
			}

		/*
		foreach ($lHTMLPages->lPages[1] as $key => $value) {
		$strPage .= $value;
		}

		echo $strPage;
		@ob_flush();//using '@' because we do not want to get notice if we do not have anything in buffer
		@flush();
		*/
		set_time_limit(30);
		$dtmx = 30;
		$t0 = microtime(true);
		$dt = (microtime(true) - $t0) * 1000;
		}
	}

?>
