<?php
/******************************************************************************
 * Photoupload
 *
 * Copyright    : (c) 2004 - 2007 The Admidio Team
 * Homepage     : http://www.admidio.org
 * Module-Owner : Jochen Erkens
 *
 * Uebergaben:
 *
 * pho_id: id der Veranstaltung zu der die Bilder hinzugefuegt werden sollen
 ******************************************************************************
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * version 2 as published by the Free Software Foundation
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 79 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 *****************************************************************************/

require("../../system/photo_event_class.php");
require("../../system/common.php");
require("../../system/login_valid.php");

// pruefen ob das Modul ueberhaupt aktiviert ist
if ($g_preferences['enable_photo_module'] != 1)
{
    // das Modul ist deaktiviert
    $g_message->show("module_disabled");
}

// erst pruefen, ob der User Fotoberarbeitungsrechte hat
if(!$g_current_user->editPhotoRight())
{
    $g_message->show("photoverwaltunsrecht");
}

// Uebergabevariablen pruefen

if(isset($_GET["pho_id"]) && is_numeric($_GET["pho_id"]) == false)
{
    $g_message->show("invalid");
}

//Kontrolle ob Server Dateiuploads zulaesst
$ini = ini_get('file_uploads');
if($ini!=1)
{
    $g_message->show("no_file_upload_server");
}

//URL auf Navigationstack ablegen
$_SESSION['navigation']->addUrl($g_current_url);

// Fotoveranstaltungs-Objekt erzeugen oder aus Session lesen
if(isset($_SESSION['photo_event']) && $_SESSION['photo_event']->getValue("pho_id") == $_GET["pho_id"])
{
    $photo_event =& $_SESSION['photo_event'];
    $photo_event->db_connection = $g_adm_con;
}
else
{
    $photo_event = new PhotoEvent($g_adm_con, $_GET["pho_id"]);
    $_SESSION['photo_event'] =& $photo_event;
}

// pruefen, ob Veranstaltung zur aktuellen Organisation gehoert
if($photo_event->getValue("pho_org_shortname") != $g_organization)
{
    $g_message->show("invalid");
}

// Html-Kopf ausgeben
$g_layout['title'] = "Fotos hochladen";
require(SERVER_PATH. "/adm_program/layout/overall_header.php");

echo"<h1 class=\"moduleHeadline\">Fotogalerien - Upload</h1>";

/**************************Formular********************************************************/
echo"
<form name=\"photoup\" method=\"post\" action=\"$g_root_path/adm_program/modules/photos/photoupload_do.php?pho_id=". $_GET['pho_id']. "\" enctype=\"multipart/form-data\">
    <div class=\"formHead\">Bilder hochladen</div>
    <div class=\"formBody\">
        <div class=\"formRow\">
			Bilder zu dieser Veranstaltung hinzuf&uuml;gen:<br>"
	        .$photo_event->getValue("pho_name")."<br>"
	        ."(Beginn: ". mysqldate("d.m.y", $photo_event->getValue("pho_begin")).")"
	        ."
		</div>
		<hr>
	    <div class=\"formRow\">
			<div class=\"formRowText\">Bild 1:</div>
			<div class=\"formRowField\"<input type=\"file\" id=\"bilddatei1\" name=\"bilddatei[]\" value=\"durchsuchen\"></div>
		</div>
	    <div class=\"formRow\">
			<div class=\"formRowText\">Bild 2:</div>
			<div class=\"formRowField\"<input type=\"file\" name=\"bilddatei[]\" value=\"durchsuchen\"></div>
		</div>
	    <div class=\"formRow\">
			<div class=\"formRowText\">Bild 2:</div>
			<div class=\"formRowField\"<input type=\"file\" name=\"bilddatei[]\" value=\"durchsuchen\"></div>
		</div>
	    <div class=\"formRow\">
			<div class=\"formRowText\">Bild 2:</div>
			<div class=\"formRowField\"<input type=\"file\" name=\"bilddatei[]\" value=\"durchsuchen\"></div>
		</div>
	    <div class=\"formRow\">
			<div class=\"formRowText\">Bild 2:</div>
			<div class=\"formRowField\"<input type=\"file\" name=\"bilddatei[]\" value=\"durchsuchen\"></div>
		</div>

		<div class=\"formRow\">
	        <hr />
	        Hilfe: <img src=\"$g_root_path/adm_program/images/help.png\" class=\"iconLink\" alt=\"Hilfe\" title=\"Hilfe\"
	                    onclick=\"window.open('$g_root_path/adm_program/system/msg_window.php?err_code=photo_up_help','Message','width=600,height=600,left=310,top=200,scrollbars=yes')\">
	        <hr />
		</div>

        <div class=\"formRow\">
            <span class=\"editorLink\">
	            <a class=\"iconLink\" href=\"$g_root_path/adm_program/system/back.php\"><img
	            class=\"iconLink\" src=\"$g_root_path/adm_program/images/back.png\" alt=\"Zur&uuml;ck\"></a>
	            <a class=\"iconLink\" href=\"$g_root_path/adm_program/system/back.php\">Zur&uuml;ck</a>
        	</span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <button name=\"upload\" type=\"submit\" value=\"speichern\">
                <img src=\"$g_root_path/adm_program/images/page_white_get.png\" alt=\"Speichern\">
                &nbsp;Bilder hochladen
            </button>
        </div>
   </div>
</form>";

//Seitenende
echo"
<script type=\"text/javascript\"><!--
        document.getElementById('bilddatei1').focus();
--></script>";

require(SERVER_PATH. "/adm_program/layout/overall_footer.php");

?>