<style type="text/css">
#top_panel {
top: <?php echo is_admin_bar_showing() ? '28px' : '0'; ?>;
}
</style>


<div id="top_panel">
	<div class="top_panel_content">
		<div class="links">
			<?php echo $links; ?>
		</div>
		
	<?php
	if (!TP_SHORT_PANEL) {
		if ( is_user_logged_in() ) :  ?>
		
			<div id="userblock" class="userblock" onclick="switchpulldown();">
				<img src="<?php echo get_user_meta(wp_get_current_user()->ID, 'avatar', true); ?>" alt="" class="avatar thumb_icon item_photo_user  thumb_icon"/>
				<a href="javascript:void(0);" class="displayname"><?php echo wp_get_current_user()->display_name ?></a>
				<div class="arrow"></div>
				<div class="userpulldown">
					<a href="<?php echo wp_get_current_user()->user_url; ?>" class="userblock_pulldown_profile"><span>Profil</span></a>
					<a href="http://muloqot.uz/members/edit/profile" class="userblock_pulldown_settings"><span>Sozlashlar</span></a>
					<a href="<?php echo function_exists('wp_logout_url') ? wp_logout_url(get_permalink()) : get_option('siteurl').'/wp-login.php?action=logout'; ?>" class="userblock_pulldown_logout"><span>Chiqish</span></a>
				</div>
			</div>
			
		<?php else: ?>
		
			<div id="loginblock">
				<a href="/wp-load.php?action=authenticate&service=muloqot&reg=1">A’zo bo‘ling</a>
				<a href="/wp-load.php?action=authenticate&service=muloqot">Kiring</a>
			</div>
			
		<?php endif;
	}
	?>
		<div style="clear:both"></div>
	</div>
</div>

<script type="text/javascript">
	function switchpulldown() {
		if (document.getElementById('userblock').classList.contains('active')) {
			document.getElementById('userblock').classList.remove('active');
		}
		else {
			document.getElementById('userblock').classList.add('active');
		}
	}
	document.body.addEventListener('click',function(e) {
		a = e;
		if(document.getElementById('userblock') && !e.target || 
			e.target != document.getElementById('userblock')
			&&
			!document.getElementById('userblock').contains(e.target)
			) { 
			if (document.getElementById('userblock').classList.contains('active')) {
				document.getElementById('userblock').classList.remove('active');
			}
		}
	});
	try {
		if ( document.body.offsetWidth < 768 ) {
			if (document.getElementById('loginblock'))
				document.getElementById('loginblock').style.display = 'none';
			else
				document.getElementById('userblock').style.display = 'none';
		}
	}
	catch(e) {}
	try {
		if ( document.body.offsetWidth < 600 ) {
			document.getElementById('tp_linksgroup').style.display = 'none';
		}
	}
	catch(e) {}

	document.body.style.marginTop = document.getElementById('top_panel').offsetHeight + 'px';
</script>