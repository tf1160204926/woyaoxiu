<?php  
    $na = $_POST['name'];
    echo $na . " Upload: " . $_FILES["photo"]["name"];
    move_uploaded_file($_FILES['photo']['tmp_name'], time().".PNG");//保存在该路径
?>