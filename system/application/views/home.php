<div id="refresh"><a href="/">New tweets have arrived. Click to refresh the page.</a></div>
<div id="left">
<table>
    <? foreach($tweets as $tweet) { ?>
    <tr>
        <td><a href="http://twitter.com/<?=$tweet->username;?>">
        <? $fp = @fopen($tweet->picURL,"r");
        if($fp) {
        ?>    
        <img src="<?=$tweet->picURL;?>" alt="<?=$tweet->username;?>" title="<?=$tweet->username;?>" height="48" width="48">
        <? } else { ?>
        <img src="/images/noprofileimage.png" alt="<?=$tweet->username;?>" title="<?=$tweet->username;?>" height="48" width="48">
        <? } ?>
        </a></td>
        <td><a href="http://twitter.com/<?=$tweet->username;?>"><?=$tweet->username;?></a> <?=$tweet->tweet;?> (<a href="<?=$tweet->URL;?>"><?=date("n.d.y, g:i a", strtotime($tweet->time) + date("Z"));?></a>) via <?=$tweet->client;?> <?if (strlen($tweet->replyto)){ ?> (<a href="<?=$tweet->replyto;?>">in response to...</a>)<?}?></td>
    </tr>
    <tr>
        <td colspan="2"><hr></td>
    </tr>
    <? } ?>
</table>
 <div id="pager"><?if($page > 2){?><div class="previous"><a href="/home?action=previous&page=<?=$page-2;?>">&lt; Previous Page</a></div><? } ?>    <?if($total_tweets >= $recordLimit){?><div class="next"><a href="/home?action=next&page=<?=$page;?>">Next Page &gt;</a></div><?}?></div>
</div>

<div id="right">
<?=form_open('', array('id' => 'add')); ?>
    <p><label for="username">Add your Twitter username</label><br />
    <input type="text" name="username" value="<?=set_value('username');?>" maxlength="100" />
    <br /><?=form_error('username');?><br />
    <input type="submit" name="Add" value="add me!"></p>
</form>
    
<p><strong>Group managers</strong></p>
 <? foreach($admins as $admin){ ?>
     <a href="http://twitter.com/<?=$admin->username;?>"><img src="<?=$admin->picURL;?>" alt="<?=$admin->username;?>" title="<?=$admin->username;?>" height="48" width="48" id="<?=$admin->username;?>" onError="updateImage(this.id);" /></a>
    <a href="http://twitter.com/<?=$admin->username;?>"><?=$admin->username;?></a><br />
<? } ?>
<br />
<hr width="100%" />
<p><strong>Group members</strong></p>
<? $count = 0; 
foreach($users as $user) {
    $count = $count + 1; ?>
    <a href="http://twitter.com/<?=$user->username;?>"><img src="<?=$user->picURL;?>" alt="<?=$user->username;?>" title="<?=$user->username;?>" height="24" width="24" class="smallUserImage" id="<?=$user->username;?>" onError="updateImage(this.id);" /></a>
    <? if ($count % 7 == 0){ ?>
        <br />
    <? } ?>
<? } ?>
</div>

<script language="javascript">
    $(document).ready(function(){
        $('#refresh').hide();
        timer();
    });
    
function timer(){
    setTimeout(checkForData, 45000);
}
    
function checkForData(){
    $.post("/home/checkForNewTweets",{value: <?=$most_recent_tweetid;?>}, function(returnVal){ 
        console.log(returnVal);
        if (returnVal){
            $('#refresh').show('slow');
        }
        timer();
    }, "text");
}

function updateImage(imgID) {
    console.log(imgID);
    $.post("/home/refreshPhoto",{value: imgID}, function(newSrc){ 
        console.log(newSrc);
        $('#' + imgID).attr("src", newSrc);
        $('#' + imgID).attr("onError", '');
    }, "text");
}
</script>