<?php

/*
+---------------------------------------------------------------------------+
| Revive Adserver                                                           |
| http://www.revive-adserver.com                                            |
|                                                                           |
| Copyright: See the COPYRIGHT.txt file.                                    |
| License: GPLv2 or later, see the LICENSE.txt file.                        |
+---------------------------------------------------------------------------+
*/

/*-------------------------------------------------------*/
/* Return misc capabilities                              */
/*-------------------------------------------------------*/

function MAX_layerGetLimitations()
{
	$agent = $GLOBALS['_MAX']['CLIENT'];

	$compatible = $agent['browser'] == 'ie' && $agent['maj_ver'] < 5 ||
				  $agent['browser'] == 'mz' && $agent['maj_ver'] < 1 ||
				  $agent['browser'] == 'fx' && $agent['maj_ver'] < 1 ||
				  $agent['browser'] == 'op' && $agent['maj_ver'] < 5
				  ? false : true;

	$richmedia = true;

	return array (
		'richmedia'  => $richmedia,
		'compatible' => $compatible
	);
}



/*-------------------------------------------------------*/
/* Output JS code for the layer                          */
/*-------------------------------------------------------*/

function MAX_layerPutJs($output, $uniqid)
{
	global $closetime, $closebutton;

	// Register input variables
	MAX_commonRegisterGlobalsArray(array('closetime'));


	$closebutton 	= 't';
	// $closetime 		= 7;

	// Calculate layer size (inc. borders)
	$layer_width = $output['width'] + 2 + $padding*2;
	$layer_height = $output['height'] + 2 + ($closebutton == 't' ? 11 : 0) + $padding*2;

?>

function MAX_findObj(n, d) {
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
  d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i>d.layers.length;i++) x=MAX_findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}

function MAX_getClientSize() {
	if (window.innerHeight >= 0) {
		return [window.innerWidth, window.innerHeight];
	} else if (document.documentElement && document.documentElement.clientWidth > 0) {
		return [document.documentElement.clientWidth,document.documentElement.clientHeight]
	} else if (document.body.clientHeight > 0) {
		return [document.body.clientWidth,document.body.clientHeight]
	} else {
		return [0, 0]
	}
}

function MAX_adlayers_place_<?php echo $uniqid; ?>()
{
	var c = MAX_findObj('MAX_<?php echo $uniqid; ?>');

	if (!c)
		return false;

	_s='style'

	var clientSize = MAX_getClientSize()
	ih = clientSize[1]
	iw = clientSize[0]

	if(document.all && !window.opera)
	{
		sl = document.body.scrollLeft || document.documentElement.scrollLeft;
		st = document.body.scrollTop || document.documentElement.scrollTop;
		of = 0;
	}
	else
	{
		sl = window.pageXOffset;
		st = window.pageYOffset;

		if (window.opera)
			of = 0;
		else
			of = 16;
	}

	c[_s].visibility = MAX_adlayers_visible_<?php echo $uniqid; ?>;
    c[_s].display = MAX_adlayers_display_<?php echo $uniqid; ?>;
    if (MAX_adlayers_display_<?php echo $uniqid; ?> == 'none') {
        c.innerHTML = '&nbsp;';
    }
}


function MAX_simplepop_<?php echo $uniqid; ?>(what)
{
	var c = MAX_findObj('MAX_<?php echo $uniqid; ?>');
	if (!c)
		return false;
	if (c.style)
		c = c.style;
	switch(what)
	{
		case 'close':
			MAX_adlayers_visible_<?php echo $uniqid; ?> = 'hidden';
            MAX_adlayers_display_<?php echo $uniqid; ?> = 'none';
			MAX_adlayers_place_<?php echo $uniqid; ?>();
			window.clearInterval(MAX_adlayers_timerid_<?php echo $uniqid; ?>);
			break;

		case 'open':
			MAX_adlayers_visible_<?php echo $uniqid; ?> = 'visible';
			MAX_adlayers_display_<?php echo $uniqid; ?> = 'block';
			MAX_adlayers_place_<?php echo $uniqid; ?>();
			MAX_adlayers_timerid_<?php echo $uniqid; ?> = window.setInterval('MAX_adlayers_place_<?php echo $uniqid; ?>()', 10);

<?php if (isset($closetime) && (int)$closetime > 0)
	echo "\t\t\treturn window.setTimeout('MAX_simplepop_".$uniqid."(\\'close\\')', ".($closetime * 1000).");";
?>
		break;
	}
}

var MAX_adlayers_timerid_<?php echo $uniqid; ?>;
var MAX_adlayers_visible_<?php echo $uniqid; ?>;
var MAX_adlayers_display_<?php echo $uniqid; ?>;
MAX_simplepop_<?php echo $uniqid; ?>('open');
<?php
}



/*-------------------------------------------------------*/
/* Return HTML code for the layer                        */
/*-------------------------------------------------------*/

function MAX_layerGetHtml($output, $uniqid)
{
	global  $closebutton;

	$conf = $GLOBALS['_MAX']['CONF'];


	// Register input variables
	MAX_commonRegisterGlobalsArray(array('closebutton'));

	$closebutton = 't';

	// Calculate layer size (inc. borders)
	$layer_width = $output['width'] + 2 + $padding*2;
	$layer_height = $output['height'] + 2 + ($closebutton == 't' ? 11 : 0) + $padding*2;

	// Create imagepath
	$imagepath = _adRenderBuildImageUrlPrefix() . '/layerstyles/phsite/';

	// return HTML code
	return '
<div id="MAX_'.$uniqid.'" style="background: rgba(0,0,0,0.7); position:absolute; width:100%; height:100%; z-index:6000; left: 0px; top: 0px; visibility: hidden;">
	<table cellspacing="0" cellpadding="0" style="height:100%;width:'.$output['width'].'px;margin:0 auto">
		<tr>
			<td align="center" valign="middle" style="display:table-cell !important;">
				<a style="display:block;margin:0 0 10px;text-align:right;color:#fff" href="javascript:;" onClick="MAX_simplepop_'.$uniqid.'(\'close\'); return false;" style="color:#0000ff">
					<img src="'.$imagepath . 'close_pop_mini.png" width="15" height="15" alt="Fermer">
				</a>'
				.$output['html']
			.'</td>
		</tr>
	</table>
</div>
';
}

?>