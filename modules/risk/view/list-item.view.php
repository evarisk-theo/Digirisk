<?php namespace digi;

if ( !defined( 'ABSPATH' ) ) exit;

if ( $risk != null ):
?>
	<li class="wp-digi-list-item wp-digi-risk-item" data-risk-id="<?php echo $risk->id; ?>" >
		<?php echo do_shortcode( '[eo_upload_button id="' . $risk->id . '" type="risk"]' ); ?>
		<span class="wp-digi-risk-list-column-cotation" ><div class="wp-digi-risk-level-<?php echo $risk->evaluation[0]->scale; ?>" ><?php echo $risk->evaluation[0]->risk_level['equivalence']; ?></div></span>
		<span class="wp-digi-risk-list-column-reference" ><?php echo $risk->unique_identifier; ?> - <?php echo $risk->evaluation[0]->unique_identifier; ?></span>
		<span class="wp-digi-risk-list-column-danger"><?php echo wp_get_attachment_image( $risk->danger_category[0]->danger[0]->thumbnail_id, 'thumbnail', false, array( 'title' => $risk->danger_category[0]->danger[0]->name ) ); ?></span>
		<?php do_shortcode( '[digi_comment id="' . $risk->id . '" type="risk" display="view"]'); ?>

		<span class="wp-digi-action wp-digi-risk-action" >
			<a href="<?php echo admin_url( 'admin-ajax.php?action=open_task&id=' . $risk->id ); ?>" class="thickbox dashicons dashicons-schedule" title="Gestion des tâches correctives pour le risque : <?php echo $risk->unique_identifier; ?> - <?php echo $risk->evaluation[0]->unique_identifier; ?>" ></a>

			<a href="#"
				data-id="<?php echo $risk->id; ?>"
				data-nonce="<?php echo wp_create_nonce( 'ajax_load_risk_' . $risk->id ); ?>"
				class="wp-digi-action wp-digi-action-load dashicons dashicons-edit" ></a>

			<a href="#"
				data-id="<?php echo $risk->id; ?>"
				data-nonce="<?php echo wp_create_nonce( 'ajax_delete_risk_' . $risk->id ); ?>"
				data-action="delete_risk"
				class="wp-digi-action wp-digi-action-delete dashicons dashicons-no-alt" ></a>
		</span>
	</li>
<?php endif ; ?>
