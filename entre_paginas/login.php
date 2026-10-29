<?php
require 'app/init.php';require 'app/auth_view.php';$error='';$identificador='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 try{verify();$identificador=trim((string)($_POST['identificador']??''));$clave=(string)($_POST['clave']??'');
 if($identificador!==''&&$clave!==''){$q=db()->prepare('SELECT id,clave FROM usuarios WHERE email=? OR usuario=? LIMIT 1');$q->execute([$identificador,$identificador]);$u=$q->fetch();if($u&&password_verify($clave,$u['clave'])){session_regenerate_id(true);$_SESSION['uid']=(int)$u['id'];go('index.php');}}
 $error='El correo, nombre de usuario o contraseña no coincide.';
 }catch(Throwable $ex){$error='No fue posible iniciar sesión. Comprueba la conexión.';}
}
auth_start('Iniciar sesión');?><h1>Bienvenido de nuevo</h1><p class="auth-sub">Continúa donde dejaste tu última lectura.</p>
<?php if($error):?><p class="auth-alert" role="alert"><?=e($error)?></p><?php endif;?>
<form method="post" class="reading-login"><input type="hidden" name="csrf" value="<?=e(token())?>"><div class="auth-field"><label for="identificador">Correo electrónico o nombre de usuario</label><div class="auth-input-wrap"><span class="auth-input-icon">✉</span><input id="identificador" name="identificador" type="text" placeholder="Tu correo o @usuario" value="<?=e($identificador)?>" autocomplete="username" required></div></div><div class="auth-field"><label for="clave">Contraseña</label><div class="auth-input-wrap"><span class="auth-input-icon">♙</span><input id="clave" name="clave" type="password" placeholder="Introduce tu contraseña" autocomplete="current-password" required><button type="button" class="auth-eye" data-password-toggle="clave" aria-label="Mostrar contraseña">◉</button></div></div><button class="auth-primary" type="submit">Iniciar sesión <span>→</span></button></form><p class="auth-switch">¿Todavía no tienes cuenta? <a href="registro.php">Crear una cuenta</a></p><?php auth_end();?>
