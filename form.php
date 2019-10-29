    <?php
    /*
    	********************************************************************************************
    	CONFIGURATION
    	********************************************************************************************
    */
    // destinataire est votre adresse mail. Pour envoyer à plusieurs à la fois, séparez-les par une virgule
    $destinataire = 'brandone.sape@gmail.com';

    // copie ? (envoie une copie au visiteur)
    $copie = 'oui'; // 'oui' ou 'non'
     
    // Messages de confirmation du mail
    $message_envoye = "Your message has been well received !";
    $message_non_envoye = "Sending mail failed, please try again.";
     
    // Messages d'erreur du formulaire
    $message_erreur_formulaire = "You must first <a href=\"CV_web.html\">send the form</a>.";
    $message_formulaire_invalide = "Check that all fields are filled in and that the Email is error-free.";
     
    /*
    	********************************************************************************************
    	FIN DE LA CONFIGURATION
    	********************************************************************************************
    */
     
    // on teste si le formulaire a été soumis
    if (!isset($_POST['send']))
    {
    	// formulaire non envoyé
    	echo '<p>'.$message_erreur_formulaire.'</p>'."\n";
    }
    else
    {
    	/*
    	 * cette fonction sert à nettoyer et enregistrer un texte
    	 */
    	function Rec($text)
    	{
    		$text = htmlspecialchars(trim($text), ENT_QUOTES);
    		if (1 === get_magic_quotes_gpc())
    		{
    			$text = stripslashes($text);
    		}
     
    		$text = nl2br($text);
    		return $text;
    	};
     
    	/*
    	 * Cette fonction sert à vérifier la syntaxe d'un email
    	 */
    	function IsEmail($email)
    	{
    		$value = preg_match('/^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!\.)){0,61}[a-zA-Z0-9_-]?\.)+[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!$)){0,61}[a-zA-Z0-9_]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/', $email);
    		return (($value === 0) || ($value === false)) ? false : true;
    	}
     
    	// formulaire envoyé, on récupère tous les champs.
        $objet   = (isset($_POST['title']))   ? Rec($_POST['title'])   : '';
    	$nom     = (isset($_POST['name']))     ? Rec($_POST['name'])     : '';
    	$email   = (isset($_POST['email']))   ? Rec($_POST['email'])   : '';
    	$message = (isset($_POST['message'])) ? Rec($_POST['message']) : '';
     
    	// On va vérifier les variables et l'email ...
    	$email = (IsEmail($email)) ? $email : ''; // soit l'email est vide si erroné, soit il vaut l'email entré
     
    	if (($objet != '') && ($nom != '') && ($email != '') && ($message != ''))
    	{
    		// les 4 variables sont remplies, on génère puis envoie le mail
    		$headers  = 'MIME-Version: 1.0' . "\r\n";
    		$headers .= 'From:'.$nom.' <'.$email.'>' . "\r\n" .
    				'Reply-To:'.$email. "\r\n" .
    				'Content-Type: text/plain; charset="utf-8"; DelSp="Yes"; format=flowed '."\r\n" .
    				'Content-Disposition: inline'. "\r\n" .
    				'Content-Transfer-Encoding: 7bit'." \r\n" .
    				'X-Mailer:PHP/'.phpversion();

            // envoyer une copie au visiteur ?
            if ($copie == 'oui')
            {
                $cible = $destinataire.';'.$email;
            }
            else
            {
                $cible = $destinataire;
            };

    		// Remplacement de certains caractères spéciaux
    		$caracteres_speciaux     = array('&#039;', '&#8217;', '&quot;', '<br>', '<br />', '&lt;', '&gt;', '&amp;', '…',   '&rsquo;', '&lsquo;');
    		$caracteres_remplacement = array("'",      "'",        '"',      '',    '',       '<',    '>',    '&',     '...', '>>',      '<<'     );
     
    		$objet = html_entity_decode($objet);
    		$objet = str_replace($caracteres_speciaux, $caracteres_remplacement, $objet);
     
    		$message = html_entity_decode($message);
    		$message = str_replace($caracteres_speciaux, $caracteres_remplacement, $message);
     
    		// Envoi du mail
    		$num_emails = 0;
    		$tmp = explode(';', $cible);
    		foreach($tmp as $email_destinataire)
    		{
    			if (mail($email_destinataire, $objet, $message, $headers))
    				$num_emails++;
    		}
     
    		if ((($copie == 'oui') && ($num_emails == 2)) || (($copie == 'non') && ($num_emails == 1)))
    		{
    			echo '<p>'.$message_envoye.'</p>';
    		}
    		else
    		{
    			echo '<p>'.$message_non_envoye.'</p>';
    		};
    	}
    	else
    	{
    		// une des 3 variables (ou plus) est vide ...
    		echo '<p>'.$message_formulaire_invalide.' <a href="CV_web.html">back to the homepage</a></p>'."\n";
    	};
    }; // fin du if (!isset($_POST['send']))
?>