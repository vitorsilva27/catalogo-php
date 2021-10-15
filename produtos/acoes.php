<?php

    session_start();

    require("../database/conexao.php");

    function validarCampos(){
        
        //ARRAY DAS MENSAGENS DE ERROS    
        $erros = [];

        //VALIDAÇÃO DE DESCRIÇÃO
        if ($_POST["decricao"] == "" || !isset($_POST["descricao"])) {

            $erros[] = "O CAMPO DESCRIÇÃO É OBRIGATORIO";

        }

        //VALIDAÇÃO DE PESO
        if ($_POST["peso"] == "" || !isset($_POST["peso"])) {

            $erros[] = "O CAMPO PESO É OBRIGATORIO";

        } elseif (!is_numeric(str_replace(",",".", $_POST["peso"]))) {
            $erros[] = "CAMPO PESO DEVE SER UM NUMERO";
        }

        //VALIDAÇÃO DE QUANTIDADE
        
        //VALIDAÇÃO DE COR
        
        //VALIDAÇÃO DE VALOR

        //VALIDAÇÃO DE DESCONTO

        //VALIDAÇÃO DE CATEGORIA

    }

    switch ($_POST["acao"]) {

        case 'inserir':
            
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
        
        default:
            # code...
            break;
    }

?>