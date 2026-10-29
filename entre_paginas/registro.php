<?php
require 'app/init.php';require 'app/auth_view.php';
$errors=[];$values=['nombre'=>'','usuario'=>'','email'=>'','bio'=>''];$selected=[];
$genres=['Clásicos','Ficción','Romance','Ciencia ficción','Misterio','Desarrollo personal','Fantasía','Otros'];
if($_SERVER['REQUEST_METHOD']==='POST'){
 try{
  verify();foreach($values as $k=>$_)$values[$k]=trim((string)($_POST[$k]??''));
  $pass=(string)($_POST['clave']??'');$selected=array_values(array_intersect($genres,(array)($_POST['generos']??[])));
  if(mb_strlen($values['nombre'])<2||mb_strlen($values['nombre'])>120)$errors['nombre']='Introduce tu nombre completo (2 a 120 caracteres).';
  if(!preg_match('/^[a-zA-Z0-9_]{3,30}$/D',$values['usuario']))$errors['usuario']='Usa 3 a 30 letras, números o guiones bajos; no uses espacios ni @.';
  if(!filter_var($values['email'],FILTER_VALIDATE_EMAIL)||strlen($values['email'])>190)$errors['email']='Introduce un correo electrónico válido.';
  if(strlen($pass)<8)$errors['clave']='La contraseña debe tener al menos 8 caracteres.';
  if(mb_strlen($values['bio'])>150)$errors['bio']='La biografía debe tener como máximo 150 caracteres.';
  if(empty($_POST['acepto']))$errors['acepto']='Debes aceptar los términos y la política de privacidad.';
  $avatar=null;
  if(!empty($_FILES['avatar']['name'])){
   $f=$_FILES['avatar'];
   if($f['error']!==UPLOAD_ERR_OK||$f['size']>2*1024*1024)$errors['avatar']='La imagen no pudo cargarse o supera 2 MB.';
   else{
    $mime=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);$ext=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$mime]??null;
    if(!$ext||!getimagesize($f['tmp_name']))$errors['avatar']='Solo se admiten imágenes JPG, PNG o WebP.';
    else $avatar='uploads/avatar_'.bin2hex(random_bytes(16)).'.'.$ext;
   }
  }
  if(!$errors){$q=db()->prepare('SELECT usuario,email FROM usuarios WHERE usuario=? OR email=?');$q->execute([$values['usuario'],$values['email']]);foreach($q->fetchAll() as $existing){if($existing['usuario']===$values['usuario'])$errors['usuario']='Este usuario ya está registrado.';if(strcasecmp($existing['email'],$values['email'])===0)$errors['email']='Este correo ya está registrado.';}}
  if(!$errors){
   if($avatar&&!move_uploaded_file($_FILES['avatar']['tmp_name'],__DIR__.'/'.$avatar))throw new RuntimeException('No se pudo guardar la imagen.');
   try{$q=db()->prepare('INSERT INTO usuarios(nombre,usuario,email,clave,avatar,bio) VALUES(?,?,?,?,?,?)');$q->execute([$values['nombre'],$values['usuario'],$values['email'],password_hash($pass,PASSWORD_DEFAULT),$avatar,$values['bio']]);$uid=(int)db()->lastInsertId();
   $q=db()->prepare('INSERT INTO preferencias_lectura(usuario_id,genero) VALUES(?,?)');foreach($selected as $genre)$q->execute([$uid,$genre]);
   session_regenerate_id(true);$_SESSION['uid']=$uid;go('index.php');}
   catch(Throwable $ex){if($avatar&&is_file(__DIR__.'/'.$avatar))unlink(__DIR__.'/'.$avatar);throw $ex;}
  }
 }catch(Throwable $ex){$errors['general']='No fue posible crear tu cuenta. Comprueba la conexión y que hayas aplicado la actualización SQL.';}
}
auth_start('Crear cuenta'); ?>
<h1>Crear cuenta</h1><p class="auth-sub">Comienza tu historia en Entre Páginas</p>
<?php if(isset($errors['general'])):?><p class="auth-alert" role="alert"><?=e($errors['general'])?></p><?php endif;?>
<form method="post" enctype="multipart/form-data" class="reading-register"><input type="hidden" name="csrf" value="<?=e(token())?>">
<div class="auth-grid">
<?php foreach(['nombre'=>['Nombre completo','text','Tu nombre y apellido','name'],'usuario'=>['Nombre de usuario','text','ej. yudaymi_ozorio','username'],'email'=>['Correo electrónico','email','tucorreo@ejemplo.com','email'],'clave'=>['Contraseña','password','Mínimo 8 caracteres','new-password']] as $k=>$f):?>
<div class="auth-field"><label for="<?=e($k)?>"><?=e($f[0])?></label><div class="auth-input-wrap"><span class="auth-input-icon"><?=['nombre'=>'♙','usuario'=>'@','email'=>'✉','clave'=>'♙'][$k]?></span><input id="<?=e($k)?>" name="<?=e($k)?>" type="<?=e($f[1])?>" placeholder="<?=e($f[2])?>" autocomplete="<?=e($f[3])?>" value="<?=$k==='clave'?'':e($values[$k])?>" <?= $k==='usuario'?'minlength="3" maxlength="30" pattern="[a-zA-Z0-9_]+"':($k==='clave'?'minlength="8"':'') ?> required aria-invalid="<?=isset($errors[$k])?'true':'false'?>"><?php if($k==='clave'):?><button type="button" class="auth-eye" data-password-toggle="clave" aria-label="Mostrar contraseña">◉</button><?php endif;?></div>
<?php if($k==='usuario'&&!isset($errors[$k])):?><small>Se usará para tu perfil público</small><?php endif;?><?php if(isset($errors[$k])):?><small class="auth-error" role="alert"><?=e($errors[$k])?></small><?php endif;?></div>
<?php endforeach;?></div>
<div class="auth-field"><label for="avatar">Foto de perfil <em>(opcional)</em></label><label class="avatar-upload" for="avatar"><span class="avatar-preview" id="avatar-preview">♧</span><span><strong>Sube una foto</strong><small>JPG, PNG o WebP (máx. 2 MB)</small></span></label><input id="avatar" type="file" name="avatar" accept="image/jpeg,image/png,image/webp" class="visually-hidden"><?php if(isset($errors['avatar'])):?><small class="auth-error"><?=e($errors['avatar'])?></small><?php endif;?></div>
<fieldset class="genre-field"><legend>¿Qué te gusta leer? <em>(Puedes seleccionar varias opciones)</em></legend><div class="genre-list"><?php foreach($genres as $genre):?><label class="genre-chip"><input type="checkbox" name="generos[]" value="<?=e($genre)?>" <?=in_array($genre,$selected,true)?'checked':''?>><span><?=e($genre)?></span></label><?php endforeach;?></div></fieldset>
<div class="auth-field"><label for="bio">Biografía corta <em>(opcional)</em></label><textarea name="bio" id="bio" maxlength="150" rows="2" placeholder="Cuéntanos un poco sobre ti y tus intereses lectores..."><?=e($values['bio'])?></textarea><small class="char-counter"><span id="bio-count"><?=mb_strlen($values['bio'])?></span>/150</small><?php if(isset($errors['bio'])):?><small class="auth-error"><?=e($errors['bio'])?></small><?php endif;?></div>
<label class="terms-row"><input type="checkbox" name="acepto" value="1" <?=!empty($_POST['acepto'])?'checked':''?>><span>Acepto los <a href="terminos.php" target="_blank">Términos de servicio</a> y la <a href="privacidad.php" target="_blank">Política de privacidad</a>.</span></label><?php if(isset($errors['acepto'])):?><small class="auth-error"><?=e($errors['acepto'])?></small><?php endif;?>
<button class="auth-primary" type="submit">Crear mi cuenta <span>→</span></button>
</form><p class="auth-switch">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a></p>
<?php auth_end(); ?>
