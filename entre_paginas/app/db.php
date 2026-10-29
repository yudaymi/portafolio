<?php
const DB_HOST='127.0.0.1'; const DB_NAME='entre_paginas'; const DB_USER='root'; const DB_PASS='';
function db(): PDO { static $pdo=null; if(!$pdo){$pdo=new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',DB_USER,DB_PASS,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);}return $pdo; }
function e($s):string{return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function me():?array {if(empty($_SESSION['uid']))return null;$q=db()->prepare('SELECT id,nombre,usuario,avatar,rol FROM usuarios WHERE id=?');$q->execute([$_SESSION['uid']]);return $q->fetch()?:null;}
function token():string{if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(32));return $_SESSION['csrf'];}
function verify():void{if(!hash_equals(token(),$_POST['csrf']??''))throw new RuntimeException('Token de seguridad inválido');}
function go($path='index.php'):never{header('Location: '.$path);exit;}
function require_user():array{$u=me();if(!$u)go('login.php');return $u;}
