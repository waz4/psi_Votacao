<script src="js/chart.min.js"></script>

<script>
    function autoClick(idvotacao) {
        var elm = document.getElementById('modal_btn' + idvotacao);
        elm.click();
    }
</script>

<!-- adicionar modal para o listar, login, registar, criar, bolachinhas -->
<!DOCTYPE html>
<html lang="en">

<?php

$erroLogin = "";
$erroRegistar = "";

$numeroDePergunta = 2;

include("conectarBd.php");

session_start();

function formatar($array)
{
    $str = " ";
    foreach ($array as $chave => $valor) {
        $str = $str . ("'" . $valor . "'" . ',');
    }
    $str = substr($str, 0, -1);
    return $str;
}

function idPorTitulo($id_votacao)
{
    $conn = OpenCon();
    $sql_votacoes = "SELECT titulo FROM votacoes WHERE id_votacao = " . $id_votacao;
    $result_votacoes = mysqli_query($conn, $sql_votacoes);
    CloseCon($conn);

    $row = mysqli_fetch_assoc($result_votacoes);

    return $row["titulo"];
}

function idPorDescricao($id_votacao)
{
    $conn = OpenCon();
    $sql_votacoes = "SELECT descricao FROM votacoes WHERE id_votacao = " . $id_votacao;
    $result_votacoes = mysqli_query($conn, $sql_votacoes);
    CloseCon($conn);

    $row = mysqli_fetch_assoc($result_votacoes);

    return $row["descricao"];
}

function lerRespostas($id_votacao)
{

    $conn = OpenCon();
    $sql_respostas = "SELECT id_resposta, texto FROM respostas WHERE id_votacao = " . $id_votacao;
    $result_respostas = mysqli_query($conn, $sql_respostas);
    CloseCon($conn);
    for ($i = 1; $i <= mysqli_num_rows($result_respostas); $i++) {
        $row = mysqli_fetch_assoc($result_respostas);
        if (isset($_POST["btn-check"]) and ((int) $_POST["btn-check"] == $i)) $checked = "checked";
        else $checked = "";
        // var_dump($row);
    ?>

        <div class="form-floating mb-3">
            <div>
                <input type="radio" class="btn-check" name="btn-check-<?php echo $id_votacao; ?>" id="btn-check-<?php echo $id_votacao; ?>-<?php echo $i; ?>" value="<?php echo $row["id_resposta"]; ?>" autocomplete="off" <?php echo $checked ?>>
                <label class="btn btn-outline-primary w-100" for="btn-check-<?php echo $id_votacao; ?>-<?php echo $i; ?>"><span><?php echo $row["texto"] ?></span></label><br>
            </div>
        </div>

        <?php
        // echo "<br>" . "id_resposta: ". $row["id_resposta"] . " Texto: ". $row["texto"] ."<br>";
    }
}

function totalVotos($id_votacao)
{
    $conn = OpenCon();

    $sql_respostas_resultado = "SELECT id_votacao FROM respostas_resultado WHERE id_votacao = " . $id_votacao;
    $result_respostas_resultado = mysqli_query($conn, $sql_respostas_resultado);

    CloseCon($conn);

    return mysqli_num_rows($result_respostas_resultado);
}

function nomePorUser($username)
{

    $conn = OpenCon();
    $sql_utilizadores = "SELECT nome FROM users WHERE username = '" . $username . "'";
    $result_utilizadores = mysqli_query($conn, $sql_utilizadores);
    CloseCon($conn);

    $row = mysqli_fetch_assoc($result_utilizadores);

    // var_dump($row);

    return $row["nome"];
}

function listarVotacoes()
{
    $conn = OpenCon();

    $sql_votacoes = "SELECT * FROM votacoes";
    $result_votacoes = mysqli_query($conn, $sql_votacoes);
    CloseCon($conn);

    if (mysqli_num_rows($result_votacoes) > 0) {
        while ($row = mysqli_fetch_assoc($result_votacoes)) {
        ?>
            <tr>
                <th scope="row"> <?php echo $row["id_votacao"]; ?></th>
                <td><a href="#" class="" data-bs-toggle="modal" data-bs-target="#exampleModal<?php echo $row["id_votacao"]; ?>" id="modal_btn<?php echo $row["id_votacao"]; ?>"><?php echo $row["titulo"]; ?></a></td>
                <td><?php echo nomePorUser($row["username"]); ?></td>
                <td><?php echo totalVotos($row["id_votacao"]); ?></td>
            </tr>
    <?php
            // echo "id_votacao: " . $row["id_votacao"] . " username: " . $row["username"] . " titulo: " . $row["titulo"] . " descricao: " . $row["descricao"] . " <hr>";
        }
    } else {
        echo "0 results";
    }
}

function updateChart($id_votacao)
{
    $conn = OpenCon();

    $sql_respostas = "SELECT id_resposta, texto FROM respostas WHERE id_votacao = " . $id_votacao;
    $result_respostas = mysqli_query($conn, $sql_respostas);
    // posso ou tirar todas as respostas e dps calclar com php
    // ou tirar o numero de respostas direto da base de dados mas isso usa mais acessos a mesma
    $sql_respostas_resultado = "SELECT id_resposta FROM respostas_resultado WHERE id_votacao = " . $id_votacao;
    $result_respostas_resultado = mysqli_query($conn, $sql_respostas_resultado);

    CloseCon($conn);


    $num_respostas = (int)mysqli_num_rows($result_respostas);

    $nrVotos = array();
    $textoRespostas = array();

    while ($row = mysqli_fetch_assoc($result_respostas)) {
        $textoRespostas += [$row["id_resposta"] => $row["texto"]];
    }

    for ($i = 1; $i <= $num_respostas; $i++) {
        $nrVotos += [$i => 0];
    }

    if (mysqli_num_rows($result_respostas_resultado) > 0) {
        while ($row = mysqli_fetch_assoc($result_respostas_resultado)) {
            $nrVotos[$row["id_resposta"]]++;
        }
    } else {
        echo "0 results";
    }
    // var_dump($nrVotos);
    // var_dump($textoRespostas);

    // var_dump(formatar($nrVotos));
    // var_dump(formatar($textoRespostas));

    ?>
    <script>
        const data<?php echo $id_votacao ?> = {
            labels: [<?php echo formatar($textoRespostas) ?>],
            datasets: [{
                label: '# of Votes',
                data: [<?php echo formatar($nrVotos) ?>],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        }

        const config<?php echo $id_votacao; ?> = {
            type: 'pie',
            data: data<?php echo $id_votacao ?>,
            options: {
                animation: {
                    duration: 0
                }
            }
        }

        const myChart<?php echo $id_votacao; ?> = new Chart(document.getElementById("myChart<?php echo $id_votacao; ?>"), config<?php echo $id_votacao; ?>);
    </script>

    <?php

}

function fazerModals()
{
    $conn = OpenCon();
    $sql_votacoes = "SELECT id_votacao FROM votacoes";
    $result_votacoes = mysqli_query($conn, $sql_votacoes);
    // var_dump($result_votacoes);
    CloseCon($conn);

    // global $mostrarVotacao;
    $display = "d-block";

    for ($id_votacao = 1; $id_votacao <= mysqli_num_rows($result_votacoes); $id_votacao++) {
        // if ($mostrarVotacao == $id_votacao) $display = "d-block";
        // else $display = "d-none";
    ?>

        <div class="modal fade" id="exampleModal<?php echo $id_votacao; ?>" tabindex="-1" aria-labelledby="exampleModalLabel<?php echo $id_votacao; ?>" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content rounded-5 shadow">

                    <div class="modal-header p-5 pb-4 border-bottom-0">
                        <h2 class="modal-title"><?php echo idPorTitulo($id_votacao); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-5 pt-0">

                        <form class="#" method="post" id="<?php echo "form" . $id_votacao;?>">
                            
                            <input type="hidden" name="id_votacao" value="<?php echo $id_votacao; ?>">

                            <div class="form-floating mb-3">
                                <p><?php echo idPorDescricao($id_votacao); ?></p>
                            </div>

                            <?php lerRespostas($id_votacao); ?>

                            <button class="w-100 mb-2 btn btn-lg rounded-4 btn-success" type="submit" name="modal_submit" value="on">Votar</button>

                            <section id="resultados" class="<?php echo $display; ?>">
                                <hr class="my-4">
                                <h2 class="fs-5 fw-bold mb-1">Resultados:</h2>
                                <div class="container mt-2">
                                    <div class="container">
                                        <canvas id="myChart<?php echo $id_votacao; ?>"></canvas>
                                    </div>
                                    <?php updateChart($id_votacao); ?>
                                </div>
                            </section>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
}

function votar($id_votacao, $id_resposta, $username)
{
    $conn = OpenCon();

    $timestamp = date("d-m-Y", time());

    mysqli_query($conn, "DELETE FROM respostas_resultado WHERE username = '" . $username . "' AND id_votacao = " . $id_votacao); // se os user random forem feitos com cookies nao preciso de checkar se o user e anonimo posso so apagar a resposta antiga se ouver e dps meter a nova
    $sql_respostas_resultado = "INSERT INTO respostas_resultado (`id_votacao`, `id_resposta`, `username`, `time_stamp`) VALUES ('" . $id_votacao . "', '" .  $id_resposta . "', '" . $username . "', '" . $timestamp . "')";
    $result_respostas_resultado = mysqli_query($conn, $sql_respostas_resultado);
    CloseCon($conn);
    // var_dump($result_respostas_resultado);
    // var_dump($sql_respostas_resultado);
    // var_dump($timestamp);
}

function verificarLogin($username, $password)
{

    $username = trim($username);

    $conn = OpenCon();

    $sql_users = "SELECT * FROM users WHERE username = '" . $username . "'";
    $result_users = mysqli_query($conn, $sql_users);

    CloseCon($conn);

    // var_dump($result_users);

    if (mysqli_num_rows($result_users) > 0) {
        while ($row = mysqli_fetch_assoc($result_users)) {

            if ($row["username"] == trim($username) && password_verify($password, $row["password"])) {
                $_SESSION["username"] = $username;
                $_SESSION["nome"] = $row["nome"];
                $_SESSION["NIVEL_UTILIZADOR"] = $row["nivel_utilizador"];

                // header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
                // header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
                // header("Location: teste.php");
                return;
            }
        }
    } else {
    }

    echo "<br> Dados introduzidos estao errados!";

    $erroLogin = "Dados Incorretos";
    return;
}

function registar($username, $nome, $sobrenome, $password)
{

    $username = trim($username);
    $password = password_hash($password, PASSWORD_DEFAULT);

    $nome = trim($nome) . " " . trim($sobrenome);

    $conn = OpenCon();

    $sql_users = "SELECT * FROM users WHERE username = '" . $username . "'";
    $result_users = mysqli_query($conn, $sql_users);

    CloseCon($conn);

    // var_dump($result_users);

    if (mysqli_num_rows($result_users) == 0) {

        $nivel_utilizador = 1;
        $sql_users = "INSERT INTO users VALUES ('" . $username . "', '" . $nome . "', '" . $password . "', " . $nivel_utilizador . ", '' )";

        $conn = OpenCon();
        $res = mysqli_query($conn, $sql_users);
        CloseCon($conn);
        // var_dump($res);
        $erroRegistar = "Conta Registada com sucesso!";
    } else {
        $erroRegistar = "Username ja registado no sistema";
    }
}

function loginSection()
{
    if (!isset($_SESSION["username"])) {
    ?>
        <a class="text-decoration-none text-white" data-bs-toggle="modal" href="#exampleModalToggle" role="button" id="aModal">Login</a>
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

function fecharSessao()
{
    session_unset();
}

function listarPerguntas()
{
    global $numeroDePergunta;
    // echo $numeroDePergunta;

    ?>
    <input type="hidden" name="numeroDePergunta" value="<?php echo $numeroDePergunta ?>">
    <?php

    for ($i = 1; $i <= $numeroDePergunta; $i++) {
    ?>
        <div class="input-group mb-3">
            <span class="input-group-text" id="inputGroup-sizing-default"><?php echo $i; ?></span>

            <input required type="text" class="form-control" aria-label="Pergunta <?php echo $i; ?>" aria-describedby="inputGroup-sizing-default" name="formPergunta<?php echo $i; ?>" value="<?php if (isset($_POST['formPergunta' . $i])) echo $_POST['formPergunta' . $i]; ?>">
        </div>
    <?php
    }
}

function criarVotacao()
{
    if (!isset($_SESSION["username"])) {
        ?> 
        <script>
            alert("Para criar votação é preciso estar registado!");
        </script>
        <?php
        return;
    }
    $titulo = $_POST["formTitulo"];
    $descricao = $_POST["formDescricao"];
    global $numeroDePergunta;
    $perguntas = array();

    for ($i = 1; $i <= $numeroDePergunta; $i++) if(isset($_POST["formPergunta" . $i])) array_push($perguntas, $_POST["formPergunta" . $i]);

    $sql_votacoes = "SELECT id_votacao FROM votacoes";

    $conn = OpenCon();

    $result_votacoes = mysqli_query($conn, $sql_votacoes);

    CloseCon($conn);

    $id_votacao = mysqli_num_rows($result_votacoes) + 1;

    $sql_votacoes = "INSERT INTO votacoes VALUES (" . $id_votacao . ", '" . $_SESSION["username"] . "', '" . $titulo . "', '" . $descricao . "');";

    $conn = OpenCon();

    $result_votacoes = mysqli_query($conn, $sql_votacoes);

    CloseCon($conn);

    $conn = OpenCon();

    mysqli_query($conn, "DELETE FROM respostas WHERE id_votacao = " . $id_votacao);
    mysqli_query($conn, "DELETE FROM respostas_respostas WHERE id_votacao = " . $id_votacao);

    $i = 1;
    foreach ($perguntas as $perguntaTexto) {
        mysqli_query($conn, "INSERT INTO respostas VALUES (" . $id_votacao . ", " . $i . ", '" . $perguntaTexto . "')");
        // echo "INSERT INTO respostas VALUES (" . $id_votacao . ", " . $i . ", '" . $perguntaTexto . "') <br>";
        $i++;
    }

    CloseCon($conn);
    
    // echo $id_votacao;
}

function abrirModal() {
    if (isset($_POST["modal_submit"])) echo "autoClick(" . $_POST["id_votacao"] . ")";
    if (isset($_POST["adicionarPergunta"]) || isset($_POST["removerPergunta"])) echo "autoClick('_criarVotacao')";
}

if (!empty($_POST)) {
    if (isset($_POST["sair"])) fecharSessao();
    if (isset($_POST["modal_submit"])) {
        if (isset($_POST["btn-check-" . $_POST["id_votacao"]])) {
            // $mostrarVotacao = $_POST["id_votacao"];
            if (isset($_SESSION["username"])) {
                votar($_POST["id_votacao"], $_POST["btn-check-" . $_POST["id_votacao"]], $_SESSION["username"]);
            }
        }
    }
    if (isset($_POST["formSubmit"])) {
        if ($_POST["tipo"] == "login") {
            verificarLogin($_POST["formUser"], $_POST["formPasswd"]);
        }
        if ($_POST["tipo"] == "registar") {
            if ($_POST["formPasswd1"] == $_POST["formPasswd2"]) {
                registar($_POST["formUser"], $_POST["formNome"], $_POST["formSobrenome"], $_POST["formPasswd1"]);
            } else $erroRegistar = "Passwords não coincidem!";
        }
    }
    if (isset($_POST["numeroDePergunta"])) $numeroDePergunta = $_POST["numeroDePergunta"];
    if (isset($_POST["adicionarPergunta"])) $numeroDePergunta++;
    if (isset($_POST["removerPergunta"]) && $numeroDePergunta > 2) $numeroDePergunta--;

    if (isset($_POST["formCriarVotacaoSubmit"])) {
        criarVotacao();
    }
}

?>

<head>
    <meta charset="UTF-8">
    <!-- <link rel="stylesheet" href="estilo.css"> -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/bootstrap.css">
    <script src="js/bootstrap.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="style/estilo.css">
    <title>FVS</title>
</head>

<body onload="<?php abrirModal(); ?>">

    <!-- <pre> -->

    <?php
    // echo "Post: <br>";
    // var_dump($_POST);
    // echo "Session: <br>";
    // var_dump($_SESSION);
    ?>

    <!-- </pre> -->

    <!-- Modal Login -->
    <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">

                <div class="modal-header border-bottom-0 text-center">
                    <h2 class="modal-title w-100">Login</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <form action="#" method="post">
                        <input type="hidden" name="tipo" value="login">
                        <label for="user" class="fs-6 input-label-text">User: </label> <br>
                        <div class="input-group mb-3">
                            <span class="input-group-text material-icons" id="formUser">person</span>

                            <input required type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1" name="formUser" id="formUser">
                        </div>
                        <label for="passwd" class="fs-6 input-label-text">Password</label><br>
                        <div class="input-group mb-3">
                            <span class="input-group-text material-icons " id="passwd-visibility" onclick="show('formPasswd')">visibility</span>

                            <input required type="password" class="form-control " placeholder="Password" aria-label="Password" aria-describedby="button-addon2" name="formPasswd" id="formPasswd">
                        </div>

                        <!-- Por enquanto isto n faz nada -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="sim" id="flexCheckDefault" name="lembrar">
                            <label class="form-check-label" for="flexCheckDefault">
                                Lembrar-me neste dispositivo
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" name="formSubmit">Iniciar Sessão</button>
                    </form>


                </div>

                <div class="modal-footer">
                    <div class="w-100">
                        <span class="float-start text-danger"><?php echo $erroRegistar; ?></span>
                        <a class="text-decoration-none text-secondary float-end" data-bs-target="#exampleModalToggle2" data-bs-toggle="modal">Não tem conta? Registe-se!</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal Registar -->
    <div class="modal fade " id="exampleModalToggle2" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">

                <div class="modal-header border-bottom-0 text-center">
                    <h2 class="modal-title w-100">Registe-se</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <form action="#" method="post">
                        <input type="hidden" name="tipo" value="registar">
                        <label for="user" class="fs-6 input-label-text">User: </label> <br>
                        <div class="input-group mb-3">
                            <span class="input-group-text material-icons" id="basic-addon1">account_circle</span>

                            <input required type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1" name="formUser" id="formUser">
                        </div>

                        <label for="user" class="fs-6 input-label-text">Nome: </label> <br>
                        <div class="input-group mb-3">
                            <span class="input-group-text material-icons">person</span>
                            <input required type="text" class="form-control" placeholder="Nome" aria-label="Nome" name="formNome">
                            <input required type="text" class="form-control" placeholder="SobreNome" aria-label="Sobrenome" name="formSobrenome">
                        </div>

                        <label for="passwd" class="fs-6 input-label-text">Password</label><br>
                        <div class="input-group mb-3">
                            <span class="input-group-text material-icons " id="passwd-visibility" onclick="show('formPasswd1')">visibility</span>

                            <input required type="password" class="form-control " placeholder="Password" aria-label="Password" aria-describedby="button-addon2" name="formPasswd1" id="formPasswd1">
                        </div>

                        <label for="passwd" class="fs-6 input-label-text">Reintroduza a sua password</label><br>
                        <div class="input-group mb-3">
                            <span class="input-group-text material-icons " id="passwd-visibility" onclick="show('formPasswd2')">visibility</span>

                            <input required type="password" class="form-control " placeholder="Password" aria-label="Password" aria-describedby="button-addon2" name="formPasswd2" id="formPasswd2">
                        </div>

                        <div class="form-check mb-3">
                            <input required class="form-check-input" type="checkbox" value="sim" id="flexCheckDefault" name="termos">
                            <label class="form-check-label" for="flexCheckDefault">
                                Concordo com os termos!
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" name="formSubmit">Criar Conta</button>
                    </form>

                </div>

                <div class="modal-footer">
                    <div class="w-100">
                        <span class="float-start text-danger"><?php echo $erroRegistar; ?></span>
                        <a class="text-decoration-none text-secondary float-end" data-bs-target="#exampleModalToggle" data-bs-toggle="modal" id="toggleModal">Já tem conta? Faça login!</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal Criar Votacao -->

    <div class="modal fade" id="modal_criar_votacao" aria-hidden="true" aria-labelledby="modal_criar_votacao_Label" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">

                <div class="modal-header border-bottom-0 text-center">
                    <h2 class="modal-title w-100">Criar Votação</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <form action="#" method="post" id="criarVotacao">
                        <label for="user" class="fs-4 input-label-text">Titulo: </label> <br>
                        <div class="input-group mb-3">
                            <span class="input-group-text material-icons" id="formTitulo">help</span>

                            <input required type="text" class="form-control" placeholder="Titulo" aria-label="Titulo" aria-describedby="basic-addon1" name="formTitulo" id="formTitulo" value="<?php if (isset($_POST["formTitulo"])) echo $_POST["formTitulo"]; ?>">
                        </div>
                        <label for="formDesc" class="fs-4 input-label-text">Descrição: </label><br>
                        <div class="input-group">
                            <span class="input-group-text material-icons">description</span>
                            <textarea class="form-control" name="formDescricao" aria-label="With textarea"><?php if (isset($_POST["formDescricao"])) echo $_POST["formDescricao"]; ?></textarea>
                        </div>

                        <div id="perguntas">
                            <h4>Perguntas</h4>

                            <?php listarPerguntas(); ?>

                            <button type="submit" class="btn btn-primary rounded-0" name="adicionarPergunta"><span class="material-icons">add</span></button>
                            <button type="submit" class="btn btn-primary rounded-0" name="removerPergunta"><span class="material-icons">remove</span></button>
                        </div>




                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100" name="formCriarVotacaoSubmit">Criar Votacão</button>
                </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Modals Votacoes -->
    <?php fazerModals(1); ?>

    <!-- NavBar -->
    <nav class="navbar navbar-expand-sm navbar-dark bg-dark" aria-label="Third navbar example">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="img/logo.png" width="30" height="30" alt="logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample03" aria-controls="navbarsExample03" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarsExample03">
                <ul class="navbar-nav me-auto mb-2 mb-sm-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Lista de Votações</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-target="#modal_criar_votacao" data-bs-toggle="modal" id="modal_btn_criarVotacao">Criar Votação</a>
                    </li>
                </ul>
                <span class="navbar-text">
                    <?php loginSection(); ?>
                </span>
            </div>
        </div>
    </nav>

    <!-- Cabecalho -->
    <header>
        <section class="jumbotron text-center margem-topo">
            <div class="container">
                <h1>Votações Para Todos</h1>
                <p class="lead text-muted">Contrua votacoes cativantes para as perguntas mais conhecidas da internet.</p>
                <p>
                    <a href="#" class="btn btn-primary my-2">Lista de Votações</a>
                    <a class="btn btn-secondary my-2" data-bs-target="#modal_criar_votacao" data-bs-toggle="modal" id="modal_btn_criarVotacao">Criar Votação</a>
                </p>
            </div>
        </section>
    </header>

    <!-- Conteudo -->
    <main class="bg-light h-100">
        <div class="d-flex d-flex aligns-items-center justify-content-center">
            <div>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Votação</th>
                            <th scope="col">Criador</th>
                            <th scope="col">NrVotos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php listarVotacoes(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    
    <!-- Rodape -->
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
        </footer>
    </div>
</body>

</html>