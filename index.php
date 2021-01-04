<?php

session_start();
if (!$_SESSION['logged_in']){
    header('Location: login.php');
}

if (isset($_POST['action']) and $_POST['action'] == 'logout'){
    session_destroy();
    header('Location: login.php');
}

function getBackPath($currentPath){
    return dirname($currentPath);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHPFileBrowser</title>
</head>
<style>
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }
    td, th {
        border: 1px solid black;
        text-align: left;
        padding: 8px;
    }
    th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: salmon;
        color: white;
    }
    tr:nth-child(even){
        background-color: sandybrown;
    }
    tr:hover {
        background-color: lightsalmon;
    }
</style>
<body>
    <?php
    if (isset($_GET['path'])){
        $path = $_GET['path'];
    } else {
        $path = ".";
    }

    if (isset($_POST['create_dir'])){
        $pathForNewDir = './' . $_GET['path'] . '/' . $_POST['create_dir'];
        if (!is_dir($pathForNewDir)){
        mkdir($pathForNewDir, 755, true);
        }
    }

     if (isset($_POST['delete'])){
         $pathDelFile = $_POST['delete'];
         $pathDelFileFixed = str_replace("&nbsp;", " " , htmlentities($pathDelFile, null, 'utf-8'));
          if (is_file($pathDelFileFixed)){
              if (file_exists($pathDelFileFixed)){
                 unlink($pathDelFileFixed);
                 header("Refresh:0");
             } 
          }
          
      }


     if (isset($_POST['download'])){
         $fileName = $_POST['download'];
         if(file_exists($fileName)){
         header('Content-Type: application/octet-stream');
         header("Content-Transfer-Encoding: Binary");
         header('Content-disposition: attachment; filename="' .basename($fileName).'"');
         header('Content-Length: ' . filesize($fileName));
         flush();
         readfile($fileName);
         die();
     } else {
         echo "File does not exist.";
     }
    }
    

    if (isset($_FILES['upload'])){
        $errors = array();
        $file_name = $_FILES['upload']['name'];
        $file_size = $_FILES['upload']['size'];
        $file_tmp = $_FILES['upload']['tmp_name'];
        $file_type = $_FILES['upload']['type'];
        $file_ext = strtolower(end(explode('.', $_FILES['upload']['name'])));
        $extensions = array("txt", "jpeg", "jpg", "png", "pdf");

        if (in_array($file_ext, $extensions) === false){
            $errors[] = "extension not allowed, please choose a TXT, JPEG, PNG or PDF file";
        }
        if ($file_size > 2097152){
            $errors[] = 'File size must be below 2 MB';
        }
        if (empty($errors) == true){
            move_uploaded_file($file_tmp, './' . $_GET['path'] . '/' . $file_name);
        } else {
            echo ($_FILES);
            echo ('<br>');
            print_r($errors);
        }
    }

    $dirContents = scandir($path);

     print('<h2> Directory contents: ' . str_replace('?path=/', '',$_SERVER['REQUEST_URI']) . '</h2>');
    print("<table><th>Type</th><th>Name</th><th>Actions></th>");
    foreach ($dirContents as $filesAndDirs){
        if ($filesAndDirs != ".." and $filesAndDirs != "."){

            $fullPath = "$path/$filesAndDirs";
            print("<tr>");
            if (is_dir($fullPath)){
                print("<td>" . "Directory" . "</td>");
                print("<td> <a href= '?path=" . $fullPath . "'>" . $filesAndDirs . "</a></td>");
                print("<td></td>");
            } else{
                print("<td>" . "Files" . "</td>");
                print("<td>" . $filesAndDirs . "</td>");
                print('<td>
                <form style="display: inline-block" action="" method="POST">
                <input type="hidden" name="delete" value="' . $fullPath . '">
                <input type="submit" value="Delete">
                </form>
                  <form style="display: inline-block" action="" method="POST">
                 <input type="hidden" name="download" value="' . $fullPath . '">
                 <input type="submit" value="Download">
                 </form>
                </td>');
            
            }
            print("</tr>");
        }
    }
    print("</table>");
    ?>
    <button style="display: block; width: 50px"><a href="<?php echo('?path=' . getBackPath($path)) ?>">Back</a></button>

    <br>
    <form action="" method="POST">
    <input type="hidden" name="name" value="<?php echo($path) ?>" />
    <input placeholder="Name of new directory" type="text" id="create_dir" name="create_dir">
    <button type="submit">Create</button>
</form>

 <form enctype="multipart/form-data" method="POST"> 
     <input type="file" name="upload"></input />  
     <input type="submit" value="Upload file"></input> 
</form>
<br>

<form action="" method="POST">
    <input type="hidden" name="action" value="logout" />
    <button type="submit">Logout</button>
</form>
</body>
</html>