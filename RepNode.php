<?php
    /*
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
	Simple and effective xml Node TO html parser
	--DB cursor (row) tag 
	--Page Header, Page Footer 
	--Count line and pages on ":ine matric printer". It's suitable for long reports.

	*/
    class Bw_App_RepNode
    {
        protected $_db;

        private static $_instance = null;
        protected $parentNode;
        protected $strParent;
        public $cnt=0;
        public $rtype;


		// protected $currentNode;
        function __construct(){

		}



		//For calling procedure from report
        public function dorProc($accountid, $db, $packageName, $lrpoc, $pp ) {
            $rtrCData = array();

            if (isset($prow)) {
                $pp = array_merge($pp, $prow);              // get last prow (to do: stack implementation for prow access)
            }
            //--------------------------------------
            
            //--------------------------------------

            //$t0 = microtime(true);
            $stmt = Bw_App_Container::getInstance(NULL)->bindVPlSqlProcedure( $accountid, $db, $packageName, $lrpoc, $pp, $rtrCData );
            //$dt = (microtime(true) - $t0)*1000;                
            //  wm('vreme za konto '. $pp['konto'].': '.$dt);
            $rtrCData =  isset( $pp['out']) ?   $pp['out']: null ;          
            //$crecData1 =  isset( $this->pp['out1']) ?   $this->pp['out1']: null ;          

            //wm( '-----------------------------------' );
            //oci_execute( $stmt );
            $r = oci_execute($stmt);
            if (!$r) {
                $error = oci_error($db->getConnection());
                echo "DB execution error:." . $error['message'];
            }
            if ($pp['valstatus'] == 1) {
                echo "Error: " .  $pp['sqlcode'].$pp['sqlerrm'];
            }
            //wm( '-----------------------------------' );
            $i = 0;
            return( $rtrCData );            
        }

		
		
		//---------------------------------------------------------------
		// Data cursor Processor - Transform data cursor to html segment
		//---------------------------------------------------------------
		public function rCursor2Html0($tagname, $htmlAttributes, $rtrCData, $lcursor, $lHTMLPages, $pnode, $prow, $xmlnode, $accountid, $db, $packageName, $pp,$_companyName,$_periodName ) {
            
            if ($rtrCData){				// Read cursor and make report
                oci_set_prefetch( $rtrCData, 63 );
                oci_execute( $rtrCData );
            }
			//$rv = false;
            //static $$lcursor;            
            //wm("go to loop and fetch data ");
            while (($$lcursor = oci_fetch_array($rtrCData, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
                $lHTMLPages->zi = ($lHTMLPages->zi+1)%2;			// Calculate "Zebra Lines" indicator 
                $$lcursor = array_change_key_case( $$lcursor );		// Fix ORACLE upper case issues

                $lHTMLPages->rrow[(string) $lcursor]= $$lcursor;	//Get pointer of ORACLE cursor
                $prow = $$lcursor;	//Get row of data from cursor

                // $lrow = $$lcursor;
                if   ($xmlnode->children()->count()>0) {        // If xmlnode have more then one childrens
                    $currentNode = new Bw_App_RepNode();        //Init current node (processing <tr> tag ...)

					//Calling HTML rendering of xml node 
					$rv = $currentNode->RepNode2HtmlPart2($tagname, $htmlAttributes, $lHTMLPages, $this, $prow, $xmlnode , $accountid,  $db, $packageName, $pp ,$_companyName,$_periodName);

					if  ($rv){ //No more nodes, exit 
                        return true;
                    }

				}  else {
                    extract($lHTMLPages->rrow);
                    $str1 = (string) $xmlnode;
                    $rv = $lHTMLPages->AddStringToPage($str1, 1, $prow );	
                    if  ($rv){ 
                       // No more recored, exit point no 2.
                        return true;
                    }
                }

            }   
			//oci_free_statement($rtrCData);
            return false;
        }

		
		// ----------------------------------------------------------
		// Report xml node processor. Part 2
		// ----------------------------------------------------------
        public function RepNode2HtmlPart2($tagname, $htmlAttributes, $lHTMLPages, $pnode, $prow, $xmlnode, $accountid, $db, $packageName, $pp,$_companyName,$_periodName ) {
        //--------------------------------------------------------------------------
            $lineoffset=0;	//reset lineffest
            $row = $prow;	//Init current row
            $rv =false;		//
            $lcolgroup ='';	
            $lheader ='';	//Header
            $lfooter ='';	//Footer

			// Page Header can be only one active and must be memorised. Must have own processing algorithm.
            if ( $this->rtype == 'pgheader' ){		
                return; 
            }

			// Page Footer can be only one active and must be memorised. Must have own processing algorithm.
            if ( $this->rtype == 'pgfooter' ){
                // if ( $tagname == 'pgfooter' ){
                // Just update here pgfooter
                return; // No direct render  for pgfooter
            }

			//Table Header. 
//			if ( $this->rtype == 'thead' ){
//          }

			//Table Footer
//			if ( $this->rtype == 'tfooter' ){
//          }

			//Bar code tag has own specific processor
            if ( $this->rtype == 'barcode' ){
                $htmlAttributes .= $lHTMLPages->CreateBarcode($xmlnode); 
            }

            //------------------------------------------
			// Page body tag
            //------------------------------------------
            if ( $this->rtype  === "pgbody") {          // If we have page body tag we must open body and  insert page header 
                $this->rtype='';
                $lHTMLPages->pgBodyOpen='<'.$tagname.' '.$htmlAttributes.'>';
                $lHTMLPages->pgHeaderOpen='<head>';
                $lHTMLPages->pgBodyClose='</'.$tagname.' '.$htmlAttributes.'>';
                $lHTMLPages->pgHeaderOpen='</head>';

            }
            elseif ( $this->rtype == 'table' ){     // If we have rtype tag table
                $lheader =  '<'.$tagname.' '.$htmlAttributes.'>';
                //wm('array_push put header and  footer on stack');
                $lheader .= $xmlnode->thead->asXml();

                $lheader .= $xmlnode->colgroup->asXml();			// <COLGROUP> tag
                array_push($lHTMLPages->pgheaderstack,$lheader) ;	// For more then one page report page haeder stack must have colgroup string 

                $lfooter =  '</'.$tagname.'>';						// Footer stack
                $lfooter .= $xmlnode->tfoot->asXml();  
                array_push($lHTMLPages->pgfooterstack, $lfooter) ;

                // Show can start here...            
				// As we using html parser on "old line matrics printer" way, we count line and increment on <TR> and <DIV> tags. 
				// All other tags we using on HTML way and keep CSS processing for them
                if(in_array( $tagname , array('tr', 'div')) ) {		// Only <tr> and <div> tag can increment lineoffset counter
                    $lineoffset =1;
                }

				//----------------------------------------------------------------------------------------------------------------------------------
				$rv = $lHTMLPages->AddStringToPage('<'.$tagname.' '.$htmlAttributes.'>', $lineoffset, $prow  );     // Open and make html node for xml node
				//----------------------------------------------------------------------------------------------------------------------------------

				if ($rv){             // If AddStringToPage close the page
                    return(true);
                } 

			} else {   
		
                // For all others xml type elements, show can start here...		if ( $rtype != 'pgbody' ){	
                if ( !in_array($tagname, array('repbody'))  ){
                    if(in_array( $tagname , array('tr', 'div')) ) {		// For <TR> and <DIV> tags increment line counter 
                        $lineoffset =1;
                    }

					if(!in_array( $tagname , array('t2r')) ) {      //t2r is "hide" tag and dosn't need processing. All oters must

						$rv = $lHTMLPages->AddStringToPage('<'.$tagname.' '.$htmlAttributes.'>', $lineoffset, $prow );

						if ($rv){	// If AddStringToPage close the page
                            return(true);
                        }         
                    }

                }
            }


			// If xml node have more then one chlidren, we must recursive process one by one 
			if   ($xmlnode->children()->count() >  0) {         
                $xtplch = $xmlnode->children();
				for ($i = 0; $i < count($xtplch); ++$i) {
					$xtplchii =$xtplch[$i];
					
					//------------------------------------------------------------------------------------------------------------------------------------------
					$currentNode = new Bw_App_RepNode();  
					$rv = $currentNode->RepNode2Html( $tagname, $lHTMLPages, $this, $prow, $xtplchii, $accountid,  $db, $packageName, $pp ,$_companyName,$_periodName);
					//------------------------------------------------------------------------------------------------------------------------------------------

					if ($rv){             // 0- line couter se pomera posle dodavaja elemetea)
						return(true);
					} 
				}
					
            }  else {

				//-------------------------------------------------------------
				//One node processor
				//-------------------------------------------------------------

				extract($lHTMLPages->rrow);       
                $str1 = (string) $xmlnode;

                if(in_array( $tagname , array('tr', 'div')) ) {	//Increment line offset for <TR> and <DIV> tag
                    $lineoffset =1;
                }
                if(!in_array( $tagname , array('t2r')) ) {          //t2r is "hide" tag
                    $rv =$lHTMLPages->AddStringToPage($str1, $lineoffset , $prow );
                    if ($rv){
                        return(true);
                    } 
                }

            }

            // Close tag
            if(in_array( $tagname , array('tr', 'div')) ) {
                $lineoffset =0;
            }

            if ( !in_array($tagname, array('repbody'))  ){         
               if(!in_array( $tagname , array('t2r')) ) {          //t2r don't nee to put put...
                    $rv = $lHTMLPages->AddStringToPage('</'.$tagname.'>', $lineoffset, $prow );
                    if ($rv){ 
                        return(true);
                    } 
                }                
            }

            if ( $tagname == 'table' ){
                array_pop($lHTMLPages->pgheaderstack) ;
                array_pop($lHTMLPages->pgfooterstack);
            }
            if ( $tagname === "body") {
                //Add blank line 
                //Add page footer.
				$rv = $lHTMLPages->AddStringToPage('</'.$tagname.' '.$htmlAttributes.'>', 0, $prow );
                if ($rv){
                    return(true);
                } 
            }        
            return false;       // nastavi dalje.. 
        }

		//----------------------------------------------------
		// XML Node to HTML parser
		//----------------------------------------------------
		public function RepNode2Html($tagname, $lHTMLPages, $pnode, $prow, $xmlnode, $accountid, $db, $packageName, $pp,$_companyName,$_periodName ) {

            $lineoffset=0;


            $this->cnt++;
            $i =0; 
            $s ='';
            $number0=0;
            $number=0;


            $this->parentNode = $pnode;
            $tagname = $xmlnode->getName();
            $str="";
            $lrpoc='';
            $lcursor='row';
            $htmlAttributes = '';
            $rowValue=1;//default value for value of row 
            $att = $xmlnode->attributes();
            $xtplch = '';
            $refHF = null;
            $rtype='';
            $lrcursor='';
            foreach(  $att as $_name => $_value ) {
                if ($_name === "rtype") {
                    $this->rtype = (string) $_value;
                    $rtype= $_value;
                } elseif ($_name === "rproc") {
                    $lrpoc= $_value;
                } elseif ($_name === "rcursor") {
                    $lrcursor= (string) $_value;
                } elseif ($_name === "ncursor") {
                    $lncursor= (string) $_value;
                } elseif ($_name==="rowValue") {
                    //rowValue reference to div , br , hr , and tr tag
                    $rowValue = $_value;
                } elseif ($_name === "refHF") {
                    $refHF = (string)$_value;
                }
                else{
                    $htmlAttributes .=  $_name. '=' .'\''.  (string)$_value  .'\''.' ';      
                }
            } 

            //--------------------------------------------------------------------------
            // Check for procedure
            //--------------------------------------------------------------------------
            if ( $lrpoc !=''){ // Tag have procedure. We must execute them
                //Bind and call procedure
                $rtrCData = $this->dorProc($accountid, $db, $packageName, $lrpoc, $pp );

            }    
            if ( $lrcursor !=''){// Tag have cursor. We must go to curssor
                //$currentNode = new Bw_App_RepNode();  
                if ( $rtrCData == NULL ){        
					// $rtrCData = $prow['r1'];
					$rtrCData = $prow[$lrcursor];
                }

				//-----------------------------------------------------------
				//Transform cursor to html 
				//-----------------------------------------------------------
                $rv = $this->rCursor2Html0($tagname, $htmlAttributes, $rtrCData, $lrcursor, $lHTMLPages, $this, $prow, $xmlnode , $accountid,  $db, $packageName, $pp ,$_companyName,$_periodName);           

                if ($rv){
                    return(true);
                }    
            }   else { 
				//Simple xml tag. Just do rendering
				$rv = $this->RepNode2HtmlPart2($tagname, $htmlAttributes, $lHTMLPages, $pnode, $prow, $xmlnode, $accountid, $db, $packageName, $pp, $_companyName, $_periodName);
            }
            if ($rv) {
                return(true);
            }
            
            return(false);

        }

    }
?>
