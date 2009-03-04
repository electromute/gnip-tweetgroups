<h1>Admin management console</h1>
<?=form_open('email/send', array('id' => 'adminForm'));?>
<table>
    <?foreach ($users as $user) {?>
    <tr>
        <td>
        <? $fp = @fopen($user->picURL,"r");
        if($fp) {
        ?>
        <img src="<?=$user->picURL;?>" alt="<?=$user->username;?>" title="<?=$user->username;?>" height="24" width="24">
        <? } else { ?>
        <img src="/images/noprofileimage.png" alt="<?=$user->username;?>" title="<?=$user->username;?>" height="24" width="24">
        <? } ?>
        <a href="http://twitter.com/<?=$user->username;?>" target="_blank"><?=$user->username;?></a>
        </td>
        <td><input type="radio" name="<?=$user->username;?>" value="1" onChange="saveChoice(this.name, this.value)" <? if($user->active == 1) { echo "checked"; }?>> Active</td>
        <td><input type="radio" name="<?=$user->username;?>" value="0" onChange="saveChoice(this.name, this.value)" <? if($user->active == 0) { echo "checked"; }?>> Inactive</td>
        <td><?if($user->admin == 0 && $user->active == 1){?><a href="/admin/promote/<?=$user->username;?>">promote</a><?} else if($user->admin == 1 && $user->active == 1) {?><a href="/admin/revoke/<?=$user->username;?>">revoke</a><?}?></td>
    </tr>
    <? } ?>
</table>
</form>

<script type="text/javascript">
function saveChoice(name, value) {
    $.post("/admin/toggleuser",{name: name, value: value}, function(j){ 
        //not really doing anything with the return value here
        }, "text");
}
</script>