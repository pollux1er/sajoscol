<?php
//ces fonctions sont utilisée dans la page test_serveur.php et dans les tests unitaires
function test_check_utf8()
{
    if (!check_utf8("auie")) {echo 'failure line 4 mod_serveur/test_encoding_functions.php'; return false;}     
    if (!check_utf8("bépowǜdlj")) {echo 'failure line 5 mod_serveur/test_encoding_functions.php'; return false;} 
    if (!check_utf8(";œ€âũ")) {echo 'failure line 6 mod_serveur/test_encoding_functions.php'; return false;} 
    
	    //$examples = array(
        //    "Valid ASCII" => "a",
        //    "Valid 2 Octet Sequence" => "\xc3\xb1",
        //    "Invalid 2 Octet Sequence" => "\xc3\x28",
        //    "Invalid Sequence Identifier" => "\xa0\xa1",
        //    "Valid 3 Octet Sequence" => "\xe2\x82\xa1",
        //    "Invalid 3 Octet Sequence (in 2nd Octet)" => "\xe2\x28\xa1",
        //    "Invalid 3 Octet Sequence (in 3rd Octet)" => "\xe2\x82\x28",
        //    "Valid 4 Octet Sequence" => "\xf0\x90\x8c\xbc",
        //    "Invalid 4 Octet Sequence (in 2nd Octet)" => "\xf0\x28\x8c\xbc",
        //    "Invalid 4 Octet Sequence (in 3rd Octet)" => "\xf0\x90\x28\xbc",
        //    "Invalid 4 Octet Sequence (in 4th Octet)" => "\xf0\x28\x8c\x28",
        //    "Valid 5 Octet Sequence (but not Unicode!)" => "\xf8\xa1\xa1\xa1\xa1",
        //    "Valid 6 Octet Sequence (but not Unicode!)" => "\xfc\xa1\xa1\xa1\xa1\xa1",
        //);
        
    if (!check_utf8("a")) {echo 'failure line 25 mod_serveur/test_encoding_functions.php'; return false;} 
    if (!check_utf8("\xc3\xb1")) {echo 'failure line 26 mod_serveur/test_encoding_functions.php'; return false;} 
    if (check_utf8("\xc3\x28")) {echo 'failure line 27 mod_serveur/test_encoding_functions.php'; return false;} 
    if (check_utf8("\xa0\xa1")) {echo 'failure line 28 mod_serveur/test_encoding_functions.php'; return false;} 
    if (!check_utf8("\xe2\x82\xa1")) {echo 'failure line 29 mod_serveur/test_encoding_functions.php'; return false;} 
    if (check_utf8("\xe2\x28\xa1")) {echo '�chec ligne 30 mod_serveur/test_encoding_functions.php'; return false;} 
    if (check_utf8("\xe2\x82\x28")) {echo 'failure line 31 mod_serveur/test_encoding_functions.php'; return false;} 
    if (!check_utf8("\xf0\x90\x8c\xbc")) {echo 'failure line 32 mod_serveur/test_encoding_functions.php'; return false;} 
    if (check_utf8("\xf0\x28\x8c\xbc")) {echo 'failure line 33 mod_serveur/test_encoding_functions.php'; return false;} 
    if (check_utf8("\xf0\x90\x28\xbc")) {echo 'failure line 34 mod_serveur/test_encoding_functions.php'; return false;} 
    if (check_utf8("\xf0\x28\x8c\x28")) {echo 'failure line 35 mod_serveur/test_encoding_functions.php'; return false;} 
    if (!check_utf8("\xf8\xa1\xa1\xa1\xa1")) {echo 'failure line 36 mod_serveur/test_encoding_functions.php'; return false;}
    if (!check_utf8("\xfc\xa1\xa1\xa1\xa1\xa1")) {echo 'failure line 37 mod_serveur/test_encoding_functions.php'; return false;}
    
    return true;
    
}
	
function test_detect_encoding()
{
    if ("UTF-8" != detect_encoding("auie")) {echo 'failure line 45 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("UTF-8" != detect_encoding("bépowǜdlj")) {echo 'failure line 46 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("UTF-8" != detect_encoding(";œ€âũ")) {echo 'failure line 47 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("UTF-8" != detect_encoding("é")) {echo 'failure line 48 mod_serveur/test_encoding_functions.php'; return false;}
    if ("ISO-8859-15" != detect_encoding("\xe9")) {echo 'failure line 48 mod_serveur/test_encoding_functions.php'; return false;}
    if ("ISO-8859-15" != detect_encoding("\xa4")) {echo 'failure line 48 mod_serveur/test_encoding_functions.php'; return false;}
	return true;
}

function test_ensure_utf8()
{
    if ("auie" != ensure_utf8("auie", 'UTF-8')) {echo '�chec ligne 56 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("auie" != ensure_utf8("auie")) {echo '�chec ligne 57 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("bépowǜdlj" != ensure_utf8("bépowǜdlj")) {echo 'failure line 58 mod_serveur/test_encoding_functions.php'; return false;} 
    if (";œ€âũ" != ensure_utf8(";œ€âũ")) {echo 'failure line 59 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("é" != ensure_utf8("é")) {echo 'failure line 60 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("é" != ensure_utf8("\xe9")) {echo 'failure line 61 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("€" != ensure_utf8("\xa4")) {echo 'failure line 62 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("€" != ensure_utf8("\xa4",'ISO-8859-15')) {echo 'failure line 63 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("¤" != ensure_utf8("\xa4",'ISO-8859-1')) {echo 'failure line 64 mod_serveur/test_encoding_functions.php'; return false;} 
    return true;
}

function test_remplace_accents()
{
    if ("auie" != remplace_accents("auie")) {echo 'failure line 70 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("auie'\"" != remplace_accents("auie'\"")) {echo 'failure line 71 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("bepowudlj" != remplace_accents("bépowûdlj")) {echo 'failure line 72 mod_serveur/test_encoding_functions.php'; return false;}
    if ("u" != remplace_accents("ǜ") && "_" != remplace_accents("ǜ")) {echo 'failure line 73 mod_serveur/test_encoding_functions.php'; return false;}
    if ("e" != remplace_accents("\xe9")) {echo 'failure line 74 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("'\" ." != remplace_accents("'\" .")) {echo 'failure line 75 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("__ e" != remplace_accents("'\" é",'all_nospace')) {echo 'failure line 76 mod_serveur/test_encoding_functions.php'; return false;} 
    if ("___e" != remplace_accents("'\" é",'all')) {echo 'failure line 77 mod_serveur/test_encoding_functions.php'; return false;} 
    return true;
}

function test_casse_mot() {
    if (function_exists('mb_convert_case')) {
        if ("AUIE" != casse_mot("auie",'maj')) {echo 'failure line 83 mod_serveur/test_encoding_functions.php'; return false;} 
        if ("auie" != casse_mot("AUIE",'min')) {echo 'failure line 84 mod_serveur/test_encoding_functions.php'; return false;} 
        if ("Bépowûdlj" != casse_mot("béPowûdlj",'majf')) {echo 'failure line 85 mod_serveur/test_encoding_functions.php'; return false;} 
        if (";Œ€ÂŨ" != casse_mot(";œ€âũ",'maj')) {echo 'failure line 86 mod_serveur/test_encoding_functions.php'; return false;} 
        if ("Bonjour Je Suis Là" != casse_mot("bonjour je suis là",'majf2')) {echo 'failure line 87 mod_serveur/test_encoding_functions.php'; return false;} 
    } else {
        if ("AUIE" != casse_mot("auie",'maj')) {echo 'failure line 89 mod_serveur/test_encoding_functions.php'; return false;} 
        if ("auie" != casse_mot("AUIE",'min')) {echo 'failure line 90 mod_serveur/test_encoding_functions.php'; return false;} 
        if ("Bepowudlj" != casse_mot("bépowǜdlj",'majf')) {echo 'failure line 91 mod_serveur/test_encoding_functions.php'; return false;} 
        if (";AE" != casse_mot(";âé",'maj')) {echo 'failure line 92 mod_serveur/test_encoding_functions.php'; return false;} 
        if ("Bonjour Je Suis La" != casse_mot("bonjour je suis là",'majf2')) {echo 'failure line 93 mod_serveur/test_encoding_functions.php'; return false;}
    } 
    return true;
}

