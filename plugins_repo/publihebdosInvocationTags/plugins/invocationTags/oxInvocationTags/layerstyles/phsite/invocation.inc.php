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

// Define constant used to place code generator
define('phpAds_adLayerLoaded', true);


// Register input variables
MAX_commonRegisterGlobalsArray(array('target', 'align', 'padding', 'closebutton', 'backcolor', 'bordercolor',
					   'valign', 'closetime', 'shifth', 'shiftv', 'nobg', 'noborder'));


/**
 *
 * Layerstyle for invocation tag plugin
 *
 */
class Plugins_oxInvocationTags_Adlayer_Layerstyles_Phsite_Invocation extends Plugins_InvocationTags_OxInvocationTags_adlayer
{

    /*-------------------------------------------------------*/
    /* Place ad-generator settings                           */
    /*-------------------------------------------------------*/

    function placeLayerSettings ()
    {
        global $closetime, $tabindex;

        if (!isset($closetime)) $closetime = '-';

        $buffer = '';

        $buffer .= "<tr><td width='30'>&nbsp;</td>";
        $buffer .= "<td width='200'>".$this->translate("Automatically close after")."</td><td width='370'>";
            $buffer .= "<input class='flat' type='text' name='closetime' size='' value='".(isset($closetime) ? $closetime : '-')."' style='width:60px;' tabindex='".($tabindex++)."'> ".$GLOBALS['strAbbrSeconds']."</td></tr>";
        $buffer .= "<tr><td width='30'><img src='" . OX::assetPath() . "/images/spacer.gif' height='5' width='100%'></td></tr>";

    	return $buffer;
    }



    /*-------------------------------------------------------*/
    /* Place ad-generator settings                           */
    /*-------------------------------------------------------*/

    function generateLayerCode(&$mi)
    {
    	$conf = $GLOBALS['_MAX']['CONF'];

    	$mi->parameters[] = 'layerstyle=phsite';

    	if (!empty($mi->charset)) {
    	    $mi->parameters[] = 'charset='.urlencode($mi->charset);
    	}
    	if (isset($closetime) && $closetime > 0) {
    		$mi->parameters[] = 'closetime='.$closetime;
    	}

    	$scriptUrl = "http:".MAX_commonConstructPartialDeliveryUrl($conf['file']['layer']);
    	if (sizeof($mi->parameters) > 0) {
    		$scriptUrl .= "?".implode ("&", $mi->parameters);
    	}

    	$buffer = "<script type='text/javascript'><!--//<![CDATA[
        if(window.matchMedia('(max-device-width: 600px)').matches) {
            var ox_u = '{$scriptUrl}';
            if (document.context) ox_u += '&context=' + escape(document.context);
            document.write(\"<scr\"+\"ipt type='text/javascript' src='\" + ox_u + \"'></scr\"+\"ipt>\");
        }
//]]>--></script>";
    	return $buffer;
    }



    /*-------------------------------------------------------*/
    /* Return $show var for generators                       */
    /*-------------------------------------------------------*/

    function getlayerShowVar ()
    {
    	return array (
            'spacer'      => MAX_PLUGINS_INVOCATION_TAGS_STANDARD,
    		'what'        => MAX_PLUGINS_INVOCATION_TAGS_STANDARD,
    		//'acid'        => MAX_PLUGINS_INVOCATION_TAGS_STANDARD,
    		'campaignid'  => MAX_PLUGINS_INVOCATION_TAGS_STANDARD,
    		'target'      => MAX_PLUGINS_INVOCATION_TAGS_STANDARD,
    		'source'      => MAX_PLUGINS_INVOCATION_TAGS_STANDARD,
    		'charset'     => MAX_PLUGINS_INVOCATION_TAGS_STANDARD,
    		'layerstyle'  => MAX_PLUGINS_INVOCATION_TAGS_CUSTOM,
    		'layercustom' => MAX_PLUGINS_INVOCATION_TAGS_CUSTOM
    	);
    }



    /*-------------------------------------------------------*/
    /* Dec2Hex                                               */
    /*-------------------------------------------------------*/

    function toHex($d)
    {
    	return strtoupper(sprintf("%02x", $d));
    }



    /*-------------------------------------------------------*/
    /* Add scripts and map for color pickers                 */
    /*-------------------------------------------------------*/

    function settings_cp_map()
    {
    	static $done = false;

    	if (!$done)
    	{
    		$done = true;
    ?>
    <script type="text/javascript">
    <!--// <![CDATA[
    var current_cp = null;
    var current_cp_oldval = null;
    var current_box = null;

    function c_pick(value)
    {
    	if (current_cp)
    	{
    		current_cp.value = value;
    		c_update();
    	}
    }

    function c_update()
    {
    	if (!current_cp.value.match(/^#[0-9a-f]{6}$/gi))
    	{
    		current_cp.value = current_cp_oldval;
    		return;
    	}

    	current_cp.value.toUpperCase();
    	current_box.style.backgroundColor = current_cp.value;
    }

    // ]]> -->
    </script>
    <?php
    		echo "<map name=\"colorpicker\">\n";

    		$x = 2;

    		for($i=1; $i <= 255*6; $i+=8)
    		{
    			if($i > 0 && $i <=255 * 1)
    				$incColor='#FF'.$this->toHex($i).'00';
    			elseif ($i>255*1 && $i <=255*2)
    				$incColor='#'.$this->toHex(255-($i-255)).'FF00';
    			elseif ($i>255*2 && $i <=255*3)
    				$incColor='#00FF'.$this->toHex($i-(2*255));
    			elseif ($i>255*3 && $i <=255*4)
    				$incColor='#00'.$this->toHex(255-($i-(3*255))).'FF';
    			elseif ($i>255*4 && $i <=255*5)
    				$incColor='#'.$this->toHex($i-(4*255)).'00FF';
    			elseif ($i>255*5 && $i <255*6)
    				$incColor='#FF00' . $this->toHex(255-($i-(5*255)));

    			echo "<area shape='rect' coords='$x,0,".($x+1).",9' alt='' href='javascript:c_pick(\"$incColor\")' />\n"; $x++;
    		}

    		$x = 2;

    		for($j = 0; $j < 255; $j += 1.34)
    		{
    			$i = round($j);
    			$incColor = '#'.$this->toHex($i).$this->toHex($i).$this->toHex($i);
    			echo "<area shape='rect' coords='$x,11,".($x+1).",20' alt='' href='javascript:c_pick(\"$incColor\")' />\n"; $x++;
    		}

    		echo "</map>";
    	}
    }
}

?>