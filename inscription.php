<?php
session_start();

require('src/connextionBDD.php');

if (isset($_POST['email']) && !empty($_POST['email']) 
	&& isset($_POST['password']) && !empty($_POST['password']) 
	&& isset($_POST['password_two']) && !empty($_POST['password_two'])) {

	$email			= htmlspecialchars($_POST['email']);
	$password		= htmlspecialchars($_POST['password']);
	$password_two	= htmlspecialchars($_POST['password_two']);

	// Vérifier que le mot de passe et la confirmation est similaire
	if ($password !== $password_two) {
		header('location: ../inscription.php?error=1&pass=1');
		exit();
	}

	// vérifier que le format du mail est correct
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		header('location: ../inscription.php?error=1&format=1');
		exit();
	}

	// Vérifier si l'email n'est pas utilisé
	$req = $bdd->prepare('SELECT COUNT(*) as x FROM user WHERE email = ?');
	$req->execute([$email]);

	while ($result = $req->fetch()) {
		if ($result['x'] != 0) {
			header('location: ../inscription.php?error=1&email=1');
			exit();
		}
	}

	// cryptage du password et du secret
	$secret = sha1($password) . time();
	$password = sha1($password) . '4328';

	//Créer un utilisateur
	$req = $bdd->prepare('INSERT INTO user(email, password,secret) VALUES (?, ?, ?)');
	$req->execute([$email, $password, $secret]);

	header('location: ../?success=1');
	exit();
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
			<h1>S'inscrire</h1>
			<?php if (isset($_GET['error'])) { 
				if (isset($_GET['pass']) == 1) { ?>
					<div class="alert alert-danger" role="alert">les mots de passe ne sont pas identiques</div>
				<?php } if (isset($_GET['format'])) { ?>
					<div class="alert alert-danger" role="alert">Email incorrect </div>
				<?php } if (isset($_GET['email'])) { ?>
					<div class="alert alert-danger" role="alert">Email déjà utilisé</div>
				<?php }
			} ?>
			<form method="post" action="inscription.php">
				<input type="email" name="email" placeholder="Votre adresse email" required />
				<input type="password" name="password" placeholder="Mot de passe" required />
				<input type="password" name="password_two" placeholder="Retapez votre mot de passe" required />
				<button type="submit">S'inscrire</button>
			</form>

			<p class="grey">Déjà sur Netflix ? <a href="index.php">Connectez-vous</a>.</p>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>
</html>