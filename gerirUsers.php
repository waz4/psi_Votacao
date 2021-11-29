<tr>
    <?php 
session_start();

include("conectarBd.php");

if(!isset($_SESSION["NIVEL_UTILIZADOR"]) || $_SESSION["NIVEL_UTILIZADOR"] != 2){
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
    header("Location: index.php");
}

$campoPesquisa = "";

function loginSection()
{
    if (!isset($_SESSION["username"])) {
    ?>
        <a class="text-decoration-none text-white" data-bs-toggle="modal" href="#modalLogin" role="button" id="modal_btn_modalLogin">Login</a>
    <?php
    } else {
    ?>
        <span class=" text-white fst-none"><?php echo $_SESSION["username"]; ?> (</span>
        <form action="#" method="post" class="d-inline m-0" id="sair">
            <input type="hidden" name="sair">
            <a href="javascript:{}" onclick="document.getElementById('sair').submit();" class="text-secondary">Sair</a>
        </form>
        <span class="text-white">)</span>
    <?php
    }
}

function deleteUser($username) {
    $conn = OpenCon();

    $result_votacoes = mysqli_query($conn, "SELECT id_votacao FROM votacoes WHERE username = '" . $username . "';");

    if (mysqli_num_rows($result_votacoes) != 0) {
        while ($row = mysqli_fetch_assoc($result_votacoes)) {
            $id_votacao = $row["id_votacao"];
            mysqli_query($conn, "DELETE FROM votacoes WHERE id_votacao = " . $id_votacao);
            mysqli_query($conn, "DELETE FROM respostas WHERE id_votacao = " . $id_votacao);
            mysqli_query($conn, "DELETE FROM respostas_resultado WHERE id_votacao = " . $id_votacao);
            mysqli_query($conn, "DELETE FROM respostas_resultado WHERE username = '" . $username . "'");
        }
    }
    $sql_users = "DELETE FROM users WHERE username = '" . $username ."'";
    
    $result_users = mysqli_query($conn, $sql_users);
    
    CloseCon($conn);
    
    if (isset($_SESSION["username"]) && $_SESSION["username"] == $username) fecharSessao();
}

function tornarAdmin($username) {
    $conn = OpenCon();

    $sql_users = "UPDATE users SET nivel_utilizador = 2 WHERE username = '" . $username . "'";
    $result_users = mysqli_query($conn, $sql_users);
    
    CloseCon($conn);
}

function listarUtilizadores()
{
    $sql_votacoes = "SELECT username, nome, password, nivel_utilizador FROM users";
    
    $conn = OpenCon();

    if ( isset($_POST['pesquisar'])) {

        $campoPesquisa = trim(mysqli_real_escape_string($conn,$_POST['campoPesquisa']));
        if ( trim($campoPesquisa)!="") {
            $sql_votacoes = "SELECT * FROM USERS  WHERE (username LIKE '%$campoPesquisa%') OR (nome LIKE '%$campoPesquisa%') OR (nivel_utilizador LIKE '%$campoPesquisa%')";
        }
    }

    $result_votacoes = mysqli_query($conn, $sql_votacoes);
    CloseCon($conn);

    if (mysqli_num_rows($result_votacoes) > 0) {
        while ($row = mysqli_fetch_assoc($result_votacoes)) {
        ?>
        
                <td>
                    <form action="#" method="post">
                    <button type="submit" class="btn material-icons" name="deleteUser" value="<?php echo $row["username"]; ?>">delete</button>
                    <button type="submit" class="btn material-icons" name="tornarAdmin" value="<?php echo $row["username"];?>">verified_user</button>
                    </form>
                </td>
                <td scope="row"> <?php echo $row["username"]; ?></td>
                <td><?php echo $row["nome"]; ?></td>
                <td><?php echo $row["password"]; ?></td>
                <td><?php echo $row["nivel_utilizador"]; ?></td>
            </tr>
    <?php
        }
    } else {
        // echo "0 results";
    }
}

function fecharSessao()
{
    session_unset();

    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
    header("Location: index.php");
}

if (!empty($_POST)) {
    if (isset($_POST["sair"])) fecharSessao();
    if (isset($_POST["deleteUser"])) deleteUser($_POST["deleteUser"]);
    if (isset($_POST["tornarAdmin"])) tornarAdmin($_POST["tornarAdmin"]);
}


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

<!-- <pre> -->
    <?php 
    // var_dump($_POST);
    ?>
<!-- </pre> -->

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
                        <?php loginSection(); ?>
                    </span>
                </div>
            </div>
        </nav>
        
        <header>
            <section class="jumbotron text-center margem-topo">
                <div class="container">
                    <h1>Votações Para Todos</h1>
                    <p class="lead text-muted">Contrua votacoes cativantes para as perguntas mais conhecidas da internet.</p>
                </div>
            </section>
        </header>
        
        
        <main class="bg-light h-100">
            <div class="d-flex d-flex aligns-items-center justify-content-center">
                <div>
                    <form action="#" method="POST">
                    
                                     Pesquisar por username, nome, ou nivel de utilizador &nbsp;
                                    <input type="text" name="campoPesquisa" value="<?php echo $campoPesquisa;?>">
                    
                                    <button type="submit" name="pesquisar" class="btn btn-secondary">Pesquisar</button>
                                    <button type="submit" class="btn btn-secondary" >Limpar</button>
                      </form>
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="material-icons">person</th>
                                <th scope="col">Username</th>
                                <th scope="col">Nome</th>
                                <th scope="col">Password</th>
                                <th scope="col">Nivel</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php listarUtilizadores(); ?>
                        </tbody>
                    </table>
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