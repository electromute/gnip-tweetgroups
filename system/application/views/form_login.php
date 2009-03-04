<h1>Log in to moderate users in this system.</h1>
<hr>
<?=(!empty($loginfeedback)) ? '<h2>'.$loginfeedback.'</h2>' : ''?>
<?=form_open('admin');?>
<p>
    <label for="username">username</label>
    <input type="text" name="username" value="<?=set_value('username');?>" maxlength="100" />
     <?=form_error('username');?>
</p>
<p>
   <label for="password">Password</label>
   <input type="password" name="password" value="<?=set_value('password');?>" maxlength="100" />
    <?=form_error('password');?>
</p>
<p><input type="submit" name="login" value="log in"></p>
</form>

