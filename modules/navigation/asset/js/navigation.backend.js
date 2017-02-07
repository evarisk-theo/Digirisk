/**
 * Initialise l'objet "navigation" ainsi que la méthode "init" obligatoire pour la bibliothèque EoxiaJS.
 *
 * @since 1.0
 * @version 6.2.4.0
 */

window.digirisk.navigation = {};

/**
 * La méthode appelée automatiquement par la bibliothèque EoxiaJS.
 *
 * @return {void}
 *
 * @since 1.0
 * @version 6.2.4.0
 */
window.digirisk.navigation.init = function() {
	window.digirisk.navigation.event();
};

/**
 * La méthode contenant tous les évènements pour la navigation.
 *
 * @return {void}
 *
 * @since 1.0
 * @version 6.2.4.0
 */
window.digirisk.navigation.event = function() {
	jQuery( document ).on( 'click', '.digirisk-wrap .navigation-container .content li span.action-attribute', window.digirisk.navigation.setItemActiveInToggle );
	jQuery( document ).on( 'click', '.digirisk-wrap .navigation-container .workunit-list li span.action-attribute', window.digirisk.navigation.setItemActiveInWorkunitList );

	jQuery( document ).on( 'keyup', '.digirisk-wrap .navigation-container .workunit-add input.title', window.digirisk.navigation.keyUpOnWorkunitTitle );
};

/**
 * Ajoutes la classe "active" sur l'item cliqué dans le toggle de la navigation.
 *
 * @return {void}
 *
 * @since 1.0
 * @version 6.2.4.0
 */
window.digirisk.navigation.setItemActiveInToggle = function( event ) {
	jQuery( '.digirisk-wrap .navigation-container .content div.active' ).removeClass( 'active' );
	jQuery( this ).closest( 'div' ).addClass( 'active' );
};

/**
 * Ajoutes la classe "active" sur l'item cliqué dans la liste des unités de travail de la navigation.
 *
 * @return {void}
 *
 * @since 1.0
 * @version 6.2.4.0
 */
window.digirisk.navigation.setItemActiveInInWorkunitList = function( event ) {
	jQuery( '.digirisk-wrap .navigation-container .workunit-list li.active' ).removeClass( 'active' );
	jQuery( this ).closest( 'li' ).addClass( 'active' );
};

/**
 * Ajoutes la classe "blue" sur l'input action dans l'ajout d'une unité de travail.
 *
 * @param  {KeyboardEvent} event L'état du clavier.
 * @return {void}
 *
 * @since 6.2.6.0
 * @version 6.2.6.0
 */
window.digirisk.navigation.keyUpOnWorkunitTitle = function( event ) {
	jQuery( '.digirisk-wrap .navigation-container .workunit-add .action-input.grey' ).removeClass( 'grey' ).addClass( 'blue' );
};
