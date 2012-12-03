/**
 * TemplateEdit
 *
 * @copyright   Dattatec.com s.r.l.
 * @author      Javier Valderrama
 */
TemplateEdit={init:function(){this.loadInteractions();this.openTemplateEditorOnEditMode()},loadInteractions:function(){var e=this;jQuery(".herramientas.templateSource").on("click","li.newBlank #loadNewBlank",function(e){e.preventDefault();TemplateEditor.importTemplateToEditor("0000_new-blank_600px",null,"save",1,"defaulTemplates");jQuery(".templateSourceOuterContainer").fadeOut("normal",function(){jQuery("[data-name=subcontenido]").removeClass("subcontenido");jQuery(".submenu-item-list").show();jQuery(".templateEditorOuterContainer").fadeIn()})}).on("click","li.fromUrl #loadFromUrl",function(e){e.preventDefault();var t=ComponenteFormularios.init();t.setComponent_type("inputText");t.setComponent_name("modifyFromUrlSource");t.setComponent_id("modifyFromUrlSource");t.setComponent_customAttributes({maxlength:150});ConfirmMessage=new Array;ConfirmMessage.push("<p>"+__("Recuerda que éstas plantillas no pueden modificarse desde el editor.")+" </p>");ConfirmMessage.push('<p class="infoBlock">'+__("Escribe la URL de la página con el contenido HTML que quieras incluir.")+"</p>");ConfirmMessage.push(t.getComponent());jQuery.confirm(__("Selecciona la URL de origen de tus contenidos."),ConfirmMessage.join("\n")).done(function(){var e=jQuery("#modifyFromUrlSource").val();var t=jQuery("input#currentRecordID").val();e=e.replace(/#.*/,"");TemplateEditor.importTemplateToEditor(t,e,"save",1,"externalUrlTemplates");jQuery(".templateSourceOuterContainer").fadeOut("normal",function(){jQuery("[data-name=subcontenido]").removeClass("subcontenido");jQuery(".submenu-item-list").show();jQuery(".templateEditorOuterContainer").fadeIn()})}).fail(function(){});jQuery("#modifyFromUrlSource").closest(".modal").find(".modal-footer .btn-primary.confirm").prop("disabled",true).addClass("disabled")}).on("click","li.fromGallery #openDefaultGallery",function(e){e.preventDefault();TemplateGallery.init("assignToTemplateEditor",false,false,"defaulTemplates","myTemplates");TemplateGallery.setTemplateEditorAction("save")}).on("click","li.fromRepository #openMyGallery",function(e){e.preventDefault();TemplateGallery.init("assignToTemplateEditor",false,false,"myTemplates","defaulTemplates");TemplateGallery.setTemplateEditorAction("save")})},openTemplateEditorOnEditMode:function(){var e=jQuery("input#currentRecordID").val();var t=jQuery("#currentTemplateInfo").find("input[name=currentTemplateName]").val();var n=jQuery("#currentTemplateInfo").find("input[name=currentTemplateAdvanceEditable]").val();if(e){}}};jQuery(function(){TemplateEdit.init()})