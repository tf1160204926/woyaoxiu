<?php 
header("content-type:text/html;charset=utf-8");
// 获取用户信息
 $name=$_POST["name"];
 $password=$_POST["password"];
 // 这里的files的avatar是传输ajax参数时，formdata里面自己设置的key，只是avatar传的是文件，不是表单数据，说以里面有name属性，就是选择的图片的名字

  // 下面获取的仅仅是图片的名字,服务器为这个图片自动存放了临时地址["tmp_name"];D:\wamp\tmp\php52F5.tmp
 $file=$_FILES["avatar"];

  $tmp=$file["tmp_name"];

 $avatar=$file["name"];
 // 指定目标地址
 // $target='F:\workSpace\www\static\assets\vendors\jquery\\'.$avatar;
     $target = 'img/' .$file['name'];


echo ("name:".$name.",password:".$password.",avatar:".$avatar.",target:".$target);

// name:admin@zce.me,password:undefined,avatar:blog1.jpg,tmp:D:\wamp\tmp\phpF417.tmp
 // name:admin@zce.me,password:undefined,avatar:team2.jpg,target:img/team2.jpg	
// 移动地址
// move_uploaded_file($tmp,"./".time().".png");
move_uploaded_file($tmp,"img/".$file["name"]);
 // move_uploaded_file($tmppath, "images/".$file["name"]);
?>	