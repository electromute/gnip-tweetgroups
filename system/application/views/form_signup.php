<h1>Create your admin account</h1>
<p>You must have received an activation code from someone.</p>
<hr>
<?=(!empty($signupfeedback)) ? '<h2>'.$signupfeedback.'</h2>' : ''?>
<?=form_open('admin/signup');?>
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
<p>
   <label for="activationcode">Activation Code</label>
   <input type="text" name="activationcode" value="<?=set_value('activationcode');?>" maxlength="100" />
    <?=form_error('activationcode');?>
</p>
<p><input type="submit" name="login" value="add yourself"></p>
</form>