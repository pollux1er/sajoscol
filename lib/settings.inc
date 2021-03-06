<?php
/** Manipulation de la table setting
 * 
 * $Id: settings.inc 8526 2011-10-24 20:52:32Z jjacquard $
 * 
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * 
 * @package Initialisation
 * @subpackage settings
 * 
 */

/**
 * Tableau des r�glages
 * 
 * $gepiSettings['name'] = 'value'
 * 
 * name le nom du r�glage dans setting.name
 * 
 * value la valeur du r�glage dans setting.value 
 *
 * @global array $GLOBALS['gepiSettings']
 * @name $gepiSettings
 */
$GLOBALS['gepiSettings'] = array();

/**
 * Charge les r�glages depuis la base de donn�es
 *
 * Recherche tous les r�glages
 * Retourne le r�sultat dans le tableau associatif $gepiSettings
 *
 * Retourne TRUE si tout c'est bien pass�, FALSE sinon
 *
 * @global array
 * @return bool TRUE if the settings are loaded
 */
function loadSettings()
{
    global $gepiSettings;
    $sql = "SELECT name, value FROM setting";
    $res = sql_query($sql);
    if (! $res) return (FALSE);
    if (sql_count($res) == 0) {
        return (FALSE);
    } else {
        for ($i = 0; ($row = sql_row($res, $i)); $i++) {
            $gepiSettings[$row[0]] = $row[1];
        }
        return (TRUE);
    }
}

/**
 * Renvoie la valeur d'un r�glage en fonction de son nom
 *
 * Utilisez cette fonction � l'int�rieur des autres fonctions afin de ne pas avoir
 * � d�clarer la variable globale $gepiSettings
 *
 * Retourne la valeur si le nom existe
 *
 * @global array 
 * @param text $_name Le nom du r�glage que vous cherchez
 * @return text La valeur correspondant � $_name
 * 
 */
function getSettingValue($_name)
{
    global $gepiSettings;
    if (isset($gepiSettings[$_name])) return ($gepiSettings[$_name]);
}

/**
 * Renvoie TRUE si le r�glage est 'yes' ou 'y'
 *
 *
 * @param text $_name Le nom du r�glage que vous cherchez
 * @return bool TRUE si le r�glage que vous cherchez est 'yes' or 'y', FALSE sinon
 */
function getSettingAOui($_name)
{
	if (getSettingValue($_name)=="yes" || getSettingValue($_name)=="y"){
		return TRUE;
	} else {
		return FALSE;
	}
}

/**
 * Sauvegarde une paire name, value dans la base
 *
 * Utilisez cette fonction ponctuellement, Si vous devez sauvegarder plusieurs r�glages,
 * vous devriez plut�t �crire votre propre code
 * 
 * @global array
 * @param text $_name Le nom du r�glage
 * @param text $_value La valeur du r�glage
 * @return bool TRUE si tout s'est bien pass�, FALSE sinon
 */
function saveSetting($_name, $_value)
{
    global $gepiSettings;
    $R=mysql_query("SELECT * FROM setting WHERE NAME='".$_name."' LIMIT 1");
   if (mysql_num_rows($R)>0) {
    $sql = "update setting set VALUE = \"" . $_value . "\" where NAME = \"" . $_name . "\"";
    $res = sql_query($sql);
    if ( ! $res) return (FALSE);
    } else {
        $sql = "insert into setting set NAME = \"" . $_name . "\", VALUE = \"" . $_value . "\"";
        $res = sql_query($sql);
        if ( ! $res) return (FALSE);
    }
    $gepiSettings[$_name] = $_value;
    return (TRUE);
}
?>
