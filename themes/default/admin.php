<?php include ('header.php'); ?>

<?php include ('top.php'); ?>

<div id="main" class="container_12">

	<div id="admin" class="prefix_1 grid_10">

		<h1><?php echo $Lang->adminpage;
		if (!empty($Pages)) : echo ' ( ' . $Lang->pagenumberbeginbrowse . $currentPage . ' ' . $Lang->of . ' ' . end($Pages) . ' ) '; endif;?></h1>

<?php	$i = 0;
	foreach ($Users AS $user) : ?>
		<div class="userlist-line alpha grid_5">
<?php	$i++; ?>

			<div class="alpha grid_3">

				<h3><?php echo $user->name; ?></h3>

				<p>Email : <?php echo $user->email; ?></p>

				<form method="post" action="?action=admin">

					<fieldset>

						<input type="checkbox" name="isadmin" id="is-admin-<?php echo $i ?>" <?php if ($user->isadmin) { echo 'checked';}?> />
						<label for="is-admin-<?php echo $i ?>"><?php echo $Lang->becomeadmin; ?></label>

						<div class="clear"></div>

						<input type="checkbox" name="islocked" id="is-locked-<?php echo $i ?>" <?php if ($user->islocked) { echo 'checked';}?> />
						<label for="is-locked-<?php echo $i ?>"><?php echo $Lang->lockuseradmin; ?></label>
							
						<div class="clear"></div>

						<input type="checkbox"name="delete" id="delete-<?php echo $i ?>" />
						<label for="delete-<?php echo $i ?>"><?php echo $Lang->deleteuseradmin; ?></label>
							
						<div class="clear"></div>

						<input type="hidden" name="id" value="<?php echo $user->id; ?>" />

					</fieldset>

					<input name="doadmin" type="submit" value="<?php echo $Lang->updatebutton; ?>" />

				</form>

			</div>

			<div class="grid_2 omega">
					
				<img src="<?php echo $user->avatar; ?>" />
					
			</div>
		</div>
<?php
	endforeach;
	
	if (!empty($Pages)) : ?>
		<div class="clear"></div>
		
		<div id="paging">		
			<a href="?action=admin&page=1"><?php echo $Lang->first; ?></a>			
<?php foreach ($Pages AS $key => $numPage) :
		if ($key < count($Pages) - 1) :?>
			<a href="?action=admin&page=<?php echo $numPage; ?>"><?php echo $numPage ?></a>	
<?php	endif;
	endforeach; ?>
			<a href="?action=admin&page=<?php echo end($Pages); ?>"><?php echo $Lang->last; ?></a>
		</div>
<?php endif; ?>

	</div>

</div>

<?php include ('footer.php'); ?>