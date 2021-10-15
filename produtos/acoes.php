<?php

session_start();

require("../database/conexao.php");

function validarCampos()
{

    //ARRAY DAS MENSAGENS DE ERROS    
    $erros = [];

    //VALIDAÇÃO DE DESCRIÇÃO
    if ($_POST["descricao"] == "" || !isset($_POST["descricao"])) {

        $erros[] = "O CAMPO DESCRIÇÃO É OBRIGATORIO";
    }

    //VALIDAÇÃO DE PESO
    if ($_POST["peso"] == "" || !isset($_POST["peso"])) {

        $erros[] = "O CAMPO PESO É OBRIGATORIO";
    } elseif (!is_numeric(str_replace(",", ".", $_POST["peso"]))) {

        $erros[] = "CAMPO PESO DEVE SER UM NUMERO";
    }

    //VALIDAÇÃO DE QUANTIDADE
    if ($_POST["quantidade"] == "" || !isset($_POST["quantidade"])) {

        $erros[] = "O CAMPO QUANTIDADE É OBRIGATORIO";
    } elseif (!is_numeric(str_replace(",", ".", $_POST["quantidade"]))) {

        $erros[] = "CAMPO QUANTIDADE DEVE SER UM NUMERO";
    }

    //VALIDAÇÃO DE COR
    if ($_POST["cor"] == "" || !isset($_POST["cor"])) {

        $erros[] = "O CAMPO COR É OBRIGATORIO";
    }

    //VALIDAÇÃO DE TAMANHO
    if ($_POST["tamanho"] == "" || !isset($_POST["tamanho"])) {

        $erros[] = "O CAMPO TAMANHO É OBRIGATORIO";
    }

    //VALIDAÇÃO DE VALOR
    if ($_POST["valor"] == "" || !isset($_POST["valor"])) {

        $erros[] = "O CAMPO VALOR É OBRIGATORIO";
    } elseif (!is_numeric(str_replace(",", ".", $_POST["valor"]))) {

        $erros[] = "CAMPO VALOR DEVE SER UM NUMERO";
    }

    //VALIDAÇÃO DE DESCONTO
    if ($_POST["desconto"] == "" || !isset($_POST["desconto"])) {

        $erros[] = "O CAMPO DESCONTO É OBRIGATORIO";
    } elseif (!is_numeric(str_replace(",", ".", $_POST["desconto"]))) {

        $erros[] = "CAMPO DESCONTO DEVE SER UM NUMERO";
    }
    //VALIDAÇÃO DE CATEGORIA
    if ($_POST["categoria"] == "" || !isset($_POST["categoria"])) {

        $erros[] = "O CAMPO CATEGORIA É OBRIGATORIO";
    }

    //VALIDAÇÃO DE IMAGEM
    if ($_FILES["foto"]["error"] == UPLOAD_ERR_NO_FILE) {

        $erros[] = "O ARQUIVO PRECISA SER UMA IMAGEM";
    } else {

        $imagemInfos = getimagesize($_FILES["foto"]["tmp"]);

        if ($_FILES["foto"]["size"] > 1024 * 1024 * 2) {

            $erros[] = "O ARQUIVO NÃO PODE SER MAIOR QUE 2MB";
        }

        $width = $imagemInfos[0];
        $height = $imagemInfos[1];

        if ($width != $height) {

            $erros[] = "A IMAGEM PRECISA SER QUADRADA";
        }
    }

    return $erros;
}

switch ($_POST["acao"]) {

    case 'inserir':

        $erros = validarCampos();

        if (count($erros) > 0) {

            $_SESSION["erros"] = $erros;

            header("location: novo/index.php");

            exit;
        }

        /* TRATAMENTO DA IMAGEM PARA UPLOAD: */

        //RECUPERA O NOME DO ARQUIVO
        $nomeArquivo = $_FILES["foto"]["name"];

        //RECUPERAR A EXTENSÃO DO ARQUIVO
        $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);

        //DEFINIR UM NOVO NOME PARA O ARQUIVO DE IMAGEM
        $novoNome = md5(microtime()) . "." . $extensao;

        //UPLOAD DO ARQUIVO:
        move_uploaded_file($_FILES["foto"]["tmp_name"], "fotos/$novoNome");

        /* INSERÇÃO DE DADOS NA BASE DE DADOS DO MYSQL: */

        //RECEBEIMENTO DOS DADOS:
        $descricao = $_POST["descricao"];
        $peso = $_POST["peso"];
        $quantidade = $_POST["quantidade"];
        $cor = $_POST["cor"];
        $tamanho = $_POST["tamanho"];
        $valor = $_POST["valor"];
        $desconto = $_POST["desconto"];
        $categoriaId = $_POST["categoria"];

        //CRIAÇÃO DA INSTRUÇÃO SQL DE INSERÇÃO:
        $sql = "INSERT INTO tbl_produto 
            (descricao, peso, quantidade, cor, tamanho, valor, desconto, imagem, categoria_id) 
            VALUES ('$descricao', $peso, $quantidade, '$cor', '$tamanho', $valor, $desconto, 
            '$novoNome', $categoriaId)";

        //EXCUÇÃO DO SQL DE INSERÇÃO:
        $resultado = mysqli_query($conexao, $sql);

        //REDIRECIONAR PARA INDEX:
        header('location: index.php');

        break;

        case "deletar":

            $produtoId = $_POST["produtoId"];

            $sql = "SELECT imagem FROM tbl_produto WHERE id = $produtoId";

            $resultado = mysqli_query($conexao, $sql);

            $produto = mysqli_fetch_array($resultado);

            $sql = "DELETE FROM tbl_produto WHERE id = $produtoId";

            $resultado = mysqli_query($conexao, $sql);

            unlink("./fotos" . $produto[0]);

            header("location: index.php");

    default:
        # code...
        break;
}
