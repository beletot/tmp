<?php
$this->data['icon'] = 'lock.png';
$this->data['header'] = $this->t('{login:user_pass_header}');

if (strlen($this->data['username']) > 0) {
    $this->data['autofocus'] = 'password';
} else {
    $this->data['autofocus'] = 'username';
}
$this->includeAtTemplateBase('includes/header.php');
?>
<?php
if ($this->data['errorcode'] !== NULL) {
    
?>
<div style="border-left: 1px solid #e8e8e8; border-bottom: 1px solid #e8e8e8; background: #f5f5f5">
    <h2>
        <?php echo $this->t('{login:error_header}'); ?>
    </h2>
    <p>
        <b>
            <?php echo $this->t('{errors:title_'.$this->data['errorcode'].'}'); ?>
        </b>
    </p>
    <p>
        <?php echo $this->t('{errors:descr_'.$this->data['errorcode'].'}'); ?>
    </p>
</div>

<script type="text/javascript">
//var url ='http://extranet.epfc.eu/index.php';
var url = 'http://extranet.epfc.eu/_dev-extranet/';
var delay = 3;
var d = delay * 1000;window.setTimeout ('parent.location.replace(url)', d);
</script>
<?php
}else{
?>
<form action="?" method="post" name="f">
    <div class="mod-rounded">
        <div class="box-t1">
            <div class="box-t2">
                <div class="box-t3">
                </div>
            </div>
        </div>
        <div class="box-1">
            <div class="box-2">
                <div class="box-3 deepest">
                    <h2 class="header">Epfc - connexion sur l'extranet</h2>
                    <span style="display: block;" class="niftyquick">
						<span class="yoo-login">
							<span class="login">
								<span class="username"><input type="text" onfocus="if(this.value=='Identifiant') this.value='';" onblur="if(this.value=='') this.value='Identifiant';" value="Identifiant" name="username" tabindex="1" id="username" size="18">
								</span>
								<span class="password">
									<input type="password" value="" alt="Mot de passe" size="10" name="password" tabindex="2" id="password">
								</span>
								<span class="login-button">
                                    <button title="Connexion" type="submit" name="Submit" value="S'identifier" tabindex="4">
                                        S'identifier
                                    </button>
                                </span>
								</span></span></span>
                    <?php
                    echo '<p><strong>Exemple d\'indentifiant :</strong>  rmichel<br />Uniquement des lettres, vous ne pouvez pas le modifier.<br />Vous avez reçu par mail <strong>votre mot de passe</strong>.
                     Vous pouvez le modifier en utilisant le bouton "Mot de passe oublié", il est situé à droite de "Connexion" sur la page d\'accueil de l\'Extranet</p>';
                    //echo('<h3>'.$this->t('{login:help_header}').'</h3>');
                    //echo('<p>'.$this->t('{login:help_text}').'</p>');
                    // //$this->includeAtTemplateBase('includes/footer.php');
                    ?>
                </div>
            </div>
        </div>
        <div class="box-b1">
            <div class="box-b2">
                <div class="box-b3">
                </div>
            </div>
        </div>
    </div>
    <?php
    foreach ($this->data['stateparams'] as $name=>$value) {
     echo('<input type="hidden" name="'.htmlspecialchars($name).'" value="'.htmlspecialchars($value).'" />');
     }
    ?>
</form>
<?php }?>
