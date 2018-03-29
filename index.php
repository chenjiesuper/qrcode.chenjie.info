<!DOCTYPE html>
<html lang="zh_CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>水熊二维码生成器_陈捷制作</title>
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <!-- Custom styles for this template -->
	<link href="css/jumbotron-narrow.css" rel="stylesheet">

    <link href="css/navbar-fixed-top.css" rel="stylesheet">
	<style type="text/css">
		.rc {margin-top:10px;}
	</style>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.0/html5shiv.min.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
<?php    
/*
 * PHP QR Code encoder
 *
 * Exemplatory usage
 *
 * PHP QR Code is distributed under LGPL 3
 * Copyright (C) 2010 Dominik Dzienia <deltalab at poczta dot fm>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */
    //echo "<h1>PHP QR Code</h1><hr/>";
    
    //set it to writable location, a place for temp generated PNG files
    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
    
    //html PNG location prefix
    $PNG_WEB_DIR = 'temp/';

    include "qrlib.php";    
    
    //ofcourse we need rights to create temp dir
    if (!file_exists($PNG_TEMP_DIR))
        mkdir($PNG_TEMP_DIR);
    
    //默认文件名
    $filename = $PNG_TEMP_DIR.'test.png';
    
    //processing form input
    //remember to sanitize user input in real-life solution !!!
    $errorCorrectionLevel = 'L';
    if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H')))
        $errorCorrectionLevel = $_REQUEST['level'];    

    $matrixPointSize = 4;
    if (isset($_REQUEST['size']))
        $matrixPointSize = min(max((int)$_REQUEST['size'], 1), 10);
		
	$color = '0,0,0';
	if (isset($_REQUEST['color'])) {
		$tmp = explode(',',$_REQUEST['color']);
		$red = $tmp[0];
		$green = $tmp[1];
		$blue = $tmp[2];
	}

    if (isset($_REQUEST['data'])) { 
   		//设置标记，标记二维码是否生成,未生成
		$isCreate=false; 
        //it's very important!
        // user data 用户数据
		$filename = $PNG_TEMP_DIR.md5($_REQUEST['data'].'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
		//增加颜色，同时在qrimage中修改对应方法，加入颜色参数
		QRcode::png($_REQUEST['data'], $filename, $errorCorrectionLevel, $matrixPointSize, 1, false, $red, $green, $blue);  
	  //上传logo并获取logo的文件相对路径	
		include("upload.class.php");
	  //增加logo是否上传的判断
		if($_FILES['up']['error']!=4){
		//使用upload上传类处理，并获取logo的路径
		$up = new up($_FILES['up']);
		$logo=$up->f_name;
		$QR=$filename;
		//添加logo到二维码中
		$QR = imagecreatefromstring(file_get_contents($QR));
		$logo = imagecreatefromstring(file_get_contents($logo));
		$QR_width = imagesx($QR);
		$QR_height = imagesy($QR);
		$logo_width = imagesx($logo);
		$logo_height = imagesy($logo);
		$logo_qr_width = $QR_width / 4;
		$scale = $logo_width / $logo_qr_width;
		$logo_qr_height = $logo_height / $scale;
		$from_width = ($QR_width - $logo_qr_width) / 2;
		imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
		//修改文件名，和无logo的文件名做区分，因为logo不同，所以文件名加入mt_rand随机数
		$filename=substr_replace ($filename , mt_rand(0,1000),-7, -4);
		imagepng($QR,$filename);
		}
		//设置标记，标记二维码是否生成,生成成功
		$isCreate=true; 		
    } else {     
        //default data 默认数据
       // echo 'You can provide data in GET parameter: <a href="?data=like_that">like that</a><hr/>';    
		QRcode::png('在此输入数据', $filename, $errorCorrectionLevel, $matrixPointSize, 1); 
    }    
        
    //display generated file

    // benchmark
    //QRtools::timeBenchmark();    
?>
 <body>
   <!-- Fixed navbar -->
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a  class="navbar-brand">水熊二维码生成器</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
 			<li><a href="http://www.chenjie.info" target="_blank">陈捷博客</a></li>
			<li><a href="http://www.waterbear.cc" target="_blank">南京seo</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>
<div class="container">
	  <div class="row marketing">
        <div class="col-lg-6">
          <h4>图片生成</h4>
		  <p><img src="<?php echo $PNG_WEB_DIR.basename($filename);?>" /></p>  
        </div>
        <div class="col-lg-6">
          <h4>使用选项</h4>
		  <p>
<?php
			    //config form
    echo '<form action="index.php" method="post" id="myForm" enctype="multipart/form-data">
        <div class=rc>输入数据:&nbsp;<input name="data" value="'.(isset($_REQUEST['data'])?htmlspecialchars($_REQUEST['data']):'在此输入数据').'" /></div>
        <div class=rc>纠错级别:&nbsp;<select name="level">
            <option value="L"'.(($errorCorrectionLevel=='L')?' selected':'').'>低</option>
            <option value="M"'.(($errorCorrectionLevel=='M')?' selected':'').'>较低</option>
            <option value="Q"'.(($errorCorrectionLevel=='Q')?' selected':'').'>较高</option>
            <option value="H"'.(($errorCorrectionLevel=='H')?' selected':'').'>高</option>
        </select></div>
        <div class=rc>点的大小:&nbsp;<select name="size"><br/>';
    for($i=1;$i<=10;$i++)
        echo '<option value="'.$i.'"'.(($matrixPointSize==$i)?' selected':'').'>'.$i.'</option>';
        
    echo '</select></div>';
	
	//增加颜色
	echo '<div class=rc>图片颜色:&nbsp;<select name="color">
		<option value="0,0,0"'.(($color=='0,0,0')?' selected':'').'>黑</option>
		<option value="204,0,0"'.(($color=='204,0,0')?' selected':'').'>红</option>
		<option value="0,128,0"'.(($color=='0,128,0')?' selected':'').'>绿</option>
		<option value="0,0,255"'.(($color=='0,0,255')?' selected':'').'>蓝</option>
	</select></div>';
	//上传logo
	echo '<div class=rc style="float:left;"><div style="float:left;">上传logo(可选):&nbsp;</div><input type="file" name="up" style="width:50%;float:left" ></div>';
	echo '<div class=rc style="float:left;"><input style="float:left;" type="submit" value="生成图片"></form><hr/></div>';
?>
		  </p>

      </div>
  </div><!-- /row marketing -->
 <div class="subtext pre-scrollable" ><code>图片地址:<?php echo  'http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['SCRIPT_NAME']),'/').'/temp/'.basename($filename); ?></code></div>
<div class="footer">
  <p>
     Copyright &copy;<a href="javascript:void(0)">水熊二维码生成器</a> All Rights Reserved. 
	
  </p>
</div> <!-- /footer -->
</div> <!-- /container -->

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
	<script type="text/javascript" charset="utf-8">
		var form=document.getElementById('myForm');
		var input=document.getElementsByName('data')[0];
		input.onfocus=function(){
			this.value='';
		}
		form.onsubmit=function(){
			if(input.value){
				return true;
			}else{
				alert('输入数据不能为空');
				return false;
			}
		}
	</script>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
<?php
	//在html代码后判断新二维码图片是否生成
	if($isCreate){
		echo "<script>alert('二维码图片生成成功');</script>";
	}
?>