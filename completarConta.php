<?php

session_start();

include("conectarBd.php");

if (!isset($_SESSION["completar_conta_status"]) || $_SESSION["completar_conta_status"] != 1) {
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
    header("Location: index.php");
}

$_SESSION["completar_conta"] = 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/bootstrap.css">
    <script src="js/bootstrap.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="style/estilo.css">
    <title>Listar Utilizadores</title>
</head>

<body>

    <pre>
    <?php
    var_dump($_POST);
    ?>
    </pre>

    <nav class="navbar navbar-expand-sm navbar-dark bg-dark" aria-label="Third navbar example">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="img/logo.png" width="30" height="30" alt="logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample03" aria-controls="navbarsExample03" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto mb-2 mb-sm-0"></ul>
                <span class="navbar-text">
                    <?php //loginSection(); 
                    ?>
                </span>
            </div>
        </div>
    </nav>

    <header>
        <section class="jumbotron text-center margem-topo">
            <div class="container">
                <h1>Preencha o captcha</h1>
                <p class="lead text-muted">Para Concluir o registo da sua conta porfavor comlete o captcha abaixo.</p>
            </div>
        </section>
    </header>


    <main class="bg-light h-100">
        <div class="d-flex d-flex aligns-items-center justify-content-center">
            <div class="mt-5">
                <form action="index.php" method="post">
                    <div class="bg-white ps-5 pt-3 pb-3 rounded-3 border border-5">
                        <?php
                        require("captcha-php/1-captcha.php");
                        $PHPCAP->prime();
                        $PHPCAP->draw();
                        ?>
                    </div>
                    <br>

                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">Digite o texto da imagem?</span>
                        <input type="text" class="form-control" placeholder="Captcha" aria-label="Captcha" aria-describedby="basic-addon1" name="captcha">
                    </div>

                    <input type="submit" value="Submeter" name="form_captcha" class="btn btn-primary mt-3">
                </form>
            </div>
        </div>
    </main>

    <div class="container">
        <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">

            <div class="col-md-4 d-flex align-items-center">
                <a href="/" class="mb-3 me-2 mb-md-0 text-muted text-decoration-none lh-1">
                    <svg class="bi" width="30" height="24">
                        <use xlink:href="#bootstrap"></use>
                    </svg>
                </a>
                <span class="text-muted">© 2021 Company, Inc</span>
            </div>

            <ul class="nav col-md-4 justify-content-end list-unstyled d-flex">
                <li class="ms-3"><a class="text-muted" href="#"><svg class="bi" width="24" height="24">
                            <use xlink:href="#twitter"></use>
                        </svg></a></li>
                <li class="ms-3"><a class="text-muted" href="#"><svg class="bi" width="24" height="24">
                            <use xlink:href="#instagram"></use>
                        </svg></a></li>
                <li class="ms-3"><a class="text-muted" href="#"><svg class="bi" width="24" height="24">
                            <use xlink:href="#facebook"></use>
                        </svg></a></li>
            </ul>
        </footer>
    </div>

</body>

</html>