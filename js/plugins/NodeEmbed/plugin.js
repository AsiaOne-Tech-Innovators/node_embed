/**
 * @file
 * CKEditor Entity Link plugin. Based on core link plugin.
 *
 * @ignore
 */

(function ($, Drupal, drupalSettings, CKEDITOR) {

  'use strict';

  CKEDITOR.plugins.add('nodeembed', {
    init: function (editor) {
      // Add the commands for link and unlink.
      editor.addCommand('nodeembed', {
        modes: {wysiwyg: 1},
        canUndo: true,
        exec: function (editor) {
          var linkElement = '';
          var linkDOMElement = null;

          // // Set existing values based on selected element.
          var existingValues = {};
          // Prepare a save callback to be used upon saving the dialog.
          var saveCallback = function (returnValues) {
            editor.fire('saveSnapshot');
            if (returnValues.attributes.id) {
                var edit_content = "[[nid:" + returnValues.attributes.id + "]]";
                editor.insertHtml( edit_content );
            }

            // Save snapshot for undo support.
            editor.fire('saveSnapshot');
          };
          var dialogSettings = {
            title: linkElement ? editor.config.NodeEmbed_dialogTitleEdit : editor.config.NodeEmbed_dialogTitleAdd,
            dialogClass: 'editor-link-dialog'
          };

          // Open the dialog for the edit form.
          Drupal.ckeditor.openDialog(editor, Drupal.url('node-embed/dialog/' + editor.config.drupal.format), existingValues, saveCallback, dialogSettings);
        }
      });

      // Add buttons for link and unlink.
      if (editor.ui.addButton) {
        editor.ui.addButton('NodeEmbed', {
          label: Drupal.t('NodeEmbed'),
          command: 'nodeembed',
          icon: this.path + '/node_embed.png'
        });
      }
    }
  });



})(jQuery, Drupal, drupalSettings, CKEDITOR);
