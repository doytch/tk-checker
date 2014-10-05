(function( $ ) {
'use strict';

var TK_COUNT = 0;

/**
 * Returns an array of indices of substring matches within a string.
 *
 * @var		string 		searchStr 		The string to search for.
 * @var		string 		str 	 		The string to search in.
 * @var		boolean		caseSensitive	Whether the search is case sensitive.
 * @return  array 						Array of the indices of matches
 */
function getIndicesOf(searchStr, str, caseSensitive) {
    var startIndex = 0, 
    	searchStrLen = searchStr.length,
    	index, 
    	indices = [];

    if (!caseSensitive) {
        str = str.toLowerCase();
        searchStr = searchStr.toLowerCase();
    }

    while ((index = str.indexOf(searchStr, startIndex)) > -1) {
        indices.push(index);
        startIndex = index + searchStrLen;
    }
    return indices;
}

/**
 * Updates the WordPress editor with the new text in the textbox. This is called
 * when changing a textbox's vaue.
 */
function updateWPEditor() {
	var editor = $('#content'),							// The WP editor.
		oldContent = editor.val(),						// The current content of the editor.
		index = parseInt($(this).attr('idx')),			// String index of the textbox's contents in the WP editor.
		textbox = $(this),								// The textbox.
		newText = textbox.val(),						// The text that the textbox just changed to.
		oldTextLength = parseInt(textbox.attr('chars'));// The length of the text that /was/ in the textbox.

	// Set the length of the text in the textbox.
	textbox.attr('chars', newText.length);

	// Update the editor with the new content.
	editor.val(oldContent.substr(0, index) + newText + oldContent.substr(index + oldTextLength));
}

/**
 * Finds wildcards in the WordPress editor and constructs live textboxes
 * that allow users to replace the wildcards.
 */
function findTKs() {
	var editor 			= $('#content'),
		indices 		= undefined,
		match 			= undefined,
		nodeToInsert 	= undefined,
		nodeAttrs 		= {
			class: 	'tk-text',
			type: 	'text'
		},
		nonce 			= $('#tk_mb_nonce');

	if (editor[0] == undefined) {
		return;
	} else {
		indices = getIndicesOf(TK_SETTINGS['wildcard'], editor.val(), true)
	}

	// Dump the old textboxes
	$('.tk-text').remove();
	$('.tk-congrats').remove();

	TK_COUNT = indices.length;
	if (TK_COUNT === 0) {	
		nonce.after('<p class="tk-congrats">' + TK_SETTINGS['no_tks_text'] + '</p>');
		return;
	}

	// Create the excerpts for each instance of the matched TK.
	// Run the loop in reverse so we can add the nodes in order using
	// jQuery's after() function, based on the nonce <div>.
	for (var i = indices.length - 1; i >= 0; i--) {
		// Get the start of the excerpt we'll show		
		if (indices[i] > TK_SETTINGS['excerpt_length']) {
			nodeAttrs['idx'] = indices[i] - TK_SETTINGS['excerpt_length'];
		} else {
			nodeAttrs['idx'] = 0;
		}

		// Fill out the present excerpt and create the node.
		nodeAttrs['value'] = editor.val().substr(nodeAttrs.idx, TK_SETTINGS['excerpt_length'] * 2);
		nodeAttrs['chars'] = TK_SETTINGS['excerpt_length'] * 2;
		nodeToInsert = $('<input/>', nodeAttrs);

		// Add handlers to update the editor and textboxes.
		nodeToInsert.on('change keyup paste', updateWPEditor);
		nodeToInsert.on('change', findTKs);

		// Append the text box after the nonce.
		nonce.after(nodeToInsert);
	}

	return;
}

$(function() {
	$('#publish').click(function() {
		if ($(this).val() == 'Publish' && TK_COUNT > 0) {
			return confirm(TK_I18N['confirmPublishPrompt']);
		}
	});
	$('#content').on('change keyup paste', findTKs);
	findTKs();
});
})( jQuery );
