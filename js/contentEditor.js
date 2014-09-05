/**
 * TemplateEditor
 *
 * @copyright   Dattatec.com s.r.l.
 * @author      Javier Valderrama
 */
TemplateEditor = {
    init: function(){
        this.cleanUp();
        this.contentIsLoaded = false;
        this.templateEditorOriginalContent = '';
        this.autoContentAlternate = true;
        this.workingBlock = false;
        this.contentTypeEditorMode = 'html';
        this.plainTextHasChanged = false;
        this.dragging = false;
        this.loadInteractions();

        this.sourceImg ="";
        this.action = '';
        this.defaultTemplateEnvolpeTop = '';
        this.defaultTemplateEnvolpeBottom = '';

        this.currentRecordID = '';

        this.advanceEditable = 0;
        this.selectedTextColor = 'rgb(102, 102, 102)';
        this.remoteTemplateUrl = '';

        this.undoHistory = new Array;
        this.appliedRestorePointTimeKey = 0;

        this.preHeaderBlock = '';

        this.unsubscribeBlock = '';

        this.setPreHeaderBlock();
        this.setUnsubscribeBlock();
        this.setDefaultEnvolpes();

    },
    /**
     *
     * @param {Integer} recordID
     */
    setCurrentRecordID: function(recordID){
        this.currentRecordID = recordID || 0;
    },
    /**
     *
     */
    setPreHeaderBlock : function(){
        var self = this;

        self.preHeaderBlock = '<!-- // PreHeader Contents \\\\ --><table width="100%" cellspacing="0" cellpadding="20" border="0" class="preHeaderContainer tobBlock tobRemovable"><tbody><tr>\n\
        <td  align="center" style="font-family: Arial,Helvetica,sans-serif; font-size: 11px; color: #888888;padding: 20px !important;">\n\
        <span  class="tobEditableText">' + __('Si no visualiza correctamente este E-Mail haga') + ' </span><a href="%HTMLVersion%"  target="_blank" class="tobEditableText">' + __('Click Aquí') + '</a>\n\
        </td></tr></tbody></table><!-- \\\\ PreHeader Contents // -->';
    },
    /**
     *
     */
    setUnsubscribeBlock : function(){
        var self = this;

        self.unsubscribeBlock = '<!-- // Footer Contents \\\\ --><table id="unsubscribeBlock" width="100%" cellspacing="0" cellpadding="20" border="0"  class="tobBlock" style="background:#FFFFFF;"><tbody><tr>\n\
        <td  align="center" style="padding:20px; font-family: Arial,Helvetica,sans-serif; font-size: 11px; color: #888888;">\n\
        <span class="tobEditableText">' + __('Para desuscribirse de nuestra lista haga') + '</span> <a href="%UnSubscribe%" target="_blank" class="tobEditableText">' + __('Click Aquí') + '</a>\n\
        </td></tr></tbody></table><!-- \\\\ Footer  Contents // -->';
    },
    /**
     *
     */
    setDefaultEnvolpes: function(){
        var self = this;

        var defaultTemplateEnvolpeTop = new Array;
        var defaultTemplateEnvolpeBottom = new Array;

        defaultTemplateEnvolpeTop.push('<div class="templateBoundary" style="background-color:#FFFFFF;">');
        defaultTemplateEnvolpeTop.push(self.preHeaderBlock);
        defaultTemplateEnvolpeTop.push('<table width="100%" cellspacing="0" cellpadding="0" border="0" class="tobBlock tobClonable"><tbody><tr><td valign="top"><center><div  class="tobEditableHtml">');

        defaultTemplateEnvolpeBottom.push('</div></center></td></tr></tbody></table>');
        defaultTemplateEnvolpeBottom.push(self.unsubscribeBlock);
        defaultTemplateEnvolpeBottom.push('</div><!-- \\\\ Template // -->');

        self.defaultTemplateEnvolpeTop = defaultTemplateEnvolpeTop.join("\n");
        self.defaultTemplateEnvolpeBottom = defaultTemplateEnvolpeBottom.join("\n");
    },
    /**
     *
     */
    insertDomComponents: function(){

        jQuery('body').prepend('<div class="topBlockClonWrapper" id="topBlockClonWrapper"><div class="innerBoundary restoreNormalCss"></div></div>');//<div class="preventMouseActionsOverlay"/>

        jQuery('#topBlockClonWrapper').append('<div id="editorOverlay"/><div class="preventMouseActionsOverlay"/>');
        jQuery('#topBlockClonWrapper').append('<div id="tobOutline" data-bootstrap-tooltip="true" title="' + __('Modificar') + '"/>');
        jQuery('#tobOutline').append('<div id="imgMaxWidth"><span>' + __('Ancho max') + ' <span class="imgMaxWidthVal"></span> px</span></div><br class="clear"/>');
        jQuery('#topBlockClonWrapper').append('<div id="tobWysiwyg"></div>');
        jQuery('#topBlockClonWrapper').append('<div id="updateFieldButtons"><div class="tobButton tobButtonAction btn btn-small btn-primary" id="tobActionConfirmEdit"><div>' + __('Confirmar') + '</div></div><div class="tobButton tobButtonCancel btn btn-small cancel" id="tobActionCancelEdit"><div>' + __('Cancelar') + '</div></div><div class="clear"/></div>');

        jQuery('#topBlockClonWrapper').append('<div id="tobBlockToolsWrapper"><div class="tobBlockTools"><div id="tobBlockToolsDuplicate" class="tobBlockToolsButton" data-bootstrap-tooltip="true" title="' + __('Duplicar Bloque') + '"></div><div id="tobBlockToolsRemove" class="tobBlockToolsButton"  data-bootstrap-tooltip="true" title="' + __('Remover Bloque') + '"></div></div><div class="tobBlockTools clearer"><div id="tobBlockToolsColorPicker" class="tobBlockToolsButton" data-bootstrap-tooltip="true" title="' + __('Modificar color de fondo del bloque') + '"></div></div></div>');
        jQuery('#tobOutline').tooltip({delay: {show: 50, hide: 150}});

        jQuery('#tobBlockToolsWrapper [data-bootstrap-tooltip]').tooltip({placement:'bottom'});
    },
    /**
     *
     */
    loadInteractions: function(){
        /** do not follow links inside the template */
        jQuery('#topBlockClonWrapper a, \
            [data-containerName=editorBlocksContainer] [data-containerName=templateEditorBody] a, \
            [data-containerName=editorBlocksContainer] [data-containerName=templateEditorBody] form').die('click').live({
            click: function(e){
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });

        /** Prevent block release on editMode on when the mouse moves out of window*/
        jQuery('#topBlockClonWrapper').live({
            mouseleave: function(){

                //alert('leave');

                var overlayIsVisible = jQuery('#editorOverlay').is(':visible');
                if (!overlayIsVisible){
                    TemplateEditor.releaseWorkingBlock();

                }

            }
        });
        /** reset all buttons on enter */
        jQuery('#tobBlockToolsWrapper').die('mouseover').live({
            mouseover: function(){
                TemplateEditor.resetEditButtons();
            }
        });

        /** send template content to server */
        jQuery('#saveAndBackToCampaign').on('mouseenter', function () {
            //
            TemplateEditor.releaseWorkingBlock();

        }).on('click',function(event){
            event.preventDefault();

            var previousEditingCampaign = jQuery.JSON.cookie('previous-editing-campaign');

            if(typeof previousEditingCampaign != 'undefined' && previousEditingCampaign != null){

                jQuery.JSON.cookie('open-campaign-panel',{
                    campaignID: previousEditingCampaign.campaignID
                },
                {
                    path: '/'
                });

                jQuery.JSON.cookie('previous-editing-campaign',null,{
                    path: '/'
                });

                TemplateEditor.persistUpdateTemplate().done(function(){
                    window.location.href = previousEditingCampaign.url;
                });
            }
        });
        /**
         *
         *  Close all comboes when click on editorOverlay
         *  #######################################
         */
        jQuery('#editorOverlay').live({
            click: function(event){
                jQuery('.comboBlock-container .comboBlock-close').trigger('click');
            }
        });



        /**
         *
         *  Template Editor Secondary Events
         *  #######################################
         */
        jQuery('[data-containerName=editorBlocksContainer] [data-containerName=templateEditorContainer]').on('mouseenter', '[data-containerName=secondaryTemplateEditorNav]',function(event){
            /**
             *  Force release block befor BackgroundColor click
             */
            event.preventDefault();
            TemplateEditor.releaseWorkingBlock();

        }).on('click','[data-containerName=modifyTemplateBoundaryBackgroundColor]',function(event){

            /**
            * Open Template Boundary Color Picker
            */

            event.preventDefault();
            TemplateEditor.releaseWorkingBlock();
            TemplateEditor.showTemplateBoundaryBackgroundColorPicker();


        }).on('click','[data-containerName=modifyTemplateSourceCode]',function(event){

            /**
            *  set sourceCode editor mode open
            */
            event.preventDefault();
            TemplateEditor.releaseWorkingBlock();

            if(!jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ TemplateEditor.currentRecordID +'] .templateEditorNav').is('.collapsed')){
                jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ TemplateEditor.currentRecordID +'] [data-toolaction=toggleMenu]').trigger('click');
            }

            TemplateEditor.contentTypeEditorMode = 'sourceCode';
            TemplateEditor.toggleContentTypeEditorMode();


        }).on('click','[data-toolaction=sourceCodeEditor-close]',function(event){

            /**
            *  set sourceCode editor mode close
            */
            event.preventDefault();

            TemplateEditor.contentTypeEditorMode = 'html';
            TemplateEditor.toggleContentTypeEditorMode();


        }).on('click','[data-toolaction=sourceCode-applyChanges]',function(event){

            /**
            *  set sourceCode editor mode close
            */
            event.preventDefault();

            TemplateEditor.advanceEditable = 1;
            TemplateEditor.remoteTemplateUrl = '';

            var sourceCodeContent = jQuery.trim(jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ TemplateEditor.currentRecordID +'] textarea[data-containerName=sourceCodeVersionContent]').val());

            var bodyStyles = sourceCodeContent;

            bodyStyles = bodyStyles.replace(/<\s*/gi, "<");
            bodyStyles = bodyStyles.replace(/<\/\s*/gi, "</");
            bodyStyles = bodyStyles.replace(/\s*>/gi, ">");
            bodyStyles = bodyStyles.match(/<\s*body.*style="(.*?)".*>/gi);

            if(bodyStyles && typeof bodyStyles[0] != undefined){
                bodyStyles = bodyStyles[0].replace(/<\s*body.*style="(.*?)".*>/gi, "jQuery1");
            } else {
                bodyStyles = '';
            }

            sourceCodeContent = TemplateEditor.stripNastyTagsFromString(sourceCodeContent);

            TemplateEditor.releaseWorkingBlock();

            var tempContent = jQuery('<div>');
                tempContent.append(sourceCodeContent);

            if(jQuery(tempContent).find('.tobBlock').length < 1){

                tempContent.empty();

                sourceCodeContent = TemplateEditor.defaultTemplateEnvolpeTop + sourceCodeContent + TemplateEditor.defaultTemplateEnvolpeBottom;

                tempContent.append(sourceCodeContent);

                tempContent = tempContent.find('.templateBoundary');

            }

            var hasUnsubscribeLink = sourceCodeContent.match(/%UnSubscribe%/g);

            if(!hasUnsubscribeLink){
                tempContent.append(TemplateEditor.unsubscribeBlock);
            }

            jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ TemplateEditor.currentRecordID +'] .savingThrobber').show();

            var templateBoundary = jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ TemplateEditor.currentRecordID +'] [data-containerName=templateEditorBody] > .templateBoundary');

            if(jQuery(templateBoundary).size() > 0){
                var tempContentString = jQuery(tempContent).html();
                jQuery(templateBoundary).html(tempContentString);
            } else {
                var tempContentString = jQuery(tempContent).outerHtml() + '<!-- \\\\ Template // -->';
                jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ TemplateEditor.currentRecordID +'] [data-containerName=templateEditorBody]').html(tempContentString);
            }

            if(bodyStyles.length > 0){
                jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ TemplateEditor.currentRecordID +'] [data-containerName=templateEditorBody] > .templateBoundary').attr('style',bodyStyles);
            }

            setTimeout(function(){
                TemplateEditor.persistTemplate(19,true).done(function(){
                    TemplateEditor.contentTypeEditorMode = 'html';
                    TemplateEditor.toggleContentTypeEditorMode();

                    jQuery('#remoteContentNotEditableWarning').slideUp();
                    jQuery('#remoteContentNotEditableWarning').find('#remoteTemplateURI').attr('href','').empty();
                    jQuery('[data-containerName=editorBlocksContainer] [data-containerName=templateEditorContainer] > .templateEditorNav .disabled').removeClass('disabled');
                }).fail(function(){

                });
            },100);

        });

        /**
         *
         *  plainText Editor Container Events
         *  #######################################
         */
        jQuery('[data-containerName=editorBlocksContainer] [data-containerName=plainTextEditorContainer]').on('click','textarea[data-containerName=plainTextVersionContent]',function(event){
            /**
            * unlock auto content on polantext content click
            */

            TemplateEditor.setPlainTextTypeMode('manual');

        }).on('keyup','textarea[data-containerName=plainTextVersionContent]',function(keyCode){
            /**
             * set  plainTextHasChanged to true
             */
            var theTextarea = jQuery(this);

            var columnsControl_timeoutHandle = jQuery(theTextarea).data('timeout_handle');
            if(columnsControl_timeoutHandle) clearTimeout(columnsControl_timeoutHandle);

            jQuery(theTextarea).data('timeout_handle', setTimeout(function(){
                TemplateEditor.plainTextHasChanged = true;
            }, 200));
        });

        /**
         *
         *  PROMPTS FORMS
         *  #######################################
         */
        jQuery('body').on('keyup','[data-modalName="promptMessage"] #previewEmailDestinationAddress',function(event){
            /**
             *  Preview by email -> email field validation
             */
            var keyCode = event.which;
            switch(keyCode){
                case KeyboardKey.Esc:
                    jQuery(this).closest('.modal').find('.modal-header .close').trigger('click');
                    break;
                default:
                    var text = jQuery(this).val();
                    if(CommonFunctions.validateData(text, 'Email', 'i', false, false, false)){
                        jQuery(this).closest('.modal').find('.modal-footer .btn-primary.confirm').prop('disabled',false).removeClass('disabled');
                    } else {
                        jQuery(this).closest('.modal').find('.modal-footer .btn-primary.confirm').prop('disabled',true).addClass('disabled');
                    }
                    break;
            }

        }).on('keyup','[data-modalName="promptMessage"] #modifyFromUrlSource',function(event){
            /**
             *  Replace contents by URL -> url field validation
             */
            var keyCode = event.which;

            switch(keyCode){
                case KeyboardKey.Esc:
                    jQuery(this).closest('.modal').find('.modal-header .close').trigger('click');
                    break;
                default:
                    var url = jQuery(this).val();
                    url = url.replace(/^(https*|ftp)\:\/\//,'');
                    url = url.replace(/^www\.{1}/,'');
                    url = url.replace(/#.*/,'');
                    url = url.replace(/\+/,'%20');

                    if(CommonFunctions.validateData(url, 'Url', 'gi', false, false, false)){
                        jQuery(this).closest('.modal').find('.modal-footer .btn-primary.confirm').prop('disabled',false).removeClass('disabled');
                    } else {
                        jQuery(this).closest('.modal').find('.modal-footer .btn-primary.confirm').prop('disabled',true).addClass('disabled');
                    }
                    break;
            }
        });


        TemplateEditor.selectWorkingBlock();
        TemplateEditor.showFieldEditButtons();

    },
    /**
     *
     *  @param {String} content
     *  @param {String} templateEditorOriginalContent
     *  @param {Integer} advanceEditable 1|0
     *  @param {String} remoteTemplateUrl
     *  @param {String} plainTextContent
     *  @param {Integer} autoContentAlternate
     *  @param {Integer} restorePointMessage
     *
     */
    writeHistoryRestorePoint: function(content, templateEditorOriginalContent, advanceEditable, remoteTemplateUrl, plainTextContent,
        autoContentAlternate, restorePointMessage ){
        var self = this;

    },
    /**
     *
     *
     */
    restoreHistoryPoint: function(restorePointID){
        var self = this;
        var restorePointID = restorePointID;

        if(typeof restorePointID != 'undefined'
            && !isNaN(parseFloat(restorePointID))
            && isFinite(restorePointID)
            && self.undoHistory[restorePointID])
        {

            var restorePoint = self.undoHistory[restorePointID];

            self.advanceEditable = restorePoint.advanceEditable;
            self.remoteTemplateUrl = restorePoint.remoteTemplateUrl;
            self.templateEditorOriginalContent = restorePoint.templateEditorOriginalContent;

            /**
             * Assign contents
             */
            jQuery('#templateEditorOriginalContent').html(self.templateEditorOriginalContent);
            jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorBody]').html(restorePoint.content);
            self.assignPlainTextContent(restorePoint.plainTextContent, restorePoint.content, restorePoint.autoContentAlternate);

            /**
             * restor Editor Mode to historic state
             */
            self.autoContentAlternate = restorePoint.autoContentAlternate;
            var plainTextEditorMode = self.autoContentAlternate ? 'auto' : 'manual';
            self.setPlainTextTypeMode(plainTextEditorMode);

            if(self.remoteTemplateUrl.length > 0){
                jQuery('#remoteContentNotEditableWarning').find('#remoteTemplateURI').attr('href',self.remoteTemplateUrl).html(self.remoteTemplateUrl);
                jQuery('#remoteContentNotEditableWarning').slideDown();
                jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorContainer] [data-toolAction=modifyTemplateBoundaryBackgroundColor],\
                   [data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorContainer] [data-toolAction=addAttachment]').addClass('disabled');

            } else {
                jQuery('#remoteContentNotEditableWarning').slideUp();
                jQuery('#remoteContentNotEditableWarning').find('#remoteTemplateURI').attr('href','').empty();
                jQuery('.optionsButtons .addAttachment').show();
                jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorContainer] > .templateEditorNav .disabled').removeClass('disabled');
            }

            self.toggleContentTypeEditorMode();

            return true;
        }
        return false;
    },
    /**
     *
     */
    selectWorkingBlock: function(){

        jQuery('body').on('mouseenter', '.tobBlock:not(.tobBlock .tobBlock)', function(event){



            if(jQuery(this).hasClass('blockHighlighted') || jQuery('#restorePointDiv .tobID-'+jQuery(TemplateEditor.workingBlock).attr("data-tobID")).length){
                return;
            }
            TemplateEditor.releaseWorkingBlock();
            TemplateEditor.workingBlock = jQuery(this);

            var blockOffset = jQuery(TemplateEditor.workingBlock).offset();
            var blockWidth = jQuery(TemplateEditor.workingBlock).outerWidth();
            var blockHeight = jQuery(TemplateEditor.workingBlock).outerHeight();
            var blockBackground = jQuery(TemplateEditor.workingBlockclosest).closest('.tobBlock').css('backgroundColor') || 'transparent';
            var showBlockTools = true;

            jQuery(TemplateEditor.workingBlock).after('<div id="restorePointDiv" class="tobID-'+jQuery(TemplateEditor.workingBlock).attr("data-tobID")+'" style="height:'+blockHeight+'px; width:'+blockWidth+'px"/>');
            jQuery('#restorePointDiv').css('background-color',jQuery(TemplateEditor.workingBlock).css('background-color'));


            jQuery(TemplateEditor.workingBlock).addClass('blockHighlighted');

                if(!self.dragging){
                    jQuery('#topBlockClonWrapper').css({
                        'top' : blockOffset.top -6,
                        'left' : blockOffset.left-6,
                        'width' : blockWidth,
                        'height' : blockHeight
                    }).show();
                 }

            jQuery('#topBlockClonWrapper .innerBoundary').css('backgroundColor',blockBackground).append(TemplateEditor.workingBlock);

            if(TemplateEditor.blockIsClonable(TemplateEditor.workingBlock)){
                showBlockTools = true;
                jQuery('#tobBlockToolsDuplicate').show();
                TemplateEditor.duplicateBlock(TemplateEditor.workingBlock);
            }

            if(TemplateEditor.blockIsRemovable(TemplateEditor.workingBlock)){
                showBlockTools = true;
                jQuery('#tobBlockToolsRemove').show();
                TemplateEditor.removeBlock(TemplateEditor.workingBlock);
            }

            jQuery('#tobBlockToolsColorPicker').unbind('click').bind({
                click: function(){
                    var blockElement = jQuery(this).closest('.topBlockClonWrapper').first('.innerBoundary').find('.tobBlock');
                    TemplateEditor.tobBlockBackgroundPicker(blockElement);
                }
            });


            if(showBlockTools){
                jQuery('#tobBlockToolsWrapper').css({
                    'top' : ( blockHeight + 12),
                    'left' : 0,
                    'width' : blockWidth + 12
                }).show();
            }

        });

    },
    /**
     *
     * @param {Element} blockElement / the target element for changing color
     * @param {Number} marginTop / margin top from envolpe
     * @param {Boolean} onCloseReleaseWorkingBlock / true to force release
     */
    tobBlockBackgroundPicker: function(blockElement, marginTop, onCloseReleaseWorkingBlock){
        var blockElement = blockElement || false;
        var marginTop = marginTop || -50;

        var onCloseReleaseWorkingBlock = onCloseReleaseWorkingBlock || false;
        if(!blockElement){
            return false;
        }
        var currentBackgroundColor = jQuery(blockElement).css('backgroundColor') || 'rgba(0, 0, 0, 0)';

        var currentBackgroundColorToRgbArray = CommonFunctions.convertRgbStringToRgbArray(currentBackgroundColor);

        var currentBackgroundColorToHex = '';


        if(currentBackgroundColorToRgbArray.length == 3){

            currentBackgroundColorToHex = CommonFunctions.rgbToHex(currentBackgroundColorToRgbArray);

        } else if (currentBackgroundColorToRgbArray.length == 4 && currentBackgroundColorToRgbArray[3] > 0){

            var rgb = [rgb[0],rgb[1],rgb[2]];

            currentBackgroundColorToHex = CommonFunctions.rgbToHex(rgb);

        }

        var containerWidth = jQuery('.topBlockClonWrapper').width();
        var containerHeight = jQuery('.topBlockClonWrapper').height();
        jQuery('.preventMouseActionsOverlay').show();

        jQuery('#editorOverlay').addClass('inBetween').show();

        jQuery('#tobWysiwyg').css({
            'width':containerWidth + 'px',
            'margin-top': marginTop + 'px'
        }).show();

        var colorpickerHtml = '<form class="wysiwyg"><fieldset style="margin:15px 20px;"><div id="colorPickerContainer"><div id="myPicker"></div></div><br clear="all"/><br clear="all"/></fieldset></form>';

        jQuery.modal(jQuery(colorpickerHtml).html(), {
            appendTo: '#tobWysiwyg',
            minHeight: '280px',
            maxWidth: '600px',
            minWidth: '600px',
            closeHTML: "<a href='#'>X</a>",
            overlayClose: true,
            onShow: function (dialog) {
                dialog.container.hide();
                jQuery('.simplemodal-container').prepend('<div class="simplemodal-title">' + __('Seleccionar color de fondo') + '</div>'),
                jQuery('.simplemodal-close').show();

                jQuery('.simplemodal-container').fadeIn('normal');

                jQuery("fieldset", dialog.data).click(function (e) {
                    e.stopPropagation();
                });

                jQuery.fn.jPicker.defaults.images.clientPath= urlColorPicker;

                jQuery('#myPicker').jPicker(
                    {
                        color:
                        {
                            alphaSupport: false,
                            active: currentBackgroundColorToHex.length == 6 ? new jQuery.jPicker.Color({ahex: currentBackgroundColorToHex}) : new jQuery.jPicker.Color()
                        }
                    },
                    function(color, context)//ok action
                    {
                        var all = color.val('all') || 'transparent';
                        var bgColor = 'transparent';
                        var contrastColor = '#333333';

                        if(typeof all['r'] != 'undefined' && typeof all['g'] != 'undefined' && typeof all['b'] != 'undefined'){
                            bgColor = 'rgb('+all['r']+','+all['g']+','+all['b']+')';
                            contrastColor = CommonFunctions.getContrastYIQFromRGB([all['r'],all['g'],all['b']]);
                        }

                        jQuery(blockElement).css('backgroundColor',bgColor);
                        var firstChildHavingbackground = jQuery(blockElement).find(':hasBgColor:first');
                        var currentBlockWidth = jQuery(blockElement).outerWidth();
                        var firstChildHavingbackgroundWidth = jQuery(firstChildHavingbackground).outerWidth();

                        if(firstChildHavingbackgroundWidth >= currentBlockWidth){
                            jQuery(firstChildHavingbackground).css('background-color','transparent');
                        }

                        if(jQuery(blockElement).is('.preHeaderContainer') || jQuery(blockElement).is('#unsubscribeBlock')){

                            if(bgColor == 'transparent'){
                                var parentColor = CommonFunctions.convertRgbStringToRgbArray(jQuery('.templateBoundary').css('background-color'));
                                contrastColor = CommonFunctions.getContrastYIQFromRGB(parentColor);

                            }

                            jQuery(blockElement).find('a, p, span').css('color',contrastColor);

                        }

                        jQuery.modal.close();

                        TemplateEditor.releaseWorkingBlock();
                        jQuery('.preventMouseActionsOverlay').show();

                        TemplateEditor.persistTemplate(16,true)

                    },
                    function(color, context)//none
                    {
                    },
                    function(color, context) ///cancel action
                    {
                        jQuery.modal.close();
                    }
                );
            },
            onClose: function (dialog) {
                jQuery.modal.close();
                jQuery('#tobWysiwyg').hide();
                jQuery('#editorOverlay').hide().removeClass('inBetween');
                jQuery('.preventMouseActionsOverlay').hide();

                if(onCloseReleaseWorkingBlock){
                    TemplateEditor.releaseWorkingBlock();
                }
            }
        });


    },
    /**
     *
     */
    showTemplateBoundaryBackgroundColorPicker: function(){
        var self = this;
        var templateBoundary = jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorBody] .templateBoundary');
        var templateBoundaryOffset = jQuery(templateBoundary).offset();
        var templateBoundaryOuterHeight = jQuery(templateBoundary).outerHeight();

        jQuery('.topBlockClonWrapper').css({
            width: jQuery(templateBoundary).outerWidth(),
            height: templateBoundaryOuterHeight,
            top: templateBoundaryOffset.top - 6,
            left: templateBoundaryOffset.left - 5
        }).show();


        TemplateEditor.workingBlock = jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorBody] .templateBoundary');

        TemplateEditor.tobBlockBackgroundPicker(jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorBody] .templateBoundary'), -templateBoundaryOuterHeight, true);

    },
    /**
     *
     *
     */
    autoSetTemplateBoundaryBackgroundColor: function(){
        var self = this;

        var templateBoundary = jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorBody] .templateBoundary');
        var templateBoundaryBackgroundColor = jQuery(templateBoundary).css('background-color');

        if(templateBoundaryBackgroundColor !== 'transparent' && templateBoundaryBackgroundColor !== 'rgba(0, 0, 0, 0)'){
            return false;
        }

        var firstChildHavingbackground = jQuery(templateBoundary).find(':hasBgColor:first');
        var templateBoundaryWidth = jQuery(templateBoundary).outerWidth();
        var firstChildHavingbackgroundWidth = jQuery(firstChildHavingbackground).outerWidth();

        if(firstChildHavingbackgroundWidth >= templateBoundaryWidth){
            jQuery(templateBoundary).css('background-color',jQuery(firstChildHavingbackground).css('background-color'));
            jQuery(firstChildHavingbackground).css('background-color','');
        }

    },
    /**
     *
     */
    showFieldEditButtons: function(){

        TemplateEditor.resetEditButtons();


        jQuery('body').on('mouseenter', '.tobEditableText, \
                                    .tobEditableHtml, \
                                    .tobEditableImg, \
                                    .tobAttachmentItemLink', function(){



                var thisElement = this;
                if(jQuery('.editModeOn').length){
                    return;
                }

                setTimeout(function(){

                    TemplateEditor.showRestoreDefaultContentButton();
                    jQuery(thisElement).addClass('drawOutline restoreNormalCss');

                    var mainWrapperOffset = jQuery('.tobBlock').offset();
                    var currentEditableBlock = jQuery(thisElement);
                    var originalContent = jQuery(thisElement).html();
                    var blockOffset = jQuery(thisElement).offset();
                    var blockWidth = jQuery(thisElement).width();
                    var blockHeight = jQuery(thisElement).height();
                    var topCorrection = 32;
                    var toolsWidth = 85;
                    var setTopPosition = blockOffset.top - topCorrection -mainWrapperOffset.top;
                    var setLeftPosition = blockOffset.left + blockWidth - toolsWidth - mainWrapperOffset.left + 10;
                    var imgMaxWidth = jQuery(currentEditableBlock).attr('data-imgMaxWidth') || false;


                    TemplateEditor.drawOutline(blockOffset.top-mainWrapperOffset.top, blockOffset.left - mainWrapperOffset.left, blockWidth+10, blockHeight+10, jQuery(thisElement).is('.tobEditableImg'), jQuery(thisElement).is('.tobAttachmentItemLink'));


                    if(imgMaxWidth){
                        TemplateEditor.showImgMaxWidthTip(imgMaxWidth);
                    }


                    /** activate edit mode */
                    jQuery('#tobOutline, .drawOutline').unbind('click').bind({
                        click: function(){
                            TemplateEditor.resetEditButtons();
                            TemplateEditor.editBlockContent(currentEditableBlock, originalContent, setTopPosition, (blockOffset.left - mainWrapperOffset.left), blockWidth, blockHeight, imgMaxWidth);
                        }
                    });
                    /** delete image block */
                    jQuery('#tobOutline .imgTool.delete, .drawOutline  .imgTool.delete').unbind('click').bind({
                        click: function(){
                            jQuery('#tobOutline, .drawOutline').unbind('click');
                            TemplateEditor.removeImageBlock(currentEditableBlock);
                            TemplateEditor.releaseWorkingBlock();
                            TemplateEditor.persistTemplate(2,true);

                        }
                    });
                    /** delete attachment block */
                    jQuery('#tobOutline .attachmentTool.delete, .drawOutline  .attachmentTool.delete').unbind('click').bind({
                        click: function(){
                            jQuery('#tobOutline, .drawOutline').unbind('click');
                            TemplateEditor.removeAttachmentBlock(currentEditableBlock);
                            TemplateEditor.releaseWorkingBlock();
                            TemplateEditor.persistTemplate(13,true);

                        }
                    });
                    /** confirm edition */
                    jQuery('#tobActionConfirmEdit').unbind('click').bind({
                        click: function(){
                            var e = jQuery.Event('keydown');
                            e.which = KeyboardKey.Enter;
                            jQuery('body').trigger(e);
                        }
                    });
                    /** cancel edition */
                    jQuery('#tobActionCancelEdit').unbind('click').bind({
                        click: function(){
                            var e = jQuery.Event('keydown');
                            e.which = KeyboardKey.Esc;
                            jQuery('body').trigger(e);
                        }
                    });

                },5);

        });
    },
    /**
     *
     * @param {Element} currentEditableBlock
     */
    removeImageBlock: function(currentEditableBlock){
        var blockParent = jQuery(currentEditableBlock).parent();


        if(jQuery(currentEditableBlock).find("img").length == 0){
            jQuery(currentEditableBlock).remove();

            if(jQuery(blockParent).children().length < 1){
                if(!jQuery(blockParent).is('.tobBlock')){
                    jQuery(blockParent).remove();
                }
            }

            return false;
        }


        jQuery(currentEditableBlock).removeAttr("data-imgmaxwidth").removeClass("tobEditableHtml").addClass("tobEditableHtml");

        jQuery(currentEditableBlock).find("img").remove();
        //jQuery(currentEditableBlock).append("<br /><br /><br /><br /><br />");


        return false;

    },
    /**
     *
     * @param {Element} currentEditableBlock
     */
    removeAttachmentBlock: function(currentEditableBlock){
        var self = this;
        var currentEditableBlock = ( (jQuery(currentEditableBlock).hasClass('tobAttachmentItemLink')) ? (jQuery(currentEditableBlock).closest('.tobAttachmentItem')) : (currentEditableBlock) );
        var blockParent = jQuery(currentEditableBlock).parent();
        var attachmentNodeTimestamp = (
                                        (jQuery(currentEditableBlock).hasClass('tobAttachmentItemLink'))
                                        ? (jQuery(currentEditableBlock).attr('data-attachmentNodeTimestamp'))
                                        : (jQuery(currentEditableBlock).find('.tobAttachmentItemLink[data-attachmentNodeTimestamp]').attr('data-attachmentNodeTimestamp'))
                                      ) || false;

        jQuery(currentEditableBlock).remove();

        if(attachmentNodeTimestamp){
            // remover gemelos
            jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorBody] .tobAttachmentItemLink[data-attachmentNodeTimestamp='+attachmentNodeTimestamp+']').closest('.tobAttachmentItem').remove();
        }

        if(jQuery(blockParent).children().length < 1){
            if(!jQuery(blockParent).is('.attachmentBlockList')){
                self.removeImageBlock(jQuery(blockParent));
            }
        }
        return false;
    },
    /**
     *
     * @param {Element} currentEditableBlock
     * @param {String} originalContent
     * @param {Integer} top
     * @param {Integer} left
     * @param {Integer} width
     * @param {Integer} height
     * @param {Integer} imgMaxWidth
     */
    editBlockContent: function(currentEditableBlock, originalContent, top, left, width, height, imgMaxWidth){

        var originalContent = originalContent || '';

        jQuery('#editorOverlay').show();

        /*******************************************************************
         *
         * Editable plain text on place
         * @todo Implementar eliminación de tags html en modo solo texto
         *
         ******************************************************************/
        if(jQuery(currentEditableBlock).hasClass('tobEditableText')){

            jQuery(currentEditableBlock).css({
                position: 'relative',
                zIndex: '1040'
            });

            jQuery(currentEditableBlock).addClass('editModeOn').addClass('inlineEditMode');
            jQuery(currentEditableBlock).attr('contentEditable',true);

            jQuery('.inlineEditMode').trigger('focus');

            var tempTextarea = jQuery('<textarea>');
            var tempOriginalBlock = currentEditableBlock;

            jQuery.each(currentEditableBlock[0].attributes, function(index, attr) {
                jQuery(tempTextarea).attr(attr.name,attr.value);
            });

            jQuery(tempTextarea).html(jQuery(currentEditableBlock).html());

            jQuery(tempTextarea).css({
                width:'250px',
                'font-size':'11px !important',
                'background-color':'#FFFFFF',
                'color':'#000000',
                'border-bottom-right-radius': 0,
                'border-bottom-left-radius': 0
            }).attr('rows','3');

            jQuery(currentEditableBlock).replaceWith(tempTextarea);

            var textareaPosition = jQuery(tempTextarea).position();

            jQuery('#updateFieldButtons').css({
                left: textareaPosition.left,
                top: textareaPosition.top + jQuery(tempTextarea).outerHeight()
            }).show();

            jQuery('body').unbind('keydown').bind({

                keydown: function(event){
                    var keyCode = event.which;

                    switch(keyCode){
                        case KeyboardKey.Enter:
                        case KeyboardKey.Tab:
                            event.preventDefault();
                            var content = TemplateEditor.stripTags(jQuery(tempTextarea).val());
                            if(content == ''){
                                ErrorDisplay.pushError(__('El campo no puede estar vacío.'));
                                ErrorDisplay.display(false,__('ERROR'),50);
                                return false;
                            } else {

                                jQuery(tempOriginalBlock).html(TemplateEditor.stripTags(jQuery(tempTextarea).val()));
                                jQuery(tempTextarea).replaceWith(tempOriginalBlock);

                                TemplateEditor.releaseWorkingBlock();
                                TemplateEditor.persistTemplate(3,true);
                            }
                            break;
                        case KeyboardKey.Esc:
                            event.preventDefault();
                            jQuery(tempTextarea).replaceWith(tempOriginalBlock);

                            TemplateEditor.releaseWorkingBlock();
                            TemplateEditor.persistTemplate(3,false);
                            break;
                        case KeyboardKey.Backspace:
                            keyCode.stopPropagation();
                            break;
                    }
                }
            });
        } else
        /*******************************************************************
         *
         * Editable HTML WYSIWYG
         *
         ******************************************************************/
        if(jQuery(currentEditableBlock).hasClass('tobEditableHtml')){

            jQuery(currentEditableBlock).addClass('editModeOn').addClass('htmlEditMode');

            var backgroundColor = backgroundColor = jQuery('#restorePointDiv').closest(':hasBgColor').css('background-color');


            jQuery('#tobWysiwyg').append('<textarea id="tobWysiwygTextArea" style="width:100%;height:'+(height + 100)+'px" cols="30" rows="10"/>').css({
                'top' : top -30,
                'left': left -12,
                'width' : width +45,
                'height': height + 70
            });
            var replacedTxt = originalContent.replace(/</g,'&lt;');
            replacedTxt.replace(/>/g,'&gt;');
            jQuery('#tobWysiwygTextArea').html(replacedTxt);
            jQuery('#tobWysiwyg').show();

            var skipColorReassignment = false;

            jQuery('.restorePointAddedOverlay').slideUp();

            jQuery('#tobWysiwygTextArea').wysiwyg({
                controls: {
                    bold          : {
                        visible : true
                    },
                    italic        : {
                        visible : true
                    },
                    underline     : {
                        visible : true
                    },
                    strikeThrough : {
                        visible : true
                    },

                    colorpicker: {
                        groupIndex: 0,
                        visible: true,
                        css: {
                            "color": function (cssValue, Wysiwyg) {

                                if(skipColorReassignment){
                                    return false;
                                }
                                TemplateEditor.selectedTextColor = cssValue;
                                skipColorReassignment = true;
                                setTimeout(function(){
                                    skipColorReassignment = false;
                                },200);

                                return false;

                            }
                        },
                        exec: function() {
                            this.savedRange = this.getInternalRange();
                            if (jQuery.wysiwyg.controls.colorpicker) {
                                jQuery.wysiwyg.controls.colorpicker.init(this, TemplateEditor.selectedTextColor);
                            }
                        },
                        tooltip: "Colorpicker"
                    },

                    increaseFontSize : {
                        visible : false
                    },
                    decreaseFontSize : {
                        visible : false
                    },

                    changeFontSize  : {
                        visible: true
                    },

                    changeFont  : {
                        visible: true
                    },

                    justifyLeft   : {
                        visible : true
                    },
                    justifyCenter : {
                        visible : true
                    },
                    justifyRight  : {
                        visible : true
                    },
                    justifyFull   : {
                        visible : true
                    },

                    undo : {
                        visible : false
                    },
                    redo : {
                        visible : false
                    },

                    insertOrderedList    : {
                        visible : true
                    },
                    insertUnorderedList  : {
                        visible : true
                    },
                    insertHorizontalRule : {
                        visible : false
                    },
                    insertTable : {
                        visible : true
                    },

                    copy  : {
                        visible : false
                    },
                    paste : {
                        visible : false
                    },
                    html  : {
                        visible: true
                    },

                    wizardPersonalizationCombo: {
                        visible : true
                    },
                    wizardPersonalizationSocialSharingCombo: {
                        visible : true
                    },
                    save  : {
                        visible: true
                    },
                    cancel  : {
                        visible: true
                    }
                },
                css : {
                    'paddingLeft': 10,
                    'paddingRight': 10,
                    'border': 0,
                    'font-family': jQuery(currentEditableBlock).parent().css('font-family'),
                    'font-size': jQuery(currentEditableBlock).parent().css('font-size'),
                    'color': jQuery(currentEditableBlock).parent().css('color'),
                    'line-height': jQuery(currentEditableBlock).parent().css('line-height'),
                    'backgroundColor': backgroundColor
                }
            });

            jQuery('div.wysiwyg ul.toolbar li:not(.cancel, .save)').tooltip({placement:'bottom'});

        } else
        /*******************************************************************
         *
         * Editable IMAGE
         *
         ******************************************************************/
        if(jQuery(currentEditableBlock).hasClass('tobEditableImg')){
            /*jQuery('#topBlockClonWrapper').prepend('<div id="fileUploader"><noscript><p' + __('Para utilizar administrador de archivos debes habilitar JavaScript.') + '</p></noscript></div>');
                jQuery('#fileUploader').css({
                'top' : top - 70,
                'left': left - 150,
                'width' : 600
            });*/
            var sourceImg = jQuery(currentEditableBlock).find('img');

            abrirModalEditarImg(imgMaxWidth, currentEditableBlock, sourceImg)

            //TemplateEditor.createInsertImageControl(imgMaxWidth, currentEditableBlock, sourceImg);



        } else
        /*******************************************************************
         *
         * AttachmentBlocks
         *
         ******************************************************************/
        if(jQuery(currentEditableBlock).hasClass('tobAttachmentItemLink')){
            jQuery('#topBlockClonWrapper').prepend('<div id="fileUploader"><noscript><p>' + __('Para utilizar administrador de archivos debes habilitar JavaScript.') + '</p></noscript></div>');
            jQuery('#fileUploader').css({
                'top' : top - 70,
                'left': left - 150,
                'width' : 600
            });
            var attachmentLinkElement = jQuery(currentEditableBlock);
            TemplateEditor.createInsertAttachmentControl(currentEditableBlock, attachmentLinkElement);
        }
    },
    /**
     *
     *
     */
    getWysiwygObject: function(){
        return jQuery('#tobWysiwygTextArea').data('wysiwyg') || false;
    },
    /**
     *
     */
    tobWysiwygTextAreaIframeAutoHeight: function(){
        var objFramePage = window.document.frames("[tobWysiwygTextAreaIFrame]").document.body;
        var objFrame = window.document.all.tobWysiwygTextAreaIFrame;
        objFrame.style.height = objFramePage.scrollHeight + (objFramePage.offsetHeight - objFramePage.clientHeight);
    },
    /**
     *
     * @param {String} contents
     */
    saveFromWysiwyg: function (contents){
        jQuery('.htmlEditMode').html(contents);

        setTimeout(function(){
            TemplateEditor.releaseWorkingBlock();
            TemplateEditor.persistTemplate(4,true);
        },50);
    },
    /**
     *
     */
    cancelFromWysiwyg: function (){
        setTimeout(function(){
            TemplateEditor.releaseWorkingBlock();
            TemplateEditor.persistTemplate(4,false);
        },50);
    },
    /**
     *
     * @param {Element} block
     */
    duplicateBlock: function(block){

        jQuery('#tobBlockToolsDuplicate').unbind('click').bind({
            click: function(){

                var duplication = jQuery(block).clone();
                jQuery(duplication).removeClass('blockHighlighted').addClass('tobRemovable');
                jQuery(duplication).insertAfter('#restorePointDiv').hide().fadeIn('fast', function(){

                    TemplateEditor.releaseWorkingBlock();
                    jQuery('.preventMouseActionsOverlay').show();

                    TemplateEditor.persistTemplate(5,true);
                });

            }
        });

    },
    /**
     *
     * @param {Element} block
     */
    removeBlock: function(block){

        jQuery('#tobBlockToolsRemove').unbind('click').bind({
            click: function(){

                TemplateEditor.releaseWorkingBlock();

                jQuery(block).next(".dropeable").remove();
                jQuery(block).remove();

                TemplateEditor.releaseWorkingBlock();
                jQuery('.preventMouseActionsOverlay').show();
                TemplateEditor.persistTemplate(6,true);
            }
        });

    },
    /**
     *
     * @param {Element} block
     */
    blockIsClonable: function(block){

        if(!jQuery(block).is('#unsubscribeBlock')){
            return true;
        }
        return false;

    },
    /**
     *
     * @param {Element} block
     */
    blockIsRemovable: function(block){

        if(!jQuery(block).is('#unsubscribeBlock')){
            return true;
        }
        return false;

    },
    /**
     *
     * @param {Integer} top
     * @param {Integer} left
     * @param {Integer} width
     * @param {Integer} height
     * @param {Boolean} isTobEditableImg
     * @param {Boolean} isTobAttachmentItemLink
     */
    drawOutline: function(top, left, width, height, isTobEditableImg, isTobAttachmentItemLink){
        var isTobEditableImg = isTobEditableImg || false;
        var isTobAttachmentItemLink = isTobAttachmentItemLink || false;

        jQuery('#tobOutline').find('.imgToolContainer').remove();
        jQuery('#tobOutline').find('.attachmentToolContainer').remove();

        if(isTobEditableImg){
            jQuery('#tobOutline').append('<div class="imgToolContainer"><div class="imgTool delete">' + __('Borrar') + '</div><div class="imgTool edit">' + __('Editar') + '</div></div>');
            jQuery('#tobOutline .imgToolContainer').css({
                'margin-left' : (width/2) - 32
            });
        }

        if(isTobAttachmentItemLink){
            jQuery('#tobOutline').append('<div class="attachmentToolContainer"><div class="attachmentTool delete">' + __('Borrar') + '</div><div class="attachmentTool edit">' + __('Editar') + '</div></div>');
            jQuery('#tobOutline .attachmentToolContainer').css({
                'margin-left' : (width/2) - 32
            });
        }

        jQuery('#tobOutline').css({
            'top' : top,
            'left': left,
            'width' : width,
            'height' : height
        }).show();

    },
    /**
     *
     */
    releaseWorkingBlock: function(){
        var self = this;
        jQuery('body').unbind('keydown');

        if(!TemplateEditor.workingBlock){
            return false;
        }

        setTimeout(function(){
            if(jQuery.modal && jQuery('#simplemodal-container') && jQuery('#simplemodal-container').size() > 0){
                jQuery.modal.close();
            }
        },50);

        self.stripNastyTagsFromElement(TemplateEditor.workingBlock);

        /** Previene que desaparezcan los bloques cuando el usuario borra todo el contenido */
        jQuery(jQuery(TemplateEditor.workingBlock).find('.tobEditableHtml, .tobEditableImg')).each(function(){
            if(jQuery(this).html() == ''){
                jQuery(this).html('&nbsp;');
            }
        });

        jQuery('.wysiwyg-imgWrapper').children('.imgToolContainer').remove();
        jQuery('.wysiwyg-imgWrapper').children('img').unwrap().removeClass('wrapped');
        jQuery('.wysiwyg-imgWrapper').remove();
        jQuery('.wysiwyg-attachmentWrapper').children('.attachmentToolContainer').remove();
        jQuery('.wysiwyg-attachmentWrapper').children('.tobAttachmentItem').unwrap().children('.wrapped').removeClass('wrapped');
        jQuery('.wysiwyg-attachmentWrapper').remove();

        jQuery('#editorOverlay').hide().removeClass('inBetween');
        jQuery('#drawOutline').hide();
        jQuery('#tobBlockToolsRemove').hide();
        jQuery('#tobBlockToolsDuplicate').hide();
        jQuery('#tobBlockToolsWrapper').hide();
        jQuery('.blockHighlighted').removeClass('blockHighlighted');
        jQuery('.preventMouseActionsOverlay').hide();

        self.resetEditableFieldsMode();
        self.resetEditButtons();

        jQuery("#restorePointDiv").replaceWith(jQuery(self.workingBlock));
        jQuery('#topBlockClonWrapper').hide();
        self.workingBlock = false;
    },
    /**
     *
     */
    resetEditButtons: function(){
        var self = this;
        jQuery('.drawOutline').removeClass('drawOutline');
        jQuery('#updateFieldButtons, #imgMaxWidth').hide();
        self.showRestoreDefaultContentButton();
    },
    /**
     *
     */
    resetEditableFieldsMode: function(){
        jQuery('[contentEditable]').removeAttr('contentEditable');
        jQuery('.editModeOn').removeClass('editModeOn');
        jQuery('.htmlEditMode').removeClass('htmlEditMode');

        jQuery('#editorOverlay, #updateFieldButtons, #imgMaxWidth').hide();
        jQuery('.inlineEditMode').removeClass('inlineEditMode').css({
            position: '',
            zIndex: ''
        });
        jQuery('#tobWysiwyg').html('').removeAttr('style').hide();
        jQuery('#fileUploader').remove();
        jQuery('body').die('keydown'); // OJO revisar
    },
    /**
     *
     */
    showRestoreDefaultContentButton: function(){

        jQuery('.drawOutline').removeClass('drawOutline');
        jQuery('#fieldEditToolsRestoreDefaultConfirmText,#fieldEditToolsRestoreDefaultConfirm, #fieldEditToolsRestoreDefaultCancel, #updateFieldButtons, #imgMaxWidth').hide();
        jQuery('#fieldEditToolsRestoreDefault').show();
        jQuery('#tobOutline').hide().css({
            'top' : 0,
            'left': 0,
            'width' : 0,
            'height' : 0
        });
    },
    /**
     *
     * @param {Integer} imgMaxWidth
     */
    showImgMaxWidthTip: function(imgMaxWidth){

        if(imgMaxWidth < 50){
            return false;
        }
        jQuery('.imgMaxWidthVal').html(imgMaxWidth);
        jQuery('#imgMaxWidth').show();
    },
    /**
     *
     * @param {Integer} tobID Id del bloque
     */
    restoreTobOriginalContent: function(tobID){

        var originalContent = jQuery('#templateEditorOriginalContent').find('[data-tobID='+tobID+']').html();

        jQuery('#topBlockClonWrapper').find('[data-tobID='+tobID+']').html(originalContent);
    },
    /**
     *
     * @param {String} string
     */
    stripTags: function(string){
        return string.replace(/(<.*?>|\r|\n)/igm,"");
    },
    /**
     *
     * @param {Integer} imgMaxWidth
     * @param {Element} currentEditableBlock
     * @param {Element} sourceImg
     */
    createInsertImageControl: function(imgMaxWidth, currentEditableBlock, sourceImg){

        var self = this;
        var imgMaxWidth = imgMaxWidth || false;
        var currentEditableBlock = currentEditableBlock || false;
        var sourceImg = sourceImg || false;
        var imglink = '';

        if(!sourceImg){
            ErrorDisplay.pushError(__('Ha ocurrido un error recuperando la imagen solicitada.'));
            ErrorDisplay.display(false,__('ERROR'),50);
        }

        if(jQuery(sourceImg).parent().is('a')){

            imglink = jQuery(sourceImg).parent().attr('href');

        }

        self.sourceImg = sourceImg;




        //InsertImage.init(false, sourceImg, imgMaxWidth, currentEditableBlock, imglink);
        //InsertImage.tooglePanels('imageProperties');
    },
    /**
     *
     * @param {Element} currentEditableBlock
     * @param {Element} attachmentLinkElement
     */
    createInsertAttachmentControl: function(currentEditableBlock, attachmentLinkElement){
        var self = this;
        var currentEditableBlock = currentEditableBlock || false;
        var attachmentLinkElement = attachmentLinkElement || false;

        if(!attachmentLinkElement){
            ErrorDisplay.pushError(__('Ha ocurrido un error recuperando el archivo solicitado.'));
            ErrorDisplay.display(false,__('ERROR'),50);
        }

        InsertAttachment.init(false, attachmentLinkElement);
        InsertAttachment.tooglePanels('attachmentProperties');

    },
    /**
     *
     * @param {Element} currentEditableBlock
     * @param {String} fileName
     * @param {String} altText
     * @param {Integer} width
     * @param {Integer} height
     * @param {String} imglink
     */
    updateTemplateImage: function(currentEditableBlock, fileName, altText, width, height, imglink){

        var altText = altText || ' ';
        var width = width || false;
        var height = height || false;
        var style = '';
        var imglink = imglink || false;
        var imageBlock = '';
        var image = jQuery(currentEditableBlock).find('img');

        width = (width > 0) ? width : '';
        height = (height> 0) ? height : '';

        jQuery(image).attr("src", fileName)
                .attr("title", altText)
                .attr("alt", altText)
                .css({"width": width,"height": height});

        jQuery(image).wrap("<span></span>");

        if( imglink ){
            jQuery(image).wrap("<a href='"+imglink+"'></a>");
        }

        var imageBlock = jQuery(image).closest('span').html();

        jQuery(currentEditableBlock).html(imageBlock);
        TemplateEditor.releaseWorkingBlock();
        TemplateEditor.persistTemplate(7,true);
    },
    /**
     *
     */
    updateTemplateAttachmentsBlock : function(){
        TemplateEditor.releaseWorkingBlock();
    },
    /**
     *
     */
    showBackToCampaignButton: function(){
        var self = this;
        /*var previousEditingCampaign = jQuery.JSON.cookie('previous-editing-campaign');

        if(typeof previousEditingCampaign != 'undefined' && previousEditingCampaign != null){
            jQuery('#saveAndBackToCampaign').show();
        } else {
            jQuery('#saveAndBackToCampaign').hide();
        }*/
    },
    /**
     *
     * @param {Integer} restorePointMessage is the index of the messages responses
     * @param {Boolean} saveRestorePoint
     */
    persistTemplate: function(restorePointMessage, saveRestorePoint){

        if(jQuery("#restorePointDiv").length > 0){

            jQuery("#restorePointDiv").remove();
        }

        var self = this;
        var restorePointMessage = restorePointMessage || '';

        jQuery(".preventMouseActionsOverlay").hide();


        alertarPageLeave = true;

        //guardarContenidoHTML(false);

    },
    /**
     *
     * @param {Integer} restorePointMessage is the index of the messages responses
     * @param {Boolean} saveRestorePoint
     */
    persistSaveTemplate: function(restorePointMessage, saveRestorePoint){
        var self = this;
        var restorePointMessage = restorePointMessage || '';
        var saveRestorePoint = saveRestorePoint || false;
        var content = jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorBody]').html();
        var plainText = jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=plainTextVersionContent]').val();

        var advanceEditable = self.advanceEditable ? 1 : 0;
        var autoContentAlternate = self.autoContentAlternate ? 1 : 0;
        var remoteTemplateUrl =  self.remoteTemplateUrl || '';
        var plainTextContent = self.assignPlainTextContent(plainText, content, self.autoContentAlternate);
        var sourceCodeContent = self.assignSourceCodeContent(jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorBody] > .templateBoundary').html());
        var recordID = self.currentRecordID || '';
        var contentCopy = content;

        var remoteUnsubscribeBlock = '';

        if(remoteTemplateUrl.length > 0){
            var node = jQuery('<div>').append(content);
            var hasUnsubscribeLink = content.match(/%UnSubscribe%/g);

            if(!hasUnsubscribeLink){
                node.append(TemplateEditor.unsubscribeBlock);
            }

            var unsuscribe = jQuery(node).find('[id="unsubscribeBlock"]:last');
                remoteUnsubscribeBlock = unsuscribe.outerHtml();
                content = '';
                advanceEditable = 0;
        }

        jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] .savingThrobber').show();

        var defer = new jQuery.Deferred();

        defer =  jQuery.ajax({
            //url: '/content/edit',
            url: urlHandler+"?accion=persistUpdateTemplate",
            data:{
                HTML: content,
                AdvanceEditable: advanceEditable,
                URL: remoteTemplateUrl,
                PlainText: plainTextContent,
                CampaignID: recordID,
                RemoteUnsubscribeBlock: remoteUnsubscribeBlock,
                AutoContentAlternate: autoContentAlternate
            },
            dataType: 'json',
            parseError: true,
            type: 'POST'
        }).done(function(data){

            var response = data && data.root && data.root.ajaxResponse;

            if(response
                && 'success' in  response
                && response.success == true)
            {
                self.action = 'update';
                self.plainTextHasChanged = false;

                if(saveRestorePoint){
                    self.writeHistoryRestorePoint(contentCopy, self.templateEditorOriginalContent, advanceEditable, remoteTemplateUrl, plainTextContent, autoContentAlternate, restorePointMessage );
                }

            }

            jQuery('.savingThrobber').hide();

        }).fail(function(jqXHR, status, errorMSG){
            jQuery('.savingThrobber').hide();
            ErrorDisplay.parseAndPushSystemErrors(errorMSG);

            ErrorDisplay.display(false,__('ERROR'),50);

        });

        return defer;

    },
    /**
     *
     * @param {Integer} restorePointMessage is the index of the messages responses
     * @param {Boolean} saveRestorePoint
     */
    persistUpdateTemplate: function(restorePointMessage, saveRestorePoint){
        var self = this;
        var restorePointMessage = restorePointMessage || '';
        var saveRestorePoint = saveRestorePoint || false;
        var content = jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorBody]').html();
        var plainText = jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=plainTextVersionContent]').val();


        var advanceEditable = self.advanceEditable ? 1 : 0;
        var autoContentAlternate = self.autoContentAlternate ? 1 : 0;
        var remoteTemplateUrl =  self.remoteTemplateUrl || '';
        var plainTextContent =  self.assignPlainTextContent(plainText, content, self.autoContentAlternate);
        var sourceCodeContent = self.assignSourceCodeContent(jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorBody] > .templateBoundary').html());
        var recordID = self.currentRecordID || '';
        var contentCopy = content;

        var remoteUnsubscribeBlock = '';

        if(remoteTemplateUrl.length > 0){
            var node = jQuery('<div>').append(content);
            var hasUnsubscribeLink = content.match(/%UnSubscribe%/g);

            if(!hasUnsubscribeLink){
                node.append(TemplateEditor.unsubscribeBlock);
            }

            var unsuscribe = jQuery(node).find('[id="unsubscribeBlock"]:last');
                remoteUnsubscribeBlock = unsuscribe.outerHtml();
                content = '';
                advanceEditable = 0;
        }

        /*
        jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] .savingThrobber').show();

        defer = jQuery.ajax({
            //url: '/content/edit',
            url: urlHandler+"?accion=persistUpdateTemplate",
            data:{
                HTML: content,
                AdvanceEditable: advanceEditable,
                URL: remoteTemplateUrl,
                PlainText: plainTextContent,
                CampaignID: recordID,
                RemoteUnsubscribeBlock: remoteUnsubscribeBlock,
                AutoContentAlternate: autoContentAlternate
            },
            dataType: 'json',
            parseError: true,
            type: 'POST'
        }).done(function(data){

            var response = data && data.root && data.root.ajaxResponse;

            jQuery('.savingThrobber').hide();

            if(response
                && 'success' in  response
                && response.success == true)
            {
                if(saveRestorePoint){
                    self.writeHistoryRestorePoint(contentCopy, self.templateEditorOriginalContent, advanceEditable, remoteTemplateUrl, plainTextContent, autoContentAlternate, restorePointMessage );
                }

                self.plainTextHasChanged = false;

            }

        }).fail(function(jqXHR, status, errorMSG){
            jQuery('.savingThrobber').hide();

            ErrorDisplay.parseAndPushSystemErrors(errorMSG);

            ErrorDisplay.display(false,__('ERROR'),50);
        });

        return defer;
            */
           alert("persistUpdateTemplate");
    },
    /**
     *
     * @param {Integer} id
     * @param {String} url The url of an external template
     * @param {String} action save|update
     * @param {Integer} advanceEditable 1|0
     * @param {String} templateType myTemplates|defaulTemplates|externalUrlTemplates
     */
    importTemplateToEditor: function(id, url, action, advanceEditable, templateType){
        var self = this;
        var id = id || false;
        var url = url || false;

        self.action = action || 'save';

        var advanceEditable = advanceEditable || 0;

        var templateType = templateType || 'myTemplates';
        jQuery('#remoteContentNotEditableWarning').slideUp();
        jQuery('#remoteContentNotEditableWarning').find('#remoteTemplateURI').attr('href','').empty();
        jQuery('[data-containerName=editorBlocksContainer] [data-containerName=templateEditorContainer] > .templateEditorNav .disabled').removeClass('disabled');

        if( id != false || url != false )
        {
            self.cleanUp();

            jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorBody]').hide();
            jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorThrobber]').fadeIn();

            if (templateType == 'myTemplates') {

                self.retrieveMyTemplateContent(id).done(function(data){
                    jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorThrobber]').hide();
                });

            } else if(templateType == 'defaulTemplates') {
                /// Default Templates
                self.retrieveDefaultTemplateContent(id, advanceEditable).done(function(data){
                    jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorThrobber]').hide();

                });
            } else if(templateType == 'externalUrlTemplates'){
                /// External Templates
                self.retrieveExternalUrlTemplatesContent(id, 0, url).done(function(data){
                    jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorThrobber]').hide();
                });
            }

            self.templateEditorOriginalContent = false;
        }
    },
    /**
     *
     * @param {Integer} templateID
     * @param {Integer} advanceEditable 1|0
     */
    retrieveDefaultTemplateContent: function(templateID, advanceEditable){
        var self = this;

        var advanceEditable = advanceEditable || 0;
        var restorePointMessage = 12;

            //(data)
            jQuery('[data-name=subcontenido]').removeClass('subcontenido');

            jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=secondaryTemplateEditorNav]').show();

            var patt1 = /<div\s+(style="[A-Za-z0-9-:;#\(\),\s]+")?\s*class="?templateBoundary(.|\r|\n)+Template\s\/\/\s-->/igm; // /<div\s+class="?templateBoundary(.|\r|\n)+Template\s\/\/\s-->/igm;
            var templateContent = data.match(patt1);

            if(self.action == 'load'){
                self.action = 'update';
            }

            self.advanceEditable = advanceEditable;
            self.remoteTemplateUrl = '';

            if(templateContent == null || templateContent.length < 0){
                var defaultTemplateEnvolpe = self.defaultTemplateEnvolpeTop;
                defaultTemplateEnvolpe += data;
                defaultTemplateEnvolpe += self.defaultTemplateEnvolpeBottom;

                templateContent = defaultTemplateEnvolpe.match(patt1);

                self.advanceEditable = 0;

            }

            templateContent[0] = templateContent[0].replace(/<\s*style(.|\r|\n)+\/style\s*>/igm, "");

            if(self.tokenizeTemplateContent(templateContent)){
                self.setPlainTextTypeMode('auto');

                jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorBody]').html(self.templateEditorOriginalContent);

                var assignPlainTextContent = self.assignPlainTextContent('', self.templateEditorOriginalContent, self.autoContentAlternate);

                jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorThrobber]').fadeOut('fast', function(){
                    jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorBody]').fadeIn();
                    jQuery('.optionsButtons .addAttachment').show();
                });
                self.showBackToCampaignButton();

                if(self.contentIsLoaded){
                    restorePointMessage = 8;
                }

                self.autoSetTemplateBoundaryBackgroundColor();

                self.persistTemplate(restorePointMessage,true);
                self.contentIsLoaded = true;

            } else {
                jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorThrobber]').fadeOut();
            }




    },
    /**
     *
     * @param {Integer} campaignID
     */
    retrieveMyTemplateContent: function(campaignID){

                    jQuery('[data-name=subcontenido]').removeClass('subcontenido');


                    jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=secondaryTemplateEditorNav]').show();

                    var patt1 = /<div\s+(style="[A-Za-z0-9-:;#\(\),\s]+")?\s*class="?templateBoundary(.|\r|\n)+Template\s\/\/\s-->/igm;
                    var templateContent  = response.content.HTML ? response.content.HTML.match(patt1) : '';
                    var plainTextContent  = response.content.PlainText ? response.content.PlainText : '';
                    var forceAutoContentAlternate =  true;
                    var autoContentAlternate = response.content.AutoContentAlternate || false;

                    if(self.action == 'load'){
                        forceAutoContentAlternate = false;
                        self.action = 'update';
                    }

                    self.advanceEditable = response.content.AdvanceEditable;

                    self.remoteTemplateUrl = '';

                    if(templateContent == null || templateContent.length <= 0){
                        var defaultTemplateEnvolpe = self.defaultTemplateEnvolpeTop;
                        defaultTemplateEnvolpe += response.content.HTML;
                        defaultTemplateEnvolpe += self.defaultTemplateEnvolpeBottom;

                        templateContent = defaultTemplateEnvolpe.match(patt1);

                        self.advanceEditable = 0;

                    }

                    templateContent[0] = templateContent[0] ? templateContent[0].replace(/<\s*style(.|\r|\n)+\/style\s*>/igm, "") : '';

                    if( self.tokenizeTemplateContent(templateContent) ){

                        var plainTextEditorMode = (( forceAutoContentAlternate || autoContentAlternate)?('auto'):('manual'));
                        self.setPlainTextTypeMode(plainTextEditorMode);

                        jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorBody]').html(self.templateEditorOriginalContent);

                        var assignPlainTextContent = self.assignPlainTextContent(plainTextContent, self.templateEditorOriginalContent, self.autoContentAlternate);

                        jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorThrobber]').fadeOut('fast', function(){
                            jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorBody]').fadeIn();
                            jQuery('.optionsButtons .addAttachment').show();
                        });
                        self.showBackToCampaignButton();

                        if(self.contentIsLoaded){
                            restorePointMessage = 9;
                        }

                        self.autoSetTemplateBoundaryBackgroundColor();

                        self.persistTemplate(restorePointMessage,true);
                        self.contentIsLoaded = true;

                    } else {
                        jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorThrobber]').fadeOut();
                    }


    },
    /**
     *
     * @param {Integer} recordID
     * @param {Integer} advanceEditable 1|0
     * @param {String} templateUrl
     */

    /**
     * Assigns content to the plainText textarea container
     *
     * @param {String} currentPlainTextContent
     * @param {String} currentHtmlContent
     * @param {Boolean} autoContentAlternate
     * @return {String}
     */
    assignPlainTextContent: function(currentPlainTextContent, currentHtmlContent, autoContentAlternate){

        var self = this;
        var currentPlainTextContent = currentPlainTextContent || '';
        var currentHtmlContent = currentHtmlContent || '';
        var autoContentAlternate = autoContentAlternate || false;

        var resultingPlainText = (
                                    (!autoContentAlternate && currentPlainTextContent.length > 0)
                                    ? self.htmlToPlainText(self.stripNastyTagsFromString(currentPlainTextContent), true)
                                    : self.htmlToPlainText(currentHtmlContent)
                                );

        try {
            jQuery('[data-containername="editorBlocksContainer"][data-recordid="'+ self.currentRecordID +'"] [data-containername="plainTextVersionContent"]').val(resultingPlainText);
        } catch(e) {}

        return resultingPlainText;
    },
    /**
     * @param {String} stringContent
     *
     */
    assignSourceCodeContent: function(stringContent){
        var self = this;
        var stringContent = stringContent || '';
            stringContent = stringContent.replace(/\t/gi, "    ");
            stringContent = jQuery.trim(stringContent);
        try {
            jQuery('[data-containerName=editorBlocksContainer][data-recordId="'+ self.currentRecordID +'"] [data-containerName=sourceCodeVersionContent]').val(stringContent);
        } catch(e){}
    },
    /**
     *
     * @param {String} templateContent
     */
    tokenizeTemplateContent: function(templateContent){
        var self = this;
        var templateContent = templateContent || false;

        if(templateContent){
            jQuery('#templateEditorOriginalContent').remove();

            var templateEditorOriginalContent = jQuery('<div id="templateEditorOriginalContent" style="display:none;">');

            templateEditorOriginalContent.append(templateContent.toString());

            jQuery('body').append(templateEditorOriginalContent);

            jQuery('#templateEditorOriginalContent').find('[data-tobID]').html();

            jQuery('#templateEditorOriginalContent .modified').removeClass('modified');

            jQuery('.tobRemovable,.tobClonable,.tobEditableText,.tobEditableImg,.tobEditableHtml, .tobAttachmentItemLink, .tobBlock').each(function(tobID) {
                jQuery(this).attr('data-tobID',tobID);
            });

            self.templateEditorOriginalContent = jQuery('#templateEditorOriginalContent').html();

            return self.templateEditorOriginalContent;

        } else {

            ErrorDisplay.pushError(__('No se ha podido procesar el contenido de la plantilla.'));
            ErrorDisplay.display(function(){
                jQuery('.savingThrobber').hide();
                jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorThrobber]').fadeOut();
                jQuery('.templateEditorOuterContainer').fadeOut('normal', function(){
                    jQuery('.submenu-item-list').hide();
                    jQuery('.templateSourceOuterContainer').fadeIn();
                    jQuery('[data-name=subcontenido]').addClass('subcontenido');
                });
            }, __('ERROR'), 50);

            return false;
        }

    },
    /**
     *
     *
     */
    spamRating: function(recordID){
        var self = this;
        var recordID = recordID || false;
        var defer = new jQuery.Deferred();

        if(!recordID){
            ErrorDisplay.pushError(__('Ha ocurrido un error'));
            ErrorDisplay.display(false,__('ERROR'),50);
            return defer.reject();
        }

        defer = jQuery.ajax({
            url: "/content/spamrating/format/json" ,
            type: 'POST',
            dataType: 'json',
            parseError: true,
            data: {
                CampaignID: recordID
            }
        }).done(function(data){
            var response = data && data.root && data.root.ajaxResponse;


            if(response
                && 'success' in  response
                && 'spamRating' in  response
                && response.success == true)
            {
                if(typeof response.spamRating.isSpam != 'undefined'
                    && typeof response.spamRating.score != 'undefined'
                    && typeof response.spamRating.required != 'undefined'
                    && typeof response.spamRating.analisis != 'undefined')
                {

                    if(response.spamRating.isSpam == 1){
                        jQuery('.spamRatingWindow .heading .icon').removeClass('loading').addClass('spam');
                        jQuery('.spamRatingWindow .heading .message').html(__('Es posible que tu email sea calificado como SPAM'));
                    } else {
                        jQuery('.spamRatingWindow .heading .icon').removeClass('loading').addClass('ok');
                        jQuery('.spamRatingWindow .heading .message').html(__('Tu email no se ha detectado como SPAM'));
                    }

                    jQuery('.spamRatingWindow .heading .score').html( __('Valoración') + ': ' + response.spamRating.score + ' / ' + response.spamRating.required );

                    var analisis = new Array;

                    for( var i in response.spamRating.analisis){
                        var item = response.spamRating.analisis[i];
                        analisis.push('<span class="item">');
                        analisis.push('<span class="score">' + item.point + '</span>');
                        analisis.push('<span class="message" data-rule="' + item.ruel + '">' + item.description + '</span>');
                        analisis.push('</span>');
                    }

                    jQuery('.spamRatingWindow .responseInfoTitle').show();

                    jQuery('.spamRatingWindow .responseInfo').html(analisis.join("\n"));

                    jQuery(".msg-notice-n-shadow").height(jQuery(".msg-notice-n").height() + 25);


                } else {
                    jQuery('.spamRatingWindow .heading .icon').remove();
                    jQuery('.spamRatingWindow .heading .message').html(__('Ha ocurrido un error en el proceso.'));
                }

            }

        }).fail(function(jqXHR, status, errorMSG){

            ErrorDisplay.parseAndPushSystemErrors(errorMSG);

            ErrorDisplay.display(false,__('ERROR'),50);

        });


    },


    /**
     *
     *
     */
    toggleContentTypeEditorMode: function(){
        var self = this;
        var type = self.contentTypeEditorMode;
        jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] textarea[data-containerName=sourceCodeVersionContent]').blur();
        switch(type){
            case'plainText':
                jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=plainTextEditorContainer], \
                    #plainTextEditorMessage').show();

                jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=htmlEditorContainer], \
                    [data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorRuler], \
                    [data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=sourceCodeEditorContainer],\
                    #sourceCodeEditorMessage').hide();

                jQuery('.templateEditorNav [data-toolAction=addAttachment]').removeClass('disabled');

                if(!self.autoContentAlternate){
                    jQuery('.templateEditorNav [data-toolAction=copyFromHtmlVersion]').removeClass('disabled');
                    jQuery('.templateEditorNav [data-toolAction=addAttachment]').addClass('disabled');
                }

                jQuery('.templateEditorOuterContainer').attr('data-currentMode',type);
                jQuery('.item-editHtml').addClass('item-editPlainText');
                jQuery('[data-toolaction=editPlainText]').addClass('selected');
                jQuery('[data-toolaction=editHtml]').removeClass('selected');
                break;
            case'sourceCode':
                jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=sourceCodeEditorContainer], \
                    #sourceCodeEditorMessage').show();
                jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] textarea[data-containerName=sourceCodeVersionContent]').focus();

                jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=htmlEditorContainer], \
                    [data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorRuler], \
                    [data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=plainTextEditorContainer], \
                     #plainTextEditorMessage').hide();

                jQuery('.templateEditorNav [data-toolAction=copyFromHtmlVersion], \
                    .templateEditorNav [data-toolAction=addAttachment]').addClass('disabled');

                jQuery('.templateEditorOuterContainer').attr('data-currentMode',type);
                jQuery('.item-editHtml').removeClass('item-editPlainText');
                jQuery('[data-toolaction=editPlainText], [data-toolaction=editHtml]').removeClass('selected');

                break;
            default:
                jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=plainTextEditorContainer], \
                    #plainTextEditorMessage, \
                    [data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=sourceCodeEditorContainer], \
                    #sourceCodeEditorMessage').hide();

                jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=htmlEditorContainer], \
                    [data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorRuler]').show();

                jQuery('.templateEditorNav [data-toolAction=addAttachment]').removeClass('disabled');
                jQuery('.templateEditorNav [data-toolAction=copyFromHtmlVersion]').addClass('disabled');
                jQuery('.templateEditorOuterContainer').attr('data-currentMode',type);
                jQuery('.item-editHtml').removeClass('item-editPlainText');
                jQuery('[data-toolaction=editPlainText]').removeClass('selected');
                jQuery('[data-toolaction=editHtml]').addClass('selected');
                break;
        }


    },
    /**
     *
     * @param {String} type auto|manual
     */
    setPlainTextTypeMode: function(type){
        var self = this;
        var type = type || 'auto';

        switch(type){
            case'manual':
                self.autoContentAlternate = false;
                jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=plainTextVersionContent]').removeClass('disabled');

                if(self.contentTypeEditorMode == 'plainText'){
                    jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorContainer] > .templateEditorNav > li > .subNav > ul > li[data-toolAction=copyFromHtmlVersion]').removeClass('disabled');
                    jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorContainer] > .templateEditorNav > li > .subNav > ul > li[data-toolAction=addAttachment]').addClass('disabled');
                }

                jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorContainer] > .templateEditorNav > li > .subNav > ul > li[data-toolAction=editPlainText] > a .tip')
                    .html('manual')
                    .attr('data-bootstrap-Popover',true)
                    .attr('data-original-title',__('Modo MANUAL:'))
                    .attr('data-content',__('La versión de texto plano del email NO se genera automaticamente.'))
                    .popover({trigger : 'hover'});

                break;
            default:
                self.autoContentAlternate = true;
                jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=plainTextVersionContent], \
                    [data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorContainer] > .templateEditorNav > li > .subNav > ul > li[data-toolAction=copyFromHtmlVersion]').addClass('disabled');
                    jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorContainer] > .templateEditorNav > li > .subNav > ul > li[data-toolAction=addAttachment]').removeClass('disabled');

                jQuery('[data-containerName=editorBlocksContainer][data-recordId='+ self.currentRecordID +'] [data-containerName=templateEditorContainer] > .templateEditorNav > li > .subNav > ul > li[data-toolAction=editPlainText] > a .tip')
                    .html('auto')
                    .attr('data-bootstrap-Popover',true)
                    .attr('data-original-title',__('Modo AUTOMATICO:'))
                    .attr('data-content',__('La versión de texto plano del email se crea AUTOMATICAMENTE a partir del contenido HTML.'))
                    .popover({trigger : 'hover'});
                break;
        }

    },
    /**
     *
     * @param {String} string
     */
    htmlToPlainText: function(string, skipPreRemoveNewLines){
        var string = string || false;
        var skipPreRemoveNewLines = skipPreRemoveNewLines || false;

        if(!string){
            return '';
        }

        if(skipPreRemoveNewLines == false){
            string = string.replace(/\n+/gi, "");
            string = string.replace(/&nbsp;+/gi, " ");
            string = string.replace(/\s{2,}/gi, " ");
        }

        string = string.replace(/<\s*a\s+/gi, "\n<a ");
        string = string.replace(/<table[^>]*>|<br[^>]*>|<\/br>|<p[^>]*>|<\/p>|<\/tr>/gi, "\n");
        string = string.replace(/<{1}\s*(?!\/*\s*a){1}\/*(?!a).*?\s*>/ig, "");
        string = string.replace(/<\s*a.*href="(.*?)".*>\s*(.*?)\s*<\/\s*a\s*>/gi, " $2 ( Link -> $1 ) ");
        string = string.replace(/<a[^>]*>|<\/a>/gi, "");
        string = string.replace(/ {2,}/gi, " ");
        string = string.replace(/\n+\s*/gi, "\n\n");


        return string
    },
    /**
     * Preforms a regexp replacement to a string and returns resulting string
     * @param {String}
     * @return {String}
     */
    stripNastyTagsFromString: function(string){
        var string = string || '';

        string = string.replace(/<\s*/gi, "<");
        string = string.replace(/<\/\s*/gi, "</");
        string = string.replace(/\s*>/gi, ">");

        string = string.replace(/<head[^\0]*<\/head>/gim, "");
        string = string.replace(/<(no)*script([\w\W]*?)<\/(no)*script>/gim, "");
        string = string.replace(/<(i)*frame([\w\W]*?)<\/(i)*frame>/gim,"");
        string = string.replace(/<link[^>]*>|<meta[^>]*>|<base[^>]*>|<\/*form[^>]*>/gi, "");
        string = string.replace(/<\s*\!*doctype\s*[^>](.*\s*)[^>]*>/gi, "");
        string = string.replace(/<\/*title[^>]*>|<\/*htm[^>]*>|<\/*body[^>]*>/gi, "");

        string = string.replace(/on[click|mouseover|mouseout|submit|focus|blur|change|dblclick|mousedown|mousemove|mouseup|keydown|keypress|keyup|load|resize|unload|select]*=\"[^\"]*\"{1}/gi, "");
        string = string.replace(/on[click|mouseover|mouseout|submit|focus|blur|change|dblclick|mousedown|mousemove|mouseup|keydown|keypress|keyup|load|resize|unload|select]*=\'[^\']*\'{1}/gi, "");


        return string;
    },
    /**
     * Performs stripNastyTagsFromString() method over a dom element and returns the element with it's content modified
     * @param {Element} element
     */
    stripNastyTagsFromElement: function(element){
        var self = TemplateEditor;
        var element = element || false;
        if(element){
            var elementContent = jQuery(element).html();
            elementContent = self.stripNastyTagsFromString(elementContent);
            jQuery(element).html(elementContent);
        }
        return true;
    },
    /**
     *
     */
    cleanUp: function(cleanUndoHistory){

        var cleanUndoHistory = cleanUndoHistory || false;

        if(cleanUndoHistory){
            TemplateEditor.undoHistory = new Array;
            jQuery('ul.restoreHistory-items').empty();
        }

        TemplateEditor.contentTypeEditorMode = 'html';

        TemplateEditor.toggleContentTypeEditorMode();

        jQuery('[contentEditable]').removeAttr('contentEditable');
        jQuery('#templateEditorOriginalContent, #topBlockClonWrapper, .preventMouseActionsOverlay').remove();
        TemplateEditor.insertDomComponents();
    }
}

jQuery(function(){
    TemplateEditor.init();
});