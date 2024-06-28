<?php
/**
 * SpokedbAccession
 * 
 * @copyright Copyright 2018 Eric C. Weig 
 * @license http://opensource.org/licenses/MIT MIT
 */

/**
 * The SpokedbAccession plugin.
 * 
 * @package Omeka\Plugins\spokedbAccession
 */
    class SpokedbAccessionPlugin extends Omeka_Plugin_AbstractPlugin
    {
    
    // Define Hooks

    protected $_hooks = array(
        'install',
        'uninstall',
		'admin_footer',
		'define_routes'
	);
	
	public function hookInstall()
    {
      
    }
    
    public function hookUninstall()
    {
     
    }
	
	function hookDefineRoutes($args)
    {
    $router = $args['router'];

    }
	
    # Ensure that interviews have an accession number.
    #
    # The interview must be assigned to a collection to get an accession
    # number.  
    
    public function hookAdminFooter(){
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; 

        if (strpos($actual_link, '/items/edit/') !== false) {
        $interview_accession = metadata('item', array('General', 'Interview Accession'));
        $collection = get_collection_for_item();
        if ($collection) {
        $collectionId = metadata($collection, 'id');
        $nitems = metadata($collection, 'total items');
        }
        if ($interview_accession == NULL && $collectionId !== NULL) {
        $proj = metadata($collection, array('Project', 'Project Code'));
	    $curYear = date('Y');
	    $abrev = "oh";
	    
	    // updated $numvar to string... --bd 02.26.24
	    // Accession string format: 
	    // YYYY + oh + 4digitaccessionforyear + _ + projectcode + 4digitaccessionforproject
	    // ...enabling 4digitaccessionforproject to be incremented  
	    // settype($numvar,'integer');
	    $numvar = "";

	    $proj_test = "_" . $proj . $numvar;
	    $items = get_records('Item', array(), 30000);
        $all_accessions = array();
	    
	    set_loop_records('items', $items);
	    foreach (loop('items') as $item): 
	    $accnums = metadata('item', array('General','Interview Accession'));
	    $accessions[] = $accnums;
	    endforeach;
	    
	    $numof = array();
	    
	    foreach($accessions as $accession) {
	    if (strpos($accession, $curYear) !== false) {
	    $totalints = substr($accession, 0, 9);
	    $totalints = strtok($accession, '_');
	    $digits = substr($totalints, -3);
	    $digits = substr($totalints, 6);
	    $numof[] = $digits;
		}
	    }
    	
    	if (!empty($numof)) { 
    	$total_seq = (max($numof));
        } else {
        $total_seq = 0;
        }
    	
	    $numbers = array();
	    
	    foreach($accessions as $accession) {
	    if (strpos($accession, $proj_test) !== false) {
	    $arrseqs = explode("_", $accession);
        $seqs = $arrseqs[1];
	    $seqs = str_replace("$proj","","$seqs");
	    $numbers[] = $seqs;
		}
	    }

	    if (!empty($numbers)) { 
    	$seq = (max($numbers));
        } else {
        $seq = 0;
        }

	    $plus_one = 1;
	    $sum_total_seq = $total_seq + $plus_one;
	    $sum_total_seq = str_pad($sum_total_seq,4,"0",STR_PAD_LEFT);
	    $sum_seq = $seq + $plus_one;
	    $sum_seq = str_pad($sum_seq,4,"0",STR_PAD_LEFT);
	    $oh = "oh";
	    $div = "_";
        $new_accession = $curYear . $oh . $sum_total_seq . $div . $proj . $sum_seq;
        } else { 
        $new_accession = "";
        }
        
    	?>
    	<script>
        var myAccession = document.getElementById("Elements-252-0-text");
        if (myAccession && myAccession.value) {
            console.log("My input has a value!");
        } else  {
        window.addEventListener('load', function() {
        document.getElementById("Elements-252-0-text").value = "<?php echo $new_accession; // HTML ?>";
       });

        }
       </script>
    
        <?php
        } elseif (strpos($actual_link, '/items/edit/') == false) {
        //do nothing
        }    
    
    }
}
