<?php
/*
	$Id: sender.php 16 2005-10-05 22:41:51Z me $
*/

// Setup PHP and start page setup.
$error=fopen($GLOBALS['mosConfig_absolute_path'].'/pdf/user/error_log.txt','a+');
	@ini_set("include_path", str_replace("\\", "/", dirname(__FILE__))."/includes");
	@ini_set("allow_url_fopen", 1);
	@ini_set("session.name", "LMSID");
	@ini_set("session.use_trans_sid", 0);
	@ini_set("session.cookie_lifetime", 0);
	@ini_set("session.cookie_secure", 0);
	@ini_set("session.referer_check", "");
	@ini_set("error_reporting",  E_ALL ^ E_NOTICE);
	@ini_set("magic_quotes_runtime", 0);
	
	@set_time_limit(300);	// Max run time, 5 minutes.
	
	define("IN_SENDER", true);
	
	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"DTD/xhtml1-transitional.dtd\">\n";
	echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n";
	echo "<body>\n";
	
	if((!$_GET["sid"]) || (strlen(trim($_GET["sid"])) < 1)) {
		echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
		echo "\talert('Failed to locate the session identifier in your request.\\n\\nPlease ensure your servers session support is properly configured and you provide an authenticated session identifier to the engine.');\n";
		echo "</script>\n";
		echo "</body>\n";
		echo "</html>\n";
		exit;
	}
	
	session_start(trim($_GET["sid"]));
	
	if(!$_SESSION["isAuthenticated"]) {
		echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
		echo "\talert('Failed to authenticate your session identifier.\\n\\nPlease ensure your servers session support is properly configured and you provide an authenticated session identifier to the engine.');\n";
		echo "</script>\n";
		echo "</body>\n";
		echo "</html>\n";
		exit;
	}

	require_once("pref_ids.inc.php");
	require_once("config.inc.php");
	require_once("classes/adodb/adodb.inc.php");	
	require_once("dbconnection.inc.php");
	require_once("functions.inc.php");
	require_once("loader.inc.php");
	//be create pdf
	//require_once("pdfWriter.php");
	require_once($GLOBALS['mosConfig_absolute_path'].'/components/com_pdf/pdf.pdf.php');

	if((!$_GET["qid"]) || (strlen(trim($_GET["qid"])) < 1)) {
		echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
		echo "\talert('Failed to locate the queue identifier in your request.\\n\\nPlease ensure you provide a valid queue identifier to the engine.');\n";
		echo "</script>\n";
		echo "</body>\n";
		echo "</html>\n";
		exit;
	} else {
		$query	= "SELECT * FROM `".TABLES_PREFIX."queue` WHERE `queue_id`='".checkslashes(trim($_GET["qid"]))."'";
		$result	= $db->GetRow($query);
		if(!$result) {
			echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
			echo "\talert('Failed to load the provided queue identifier in your request.\\n\\nPlease ensure you provide a valid queue identifier to the engine.');\n";
			echo "</script>\n";
			echo "</body>\n";
			echo "</html>\n";
			exit;
		} else {
			$_SESSION["queue_details"]				= array();
			$_SESSION["queue_details"]["queue_id"]		= $result["queue_id"];
			$_SESSION["queue_details"]["message_id"]	= $result["message_id"];
			$_SESSION["queue_details"]["date"]			= $result["date"];
			$_SESSION["queue_details"]["touch"]		= $result["touch"];
			$_SESSION["queue_details"]["target"]		= unserialize($result["target"]);
			$_SESSION["queue_details"]["progress"]		= $result["progress"];
			$_SESSION["queue_details"]["total"]		= $result["total"];
			$_SESSION["queue_details"]["status"]		= $result["status"];
		}
	}

	switch($_GET["action"]) {
		case "cancel" :
			$query = "DELETE FROM `".TABLES_PREFIX."sending` WHERE `queue_id`='".checkslashes($_SESSION["queue_details"]["queue_id"])."'";
			if($db->Execute($query)) {
				$query = "OPTIMIZE TABLE `".TABLES_PREFIX."sending`";
				if(!$db->Execute($query)) {
					if($_SESSION["config"][PREF_ERROR_LOGGING] == "yes") {
						@error_log(display_date("r", time())."\t".__FILE__." [Line: ".__LINE__."]\tUnable to optimize the sending table. Database server said: ".$db->ErrorMsg()."\n", 3, $_SESSION["config"][PREF_PRIVATE_PATH]."logs/error_log.txt");
					}
				}
				$query = "UPDATE `".TABLES_PREFIX."queue` SET `status`='Cancelled' WHERE `queue_id`='".checkslashes($_SESSION["queue_details"]["queue_id"])."'";
				if(!$db->Execute($query)) {
					if($_SESSION["config"][PREF_ERROR_LOGGING] == "yes") {
						@error_log(display_date("r", time())."\t".__FILE__." [Line: ".__LINE__."]\tUnable to update queue status in the queue table. Database server said: ".$db->ErrorMsg()."\n", 3, $_SESSION["config"][PREF_PRIVATE_PATH]."logs/error_log.txt");
					}
				}
			} else {
				if($_SESSION["config"][PREF_ERROR_LOGGING] == "yes") {
					@error_log(display_date("r", time())."\t".__FILE__." [Line: ".__LINE__."]\tUnable to delete queue data in the sending table. Database server said: ".$db->ErrorMsg()."\n", 3, $_SESSION["config"][PREF_PRIVATE_PATH]."logs/error_log.txt");
				}
			}
			
			echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
			echo "\tparent.document.getElementById('buttonHTML').innerHTML	= '';\n";
			echo "\tparent.document.getElementById('progressText').innerHTML = 'Successfully cancelled; returning to the message centre in 5 seconds.';\n";
			echo "\tsetTimeout('parent.window.location=\'index2.php?option=com_lm&section=message&action=view&id=".$_SESSION["message_details"]["message_id"]."\'', 5000);";
			echo "</script>\n";

			unset($_SESSION["queue_details"], $_SESSION["message_details"]);
		break;
		case "pause" :
			if($_SESSION["queue_details"]["status"] != "Paused") {
				$query = "UPDATE `".TABLES_PREFIX."queue` SET `status`='Paused', `touch`='".time()."' WHERE `queue_id`='".checkslashes($_SESSION["queue_details"]["queue_id"])."'";
				$db->Execute($query);
			}
			echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
			echo "\tparent.document.getElementById('buttonHTML').innerHTML	= '<form><input type=\"button\" class=\"button\" value=\"Resume\" onClick=\"document.getElementById(\'workerFrame\').src = \'index3.php?option=com_lm&task=sender&sid=".session_id()."&qid=".$_SESSION["queue_details"]["queue_id"]."&action=resume\'\" />&nbsp;<input type=\"button\" class=\"button\" value=\"Cancel\" onClick=\"document.getElementById(\'workerFrame\').src = \'index3.php?option=com_lm&task=sender&sid=".session_id()."&qid=".$_SESSION["queue_details"]["queue_id"]."&action=cancel\'\" /></form>';\n";
			echo "\tparent.document.getElementById('progressText').innerHTML	= 'This send has been paused.';\n";
			echo "</script>\n";
			@flush();
			@ob_flush();
		break;
		case "resume" :
			if($_SESSION["queue_details"]["status"] != "Resuming") {
				$query = "UPDATE `".TABLES_PREFIX."queue` SET `status`='Resuming', `touch`='".time()."' WHERE `queue_id`='".checkslashes($_SESSION["queue_details"]["queue_id"])."'";
				$db->Execute($query);
			}
			echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
			echo "\tparent.document.getElementById('buttonHTML').innerHTML	= '';\n";
			echo "\tparent.document.getElementById('progressText').innerHTML	= 'Resuming the send. Please wait...';\n";
			echo "\twindow.location='index3.php?option=com_lm&task=sender&sid=".session_id()."&qid=".$_SESSION["queue_details"]["queue_id"]."&action=send';";
			echo "</script>\n";
			@flush();
			@ob_flush();
		break;
		case "send" :
			if($_SESSION["queue_details"]["status"] != "Sending") {
				$query = "UPDATE `".TABLES_PREFIX."queue` SET `status`='Sending', `touch`='".time()."' WHERE `queue_id`='".checkslashes($_SESSION["queue_details"]["queue_id"])."'";
				$db->Execute($query);
			}
			echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
			echo "\tparent.document.getElementById('progressBar').style.display = ''\n";
			echo "\tparent.document.getElementById('progressText').innerHTML = 'Sending messages. Please wait...';\n";
			echo "\tparent.document.getElementById('buttonHTML').innerHTML	= '<form><input type=\"button\" class=\"button\" value=\"Pause\" onClick=\"document.getElementById(\'workerFrame\').src = \'index3.php?option=com_lm&task=sender&sid=".session_id()."&qid=".$_SESSION["queue_details"]["queue_id"]."&action=pause\'\" />&nbsp;<input type=\"button\" class=\"button\" value=\"Cancel\" onClick=\"document.getElementById(\'workerFrame\').src = \'index3.php?option=com_lm&task=sender&sid=".session_id()."&qid=".$_SESSION["queue_details"]["queue_id"]."&action=cancel\'\" /></form>';\n";
			echo "</script>\n";
			@flush();
			@ob_flush();
			
			@ini_set("sendmail_from", $_SESSION["config"][PREF_ERREMAL_ID]);

			require_once($GLOBALS['mosConfig_absolute_path']."/administrator/components/com_lm/includes/classes/phpmailer/class.phpmailer.php");

			$mail = new PHPMailer();
			$mail->PluginDir = $GLOBALS['mosConfig_absolute_path']."/administrator/components/com_lm/includes/classes/phpmailer/";
			$mail->SetLanguage("en",$GLOBALS['mosConfig_absolute_path']."/administrator/components/com_lm/includes/classes/phpmailer/language/");
					
			/*echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
			echo "\tparent.document.getElementById('debug').value += 'faxby -> ".MAIL_BY."\\n';\n";
			echo'</script>';*/
							
			$mail->Priority	= $_SESSION["message_details"]["message_priority"];
			$mail->CharSet		= $_SESSION["config"][PREF_DEFAULT_CHARSET];
			$mail->Encoding	= "8bit";
			$mail->WordWrap	= $_SESSION["config"][PREF_WORDWRAP];
					
			$from_pieces		= explode("\" <", $_SESSION["message_details"]["message_from"]);	
			$mail->From     	= substr($from_pieces[1], 0, (@strlen($from_pieces[1])-1));
			$mail->FromName	= substr($from_pieces[0], 1, (@strlen($from_pieces[0])));

			$mail->Sender		= $_SESSION["config"][PREF_ERREMAL_ID];
					
			$reply_pieces		= explode("\" <", $_SESSION["message_details"]["message_reply"]);	
			$mail->AddReplyTo(substr($reply_pieces[1], 0, (@strlen($reply_pieces[1])-1)), substr($reply_pieces[0], 1, (@strlen($reply_pieces[0]))));
							
			$date			= $_SESSION["message_details"]["message_date"];
			$subject			= $_SESSION["message_details"]["message_subject"];
					
			$html_template		= $_SESSION["message_details"]["html_template"];
			$html_message		= $_SESSION["message_details"]["html_message"];
					
			$text_template		= $_SESSION["message_details"]["text_template"];
			$text_message		= $_SESSION["message_details"]["text_message"];
			//be read session
			$sbc_message		= $_SESSION["message_details"]["sbc"];
			$spromo_message		= $_SESSION["message_details"]["spromo"];
			$sfusion_message	= $_SESSION["message_details"]["sfusion"];
			$starifs_message	= $_SESSION["message_details"]["starifs"];
			$spos_message		= $_SESSION["message_details"]["spos"];
			$company			= $_SESSION["message_details"]["company"];
			

			// Look for attachments on this message, if they're there and valid, attach them.
			//be attacher un ou plusieurs fichiers fichier
			//empty array
			if($_SESSION["message_details"]["attachments"] != "") {
				$attachments = unserialize($_SESSION["message_details"]["attachments"]);
				if((@is_array($attachments)) && (@count($attachments) > 0)) {
					foreach($attachments as $filename) {
						if(@file_exists($_SESSION["config"][PREF_PUBLIC_PATH]."files/".$filename)) {
							$mail->AddAttachment($_SESSION["config"][PREF_PUBLIC_PATH]."files/".$filename);
						}
					}
					/*echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
					echo "\tparent.document.getElementById('debug').value += 'print -> ".print_r($_SESSION["message_details"]["attachments"])."\\n';\n";
					echo'</script>';*/
				}
			}
			$progress			= $_SESSION["queue_details"]["progress"];
			$errors			= 0;
	
			$query	= "SELECT * FROM `".TABLES_PREFIX."sending` WHERE `queue_id`='".checkslashes($_GET["qid"])."' ORDER BY `sending_id` ASC LIMIT ".checkslashes($progress).", ".$_SESSION["config"][PREF_MSG_PER_REFRESH];
			$results	= $db->GetAll($query);
			
			//bertrand
			//lecture des infos du user
			
			//fax rajoute une marge de 60
			if($results) {
				foreach($results as $result) {
					$progress++;
					$user_data = @unserialize($result["user_data"]);					
					$attachments = array();
					$mail->ClearAttachments();
					//error_log('227 ->'.$user_data['semail']);
					if($user_data["semail"]== 1 && $user_data["sfax"] == 1 ){
						$user_data["sfax"] == 0 ;
					}
					if( ($user_data["semail"] == 1 && valid_address($user_data["email"]) == 1)|
					($user_data["sfax"] == 1 && $user_data["fax"] <> '')){
						if ($user_data["sfax"] == 1){$margin = '60';}else{$margin = '0';}
						if($sbc_message == 1){
							$sbc_message_object = new pdfwriter;
							//$sbc_message_object->listePerso($user_data["userid"],$user_data["company"],$margin);
							$sbc_message_object->listePerso($user_data["userid"], $user_data["company"],$margin);
							$attachments[] = "Lp-".$user_data["userid"].".pdf";
							//error_log('pdf lp');
						}
						if($spromo_message == 1){
							$spromo_message_object = new pdfwriter;
							//$spromo_message_object->promo($user_data["userid"],$user_data["company"],$margin);
							$spromo_message_object->userPromo($user_data["userid"],$user_data["company"],$margin);
							$attachments[] = "Promo-".$user_data["userid"].".pdf";
							//error_log('pdf promo');
						}
						if($sfusion_message == 1){
							//$sfusion_message_object = new pdfwriter;
							//$sfusion_message_object->userFusion($user_data["userid"],$user_data["company"],$margin);
							$attachments[] = "Bon-de-commande-pre-etabli-Alpha-Sales-Bestelbon-vooraf-bepaald-".$user_data["userid"].".pdf";
							//error_log('fusion lp');
						}
						if($starifs_message == 1 && $user_data["sfax"] != 1){
							$starifs_message_object = new pdfwriter;
							//$starifs_message_object->tarifs($margin);
							$starifs_message_object->userShopList($margin);
							$attachments[] = "ShopList-".$user_data["userid"].".pdf";
							//error_log('pdf liste');
						}
						if($spos_message == 1){
							$spos_message_object = new pdfwriter;
							//$spos_message_object->pos($margin);
							$spos_message_object->userPos($margin);
							$attachments[] = "Gift-".$user_data["userid"].".pdf";
							//error_log('pos');
						}
					}else{
						//error_log ('valid email -> '.$user_data["semail"].' / '.valid_address($user_data["email"]).' / '.$user_data["email"],0);
						//error_log ('valid fax -> '.$user_data["sfax"].' / '.$user_data["fax"],0);
					}
					if((@is_array($attachments)) && (@count($attachments) > 0))
					{
						foreach($attachments as $filename) 
						{
							if(@file_exists($GLOBALS['mosConfig_absolute_path'].'/pdf/user/'.$filename)) 
							{
								fwrite($error,$GLOBALS['mosConfig_absolute_path'].'/pdf/user/'.$filename."\n");
								//$mail->AddAttachment($_SESSION["config"][PREF_PUBLIC_PATH]."files/".$filename);
								$mail->AddAttachment($GLOBALS['mosConfig_absolute_path'].'/pdf/user/'.$filename);
								/*echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
								echo "\tparent.document.getElementById('debug').value += 'attach -> ".$GLOBALS['mosConfig_live_site'].'/pdf/user/'.$filename."\\n';\n";
								echo'</script>';*/
								//fwrite($error,$filename."\n");
							}
						}
					}
					fwrite($error,'countattach '.count($attachments)	."\n");
					fwrite($error,$user_data["email"]	."\n");
						
					//echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
					//echo "\tparent.document.getElementById('debug').value += 'count files -> ".count($attachments)."\\n';\n";
					//echo "\tparent.document.getElementById('debug').value += 'mail ï¿½ envoyer -> ".$result["sent"].' -> '."\\n';\n";
					/*echo "\tparent.document.getElementById('debug').value += 'user data semail -> ".$user_data["semail"]."\\n';\n";*/
					/*echo'</script>';*/
					/*****************************
					//envoi par mail
					*******************************/
					//if($result["sent"] == "0" && $user_data["semail"] == 1) {
						if((@is_array($user_data)) && (valid_address($user_data["email"]))) {
							$mail->AddCustomHeader("X-Originating-IP: ".$_SERVER["REMOTE_ADDR"]);
							$mail->AddCustomHeader("List-Help: <".$_SESSION["config"][PREF_PUBLIC_URL].$_SESSION["config"][ENDUSER_HELP_FILENAME].">");
							$mail->AddCustomHeader("List-Owner: <mailto:".$mail->From."> (".$mail->FromName.")");
							$mail->AddCustomHeader("List-Unsubscribe: <".$_SESSION["config"][PREF_PUBLIC_URL].$_SESSION["config"][ENDUSER_UNSUB_FILENAME]."&addr=".$user_data["email"].">");
							$mail->AddCustomHeader("List-Archive: <".$_SESSION["config"][PREF_PUBLIC_URL].$_SESSION["config"][ENDUSER_ARCHIVE_FILENAME].">");
							$mail->AddCustomHeader("List-Post: NO");
	
							$mail->Subject	= custom_data($user_data, $subject);
							$mail->ClearAddresses();
							//be $mail send to
							if($user_data["sfax"] == 1)
							//if($result["sent"] == "0" && $user_data["semail"] == 1) 
							{
								//$mail->AltBody	= custom_data($user_data, insert_template("text", $text_template, $text_message), "text", (($user_data["groupid"]) ? true : false));
								$mail->Body	= custom_data($user_data, insert_template("text", $text_template, $text_message), "text", (($user_data["groupid"]) ? true : false));
								$mail->AltBody	= '';
								$mail->IsSMTP();
								$mail->Host     		= "mail.distri-one.be";
								$mail->Port 			= "2525";
								$mail->Password 		= "smtpPassword";
								$mail->SMTPKeepAlive	= true; 
								$mail->AddAddress($user_data["fax"].'@fax.adsgroup.eu', $user_data["company"]);		
							}
							if($result["sent"] == "0" && $user_data["semail"] == 1) {
								//error_log('send mail');
								if(strlen(trim($html_message)) > 0) {
									//be rajoute le lien pour se dï¿½sincrire
									//$mail->Body	= custom_data($user_data, unsubscribe_message(insert_template("html", $html_template, $html_message), "html", (($user_data["groupid"]) ? true : false)));
									$mail->Body	= custom_data($user_data, insert_template("html", $html_template, $html_message), "html", (($user_data["groupid"]) ? true : false));
									$mail->AltBody	= custom_data($user_data, insert_template("text", $text_template, $text_message), "text", (($user_data["groupid"]) ? true : false));
								} else {
									$mail->Body	= custom_data($user_data, unsubscribe_message(insert_template("text", $text_template, $text_message), "text", (($user_data["groupid"]) ? true : false)));
								}
								$mail->IsMail();
								$mail->AddAddress($user_data["email"], $user_data["company"]);
							}
							/****************************
							//bertrand mail dï¿½sactivï¿½
							 * gestion erreur pour fax à rajouter
							****************************/
							if($user_data["sfax"] != '1'){
							if($mail->Send()) {
							//if(true) {
								fwrite($error,'mail send to '.$user_data["email"].' - '.$user_data["id"]."\n");
								$query	= "UPDATE `".TABLES_PREFIX."sending` SET `sent`='1' WHERE `sending_id`='".$result["sending_id"]."'";
								$result	= $db->Execute($query);
								if($result) {
									$percentage = (ceil(($progress / $_SESSION["queue_details"]["total"]) * 100));
									echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
									echo "\tparent.document.getElementById('progressStatus').style.width = '".$percentage."%';\n";
									if($percentage > 3) {
										echo "\tparent.document.getElementById('progressStatus').innerHTML = '".$percentage."%';\n";
									} else {
										echo "\tparent.document.getElementById('progressStatus').innerHTML = '';\n";
									}
									echo "</script>\n";
								} else {
									$errors++;
									echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
									echo "\tparent.document.getElementById('progressStatus').innerHTML = '';\n";
									echo "\tparent.document.getElementById('errorText').value += 'Failed to update sending record for ".$user_data["email"].", check error log for details.\\n';\n";
									echo "</script>\n";
									if($_SESSION["config"][PREF_ERROR_LOGGING] == "yes") {
										@error_log(display_date("r", time())."\t".__FILE__." [Line: ".__LINE__."]\tUnable to update sending record for ".$user_data["email"].". Database server said: ".$db->ErrorMsg()."\n", 3, $_SESSION["config"][PREF_PRIVATE_PATH]."logs/error_log.txt");
									}
								}
							} else if ($user_data["smail"] != '1'){
								$error++;
								echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
								echo "\tparent.document.getElementById('progressStatus').innerHTML = '';\n";
								echo "\tparent.document.getElementById('errorText').value += 'No sending to ".$user_data["email"].", update your data to send an fax or email.\\n';\n";
								echo "</script>\n";
								if($_SESSION["config"][PREF_ERROR_LOGGING] == "yes") {
									@error_log(display_date("r", time())."\t".__FILE__." [Line: ".__LINE__."]\tUnable to send message to ".$user_data["email"].". PHPMailer said: ".$mail->ErrorInfo."\n", 3, $_SESSION["config"][PREF_PRIVATE_PATH]."logs/error_log.txt");
								}
							}else {
								$error++;
								echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
								echo "\tparent.document.getElementById('progressStatus').innerHTML = '';\n";
								echo "\tparent.document.getElementById('errorText').value += 'Failed sending to ".$user_data["email"].", check error log for details.\\n';\n";
								echo "</script>\n";
								if($_SESSION["config"][PREF_ERROR_LOGGING] == "yes") {
									@error_log(display_date("r", time())."\t".__FILE__." [Line: ".__LINE__."]\tUnable to send message to ".$user_data["email"].". PHPMailer said: ".$mail->ErrorInfo."\n", 3, $_SESSION["config"][PREF_PRIVATE_PATH]."logs/error_log.txt");
								}
							}
						}else{error_log('Pas de fax '.$user_data["userid"]);}
							$mail->ClearCustomHeaders();
							@flush();
							@ob_flush();
						}
					///faxing data
					//}else if($user_data["sfax"] == 1 && $user_data["fax"])
					/*{
						echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
						echo "\tparent.document.getElementById('progressStatus').value += 'fax sended to -> ".$user_data["name"]."\\n';\n";
						echo'</script>';	
					}*/
				}
				//be end foreach
				
	
				if($mail->Mailer == "smtp") $mail->SmtpClose();
		
				$mail->ClearAttachments();
		
				@ini_restore("sendmail_from");
			
				$query = "UPDATE `".TABLES_PREFIX."queue` SET `progress`='".checkslashes($progress)."', `touch`='".time()."' WHERE `queue_id`='".checkslashes($_SESSION["queue_details"]["queue_id"])."'";
				if($db->Execute($query)) {
					$total_batches	= ceil($_SESSION["queue_details"]["total"] / $_SESSION["config"][PREF_MSG_PER_REFRESH]);
					$sent_batch	= ($total_batches - (ceil(($_SESSION["queue_details"]["total"] - $progress) / $_SESSION["config"][PREF_MSG_PER_REFRESH])));
					
					if($sent_batch != $total_batches) {
						echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
						echo "\tparent.document.getElementById('progressText').innerHTML = 'Sent batch ".$sent_batch." of ".$total_batches."; pausing for ".$_SESSION["config"][PREF_PAUSE_BETWEEN]." second".(($_SESSION["config"][PREF_PAUSE_BETWEEN] != 1) ? "s" : "").".';\n";
						echo "\tsetTimeout('window.location=\'index3.php?option=com_lm&task=sender&sid=".session_id()."&qid=".$_SESSION["queue_details"]["queue_id"]."&action=send\'', ".($_SESSION["config"][PREF_PAUSE_BETWEEN] * 1000).");";
						echo "</script>\n";
					} else {
						echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
						echo "\tparent.document.getElementById('buttonHTML').innerHTML	= '';\n";
						echo "\tparent.document.getElementById('progressText').innerHTML = 'Sent batch ".$sent_batch." of ".$total_batches.".';\n";
						echo "\twindow.location = 'index3.php?option=com_lm&task=sender&sid=".session_id()."&qid=".$_SESSION["queue_details"]["queue_id"]."&action=send';";
						echo "</script>\n";
					}
				} else {
					if($_SESSION["config"][PREF_ERROR_LOGGING] == "yes") {
						@error_log(display_date("r", time())."\t".__FILE__." [Line: ".__LINE__."]\tUnable to update queue information. Database server said: ".$db->ErrorMsg()."\n", 3, $_SESSION["config"][PREF_PRIVATE_PATH]."logs/error_log.txt");
					}
				}
			} else {
				$query = "DELETE FROM `".TABLES_PREFIX."sending` WHERE `queue_id`='".checkslashes($_SESSION["queue_details"]["queue_id"])."'";
				if($db->Execute($query)) {
					$query = "OPTIMIZE TABLE `".TABLES_PREFIX."sending`";
					if(!$db->Execute($query)) {
						if($_SESSION["config"][PREF_ERROR_LOGGING] == "yes") {
							@error_log(display_date("r", time())."\t".__FILE__." [Line: ".__LINE__."]\tUnable to optimize the sending table. Database server said: ".$db->ErrorMsg()."\n", 3, $_SESSION["config"][PREF_PRIVATE_PATH]."logs/error_log.txt");
						}
					}
					$query = "UPDATE `".TABLES_PREFIX."queue` SET `status`='Complete' WHERE `queue_id`='".checkslashes($_SESSION["queue_details"]["queue_id"])."'";
					if(!$db->Execute($query)) {
						if($_SESSION["config"][PREF_ERROR_LOGGING] == "yes") {
							@error_log(display_date("r", time())."\t".__FILE__." [Line: ".__LINE__."]\tUnable to update queue status in the queue table. Database server said: ".$db->ErrorMsg()."\n", 3, $_SESSION["config"][PREF_PRIVATE_PATH]."logs/error_log.txt");
						}
					}
				} else {
					if($_SESSION["config"][PREF_ERROR_LOGGING] == "yes") {
						@error_log(display_date("r", time())."\t".__FILE__." [Line: ".__LINE__."]\tUnable to delete queue data in the sending table. Database server said: ".$db->ErrorMsg()."\n", 3, $_SESSION["config"][PREF_PRIVATE_PATH]."logs/error_log.txt");
					}
				}
	
				echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
				echo "\tparent.document.getElementById('progressText').innerHTML = 'Completed sending your message to ".$_SESSION["queue_details"]["total"]." subscriber".(($_SESSION["queue_details"]["total"] != "1") ? "s" : "").". Click finish to continue.';\n";
				echo "\tparent.document.getElementById('buttonHTML').innerHTML	= '<form><input type=\"button\" class=\"button\" value=\"Finish\" onClick=\"parent.window.location=\'index2.php?option=com_lm&section=message&action=view&id=".$_SESSION["message_details"]["message_id"]."\'\" /></form>';\n";
				echo "</script>\n";

				unset($_SESSION["unsubscribe_message"], $_SESSION["queue_details"], $_SESSION["message_details"]);
			}
		break;
		default : // Queue Messages.
			$query = "UPDATE `".TABLES_PREFIX."queue` SET `status`='Preparing' WHERE `queue_id`='".checkslashes($_SESSION["queue_details"]["queue_id"])."'";
			$db->Execute($query);

			echo "<script language=\"JavaScript\" type=\"text/javascript\">parent.document.getElementById('progressText').innerHTML = 'Preparing data to generate the queue. Please wait...';</script>\n";
			@flush();
			@ob_flush();

			if((@is_array($_SESSION["queue_details"]["target"])) && (@count($_SESSION["queue_details"]["target"]) > 0)) {
				$where_clause = $_SESSION["queue_details"]["target"];
				array_walk($where_clause, "where_clause");

				$query	= "SELECT DISTINCT `user_email` FROM `".TABLES_PREFIX."vm_user_info` WHERE ".implode(" OR ", $where_clause);
				$results	= $db->GetAll($query);
				if(($results) && (@count($results) > 0)) {
					if((@is_writable($_SESSION["config"][PREF_PRIVATE_PATH]."tmp/")) && (PREF_LOAD_FILE == "yes")) {
						echo "<script language=\"JavaScript\" type=\"text/javascript\">parent.document.getElementById('progressText').innerHTML = 'Creating a dump file to be loaded into the database. Please wait...';</script>\n";
						@flush();
						@ob_flush();

						$load_file = $_SESSION["config"][PREF_PRIVATE_PATH]."tmp/.".$_SESSION["queue_details"]["queue_id"]."_".$_SESSION["queue_details"]["date"];
						if(!$handle = @fopen($load_file, "w")) {
							echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
							echo "\tparent.document.getElementById('progressText').innerHTML = 'Failed to create the temporary load file.';\n";
							echo "\talert('Failed to create the load file in your temp directory. Make sure your specified temporary directory is writeable and accessible by PHP.\n\nIf this keeps failing, try setting \"Use MySQL Load Data\" to \"No\" in Preferences.');\n";
							echo "</script>\n";
							echo "</body>\n";
							echo "</html>\n";
							exit;
						}
						foreach($results as $result) {
							$user_data	= get_custom_data($result["user_email"]);
							$row			= $_SESSION["queue_details"]["queue_id"]."\t".serialize($user_data)."\t0\n";

							if (@fwrite($handle, $row) === FALSE) {
								echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
								echo "\tparent.document.getElementById('progressText').innerHTML = 'Failed to write the temporary load file.';\n";
								echo "\talert('Failed to write to the load file in your temp directory. Make sure your specified temporary directory is writeable and accessible by PHP.\n\nIf this keeps failing, try setting \"Use MySQL Load Data\" to \"No\" in Preferences.');\n";
								echo "</script>\n";
								echo "</body>\n";
								echo "</html>\n";
								exit;
							}
						}
						
						@fclose($handle);
						
						$query	= "LOAD DATA INFILE '".$load_file."' INTO TABLE `".DATABASE_NAME."`.`".TABLES_PREFIX."sending` FIELDS TERMINATED BY '\\t' LINES TERMINATED BY '\\n'(`queue_id` , `user_data` , `sent`)";
						if($db->Execute($query)) {
							$num	= $db->Affected_Rows();
							echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
							echo "function readyToGo() {\n";
							echo "\tvar is_confirmed = confirm('Your message has been successfully queued and is ready to be sent.\\n\\nIf you would like to proceed to send this message to ".$num." subscriber".(($num != 1) ? "s" : "")." press OK, otherwise click Cancel to cancel the queue and return to the message centre.');\n";
							echo "\tif (is_confirmed == true) {\n";
							echo "\t\twindow.location='index3.php?option=com_lm&task=sender&sid=".session_id()."&qid=".$_SESSION["queue_details"]["queue_id"]."&action=send';\n";
							echo "\t\treturn;\n";					
							echo "\t} else {\n";
							echo "\t\twindow.location='index3.php?option=com_lm&task=sender&sid=".session_id()."&qid=".$_SESSION["queue_details"]["queue_id"]."&action=cancel';\n";
							echo "\t\treturn;\n";
							echo "\t}\n";
							echo "}\n\n";							
							echo "parent.document.getElementById('progressText').innerHTML = 'Successfully loaded ".$num." address".(($num != 1) ? "es" : "")." into the sending queue.';\n";
							echo "readyToGo();\n";
							echo "</script>\n";
							@flush();
							@ob_flush();	
						} else {
							if($_SESSION["config"][PREF_ERROR_LOGGING] == "yes") {
								@error_log(display_date("r", time())."\t".__FILE__." [Line: ".__LINE__."]\tUnable to load data into sending table. Database server said: ".$db->ErrorMsg()."\n", 3, $_SESSION["config"][PREF_PRIVATE_PATH]."logs/error_log.txt");
							}
							echo "<script language=\"JavaScript\" type=\"text/javascript\">parent.document.getElementById('progressText').innerHTML = 'Failed to load queue data into sending table. Please see your error log for more details.';</script>\n";
							@flush();
							@ob_flush();
						}
						@unlink($load_file);
					} else {
						echo "<script language=\"JavaScript\" type=\"text/javascript\">parent.document.getElementById('progressText').innerHTML = 'Inserting queue data into the database. Please wait...';</script>\n";
						@flush();
						@ob_flush();

						$num = 0;
						foreach($results as $result) {
							$user_data	= get_custom_data($result["user_email"]);
							$query		= "INSERT INTO `".TABLES_PREFIX."sending` (`sending_id`, `queue_id`, `user_data`, `sent`) VALUES ('', '".$_SESSION["queue_details"]["queue_id"]."', '".addslashes(serialize($user_data))."', '0');";
							if(!$db->Execute($query)) {
								if($_SESSION["config"][PREF_ERROR_LOGGING] == "yes") {
									@error_log(display_date("r", time())."\t".__FILE__." [Line: ".__LINE__."]\tUnable to add ".$result["user_email"]." to send queue. Database server said: ".$db->ErrorMsg()."\n", 3, $_SESSION["config"][PREF_PRIVATE_PATH]."logs/error_log.txt");
								}
							} else {
								$num++;
							}
						}
						echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
						echo "function readyToGo() {\n";
						echo "\tvar is_confirmed = confirm('Your message has been successfully queued and is ready to be sent.\\n\\nIf you would like to proceed to send this message to ".$num." subscriber".(($num != 1) ? "s" : "")." press OK, otherwise click Cancel to cancel the queue and return to the message centre.');\n";
						echo "\tif (is_confirmed == true) {\n";
						echo "\t\twindow.location='index3.php?option=com_lm&task=sender&sid=".session_id()."&qid=".$_SESSION["queue_details"]["queue_id"]."&action=send';\n";
						echo "\t\treturn;\n";					
						echo "\t} else {\n";
						echo "\t\twindow.location='index3.php?option=com_lm&task=sender&sid=".session_id()."&qid=".$_SESSION["queue_details"]["queue_id"]."&action=cancel';\n";
						echo "\t\treturn;\n";
						echo "\t}\n";
						echo "}\n\n";							
						echo "parent.document.getElementById('progressText').innerHTML = 'Successfully inserted ".$num." address".(($num != 1) ? "es" : "")." into the sending queue.';\n";
						echo "readyToGo();\n";
						echo "</script>\n";
						@flush();
						@ob_flush();
					}
					
				} else {
					if($_SESSION["config"][PREF_ERROR_LOGGING] == "yes") {
						@error_log(display_date("r", time())."\t".__FILE__." [Line: ".__LINE__."]\tThere are no e-mail addresses in the query. Database server said: ".$db->ErrorMsg()."\n", 3, $_SESSION["config"][PREF_PRIVATE_PATH]."logs/error_log.txt");
					}
					echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
					echo "\tparent.document.getElementById('progressText').innerHTML = 'No subscribers to load into queue.';\n";
					echo "\talert('There were no subscribers in any of the groups you selected to load into the sending queue.\n\nPlease choose a group to send to which contains subscribers.');\n";
					echo "</script>\n";
					echo "</body>\n";
					echo "</html>\n";
					exit;
				}
				
			} else {
				if($_SESSION["config"][PREF_ERROR_LOGGING] == "yes") {
					@error_log(display_date("r", time())."\t".__FILE__." [Line: ".__LINE__."]\tThe sending engine was provided with a group to send to which was not an array. Request made by: ".$_SERVER["REMOTE_ADDR"]."\n", 3, $_SESSION["config"][PREF_PRIVATE_PATH]."logs/error_log.txt");
				}
				echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
				echo "\tparent.document.getElementById('progressText').innerHTML = 'Invalid group selected to send to.';\n";
				echo "\talert('Sending engine was provided with invalid groups of users.');\n";
				echo "</script>\n";
				echo "</body>\n";
				echo "</html>\n";
				exit;
			}
		break;
	}
	echo "</body>\n";
	echo "</html>\n";

	@ini_restore("include_path");
	@ini_restore("allow_url_fopen");
	@ini_restore("session.name");
	@ini_restore("session.use_trans_sid");
	@ini_restore("session.cookie_lifetime");
	@ini_restore("session.cookie_secure");
	@ini_restore("session.referer_check");
	@ini_restore("error_reporting");
	@ini_restore("magic_quotes_runtime");
	fclose($error);
?>