<?php


    if (isset($_COOKIE['auth']) && !isset($_SESSION['connect'])) {

        require('src/connextionBDD.php');

        $secret = htmlspecialchars($_COOKIE['auth']);

        $req = $bdd->prepare('SELECT count(*) as x FROM user WHERE secret = ?');
        $req->execute([$secret]);

        while ($user = $req->fetch()) {
            if ($user['x'] == 1) {
                
                $reqUser = $bdd->prepare('SELECT * FROM user WHERE secret = ?');
                $reqUser->execute([$secret]);
                
                while ($userAccount = $reqUser->fetch()) {

                    $_SESSION['connect'] = 1;
                    $_SESSION['email'] = $userAccount['email'];
                }
            }
        }
    }

?>