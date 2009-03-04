<h1>Success!</h1>

<p>Please send an email to the new admin with this verification code and the following link so they can create their own password.</p>

<p>Your new moderator must user their Twitter username, but can use any password they choose.</p>

<p>Verification Code: <?=$activationcode;?></p>

<p>URL: <?=anchor('/admin/signup', 'http://groupsdemo.gnip.com/admin/signup');?></p>

<p>Go <?=anchor('/admin/dashboard', 'back to the admin dashboard.');?></p>

<p><?=anchor('/admin/logout', 'logout');?></p>