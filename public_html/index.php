<?php
$usuario = $_SERVER['PHP_AUTH_USER'];

$cliente = $_GET['cliente'] ?? null;
$pastaArquivos = "arquivos-". $cliente ."/";

if (empty($cliente)) {
    throw new \Exception('Cliente não especificado!');
}

$msg = "";
$erro = false;
if (!empty($_POST)) {
    
    try{
        doUpload($pastaArquivos, $cliente);
        $msg = "Arquivo Enviado com sucesso!";
    } catch (Exception $ex) {
        $msg = $ex->getMessage();
        $erro = true;
    }
    
}

function doUpload(string $pastaArquivos, string $cliente) {
    if($_FILES['arquivo']['error'] != 0) {
        throw new Exception('Erro no envio do arquivo!');
    }
    
    $destino = $pastaArquivos . $_FILES['arquivo']['name'];

    if(file_exists($destino)) {
        throw new Exception('Esse arquivo já foi enviado!');
    }
    
    $moveu = move_uploaded_file($_FILES['arquivo']['tmp_name'], $destino);
    
    if($moveu === false) {
        throw new Exception("Erro ao enviar o arquivo!");
    }
    
    mail('peritasonia@gmail.com', 'Novo arquivo enviado cliente ' . $cliente, 'Novo arquivo enviado. Nome: ' . basename($destino));
    
    return true;
}

?>


<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />        
        <title>Sonia Arquivos</title>
        <meta http-equiv="Content-Language" content="pt-br">

        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    </head>

    <body>    
        <div id="container">
            <h1>Envio de arquivos de processos para perita Sonia Staub</h1>
            
            <?php
            if(!empty($msg)) {
                
                $style = 'color: green';
                if($erro) {
                    $style = 'color: red';
                }
                echo '<h2 style="'. $style .'">'. $msg .'</h2>';
            }
            ?>
            <br />
            <br />
            <form method="post" action="" enctype="multipart/form-data">
                
                <label for="Arquivo">Selecione o arquivo (Tamanho máximo: <?= ini_get('upload_max_filesize') ?>)</label>
                <br />
                <input name="arquivo" type="file"  />
                
                <br /><br /><br />
                <input type="submit" name="enviar" value="Enviar" />
            </form>
            
            
            <?php 
            $arquivos = glob($pastaArquivos . '*.*');

            if(!empty($arquivos)) {
                echo '<h2>Arquivos existentes:</h2>';
            }
            
            $arquivosPorData = [];
            foreach($arquivos as $arquivo) {
                $time = filemtime($arquivo);
                $arquivosPorData[] = [$time,$arquivo];
            }
            
            usort($arquivosPorData, function ($a, $b){
                if($a[0] == $b[0]) {
                    return 0;
                }
                
                return ($a[0] < $b[0]) ? 1 : -1;
            });
            
            foreach($arquivosPorData as $arquivo) {
                echo '<a href="'. $arquivo[1] .'">' . date('d/m/Y H:i', $arquivo[0]) . ' - ' . $arquivo[1] . '</a><br />';
            }
            
            ?>
        </div>
    </body>
</html>