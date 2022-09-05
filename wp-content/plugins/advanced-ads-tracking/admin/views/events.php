<div class="wrap">
    <h1><?php _e('Event-Tracking', 'advanced-ads-tracking'); ?></h1>
    <?php if (count($clients) == 0) : ?>
        <p><?php _e('No clients yet.', 'advanced-ads-tracking'); ?></p>
    <?php else : ?>
        <form action="" method="post">
    	<table class="advads-tracking-events-table">
    	    <tr>
    		<th>Clients</th>
    		<th>Token</th>
    		<th><?php _e('active', 'advanced-ads-tracking'); ?></th>
    		<th></th>
    	    </tr>
		<?php foreach ($clients as $client) : ?>
		    <tr>
			<td><?php echo $client['name']; ?></td>
			<td><?php echo $client['token']; ?></td>
			<td><input type="checkbox" name="client[<?php echo $client['id']; ?>][active]" checked=<?php checked($client['expired'] !== null); ?></td>
			<td><a href="?page=advanced-ads-tracking-events&preview_client_id=<?php echo $client['id']; ?>"><?php _e('view script', 'advanced-ads-tracking'); ?></a></td>
		    </tr>
		<?php endforeach; ?>
    	</table>
    	<input type="submit" name="update_client" class="button-primary" value="<?php _e('update clients', 'advanced-ads-tracking'); ?>" />
        </form>
        <h2><?php _e('Events', 'advanced-ads-tracking'); ?></h2>
	<?php foreach ($clients as $client) : ?>
	    <h4><?php echo $client['name']; ?></h4>
	    <table class="advads-tracking-events-table">
		<tr>
		    <th><?php _e('Date', 'advanced-ads-tracking'); ?></th>
		    <th><?php _e('ID', 'advanced-ads-tracking'); ?></th>
		    <th><?php _e('Price', 'advanced-ads-tracking'); ?></th>
		    <th><?php _e('Commission', 'advanced-ads-tracking'); ?></th>
		</tr>
		<?php if (isset($client['events'])) :
		    foreach ($client['events'] as $_event) :
			?>
			<tr>
			    <td><?php echo date(get_option('date_format') . ', ' . get_option('time_format'), $_event['event_time']); ?></td>
			    <td><?php echo $_event['event_id'] ?></td>	  
			    <td><?php echo $_event['event_meta']->price . ' ' . $_event['event_meta']->price_currency; ?></td>	  
			    <td><?php echo $_event['event_meta']->provision . ' ' . $_event['event_meta']->provision_currency; ?></td>	  
			</tr>
			<?php endforeach;
		    endif;
		    ?>
	<?php endforeach; ?>
        </table>
<?php endif; ?>
</div>
<h2><?php _e('New Client', 'advanced-ads-tracking'); ?></h2>
<form action="" method="post">
    <input type="text" name="client_name" value="" placeholder="<?php _e('client name', 'advanced-ads-tracking'); ?>" autocomplete=off />
    <input type="submit" class="button-secondary" name="create_client" value="<?php _e('create client', 'advanced-ads-tracking'); ?>" />
</form>
<style>
    .advads-tracking-events-table td,
    .advads-tracking-events-table th { padding: .3em; text-align: left; }
</style>