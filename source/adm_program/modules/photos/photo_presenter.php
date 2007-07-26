<?php
/******************************************************************************
 * Photogalerien
 *
 * Copyright    : (c) 2004 - 2007 The Admidio Team
 * Homepage     : http://www.admidio.org
 * Module-Owner : Jochen Erkens
 *
 * Uebergaben:
 *
 * Bild: welches Bild soll angezeigt werden
 * pho_id: Id der Veranstaltung aus der das Bild stammt
 *
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
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 *****************************************************************************/

require("../../system/photo_event_class.php");
require("../../system/common.php");

// pruefen ob das Modul ueberhaupt aktiviert ist
if ($g_preferences['enable_photo_module'] != 1)
{
    // das Modul ist deaktiviert
    $g_message->show("module_disabled");
}

// Uebergabevariablen pruefen

if(isset($_GET["pho_id"]) && is_numeric($_GET["pho_id"]) == false)
{
    $g_message->show("invalid");
}

if(isset($_GET["bild"]) && is_numeric($_GET["bild"]) == false)
{
    $g_message->show("invalid");
}

//Uebernahme der uebergebenen variablen
$pho_id = $_GET['pho_id'];
$bild   = $_GET['bild'];

//erfassen der Veranstaltung falls noch nicht in Session gespeichert
if(isset($_SESSION['photo_event']) && $_SESSION['photo_event']->getValue("pho_id") == $pho_id)
{
    $photo_event =& $_SESSION['photo_event'];
    $photo_event->db_connection = $g_adm_con;
}
else
{
    $photo_event = new PhotoEvent($g_adm_con, $pho_id);
    $_SESSION['photo_event'] =& $photo_event;
}

//Naechstes und Letztes Bild
$prev_image = $bild-1;
$next_image = $bild+1;

//Ordnerpfad zusammensetzen
$ordner_foto = "/adm_my_files/photos/".$photo_event->getValue("pho_begin")."_".$photo_event->getValue("pho_id");
$ordner      = SERVER_PATH. $ordner_foto;
$ordner_url  = $g_root_path. $ordner_foto;

$body_height = $g_preferences['photo_show_height'] + 130;
$body_with   = $g_preferences['photo_show_width']  + 20;

//Photomodulspezifische CSS laden
$g_layout['header'] = $g_layout['header']."<link rel=\"stylesheet\" href=\"$g_root_path/adm_program/layout/photos.css\" type=\"text/css\" media=\"screen\" />";

// Html-Kopf ausgeben
$g_layout['title']    = "Fotogalerien";

//wenn Popupmode normalen kopf unterdruecken
if($g_preferences['photo_show_mode']==0)
{                      
	$g_layout['includes'] = false;
}

require(SERVER_PATH. "/adm_program/layout/overall_header.php");

//Ausgabe der Eine Tabelle Kopfzelle mit &Uuml;berschrift, Photographen und Datum
//untere Zelle mit Buttons Bild und Fenster Schlie&szlig;en Button
echo "
<div class=\"formHead\" style=\"width:".$body_with."px\">".$photo_event->getValue("pho_name")."</div>
<div class=\"formBody\" style=\"width:".$body_with."px; height: ".$body_height."px;\">";
    echo"Datum: ".mysqldate("d.m.y", $photo_event->getValue("pho_begin"));
    if($photo_event->getValue("pho_end") != $photo_event->getValue("pho_begin")
    && strlen($photo_event->getValue("pho_end")) > 0)
    {
        echo " bis ".mysqldate("d.m.y", $photo_event->getValue("pho_end"));
    }
    echo "<br>Fotos von: ".$photo_event->getValue("pho_photographers")."<br><br>";

    //Vor und zurueck buttons
	echo"<div class=\"pageNavigation\">";
	    if($prev_image > 0)
	    {
	        echo"<span class=\"iconLink\">
	            <a class=\"iconLink\" href=\"$g_root_path/adm_program/modules/photos/photo_presenter.php?bild=$prev_image&pho_id=$pho_id\"><img 
	                class=\"navigationArrow\" src=\"$g_root_path/adm_program/images/back.png\" alt=\"Vorheriges Bild\">
	            </a>
	            <a class=\"iconLink\" href=\"$g_root_path/adm_program/modules/photos/photo_presenter.php?bild=$prev_image&pho_id=$pho_id\">Vorheriges Bild</a>
	        </span>
	        &nbsp;&nbsp;&nbsp;&nbsp;";
	    }
	    if($next_image <= $photo_event->getValue("pho_quantity"))
	    {
	        echo"<span class=\"iconLink\">
	            <a class=\"iconLink\" href=\"$g_root_path/adm_program/modules/photos/photo_presenter.php?bild=$next_image&pho_id=$pho_id\">N&auml;chstes Bild</a>
	            <a class=\"iconLink\" href=\"$g_root_path/adm_program/modules/photos/photo_presenter.php?bild=$next_image&pho_id=$pho_id\"><img 
	                class=\"navigationArrow\" src=\"$g_root_path/adm_program/images/forward.png\" alt=\"N&auml;chstes Bild\">
	            </a>
	        </span>";
	    }
	    echo"<br><br>
	</div>";

    //Ermittlung der Original Bildgroesse
    $bildgroesse = getimagesize("$ordner/$bild.jpg");
    //Entscheidung ueber scallierung
    //Hochformat Bilder
    if ($bildgroesse[0]<=$bildgroesse[1])
    {
        $side="y";
        if ($bildgroesse[1]>$g_preferences['photo_show_height']){
            $scal=$g_preferences['photo_show_height'];
        }
        else
        {
            $scal=$bildgroesse[1];
        }
    }

    //Querformat Bilder
    if ($bildgroesse[0]>$bildgroesse[1])
    {
        $side="x";
        if ($bildgroesse[0]>$g_preferences['photo_show_width'])
        {
            $scal=$g_preferences['photo_show_width'];
        }
        else{
            $scal=$bildgroesse[0];
        }
    }

    //Ausgabe Bild
    echo"
        <div><img class=\"photoOutput\" src=\"$g_root_path/adm_program/modules/photos/photo_show.php?pho_id=".$pho_id."&amp;pic_nr=".$bild."&amp;pho_begin=".$photo_event->getValue("pho_begin")."&amp;scal=".$scal."&amp;side=".$side."\"border=\"0\" alt=\"$ordner_url $bild\">";

    //Fenster schliessen Button
    //wenn Popupmode
	if($g_preferences['photo_show_mode']==0)
	{   
    	echo"<div class=\"editorLink\"><br />
        <span class=\"iconLink\">
            <a href=\"javascript:parent.window.close()\"><img
            class=\"iconLink\" src=\"$g_root_path/adm_program/images/door_in.png\" alt=\"Login\"></a>
            <a class=\"iconLink\" href=\"javascript:parent.window.close()\">Fenster schlie&szlig;en</a>
        </span>
    	</div>";
	}
	
	//Zurueck zur Uebersicht Button
    //wenn Fenstermode
	if($g_preferences['photo_show_mode']==2)
	{   
    	echo"<div class=\"editorLink\">
        <span class=\"iconLink\">
            <img onclick=\"self.location.href='$g_root_path/adm_program/modules/photos/photos.php?pho_id=$pho_id'\" class=\"iconLink\" src=\"$g_root_path/adm_program/images/application_view_tile.png\" alt=\"Login\"></a>
            <a class=\"iconLink\" href=\"$g_root_path/adm_program/modules/photos/photos.php?pho_id=$pho_id\">zur &Uuml;bersicht</a>
        </span>
    	</div>";
	}
echo"</div>";
        
require(SERVER_PATH. "/adm_program/layout/overall_footer.php");

?>