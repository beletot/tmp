<?php
// i -> insensible à la case
// | -> condition, ou
// ^ (accent circonflexe) : indique le début d'une chaîne.
// $ (dollar) : indique la fin d'une chaîne.
// \ escape a Character
/**
 *     ? (point d'interrogation) : ce symbole indique que la lettre est facultative. Elle peut y être 0 ou 1 fois.
    Ainsi, #a?# reconnaît 0 ou 1 "a".
    + (signe plus) : la lettre est obligatoire. Elle peut apparaître 1 ou plusieurs fois.
    Ainsi, #a+# reconnaît "a", "aa", "aaa", "aaaa" etc...
    * (étoile) : la lettre est facultative. Elle peut apparaître 0, 1 ou plusieurs fois.
    Ainsi, #a*# reconnaît "a", "aa", "aaa", "aaaa" etc... Mais s'il n'y a pas de "a", ça fonctionne aussi !
 * 
 */
/*
 *
 * #[^0-9]# -pas de chiffre
 * #Ay(ay)*# -> ay + aya sur répétant, nbr de fois non défini
 */

// #^user_pref#i

//user_pref("mail.identity.id1.fullName", "Bertrand Letot");
//user_pref("mail.identity.id1.useremail", "beletot@epfc.eu");
//comparer les valeurs si config dossier différentes

// accessibility.typeaheadfind.flashBar
$line = '
	user_pref("mail.identity.id7.useremail", "dev@epfc.eu"); 
';

$row = new stdClass;
$position = '';
//$line = 'user_pref("app.update.disable_button.showUpdateHistory", "45645ghj"); ';
$toReplace = array('(', '"', ',', ' ');
//if (preg_match('#\("[a-z.2]{1,}"#i', $line, $matches)) {
//if (preg_match('#\("mail.identity.id[0-9]+.useremail"#i', $line, $matches)) {
if (preg_match('#[a-z]+@epfc.eu#i', $line, $matches)) {
	echo '<pre>'.print_r($matches,true).'</pre>';
	//echo $key.'<br />';
}


/*user_pref("mail.identity.id1.archive_folder", "imap://beletot%40epfc.eu@imap.googlemail.com/[Gmail]/Tous les messages");
 user_pref("mail.identity.id1.archives_folder_picker_mode", "1");
 user_pref("mail.identity.id1.attach_signature", true);
 user_pref("mail.identity.id1.doBcc", false);
 user_pref("mail.identity.id1.draft_folder", "imap://beletot%40epfc.eu@imap.googlemail.com/[Gmail]/Brouillons");
 user_pref("mail.identity.id1.drafts_folder_picker_mode", "1");
 user_pref("mail.identity.id1.fcc", false);
 user_pref("mail.identity.id1.fcc_folder", "imap://beletot%40epfc.eu@imap.googlemail.com/[Gmail]/Messages envoy&AOk-s");
 user_pref("mail.identity.id1.fcc_folder_picker_mode", "1");
 user_pref("mail.identity.id1.fullName", "Bertrand Letot");
 user_pref("mail.identity.id1.reply_on_top", 1);
 user_pref("mail.identity.id1.sig_bottom", false);
 user_pref("mail.identity.id1.sig_file", "F:\\TEXTES\\SIGNATURE\\CAM\\Signature_ beletot.html");
 user_pref("mail.identity.id1.sig_file-rel", "[ProfD]../../../F:/TEXTES/SIGNATURE/CAM/Signature_ beletot.html");
 user_pref("mail.identity.id1.stationery_folder", "imap://beletot%40epfc.eu@imap.googlemail.com/Templates");
 user_pref("mail.identity.id1.tmpl_folder_picker_mode", "0");
 user_pref("mail.identity.id1.useremail", "beletot@epfc.eu");
 user_pref("mail.identity.id1.valid", true);*/
?>

