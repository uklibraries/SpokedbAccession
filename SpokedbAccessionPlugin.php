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
    # for 2019, change in line 112 from $new_accession to $new_accession2
    
    public function hookAdminFooter(){
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; 

        if (strpos($actual_link, '/items/edit/') !== false) {
        $interview_accession = metadata('item', array('General', 'Interview Accession'));
        $collection = get_collection_for_item();
        if ($collection) {
        $collectionId = metadata($collection, 'id');
        }
        if ($interview_accession == NULL && $collectionId !== NULL) {
        $proj = metadata($collection, array('Project', 'Project Code'));
	    $curYear = date('Y');
	    $abrev = "oh";
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
	    $digits = substr($totalints, -3); 
	    $numof[] = $digits;
		}
	    }
    	$total_seq = (max($numof));
	    $numbers = array();
	    
	    foreach($accessions as $accession) {
	    if (strpos($accession, $proj) !== false) {
	    $seqs = substr($accession, -3);
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
	    $sum_total_seq = str_pad($sum_total_seq,3,"0",STR_PAD_LEFT);
	    $sum_total_seq2 = str_pad($sum_total_seq,4,"0",STR_PAD_LEFT);
	    $sum_seq = $seq + $plus_one;
	    $sum_seq = str_pad($sum_seq,3,"0",STR_PAD_LEFT);
	    $sum_seq2 = str_pad($sum_seq,4,"0",STR_PAD_LEFT);
	    $oh = "oh";
	    $div = "_";
	    $new_accession = $curYear . $oh . $sum_total_seq . $div . $proj . $sum_seq;
        $new_accession2 = $curYear . $oh . $sum_total_seq2 . $div . $proj . $sum_seq2;
        } else { 
        $new_accession = "";
        $new_accession2 = "";
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
