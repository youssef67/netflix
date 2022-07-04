<?php

session_start();

require('src/log.php');

if (isset($_POST['email']) && !empty($_POST['email']) 
	&& isset($_POST['password']) && !empty($_POST['password'])) {
		
		require('src/connextionBDD.php');

		$email		= htmlspecialchars($_POST['email']);
		$password	= htmlspecialchars($_POST['password']);

		// Vérifier si l'adresse email est valide
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			header("location: ../index.php?error=1&email=1");
			exit();
		}

		
		//Cryptage du mot de passe
		$passwordCrypt = sha1($password . '4328') . "25";
		
		// Verifier en BDD si email existe
		$req = $bdd->prepare('SELECT COUNT(*) AS x FROM user WHERE email = ?');
		$req->execute([$email]);

		while ($result = $req->fetch()) {
			if ($result['x'] != 1) {
				header('location: ../index.php?error=1&noEmail=1');
				exit();
			}
		}

		//connexion 
		$req = $bdd->prepare('SELECT * FROM user WHERE email = ?');
		$req->execute([$email]);

		while ($user = $req->fetch()) {
			if ($passwordCrypt == $user['password']) {
				$error = 0;
				$_SESSION['connect'] = 1;
				$_SESSION['email'] = $user['email'];


				if (isset($_POST['auto'])) {
					setcookie('auth', $user['secret'], time() + 365*24*3600, '/', null, false, true);
				}

				header('location: ../index.php?connect=1');
                exit();

			} else {
				header('location: ../index.php?error=1&email=1');
			}
		}
	}

?> 


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Netflix</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<link rel="icon" type="image/pngn" href="img/favicon.png">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
</head>
<body>

	<?php include('src/header.php'); ?>
	
	<section>
		<div id="login-body">
				<?php if (!isset($_SESSION['connect'])) { ?>
				<h1>S'identifier</h1>
				<?php if (isset($_GET['success'])) { ?> 
					<div class="alert alert-success" role="alert">Inscription réussie</div>
				<?php } ?>
				<?php if (isset($_GET['error']) == 1) { 
					if (isset($_GET['email']) == 1) { ?> 
					<div class="alert alert-danger" role="alert">Mot de passe incorrect</div>
				<?php } else if (isset($_GET['noEmail']) == 1) { ?> 
					<div class="alert alert-danger" role="alert">Cet email n'existe pas</div>
				<?php }
				} ?>
				<form method="post" action="index.php">
					<input type="email" name="email" placeholder="Votre adresse email" required />
					<input type="password" name="password" placeholder="Mot de passe" required />
					<button type="submit">S'identifier</button>
					<label id="option"><input type="checkbox" name="auto" checked />Se souvenir de moi</label>
				</form>
			

				<p class="grey">Première visite sur Netflix ? <a href="inscription.php">Inscrivez-vous</a>.</p>
				<?php } else { ?>
					<div> bienvenue <?= $_SESSION['email'] ?></div>
					<a class="btn btn-primary" role="button" href="src/deconnexion.php">me deconnecter</a>
				<?php } ?>	
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>
</html>