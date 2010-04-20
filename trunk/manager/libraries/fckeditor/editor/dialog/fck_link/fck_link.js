/*
 * FCKeditor - The text editor for Internet - http://www.fckeditor.net
 * Copyright (C) 2003-2009 Frederico Caldeira Knabben
 *
 * == BEGIN LICENSE ==
 *
 * Licensed under the terms of any of the following licenses at your
 * choice:
 *
 *  - GNU General Public License Version 2 or later (the "GPL")
 *    http://www.gnu.org/licenses/gpl.html
 *
 *  - GNU Lesser General Public License Version 2.1 or later (the "LGPL")
 *    http://www.gnu.org/licenses/lgpl.html
 *
 *  - Mozilla Public License Version 1.1 or later (the "MPL")
 *    http://www.mozilla.org/MPL/MPL-1.1.html
 *
 * == END LICENSE ==
 *
 * Scripts related to the Link dialog window (see fck_link.html).
 */

var dialog	= window.parent ;
var oEditor = dialog.InnerDialogLoaded() ;

var FCK			= oEditor.FCK ;
var FCKLang		= oEditor.FCKLang ;
var FCKConfig	= oEditor.FCKConfig ;
var FCKRegexLib	= oEditor.FCKRegexLib ;
var FCKTools	= oEditor.FCKTools ;
var blnInit		= false ;

//#### Dialog Tabs

// Set the dialog tabs.
dialog.AddTab( 'Info', FCKLang.DlgLnkInfoTab ) ;

if ( !FCKConfig.LinkDlgHideTarget )
	dialog.AddTab( 'Target', FCKLang.DlgLnkTargetTab, true ) ;

if ( FCKConfig.LinkUpload )
	dialog.AddTab( 'Upload', FCKLang.DlgLnkUpload, true ) ;

if ( !FCKConfig.LinkDlgHideAdvanced )
	dialog.AddTab( 'Advanced', FCKLang.DlgAdvancedTag ) ;

// Function called when a dialog tag is selected.
function OnDialogTabChange( tabCode )
{
	ShowE('divInfo'		, ( tabCode == 'Info' ) ) ;
	ShowE('divTarget'	, ( tabCode == 'Target' ) ) ;
	ShowE('divUpload'	, ( tabCode == 'Upload' ) ) ;
	ShowE('divAttribs'	, ( tabCode == 'Advanced' ) ) ;

	dialog.SetAutoSize( true ) ;
}

//#### Regular Expressions library.
var oRegex = new Object() ;

oRegex.UriProtocol = /^(((http|https|ftp|news):\/\/)|mailto:)/gi ;

oRegex.UrlOnChangeProtocol = /^(http|https|ftp|news):\/\/(?=.)/gi ;

oRegex.UrlOnChangeTestOther = /^((javascript:)|[#\/\.])/gi ;

oRegex.ReserveTarget = /^_(blank|self|top|parent)$/i ;

oRegex.PopupUri = /^javascript:void\(\s*window.open\(\s*'([^']+)'\s*,\s*(?:'([^']*)'|null)\s*,\s*'([^']*)'\s*\)\s*\)\s*$/ ;

// Accessible popups
oRegex.OnClickPopup = /^\s*on[cC]lick="\s*window.open\(\s*this\.href\s*,\s*(?:'([^']*)'|null)\s*,\s*'([^']*)'\s*\)\s*;\s*return\s*false;*\s*"$/ ;

oRegex.PopupFeatures = /(?:^|,)([^=]+)=(\d+|yes|no)/gi ;

//#### Parser Functions

var oParser = new Object() ;

// This method simply returns the two inputs in numerical order. You can even
// provide strings, as the method would parseInt() the values.
oParser.SortNumerical = function(a, b)
{
	return parseInt( a, 10 ) - parseInt( b, 10 ) ;
}

oParser.ParseEMailParams = function(sParams)
{
	// Initialize the oEMailParams object.
	var oEMailParams = new Object() ;
	oEMailParams.Subject = '' ;
	oEMailParams.Body = '' ;

	var aMatch = sParams.match( /(^|^\?|&)subject=([^&]+)/i ) ;
	if ( aMatch ) oEMailParams.Subject = decodeURIComponent( aMatch[2] ) ;

	aMatch = sParams.match( /(^|^\?|&)body=([^&]+)/i ) ;
	if ( aMatch ) oEMailParams.Body = decodeURIComponent( aMatch[2] ) ;

	return oEMailParams ;
}

// This method returns either an object containing the email info, or FALSE
// if the parameter is not an email link.
oParser.ParseEMailUri = function( sUrl )
{
	// Initializes the EMailInfo object.
	var oEMailInfo = new Object() ;
	oEMailInfo.Address = '' ;
	oEMailInfo.Subject = '' ;
	oEMailInfo.Body = '' ;

	var aLinkInfo = sUrl.match( /^(\w+):(.*)$/ ) ;
	if ( aLinkInfo && aLinkInfo[1] == 'mailto' )
	{
		// This seems to be an unprotected email link.
		var aParts = aLinkInfo[2].match( /^([^\?]+)\??(.+)?/ ) ;
		if ( aParts )
		{
			// Set the e-mail address.
			oEMailInfo.Address = aParts[1] ;

			// Look for the optional e-mail parameters.
			if ( aParts[2] )
			{
				var oEMailParams = oParser.ParseEMailParams( aParts[2] ) ;
				oEMailInfo.Subject = oEMailParams.Subject ;
				oEMailInfo.Body = oEMailParams.Body ;
			}
		}
		return oEMailInfo ;
	}
	else if ( aLinkInfo && aLinkInfo[1] == 'javascript' )
	{
		// This may be a protected email.

		// Try to match the url against the EMailProtectionFunction.
		var func = FCKConfig.EMailProtectionFunction ;
		if ( func != null )
		{
			try
			{
				// Escape special chars.
				func = func.replace( /([\/^$*+.?()\[\]])/g, '\\$1' ) ;

				// Define the possible keys.
				var keys = new Array('NAME', 'DOMAIN', 'SUBJECT', 'BODY') ;

				// Get the order of the keys (hold them in the array <pos>) and
				// the function replaced by regular expression patterns.
				var sFunc = func ;
				var pos = new Array() ;
				for ( var i = 0 ; i < keys.length ; i ++ )
				{
					var rexp = new RegExp( keys[i] ) ;
					var p = func.search( rexp ) ;
					if ( p >= 0 )
					{
						sFunc = sFunc.replace( rexp, '\'([^\']*)\'' ) ;
						pos[pos.length] = p + ':' + keys[i] ;
					}
				}

				// Sort the available keys.
				pos.sort( oParser.SortNumerical ) ;

				// Replace the excaped single quotes in the url, such they do
				// not affect the regexp afterwards.
				aLinkInfo[2] = aLinkInfo[2].replace( /\\'/g, '###SINGLE_QUOTE###' ) ;

				// Create the regexp and execute it.
				var rFunc = new RegExp( '^' + sFunc + '$' ) ;
				var aMatch = rFunc.exec( aLinkInfo[2] ) ;
				if ( aMatch )
				{
					var aInfo = new Array();
					for ( var i = 1 ; i < aMatch.length ; i ++ )
					{
						var k = pos[i-1].match(/^\d+:(.+)$/) ;
						aInfo[k[1]] = aMatch[i].replace(/###SINGLE_QUOTE###/g, '\'') ;
					}

					// Fill the EMailInfo object that will be returned
					oEMailInfo.Address = aInfo['NAME'] + '@' + aInfo['DOMAIN'] ;
					oEMailInfo.Subject = decodeURIComponent( aInfo['SUBJECT'] ) ;
					oEMailInfo.Body = decodeURIComponent( aInfo['BODY'] ) ;

					return oEMailInfo ;
				}
			}
			catch (e)
			{
			}
		}

		// Try to match the email against the encode protection.
		var aMatch = aLinkInfo[2].match( /^(?:void\()?location\.href='mailto:'\+(String\.fromCharCode\([\d,]+\))\+'(.*)'\)?$/ ) ;
		if ( aMatch )
		{
			// The link is encoded
			oEMailInfo.Address = eval( aMatch[1] ) ;
			if ( aMatch[2] )
			{
				var oEMailParams = oParser.ParseEMailParams( aMatch[2] ) ;
				oEMailInfo.Subject = oEMailParams.Subject ;
				oEMailInfo.Body = oEMailParams.Body ;
			}
			return oEMailInfo ;
		}
	}
	return false;
}

oParser.CreateEMailUri = function( address, subject, body )
{
	// Switch for the EMailProtection setting.
	switch ( FCKConfig.EMailProtection )
	{
		case 'function' :
			var func = FCKConfig.EMailProtectionFunction ;
			if ( func == null )
			{
				if ( FCKConfig.Debug )
				{
					alert('EMailProtection alert!\nNo function defined. Please set "FCKConfig.EMailProtectionFunction"') ;
				}
				return '';
			}

			// Split the email address into name and domain parts.
			var aAddressParts = address.split( '@', 2 ) ;
			if ( aAddressParts[1] == undefined )
			{
				aAddressParts[1] = '' ;
			}

			// Replace the keys by their values (embedded in single quotes).
			func = func.replace(/NAME/g, "'" + aAddressParts[0].replace(/'/g, '\\\'') + "'") ;
			func = func.replace(/DOMAIN/g, "'" + aAddressParts[1].replace(/'/g, '\\\'') + "'") ;
			func = func.replace(/SUBJECT/g, "'" + encodeURIComponent( subject ).replace(/'/g, '\\\'') + "'") ;
			func = func.replace(/BODY/g, "'" + encodeURIComponent( body ).replace(/'/g, '\\\'') + "'") ;

			return 'javascript:' + func ;

		case 'encode' :
			var aParams = [] ;
			var aAddressCode = [] ;

			if ( subject.length > 0 )
				aParams.push( 'subject='+ encodeURIComponent( subject ) ) ;
			if ( body.length > 0 )
				aParams.push( 'body=' + encodeURIComponent( body ) ) ;
			for ( var i = 0 ; i < address.length ; i++ )
				aAddressCode.push( address.charCodeAt( i ) ) ;

			return 'javascript:void(location.href=\'mailto:\'+String.fromCharCode(' + aAddressCode.join( ',' ) + ')+\'?' + aParams.join( '&' ) + '\')' ;
	}

	// EMailProtection 'none'

	var sBaseUri = 'mailto:' + address ;

	var sParams = '' ;

	if ( subject.length > 0 )
		sParams = '?subject=' + encodeURIComponent( subject ) ;

	if ( body.length > 0 )
	{
		sParams += ( sParams.length == 0 ? '?' : '&' ) ;
		sParams += 'body=' + encodeURIComponent( body ) ;
	}

	return sBaseUri + sParams ;
}

//#### Initialization Code

// oLink: The actual selected link in the editor.
var oLink = dialog.Selection.GetSelection().MoveToAncestorNode( 'A' ) ;
if ( oLink )
	FCK.Selection.SelectNode( oLink ) ;

window.onload = function()
{
	// Translate the dialog box texts.
	oEditor.FCKLanguageManager.TranslatePage(document) ;

	// Fill the Anchor Names and Ids combos.
	LoadAnchorNamesAndIds() ;

	// Load the selected link information (if any).
	LoadSelection() ;

	// Update the dialog box.
	SetLinkType( GetE('cmbLinkType').value ) ;

	// Show/Hide the "Browse Server" button.
	GetE('divBrowseServer').style.display = FCKConfig.LinkBrowser ? '' : 'none' ;

	// Show the initial dialog content.
	GetE('divInfo').style.display = '' ;

	// Set the actual uploader URL.
	if ( FCKConfig.LinkUpload )
		GetE('frmUpload').action = FCKConfig.LinkUploadURL ;

	// Set the default target (from configuration).
	SetDefaultTarget() ;

	// Activate the "OK" button.
	dialog.SetOkButton( true ) ;

	// Select the first field.
	switch( GetE('cmbLinkType').value )
	{
		case 'url' :
			SelectField( 'txtUrl' ) ;
			break ;
		case 'email' :
			SelectField( 'txtEMailAddress' ) ;
			break ;
		case 'anchor' :
			if ( GetE('divSelAnchor').style.display != 'none' )
				SelectField( 'cmbAnchorName' ) ;
			else
				SelectField( 'cmbLinkType' ) ;
	}
}

var bHasAnchors ;

function LoadAnchorNamesAndIds()
{
	// Since version 2.0, the anchors are replaced in the DOM by IMGs so the user see the icon
	// to edit them. So, we must look for that images now.
	var aAnchors = new Array() ;
	var i ;
	var oImages = oEditor.FCK.EditorDocument.getElementsByTagName( 'IMG' ) ;
	for( i = 0 ; i < oImages.length ; i++ )
	{
		if ( oImages[i].getAttribute('_fckanchor') )
			aAnchors[ aAnchors.length ] = oEditor.FCK.GetRealElement( oImages[i] ) ;
	}

	// Add also real anchors
	var oLinks = oEditor.FCK.EditorDocument.getElementsByTagName( 'A' ) ;
	for( i = 0 ; i < oLinks.length ; i++ )
	{
		if ( oLinks[i].name && ( oLinks[i].name.length > 0 ) )
			aAnchors[ aAnchors.length ] = oLinks[i] ;
	}

	var aIds = FCKTools.GetAllChildrenIds( oEditor.FCK.EditorDocument.body ) ;

	bHasAnchors = ( aAnchors.length > 0 || aIds.length > 0 ) ;

	for ( i = 0 ; i < aAnchors.length ; i++ )
	{
		var sName = aAnchors[i].name ;
		if ( sName && sName.length > 0 )
			FCKTools.AddSelectOption( GetE('cmbAnchorName'), sName, sName ) ;
	}

	for ( i = 0 ; i < aIds.length ; i++ )
	{
		FCKTools.AddSelectOption( GetE('cmbAnchorId'), aIds[i], aIds[i] ) ;
	}

	ShowE( 'divSelAnchor'	, bHasAnchors ) ;
	ShowE( 'divNoAnchor'	, !bHasAnchors ) ;
}

function LoadSelection()
{
	if ( !oLink ) return ;

	var sType = 'url' ;

	// Get the actual Link href.
	var sHRef = oLink.getAttribute( '_fcksavedurl' ) ;
	if ( sHRef == null )
		sHRef = oLink.getAttribute( 'href' , 2 ) || '' ;

	// Look for a popup javascript link.
	var oPopupMatch = oRegex.PopupUri.exec( sHRef ) ;
	if( oPopupMatch )
	{
		GetE('cmbTarget').value = 'popup' ;
		sHRef = oPopupMatch[1] ;
		FillPopupFields( oPopupMatch[2], oPopupMatch[3] ) ;
		SetTarget( 'popup' ) ;
	}

	// Accessible popups, the popup data is in the onclick attribute
	if ( !oPopupMatch )
	{
		var onclick = oLink.getAttribute( 'onclick_fckprotectedatt' ) ;
		if ( onclick )
		{
			// Decode the protected string
			onclick = decodeURIComponent( onclick ) ;

			oPopupMatch = oRegex.OnClickPopup.exec( onclick ) ;
			if( oPopupMatch )
			{
				GetE( 'cmbTarget' ).value = 'popup' ;
				FillPopupFields( oPopupMatch[1], oPopupMatch[2] ) ;
				SetTarget( 'popup' ) ;
			}
		}
	}

	// Search for the protocol.
	var sProtocol = oRegex.UriProtocol.exec( sHRef ) ;

	// Search for a protected email link.
	var oEMailInfo = oParser.ParseEMailUri( sHRef );

	if ( oEMailInfo )
	{
		sType = 'email' ;

		GetE('txtEMailAddress').value = oEMailInfo.Address ;
		GetE('txtEMailSubject').value = oEMailInfo.Subject ;
		GetE('txtEMailBody').value    = oEMailInfo.Body ;
	}
	else if ( sProtocol )
	{
		sProtocol = sProtocol[0].toLowerCase() ;
		GetE('cmbLinkProtocol').value = sProtocol ;

		// Remove the protocol and get the remaining URL.
		var sUrl = sHRef.replace( oRegex.UriProtocol, '' ) ;
		sType = 'url' ;
		GetE('txtUrl').value = sUrl ;
	}
	else if ( sHRef.substr(0,1) == '#' && sHRef.length > 1 )	// It is an anchor link.
	{
		sType = 'anchor' ;
		GetE('cmbAnchorName').value = GetE('cmbAnchorId').value = sHRef.substr(1) ;
	}
	else if ( sHRef.substr(0,5) == '?eid=' && sHRef.length > 5 )	// It is an cms element link.
	{
		sType = 'cms_element' ;
		GetE('txtElement').value = sHRef.substr(5) ;
		blnInit = true;
	}
	else if ( sHRef.substr(0,5) == '?mid=' && sHRef.length > 5 )	// It is an cms media link.
	{
		sType = 'cms_media' ;
		GetE('txtMedia').value = sHRef.substr(5) ;
		blnInit = true;
	}
	else					// It is another type of link.
	{
		sType = 'url' ;

		GetE('cmbLinkProtocol').value = '' ;
		GetE('txtUrl').value = sHRef ;
	}

	if ( !oPopupMatch )
	{
		// Get the target.
		var sTarget = oLink.target ;

		if ( sTarget && sTarget.length > 0 )
		{
			if ( oRegex.ReserveTarget.test( sTarget ) )
			{
				sTarget = sTarget.toLowerCase() ;
				GetE('cmbTarget').value = sTarget ;
			}
			else
				GetE('cmbTarget').value = 'frame' ;
			GetE('txtTargetFrame').value = sTarget ;
		}
	}

	// Get Advances Attributes
	GetE('txtAttId').value			= oLink.id ;
	GetE('txtAttName').value		= oLink.name ;
	GetE('cmbAttLangDir').value		= oLink.dir ;
	GetE('txtAttLangCode').value	= oLink.lang ;
	GetE('txtAttAccessKey').value	= oLink.accessKey ;
	GetE('txtAttTabIndex').value	= oLink.tabIndex <= 0 ? '' : oLink.tabIndex ;
	GetE('txtAttTitle').value		= oLink.title ;
	GetE('txtAttContentType').value	= oLink.type ;
	GetE('txtAttCharSet').value		= oLink.charset ;

	var sClass ;
	if ( oEditor.FCKBrowserInfo.IsIE )
	{
		sClass	= oLink.getAttribute('className',2) || '' ;
		// Clean up temporary classes for internal use:
		sClass = sClass.replace( FCKRegexLib.FCK_Class, '' ) ;

		GetE('txtAttStyle').value	= oLink.style.cssText ;
	}
	else
	{
		sClass	= oLink.getAttribute('class',2) || '' ;
		GetE('txtAttStyle').value	= oLink.getAttribute('style',2) || '' ;
	}
	GetE('txtAttClasses').value	= sClass ;

	// Update the Link type combo.
	GetE('cmbLinkType').value = sType ;
}

//#### Link type selection.
function SetLinkType( linkType )
{
	ShowE('divLinkTypeUrl'		, (linkType == 'url') ) ;
	ShowE('divLinkTypeAnchor'	, (linkType == 'anchor') ) ;
	ShowE('divLinkTypeEMail'	, (linkType == 'email') ) ;
	ShowE('divLinkTypeElmntCMS'	, (linkType == 'cms_element') ) ;
	ShowE('divLinkTypeMediaCMS'	, (linkType == 'cms_media') ) ;

	if ( !FCKConfig.LinkDlgHideTarget )
		dialog.SetTabVisibility( 'Target'	, (linkType == 'url') ) ;

	if ( FCKConfig.LinkUpload )
		dialog.SetTabVisibility( 'Upload'	, (linkType == 'url') ) ;

	if ( !FCKConfig.LinkDlgHideAdvanced )
		dialog.SetTabVisibility( 'Advanced'	, (linkType != 'anchor' || bHasAnchors) ) ;

	if ( linkType == 'email' )
		dialog.SetAutoSize( true ) ;
		
	if ( linkType == 'cms_element' ) 
		LoadCMSElements(blnInit);
		
	if ( linkType == 'cms_media' ) 
		LoadCMSMediaItems(blnInit);
}

//#### Target type selection.
function SetTarget( targetType )
{
	GetE('tdTargetFrame').style.display	= ( targetType == 'popup' ? 'none' : '' ) ;
	GetE('tdPopupName').style.display	=
	GetE('tablePopupFeatures').style.display = ( targetType == 'popup' ? '' : 'none' ) ;

	switch ( targetType )
	{
		case "_blank" :
		case "_self" :
		case "_parent" :
		case "_top" :
			GetE('txtTargetFrame').value = targetType ;
			break ;
		case "" :
			GetE('txtTargetFrame').value = '' ;
			break ;
	}

	if ( targetType == 'popup' )
		dialog.SetAutoSize( true ) ;
}

//#### Called while the user types the URL.
function OnUrlChange()
{
	var sUrl = GetE('txtUrl').value ;
	var sProtocol = oRegex.UrlOnChangeProtocol.exec( sUrl ) ;

	if ( sProtocol )
	{
		sUrl = sUrl.substr( sProtocol[0].length ) ;
		GetE('txtUrl').value = sUrl ;
		GetE('cmbLinkProtocol').value = sProtocol[0].toLowerCase() ;
	}
	else if ( oRegex.UrlOnChangeTestOther.test( sUrl ) )
	{
		GetE('cmbLinkProtocol').value = '' ;
	}
}

//#### Called while the user types the target name.
function OnTargetNameChange()
{
	var sFrame = GetE('txtTargetFrame').value ;

	if ( sFrame.length == 0 )
		GetE('cmbTarget').value = '' ;
	else if ( oRegex.ReserveTarget.test( sFrame ) )
		GetE('cmbTarget').value = sFrame.toLowerCase() ;
	else
		GetE('cmbTarget').value = 'frame' ;
}

// Accessible popups
function BuildOnClickPopup()
{
	var sWindowName = "'" + GetE('txtPopupName').value.replace(/\W/gi, "") + "'" ;

	var sFeatures = '' ;
	var aChkFeatures = document.getElementsByName( 'chkFeature' ) ;
	for ( var i = 0 ; i < aChkFeatures.length ; i++ )
	{
		if ( i > 0 ) sFeatures += ',' ;
		sFeatures += aChkFeatures[i].value + '=' + ( aChkFeatures[i].checked ? 'yes' : 'no' ) ;
	}

	if ( GetE('txtPopupWidth').value.length > 0 )	sFeatures += ',width=' + GetE('txtPopupWidth').value ;
	if ( GetE('txtPopupHeight').value.length > 0 )	sFeatures += ',height=' + GetE('txtPopupHeight').value ;
	if ( GetE('txtPopupLeft').value.length > 0 )	sFeatures += ',left=' + GetE('txtPopupLeft').value ;
	if ( GetE('txtPopupTop').value.length > 0 )		sFeatures += ',top=' + GetE('txtPopupTop').value ;

	if ( sFeatures != '' )
		sFeatures = sFeatures + ",status" ;

	return ( "window.open(this.href," + sWindowName + ",'" + sFeatures + "'); return false" ) ;
}

//#### Fills all Popup related fields.
function FillPopupFields( windowName, features )
{
	if ( windowName )
		GetE('txtPopupName').value = windowName ;

	var oFeatures = new Object() ;
	var oFeaturesMatch ;
	while( ( oFeaturesMatch = oRegex.PopupFeatures.exec( features ) ) != null )
	{
		var sValue = oFeaturesMatch[2] ;
		if ( sValue == ( 'yes' || '1' ) )
			oFeatures[ oFeaturesMatch[1] ] = true ;
		else if ( ! isNaN( sValue ) && sValue != 0 )
			oFeatures[ oFeaturesMatch[1] ] = sValue ;
	}

	// Update all features check boxes.
	var aChkFeatures = document.getElementsByName('chkFeature') ;
	for ( var i = 0 ; i < aChkFeatures.length ; i++ )
	{
		if ( oFeatures[ aChkFeatures[i].value ] )
			aChkFeatures[i].checked = true ;
	}

	// Update position and size text boxes.
	if ( oFeatures['width'] )	GetE('txtPopupWidth').value		= oFeatures['width'] ;
	if ( oFeatures['height'] )	GetE('txtPopupHeight').value	= oFeatures['height'] ;
	if ( oFeatures['left'] )	GetE('txtPopupLeft').value		= oFeatures['left'] ;
	if ( oFeatures['top'] )		GetE('txtPopupTop').value		= oFeatures['top'] ;
}

//#### The OK button was hit.
function Ok()
{
	var sUri, sInnerHtml ;
	oEditor.FCKUndo.SaveUndoStep() ;

	switch ( GetE('cmbLinkType').value )
	{
		case 'url' :
			sUri = GetE('txtUrl').value ;

			if ( sUri.length == 0 )
			{
				alert( FCKLang.DlnLnkMsgNoUrl ) ;
				return false ;
			}

			sUri = GetE('cmbLinkProtocol').value + sUri ;

			break ;

		case 'email' :
			sUri = GetE('txtEMailAddress').value ;

			if ( sUri.length == 0 )
			{
				alert( FCKLang.DlnLnkMsgNoEMail ) ;
				return false ;
			}

			sUri = oParser.CreateEMailUri(
				sUri,
				GetE('txtEMailSubject').value,
				GetE('txtEMailBody').value ) ;
			break ;

		case 'anchor' :
			var sAnchor = GetE('cmbAnchorName').value ;
			if ( sAnchor.length == 0 ) sAnchor = GetE('cmbAnchorId').value ;

			if ( sAnchor.length == 0 )
			{
				alert( FCKLang.DlnLnkMsgNoAnchor ) ;
				return false ;
			}

			sUri = '#' + sAnchor ;
			break ;

		case 'cms_element' :
			sUri = GetE('txtElement').value ;

			if ( sUri.length == 0 )
			{
				alert( FCKLang.DlnLnkMsgNoCMSElmnt ) ;
				return false ;
			}

			sUri = "?eid=" + sUri;
			break ;

		case 'cms_media' :
			sUri = GetE('txtMedia').value;
			var mType = sUri.split('_').shift();
			var mId = sUri.split('_').pop();

			if ( sUri.length == 0 )
			{
				alert( FCKLang.DlnLnkMsgNoCMSMedia ) ;
				return false ;
			}
			
			if (mType != 2) 
			{
				alert( FCKLang.DlnLnkMsgNoCMSMedia ) ;
				return false ;
			}

			sUri = "?mid=" + mId;
			break ;
	}

	// If no link is selected, create a new one (it may result in more than one link creation - #220).
	var aLinks = oLink ? [ oLink ] : oEditor.FCK.CreateLink( sUri, true ) ;

	// If no selection, no links are created, so use the uri as the link text (by dom, 2006-05-26)
	var aHasSelection = ( aLinks.length > 0 ) ;
	if ( !aHasSelection )
	{
		sInnerHtml = sUri;

		// Built a better text for empty links.
		switch ( GetE('cmbLinkType').value )
		{
			// anchor: use old behavior --> return true
			case 'anchor':
				sInnerHtml = sInnerHtml.replace( /^#/, '' ) ;
				break ;

			// url: try to get path
			case 'url':
				var oLinkPathRegEx = new RegExp("//?([^?\"']+)([?].*)?$") ;
				var asLinkPath = oLinkPathRegEx.exec( sUri ) ;
				if (asLinkPath != null)
					sInnerHtml = asLinkPath[1];  // use matched path
				break ;

			// mailto: try to get email address
			case 'email':
				sInnerHtml = GetE('txtEMailAddress').value ;
				break ;

			// cms: try to get the name of the element of folder
			case 'cms_element':
				sInnerHtml = GetE('txtElement').value ;
				break ;

			// cms: try to get the name of the element of folder
			case 'cms_media':
				sInnerHtml = GetE('txtMedia').value ;
				break ;
		}

		// Create a new (empty) anchor.
		aLinks = [ oEditor.FCK.InsertElement( 'a' ) ] ;
	}

	for ( var i = 0 ; i < aLinks.length ; i++ )
	{
		oLink = aLinks[i] ;

		if ( aHasSelection )
			sInnerHtml = oLink.innerHTML ;		// Save the innerHTML (IE changes it if it is like an URL).

		oLink.href = sUri ;
		SetAttribute( oLink, '_fcksavedurl', sUri ) ;

		var onclick;
		// Accessible popups
		if( GetE('cmbTarget').value == 'popup' )
		{
			onclick = BuildOnClickPopup() ;
			// Encode the attribute
			onclick = encodeURIComponent( " onclick=\"" + onclick + "\"" )  ;
			SetAttribute( oLink, 'onclick_fckprotectedatt', onclick ) ;
		}
		else
		{
			// Check if the previous onclick was for a popup:
			// In that case remove the onclick handler.
			onclick = oLink.getAttribute( 'onclick_fckprotectedatt' ) ;
			if ( onclick )
			{
				// Decode the protected string
				onclick = decodeURIComponent( onclick ) ;

				if( oRegex.OnClickPopup.test( onclick ) )
					SetAttribute( oLink, 'onclick_fckprotectedatt', '' ) ;
			}
		}

		oLink.innerHTML = sInnerHtml ;		// Set (or restore) the innerHTML

		// Target
		if( GetE('cmbTarget').value != 'popup' )
			SetAttribute( oLink, 'target', GetE('txtTargetFrame').value ) ;
		else
			SetAttribute( oLink, 'target', null ) ;

		// Let's set the "id" only for the first link to avoid duplication.
		if ( i == 0 )
			SetAttribute( oLink, 'id', GetE('txtAttId').value ) ;

		// Advances Attributes
		SetAttribute( oLink, 'name'		, GetE('txtAttName').value ) ;
		SetAttribute( oLink, 'dir'		, GetE('cmbAttLangDir').value ) ;
		SetAttribute( oLink, 'lang'		, GetE('txtAttLangCode').value ) ;
		SetAttribute( oLink, 'accesskey', GetE('txtAttAccessKey').value ) ;
		SetAttribute( oLink, 'tabindex'	, ( GetE('txtAttTabIndex').value > 0 ? GetE('txtAttTabIndex').value : null ) ) ;
		SetAttribute( oLink, 'title'	, GetE('txtAttTitle').value ) ;
		SetAttribute( oLink, 'type'		, GetE('txtAttContentType').value ) ;
		SetAttribute( oLink, 'charset'	, GetE('txtAttCharSet').value ) ;

		if ( oEditor.FCKBrowserInfo.IsIE )
		{
			var sClass = GetE('txtAttClasses').value ;
			// If it's also an anchor add an internal class
			if ( GetE('txtAttName').value.length != 0 )
				sClass += ' FCK__AnchorC' ;
			SetAttribute( oLink, 'className', sClass ) ;

			oLink.style.cssText = GetE('txtAttStyle').value ;
		}
		else
		{
			SetAttribute( oLink, 'class', GetE('txtAttClasses').value ) ;
			SetAttribute( oLink, 'style', GetE('txtAttStyle').value ) ;
		}
	}

	// Select the (first) link.
	oEditor.FCKSelection.SelectNode( aLinks[0] );

	return true ;
}

function BrowseServer()
{
	OpenFileBrowser( FCKConfig.LinkBrowserURL, FCKConfig.LinkBrowserWindowWidth, FCKConfig.LinkBrowserWindowHeight ) ;
}

function SetUrl( url )
{
	GetE('txtUrl').value = url ;
	OnUrlChange() ;
	dialog.SetSelectedTab( 'Info' ) ;
}

function OnUploadCompleted( errorNumber, fileUrl, fileName, customMsg )
{
	// Remove animation
	window.parent.Throbber.Hide() ;
	GetE( 'divUpload' ).style.display  = '' ;

	switch ( errorNumber )
	{
		case 0 :	// No errors
			alert( 'Your file has been successfully uploaded' ) ;
			break ;
		case 1 :	// Custom error
			alert( customMsg ) ;
			return ;
		case 101 :	// Custom warning
			alert( customMsg ) ;
			break ;
		case 201 :
			alert( 'A file with the same name is already available. The uploaded file has been renamed to "' + fileName + '"' ) ;
			break ;
		case 202 :
			alert( 'Invalid file type' ) ;
			return ;
		case 203 :
			alert( "Security error. You probably don't have enough permissions to upload. Please check your server." ) ;
			return ;
		case 500 :
			alert( 'The connector is disabled' ) ;
			break ;
		default :
			alert( 'Error on file upload. Error number: ' + errorNumber ) ;
			return ;
	}

	SetUrl( fileUrl ) ;
	GetE('frmUpload').reset() ;
}

var oUploadAllowedExtRegex	= new RegExp( FCKConfig.LinkUploadAllowedExtensions, 'i' ) ;
var oUploadDeniedExtRegex	= new RegExp( FCKConfig.LinkUploadDeniedExtensions, 'i' ) ;

function CheckUpload()
{
	var sFile = GetE('txtUploadFile').value ;

	if ( sFile.length == 0 )
	{
		alert( 'Please select a file to upload' ) ;
		return false ;
	}

	if ( ( FCKConfig.LinkUploadAllowedExtensions.length > 0 && !oUploadAllowedExtRegex.test( sFile ) ) ||
		( FCKConfig.LinkUploadDeniedExtensions.length > 0 && oUploadDeniedExtRegex.test( sFile ) ) )
	{
		OnUploadCompleted( 202 ) ;
		return false ;
	}

	// Show animation
	window.parent.Throbber.Show( 100 ) ;
	GetE( 'divUpload' ).style.display  = 'none' ;

	return true ;
}

function SetDefaultTarget()
{
	var target = FCKConfig.DefaultLinkTarget || '' ;

	if ( oLink || target.length == 0 )
		return ;

	switch ( target )
	{
		case '_blank' :
		case '_self' :
		case '_parent' :
		case '_top' :
			GetE('cmbTarget').value = target ;
			break ;
		default :
			GetE('cmbTarget').value = 'frame' ;
			break ;
	}

	GetE('txtTargetFrame').value = target ;
}


//#### START PunchCMS specific code.
var objLoader;
var objElmntTraverse;
var objMediaTraverse;
var intElmntId = 0;
var intMediaId = 0;

function LoadCMSElements(blnInit) {
	var strUrl = "../../../../ajax.php";
	if (blnInit != undefined && blnInit) {
		intElmntId = GetE('txtElement').value;
		var strPost = "cmd=Elements::getParentHTML&eid=" + intElmntId;
	} else {
		var strPost = "cmd=Elements::getChildrenHTML&parentId=" + intElmntId;
	}
	var objTable = $('divElements').getElementsBySelector('tbody')[0];
		
	objLoader = CMSElementsLoader();
	objTable.appendChild(objLoader);
	if (objElmntTraverse) objTable.removeChild(objElmntTraverse);
		
	var myAjax = new Ajax.Request(
			strUrl, 
			{
				method: 'get', 
				parameters: strPost, 
				onComplete: ShowCMSElements
			});
}

function SetCMSElement(objSelect) {
	var intParentId = objSelect[objSelect.selectedIndex].value;
	var objParentRow = $(objSelect.id).up('tr');
	var objRows = objParentRow.nextSiblings();
	var objSelects = $('divElements').getElementsBySelector('select');
	
	for (var intCount = 0; intCount < objSelects.length; intCount++) {
		var objRow = $(objSelects[intCount].id).up('tr');
		for (var i = 0; i < objRows.length; i++) {
			if (objRows[i] == objRow) {
				objRow.remove();
			}
		}
	}
	
	intElmntId = intParentId;
	$("txtElement").value = intParentId;

	var objTable = $('divElements').getElementsBySelector('tbody')[0];
	objElmntTraverse = CMSElementsTraverse();
	objTable.appendChild(objElmntTraverse);
}

function ShowCMSElements(objXHR) {
	var objResponse = objXHR.responseXML;
	var objFields = objResponse.getElementsByTagName("field");
	var objTable = $('divElements').getElementsBySelector('tbody')[0];
	
	if (objLoader) objTable.removeChild(objLoader);
	
	for (var i = 0; i < objFields.length; i++) {
		var strValue = objFields[i].firstChild.nodeValue;
		var intParentId = objFields[i].attributes[0].value;
		if (strValue) {
			var objRow = CMSElementsRow(strValue, intParentId);
			objTable.appendChild(objRow);
		}
	}
			
	window.parent.SetAutoSize(true);
	var objSelect = $("cmbLinkParent_" + intParentId);
	if (objSelect) {
		SetCMSElement(objSelect);
	}
}

function CMSElementsLoader() {	
	var strReturn = "<td>Loading...</td>";
	
	var objColumn = document.createElement("td");
	objColumn.innerHTML = strReturn;
	var objReturn = document.createElement("tr");
	objReturn.appendChild(objColumn);
	
	return objReturn;
}

function CMSElementsTraverse() {
	var strReturn = "<td><td><a href=\"javascript:;\" onclick=\"LoadCMSElements()\">One level deeper</a></td></td>";
	
	var objColumn = document.createElement("td");
	objColumn.innerHTML = strReturn;
	var objReturn = document.createElement("tr");
	objReturn.appendChild(objColumn);
	
	return objReturn;
}

function CMSElementsRow(strValue, intParentId) {
	var strReturn = "<select id=\"cmbLinkParent_" + intParentId + "\" style=\"width:100%;margin-bottom:10px;\" onchange=\"SetCMSElement(this)\">";
	strReturn += strValue;
	strReturn += "</select>";

	var objColumn = document.createElement("td");
	objColumn.innerHTML = strReturn;
	var objReturn = document.createElement("tr");
	objReturn.appendChild(objColumn);
	
	return objReturn;
}

function LoadCMSMediaItems(blnInit) {
	var strUrl = "../../../../ajax.php";
	if (blnInit != undefined && blnInit) {
		intMediaId = GetE('txtMedia').value;
		var strPost = "cmd=StorageItems::getParentHTML&eid=" + String(intMediaId).split('_').pop();
	} else {
		var strPost = "cmd=StorageItems::getChildrenHTML&parentId=" + String(intMediaId).split('_').pop();
	}
	var objTable = $('divMedia').getElementsBySelector('tbody')[0];
		
	objLoader = CMSElementsLoader();
	objTable.appendChild(objLoader);
	if (objMediaTraverse) objTable.removeChild(objMediaTraverse);
		
	var myAjax = new Ajax.Request(
			strUrl, 
			{
				method: 'get', 
				parameters: strPost, 
				onComplete: ShowCMSMediaItems
			});
}

function SetCMSMedia(objSelect) {
	var intParentId = objSelect[objSelect.selectedIndex].value;
	var objParentRow = $(objSelect.id).up('tr');
	var objRows = objParentRow.nextSiblings();
	var objSelects = $('divMedia').getElementsBySelector('select');
	
	for (var intCount = 0; intCount < objSelects.length; intCount++) {
		var objRow = $(objSelects[intCount].id).up('tr');
		for (var i = 0; i < objRows.length; i++) {
			if (objRows[i] == objRow) {
				objRow.remove();
			}
		}
	}
	
	intMediaId = intParentId;
	$("txtMedia").value = intParentId;
}

function ShowCMSMediaItems(objXHR) {
	var objResponse = objXHR.responseXML;
	var objFields = objResponse.getElementsByTagName("field");
	var objTable = $('divMedia').getElementsBySelector('tbody')[0];
	
	if (objLoader) objTable.removeChild(objLoader);
	
	for (var i = 0; i < objFields.length; i++) {
		var strValue = objFields[i].firstChild.nodeValue;
		var intParentId = objFields[i].attributes[0].value;
		if (strValue) {
			var objRow = CMSMediaRow(strValue, intParentId);
			objTable.appendChild(objRow);
		}
	}
			
	window.parent.SetAutoSize(true);
	var objSelect = $("cmbLinkParent_" + intParentId);
	SetCMSMedia(objSelect);
	
	objMediaTraverse = CMSMediaTraverse();
	objTable.appendChild(objMediaTraverse);
}

function CMSMediaRow(strValue, intParentId) {
	var strReturn = "<select id=\"cmbLinkParent_" + intParentId + "\" style=\"width:100%;margin-bottom:10px;\" onchange=\"SetCMSMedia(this)\">";
	strReturn += strValue;
	strReturn += "</select>";

	var objColumn = document.createElement("td");
	objColumn.innerHTML = strReturn;
	var objReturn = document.createElement("tr");
	objReturn.appendChild(objColumn);
	
	return objReturn;
}

function CMSMediaTraverse() {
	var strReturn = "<td><td><a href=\"javascript:;\" onclick=\"LoadCMSMediaItems()\">One level deeper</a></td></td>";
	
	var objColumn = document.createElement("td");
	objColumn.innerHTML = strReturn;
	var objReturn = document.createElement("tr");
	objReturn.appendChild(objColumn);
	
	return objReturn;
}
//#### END PunchCMS specific code.
