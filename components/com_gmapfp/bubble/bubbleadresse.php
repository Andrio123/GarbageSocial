<?php
	/*
	* GMapFP Component Google Map for Joomla! 1.7.x
	* Version 10.25pro
	* Creation date: D�cembre 2011
	* Author: Fabrice4821 - www.gmapfp.org
	* Author email: webmaster@gmapfp.org
	* License GNU/GPL
	*/

if (@$row->adresse and !$config->get('gmapfpadresse1_view', 0))     {$adr   = addslashes(trim($row->adresse))."<br />";}else{$adr='';};
if (@$row->adresse2 and !$config->get('gmapfpadresse2_view', 0))    {$adr2  = addslashes(trim($row->adresse2))."<br />";}else{$adr2='';};
if (@$row->codepostal and !$config->get('gmapfp_cp_view', 0)) {
	$cp    = addslashes(trim($row->codepostal));
	if (!$config->get('gmapfp_zipcode_ville')) {
		$cp .="<br />";
	}else{
		$cp .="&nbsp;";
	}
}else{ $cp=''; };
if (@$row->ville and !$config->get('gmapfp_ville_view', 0)) {
	$ville = addslashes(trim($row->ville));
	if ($config->get('gmapfp_zipcode_ville')) {
		$ville .="<br />";
	}else{
		$ville .="&nbsp;";
	}
}else{$ville='';};
if (@$row->departement and !$config->get('gmapfp_departement_view', 0)) {$dep   = addslashes(trim($row->departement))."<br />";}else{$dep='';};
if (@$row->pay and !$config->get('gmapfp_pays_view', 0))         {$pays  = addslashes(trim($row->pay))."<br />";}else{$pays='';};

if ($config->get('gmapfp_zipcode_ville')) {
	$adresse = $adr.$adr2.$cp.$ville.$dep.$pays;
}else{
	$adresse = $adr.$adr2.$ville.$dep.$pays.$cp;
};

$bubble = ($this->_num_marqueurs+1000).", \"<div class='gmapfp_marqueur' style='overflow-y:auto;';><p><span class='titre'>".$nom."</span></p><p class='adresse'>".$adresse."</p>";
$bubble.="<br />".$plus_detail."</div>\",\"";

return $bubble;
?>